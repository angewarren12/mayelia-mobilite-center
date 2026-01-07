<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Service;
use App\Models\Client;
use App\Models\DossierOuvert;
use App\Models\RetraitCarte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RetraitCarteController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Vérifie si l'utilisateur a accès au module
     */
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user || !($this->authService->isAdmin() || in_array($user->role, ['agent', 'agent_biometrie']))) {
            abort(403, 'Accès non autorisé au module de retrait.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        if (!$centreId) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre associé à votre compte.');
        }

        // Trouver le service de retrait
        $serviceRetrait = Service::where('nom', 'Retrait de Carte')->first();
        
        if (!$serviceRetrait) {
            return redirect()->route('dashboard')->with('error', 'Le service de retrait n\'est pas configuré.');
        }

        $query = Ticket::where('centre_id', $centreId)
            ->where('service_id', $serviceRetrait->id)
            ->with(['retraitCarte.client', 'retraitCarte.dossier']);

        // Filtre Recherche (Nom client ou Numéro Ticket)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('retraitCarte.client', function($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre Statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre Date (Optionnel)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('retraits.index', compact('tickets', 'serviceRetrait'));
    }

    /**
     * Création manuelle d'un ticket de retrait (sans passer par la borne)
     */
    public function createManual()
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        $serviceRetrait = Service::where('nom', 'Retrait de Carte')->first();
        if (!$serviceRetrait) {
             return back()->with('error', 'Le service de retrait n\'est pas configuré.');
        }

        // Générer un numéro manuel
        $prefix = 'M';
        $count = Ticket::where('centre_id', $centreId)
            ->whereDate('created_at', today())
            ->where('numero', 'like', $prefix . '%')
            ->count();
        
        $numero = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'numero' => $numero,
            'centre_id' => $centreId,
            'service_id' => $serviceRetrait->id,
            'type' => Ticket::TYPE_SANS_RDV,
            'statut' => Ticket::STATUT_EN_COURS,
            'priorite' => 1
        ]);

        // Initialiser le retrait carte
        RetraitCarte::create([
            'ticket_id' => $ticket->id,
            'type_piece' => 'CNI' // Par défaut, modifiable en traitement
        ]);

        return redirect()->route('retraits.traitement', $ticket)->with('info', 'Nouveau retrait manuel initié.');
    }

    public function traitement(Ticket $ticket)
    {
        $this->checkAccess();
        $ticket->load(['service', 'centre', 'retraitCarte.client']);
        
        // Passer le ticket en cours si nécessaire
        if ($ticket->statut === Ticket::STATUT_EN_ATTENTE || $ticket->statut === Ticket::STATUT_APPELÉ) {
            $ticket->update(['statut' => Ticket::STATUT_EN_COURS]);
        }

        return view('retraits.traitement', compact('ticket'));
    }

    /**
     * Enregistrer les informations de retrait
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->checkAccess();
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'nom' => 'required_without:client_id|string|max:255',
            'prenom' => 'required_without:client_id|string|max:255',
            'telephone' => 'required_without:client_id|string|max:255',
            'type_piece' => 'required|in:CNI,Résident',
            'numero_recepisse' => 'required|string|max:255',
            'scan_recepisse' => 'nullable|image|max:5120', // 5MB
        ]);

        try {
            DB::beginTransaction();

            // 1. Gérer le client
            if ($request->filled('client_id')) {
                $client = Client::find($request->client_id);
            } else {
                $client = Client::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'telephone' => $request->telephone,
                    'statut' => 'actif'
                ]);
            }

            // 2. Créer ou mettre à jour le RetraitCarte
            $retrait = RetraitCarte::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'client_id' => $client->id,
                    'type_piece' => $request->type_piece,
                    'numero_recepisse' => $request->numero_recepisse,
                ]
            );
            
            // Tenter de trouver un dossier lié
            $dossier = DossierOuvert::whereHas('rendezVous', function($q) use ($client) {
                $q->where('client_id', $client->id);
            })->where('statut_oneci', 'carte_prete')->latest()->first();

            if ($dossier) {
                $retrait->update(['dossier_id' => $dossier->id]);
            }

            if ($request->hasFile('scan_recepisse')) {
                $path = $request->file('scan_recepisse')->store('retraits/' . $ticket->id, 'public');
                $retrait->update(['scan_recepisse' => $path]);
            }

            $ticket->update(['statut' => Ticket::STATUT_EN_COURS]);

            DB::commit();

            return redirect()->route('retraits.index')->with('success', 'Informations de retrait enregistrées. Le client peut maintenant se rendre au guichet de remise.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait store: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement.');
        }
    }

    /**
     * Finaliser le retrait (Remise physique effectuée)
     */
    public function finaliser(Request $request, Ticket $ticket)
    {
        $this->checkAccess();

        $request->validate([
            'numero_piece' => 'required|string|max:255',
            'date_expiration' => 'required|date|after_or_equal:today',
        ]);

        try {
            DB::beginTransaction();

            $retrait = $ticket->retraitCarte;
            if (!$retrait) {
                throw new \Exception("Données de retrait introuvables.");
            }

            $retrait->update([
                'numero_piece_finale' => $request->numero_piece,
                'date_expiration_piece' => $request->date_expiration,
            ]);

            $ticket->update([
                'statut' => Ticket::STATUT_TERMINÉ,
                'completed_at' => now(),
            ]);

            // Mettre à jour le client si lié
            if ($retrait->client_id) {
                $client = $retrait->client;
                if ($client) {
                    $client->update([
                        'numero_piece_identite' => $request->numero_piece,
                        'type_piece_identite' => $retrait->type_piece,
                    ]);
                }
            }

            // Si un dossier est lié, on le met à jour
            if ($retrait->dossier_id) {
                $dossier = $retrait->dossier;
                if ($dossier) {
                    $dossier->update([
                        'statut_oneci' => 'recupere',
                        'date_recuperation' => now()
                    ]);
                    
                    $dossier->logAction('carte_recuperee', 'Carte retirée physiquement par le client.', [
                        'ticket' => $ticket->numero
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('retraits.index')->with('success', 'La pièce a été marquée comme récupérée. Retrait clôturé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait finaliser: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue.');
        }
    }
    /**
     * Exporter les retraits en PDF
     */
    public function exportPdf(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        $serviceRetrait = Service::where('nom', 'Retrait de Carte')->first();
        
        $query = Ticket::where('centre_id', $centreId)
            ->where('service_id', $serviceRetrait->id)
            ->with(['retraitCarte.client', 'retraitCarte.dossier']);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('retraitCarte.client', function($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tickets = $query->orderBy('created_at', 'asc')->get();
        
        $data = [
            'tickets' => $tickets,
            'centre' => $user->centre,
            'date_export' => now()->format('d/m/Y H:i'),
            'filters' => [
                'start' => $request->start_date,
                'end' => $request->end_date,
                'statut' => $request->statut
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('retraits.export-pdf', $data);
        return $pdf->download('retraits_carte_' . now()->format('Ymd_His') . '.pdf');
    }
}
