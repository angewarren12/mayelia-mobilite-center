<?php

namespace App\Http\Controllers;

use App\Models\DossierOneciItem;
use App\Models\DossierOneciTransfer;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OneciController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Dashboard ONECI
     */
    public function dashboard()
    {
        // Le middleware s'occupe de la vérification du rôle
        $user = Auth::user();

        // Statistiques
        $stats = [
            'recus_aujourdhui' => DossierOneciItem::where('statut', 'recu')
                ->whereDate('date_reception', today())
                ->count(),
            'en_traitement' => DossierOneciItem::where('statut', 'traite')->count(),
            'cartes_prete' => DossierOneciItem::where('statut', 'carte_prete')->count(),
            'recuperes' => DossierOneciItem::where('statut', 'recupere')
                ->whereDate('date_recuperation', today())
                ->count(),
        ];

        // Transferts récents (au lieu de dossiers récents)
        $transfertsRecents = DossierOneciTransfer::with([
            'centre',
            'agentMayelia',
            'items' => function($query) {
                $query->with('dossierOuvert.rendezVous.client');
            }
        ])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('oneci.dashboard', compact('stats', 'transfertsRecents'));
    }

    /**
     * Liste des dossiers reçus
     */
    public function dossiers(Request $request)
    {
        $user = Auth::user();

        $query = DossierOneciItem::with([
            'dossierOuvert.rendezVous.client',
            'dossierOuvert.rendezVous.service',
            'transfer.centre',
            'agentOneci'
        ]);

        // Filtres
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

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

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date_reception', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date_reception', '<=', $request->date_to);
        }

        $dossiers = $query->orderBy('date_reception', 'desc')->paginate(20);

        return view('oneci.dossiers', compact('dossiers'));
    }

    /**
     * Interface de scan
     */
    public function scanner()
    {
        $user = Auth::user();

        return view('oneci.scanner');
    }

    /**
     * API pour scanner un code-barres
     */
    public function scannerCode(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'code_barre' => 'required|string'
        ]);

        $item = DossierOneciItem::where('code_barre', $request->code_barre)
            ->with([
                'dossierOuvert.rendezVous.client',
                'dossierOuvert.rendezVous.service',
                'transfer.centre'
            ])
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Code-barres introuvable'
            ], 404);
        }

        // Vérifier que le dossier est bien reçu à l'ONECI
        if (!in_array($item->statut, ['en_attente', 'recu', 'traite'])) {
            return response()->json([
                'success' => false,
                'message' => "Ce dossier a déjà le statut: {$item->statut_formate}"
            ], 400);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'code_barre' => $item->code_barre,
                'statut' => $item->statut,
                'statut_formate' => $item->statut_formate,
                'client' => $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A',
                'service' => $item->dossierOuvert->rendezVous->service->nom ?? 'N/A',
                'centre' => $item->transfer->centre->nom ?? 'N/A',
                'date_reception' => $item->date_reception?->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Marquer une carte comme prête
     */
    public function marquerCartePrete(DossierOneciItem $item)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Mettre à jour l'item
            $item->update([
                'statut' => 'carte_prete',
                'date_carte_prete' => now(),
                'agent_oneci_id' => $user->id,
            ]);

            // Mettre à jour le dossier
            $item->dossierOuvert->update([
                'statut_oneci' => 'carte_prete',
                'date_carte_prete' => now(),
            ]);

            // Mettre à jour le transfert si tous les items sont prêts
            $transfer = $item->transfer;
            $allReady = $transfer->items()->where('statut', '!=', 'carte_prete')->count() === 0;
            if ($allReady) {
                $transfer->update([
                    'statut' => 'carte_prete',
                    'date_carte_prete' => now(),
                ]);
            }

            // Envoyer notification au centre Mayelia
            $this->notificationService->notifyAgentMayelia($item);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Carte marquée comme prête. Notification envoyée au centre Mayelia.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des dossiers avec cartes prêtes
     */
    public function dossiersCartesPrete()
    {
        $user = Auth::user();

        $dossiers = DossierOneciItem::where('statut', 'carte_prete')
            ->with([
                'dossierOuvert.rendezVous.client',
                'dossierOuvert.rendezVous.service',
                'transfer.centre',
                'agentOneci'
            ])
            ->orderBy('date_carte_prete', 'desc')
            ->paginate(20);

        return view('oneci.cartes-prete', compact('dossiers'));
    }

    /**
     * Voir le workflow complet d'un dossier (lecture seule)
     */
    public function voirWorkflow(DossierOneciItem $item)
    {
        $user = Auth::user();

        // Charger toutes les relations nécessaires
        $item->load([
            'dossierOuvert.rendezVous.client',
            'dossierOuvert.rendezVous.service',
            'dossierOuvert.rendezVous.formule',
            'dossierOuvert.rendezVous.centre',
            'dossierOuvert.agent',
            'dossierOuvert.documentVerifications.documentRequis',
            'dossierOuvert.paiementVerification',
            'transfer.centre',
            'transfer.agentMayelia'
        ]);

        $dossierOuvert = $item->dossierOuvert;

        // Récupérer les documents requis pour le service
        $documentsRequis = \App\Models\DocumentRequis::where('service_id', $dossierOuvert->rendezVous->service_id)
            ->orderBy('ordre')
            ->get();

        // Charger les vérifications de documents avec leurs relations
        $dossierOuvert->load('documentVerifications.documentRequis', 'documentVerifications.verifiePar');

        return view('oneci.workflow', compact('item', 'dossierOuvert', 'documentsRequis'));
    }

    /**
     * Voir les détails d'un transfert avec liste des dossiers
     */
    public function voirTransfert(DossierOneciTransfer $transfer)
    {
        $user = Auth::user();

        $transfer->load([
            'centre',
            'agentMayelia',
            'agentOneci',
            'items.dossierOuvert.rendezVous.client',
            'items.dossierOuvert.rendezVous.service',
            'items.agentOneci'
        ]);

        return view('oneci.transfert-detail', compact('transfer'));
    }
}
