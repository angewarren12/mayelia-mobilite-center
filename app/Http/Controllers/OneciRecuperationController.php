<?php

namespace App\Http\Controllers;

use App\Models\DossierOneciItem;
use App\Services\SmsService;
use App\Jobs\SendSmsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OneciRecuperationController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Liste des dossiers envoyés à ONECI (prêts pour récupération après appel ONECI)
     */
    public function cartesPrete(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        // Récupérer tous les dossiers envoyés et non encore récupérés
        $query = DossierOneciItem::whereHas('transfer', function($q) use ($user) {
                $q->where('statut', 'envoye');
                // Filtrer par centre si agent
                if ($user->role === 'agent' && $user->centre_id) {
                    $q->where('centre_id', $user->centre_id);
                }
            })
            ->where('statut', '!=', 'recupere') // Pas déjà récupérés
            ->with([
                'dossierOuvert.rendezVous.client',
                'dossierOuvert.rendezVous.service',
                'transfer.centre'
            ]);

        // Filtres
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code_barre', 'like', "%{$search}%")
                  ->orWhereHas('dossierOuvert.rendezVous.client', function($q2) use ($search) {
                      $q2->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $dossiers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('oneci-recuperation.cartes-prete', compact('dossiers'));
    }

    /**
     * Interface de scan pour récupération
     */
    public function scannerRecuperation()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        return view('oneci-recuperation.scanner');
    }

    /**
     * API pour scanner lors de la récupération
     */
    public function scannerCodeRecuperation(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'code_barre' => 'required|string'
        ]);

        // Accepter tous les dossiers envoyés (pas besoin que carte_prete soit défini)
        // ONECI appelle directement, donc on accepte tous les dossiers du transfert
        $item = DossierOneciItem::where('code_barre', $request->code_barre)
            ->whereHas('transfer', function($q) {
                $q->where('statut', 'envoye'); // Transfert doit être envoyé
            })
            ->where('statut', '!=', 'recupere') // Pas déjà récupéré
            ->with([
                'dossierOuvert.rendezVous.client',
                'dossierOuvert.rendezVous.service',
                'transfer.centre'
            ])
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Code-barres introuvable, dossier déjà récupéré, ou transfert non envoyé'
            ], 404);
        }

        // Vérifier que le centre correspond si agent
        if ($user->role === 'agent' && $user->centre_id && $item->transfer->centre_id !== $user->centre_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce dossier n\'appartient pas à votre centre'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'code_barre' => $item->code_barre,
                'client' => $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A',
                'service' => $item->dossierOuvert->rendezVous->service->nom ?? 'N/A',
                'centre' => $item->transfer->centre->nom ?? 'N/A',
                'date_envoi' => $item->transfer->date_envoi->format('d/m/Y'),
                'statut' => $item->statut,
            ]
        ]);
    }

    /**
     * Confirmer la récupération d'un dossier (appelé après scan)
     */
    public function confirmerRecuperation(DossierOneciItem $item)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        // Vérifier que le dossier n'est pas déjà récupéré
        if ($item->statut === 'recupere') {
            return back()->with('error', 'Ce dossier a déjà été récupéré.');
        }

        // Vérifier que le transfert est bien envoyé
        if ($item->transfer->statut !== 'envoye') {
            return back()->with('error', 'Ce transfert n\'a pas encore été envoyé à l\'ONECI.');
        }

        // Vérifier que le centre correspond si agent
        if ($user->role === 'agent' && $user->centre_id && $item->transfer->centre_id !== $user->centre_id) {
            return back()->with('error', 'Ce dossier n\'appartient pas à votre centre.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour l'item - passage direct à récupéré
            $item->update([
                'statut' => 'recupere',
                'date_recuperation' => now(),
                'agent_mayelia_id' => $user->id,
            ]);

            // Mettre à jour le dossier
            $item->dossierOuvert->update([
                'statut_oneci' => 'recupere',
                'date_recuperation' => now(),
            ]);

            // Mettre à jour le transfert si tous les items sont récupérés
            $transfer = $item->transfer;
            $allRecuperes = $transfer->items()->where('statut', '!=', 'recupere')->count() === 0;
            if ($allRecuperes) {
                $transfer->update([
                    'statut' => 'recupere',
                    'date_recuperation' => now(),
                ]);
            }

            // Envoyer SMS au client
            $rendezVous = $item->dossierOuvert->rendezVous;
            $client = $rendezVous->client;
            $centre = $rendezVous->centre;

            $message = "Votre carte est prête et récupérable à l'agence {$centre->nom}. Merci de passer la récupérer.";
            SendSmsJob::dispatch($client->telephone, $message);

            DB::commit();

            return back()->with('success', 'Récupération confirmée. SMS envoyé au client.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Interface de scan en lot
     */
    public function scannerLot()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        return view('oneci-recuperation.scanner-lot');
    }

    /**
     * Confirmer la récupération en lot (plusieurs dossiers à la fois)
     */
    public function confirmerRecuperationLot(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'exists:dossier_oneci_items,id'
        ]);

        $items = DossierOneciItem::whereIn('id', $request->items)
            ->where('statut', '!=', 'recupere')
            ->whereHas('transfer', function($q) use ($user) {
                $q->where('statut', 'envoye');
                if ($user->role === 'agent' && $user->centre_id) {
                    $q->where('centre_id', $user->centre_id);
                }
            })
            ->with(['dossierOuvert.rendezVous.client', 'dossierOuvert.rendezVous.centre', 'transfer'])
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier valide à confirmer'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $confirmed = 0;
            $smsSent = 0;

            foreach ($items as $item) {
                // Mettre à jour l'item
                $item->update([
                    'statut' => 'recupere',
                    'date_recuperation' => now(),
                    'agent_mayelia_id' => $user->id,
                ]);

                // Mettre à jour le dossier
                $item->dossierOuvert->update([
                    'statut_oneci' => 'recupere',
                    'date_recuperation' => now(),
                ]);

                // Envoyer SMS au client
                $client = $item->dossierOuvert->rendezVous->client;
                $centre = $item->dossierOuvert->rendezVous->centre;
                
                if ($client && $client->telephone) {
                    $message = "Votre carte est prête et récupérable à l'agence {$centre->nom}. Merci de passer la récupérer.";
                    SendSmsJob::dispatch($client->telephone, $message);
                    $smsSent++;
                }

                $confirmed++;

                // Mettre à jour le transfert si tous les items sont récupérés
                $transfer = $item->transfer;
                $allRecuperes = $transfer->items()->where('statut', '!=', 'recupere')->count() === 0;
                if ($allRecuperes) {
                    $transfer->update([
                        'statut' => 'recupere',
                        'date_recuperation' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$confirmed} dossier(s) confirmé(s). {$smsSent} SMS envoyé(s)."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation: ' . $e->getMessage()
            ], 500);
        }
    }
}
