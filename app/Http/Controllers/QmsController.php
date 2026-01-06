<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\Guichet;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\Ticket;
use App\Services\QmsPriorityService;
use App\Services\ThermalPrintService;
use App\Http\Requests\Qms\StoreTicketRequest;
use App\Http\Requests\Qms\CheckRdvRequest;
use App\Http\Requests\Qms\CallNextTicketRequest;
use App\Http\Requests\Qms\CompleteTicketRequest;
use App\Http\Requests\Qms\CancelTicketRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Events\TicketCreated;

class QmsController extends Controller
{
    protected $priorityService;
    protected $thermalPrintService;

    public function __construct(QmsPriorityService $priorityService, ThermalPrintService $thermalPrintService)
    {
        $this->priorityService = $priorityService;
        $this->thermalPrintService = $thermalPrintService;
    }
    /**
     * Interface Borne (Kiosk)
     */
    public function kiosk($centreId)
    {
        $centre = Centre::findOrFail($centreId);
        $services = $centre->services()->where('statut', 'actif')->get();
        
        return view('qms.kiosk', compact('centre', 'services'));
    }

    /**
     * Interface TV (Affichage)
     */
    public function display($centreId)
    {
        $centre = Centre::findOrFail($centreId);
        return view('qms.display', compact('centre'));
    }

    /**
     * Interface Agent
     */
    public function agent()
    {
        $user = Auth::user();
        
        // L'agent ne sélectionne plus son guichet, il utilise celui assigné par l'admin
        $assignedGuichet = Guichet::where('user_id', $user->id)
            ->with(['centre'])
            ->first();
            
        // Si c'est un admin et qu'il n'a pas de guichet assigné, on lui permet d'utiliser le premier guichet 
        // de son centre, ou le tout premier guichet existant s'il n'a pas de centre assigné.
        if (!$assignedGuichet && $user->role === 'admin') {
            if ($user->centre_id) {
                $assignedGuichet = Guichet::where('centre_id', $user->centre_id)
                    ->with(['centre'])
                    ->first();
            } else {
                $assignedGuichet = Guichet::with(['centre'])->first();
            }
        }
            
        if (!$assignedGuichet) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé : Aucun guichet ne vous est assigné pour le moment. Veuillez demander à votre administrateur de vous assigner à un guichet.');
        }
        
        $centreId = $assignedGuichet->centre_id;
        

        
        return view('qms.agent', [
            'assignedGuichet' => $assignedGuichet,
            'centreId' => $centreId
        ]);
    }

    /**
     * Création d'un ticket (depuis la borne)
     */
    public function storeTicket(StoreTicketRequest $request)
    {

        try {
            DB::beginTransaction();

            $centre = Centre::findOrFail($request->centre_id);

            // Génération du numéro de ticket
            // Format: [Lettre Service][001-999]
            $service = Service::find($request->service_id);
            $prefix = $service ? strtoupper(substr($service->nom, 0, 1)) : 'T';
            
            // Compter les tickets du jour pour ce service/centre
            $count = Ticket::where('centre_id', $request->centre_id)
                ->whereDate('created_at', Carbon::today())
                ->where('numero', 'like', $prefix . '%')
                ->count();
            
            $numero = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            // Récupérer l'heure du RDV si applicable
            $heureRdv = null;
            if ($request->type === Ticket::TYPE_RDV && $request->numero_rdv) {
                $rdv = RendezVous::where('numero_suivi', $request->numero_rdv)->first();
                $heureRdv = $rdv?->tranche_horaire;
            }

            $ticket = Ticket::create([
                'numero' => $numero,
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'type' => $request->type,
                'heure_rdv' => $heureRdv,
                'priorite' => 1, // Sera recalculée
                'statut' => Ticket::STATUT_EN_ATTENTE
            ]);

            // Calculer la priorité selon le mode du centre
            $priorite = $this->priorityService->calculatePriority($centre, $ticket);
            $ticket->update(['priorite' => $priorite]);

            DB::commit();

            // Déclencher l'événement
            event(new TicketCreated($ticket->fresh()));

            return response()->json([
                'success' => true,
                'ticket' => $ticket,
                'message' => 'Ticket créé avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Appeler le prochain ticket
     */
    public function callTicket(Request $request, Ticket $ticket = null)
    {
        $user = Auth::user();

        // Si un ticket spécifique est demandé (rappel)
        if ($ticket) {
            // Sécurité : Vérifier que le ticket appartient au centre de l'agent
            if (!$user->canAccessCentre($ticket->centre_id)) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            $ticket->update([
                'statut' => 'appelé',
                'called_at' => now(),
                'guichet_id' => $request->guichet_id
            ]);
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }

        // Filtrer par centre : 
        // 1. Si l'utilisateur est rattaché à un centre, on force ce centre (Isolation)
        // 2. Si c'est un Super Admin (pas de centre), il peut choisir via centre_id ou utiliser un centre par défaut
        $centreId = null;
        if ($user->centre_id) {
            $centreId = $user->centre_id;
        } elseif ($request->filled('centre_id')) {
            $centreId = $request->centre_id;
        }

        if (!$centreId) {
            return response()->json(['success' => false, 'message' => 'Aucun centre spécifié'], 400);
        }

        $centre = Centre::findOrFail($centreId);
        
        $guichet = Guichet::findOrFail($request->guichet_id);
        
        // NETTOYAGE: Terminer automatiquement tout ticket encore en "appelé" pour ce guichet
        // Cela évite les "tickets zombies" qui réapparaissent sur la TV si l'agent a oublié de terminer
        Ticket::where('guichet_id', $guichet->id)
              ->where('statut', Ticket::STATUT_APPELÉ)
              ->update(['statut' => Ticket::STATUT_TERMINÉ, 'completed_at' => now()]);

        // Recalculer les priorités selon le mode actif du centre
        $this->priorityService->updateAllPriorities($centre);

        // LOGIQUE DE FILE D'ATTENTE BIOMÉTRIE
        if ($user->is_agent_biometrie) {
            // L'agent biométrie récupère les tickets en attente de biométrie
            // On trie par updated_at (date de mise en file biométrie)
            $query = Ticket::pourCentre($centre->id)
                ->where('statut', Ticket::STATUT_EN_ATTENTE_BIOMETRIE)
                ->orderBy('updated_at', 'asc');
        } else {
            // Sélectionner le prochain ticket standard
            $query = Ticket::pourCentre($centre->id)
                ->enAttente()
                ->parPriorite();
                
            // Si le guichet a des types de services spécifiques autorisés
            // (Uniquement pour les agents standards, pas biométrie qui prend tout le flux)
            if ($guichet->type_services && is_array($guichet->type_services) && count($guichet->type_services) > 0) {
                $query->whereIn('service_id', $guichet->type_services);
            }
        }

        $nextTicket = $query->first();

        if ($nextTicket) {
            $nextTicket->update([
                'statut' => Ticket::STATUT_APPELÉ, // Reste 'appelé' mais contextuellement c'est pour biométrie
                // Note: On pourrait utiliser STATUT_EN_COURS_BIOMETRIE pour différencier sur la TV si besoin
                'called_at' => now(),
                'guichet_id' => $request->guichet_id,
                'user_id' => Auth::id() // Agent qui appelle
            ]);
            
            return response()->json([
                'success' => true,
                'ticket' => $nextTicket
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun ticket en attente'
        ]);
    }

    /**
     * Terminer un ticket
     */
    public function completeTicket(CompleteTicketRequest $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Si l'agent EST un agent biométrie, clore définitivement le ticket
        if ($user->is_agent_biometrie) {
            $ticket->update([
                'statut' => Ticket::STATUT_TERMINÉ,
                'completed_at' => now()
            ]);
        } else {
            // Si c'est un agent d'accueil/dossier, on transfère vers la biométrie
            // Au lieu de terminer, on met en attente biométrie
            $ticket->update([
                'statut' => Ticket::STATUT_EN_ATTENTE_BIOMETRIE,
                'guichet_id' => null, // Détacher du guichet actuel pour le remettre dans le pool
                'user_id' => null     // Détacher de l'agent actuel
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Annuler un ticket (Client absent)
     */
    public function cancelTicket(CancelTicketRequest $request, Ticket $ticket)
    {
        $ticket->update([
            'statut' => Ticket::STATUT_ABSENT,
            'completed_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Rappeler un ticket
     */
    public function recallTicket(Ticket $ticket)
    {
        $ticket->update([
            'statut' => Ticket::STATUT_APPELÉ,
            'called_at' => now() // Met à jour l'heure d'appel pour le faire remonter sur la TV
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Vérifier un numéro de RDV (Borne)
     */
    public function checkRdv(CheckRdvRequest $request)
    {
        // Validation déjà effectuée par CheckRdvRequest

        // Rechercher le RDV
        // On suppose que le modèle RendezVous a les champs necessaires ou qu'on check dans une table externe
        // Ici on simplifie en cherchant par numero_suivi
        $rdv = RendezVous::where('numero_suivi', $request->numero)
            ->where('centre_id', $request->centre_id)
            ->whereDate('date_rendez_vous', Carbon::today())
            ->first();

        if ($rdv) {
            return response()->json([
                'success' => true,
                'rdv' => [
                    'id' => $rdv->id,
                    'client_nom' => $rdv->client_nom ?? $rdv->client?->nom_complet ?? 'Client',
                    'heure' => $rdv->tranche_horaire,
                    'service_id' => $rdv->service_id ?? null // Service lié au RDV
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Rendez-vous introuvable pour aujourd\'hui.'
        ]);
    }

    /**
     * Vue impression ticket
     */
    public function printTicket(Ticket $ticket)
    {
        // Charger les relations nécessaires pour éviter les requêtes N+1
        $ticket->load(['centre', 'service']);
        
        $printData = $this->thermalPrintService->prepareTicketPrintData($ticket);
        return view('qms.ticket-print', $printData);
    }

    /**
     * Récupérer les informations d'un centre (pour le kiosk)
     * Cache: 1 heure (les infos du centre changent rarement)
     */
    public function getCentreInfo($centreId)
    {
        $cacheKey = "centre_info_{$centreId}";
        
        $data = Cache::remember($cacheKey, 3600, function () use ($centreId) {
            $centre = Centre::findOrFail($centreId);
            
            return [
                'id' => $centre->id,
                'nom' => $centre->nom,
                'qms_mode' => $centre->qms_mode,
                'qms_fenetre_minutes' => $centre->qms_fenetre_minutes,
            ];
        });
        
        return response()->json($data);
    }

    /**
     * Récupérer les services d'un centre (pour sélection dans le kiosk)
     * Cache: 30 minutes (les services changent occasionnellement)
     */
    public function getServices($centreId)
    {
        $cacheKey = "centre_services_{$centreId}";
        
        $services = Cache::remember($cacheKey, 1800, function () use ($centreId) {
            $centre = Centre::findOrFail($centreId);
            // Utiliser servicesActives() qui filtre déjà par actif dans la table pivot
            // et aussi filtrer par statut actif du service
            return $centre->servicesActives()
                ->actif()
                ->get(['services.id', 'services.nom']);
        });
        
        return response()->json($services);
    }

    /**
     * Données pour l'affichage TV et Agent (Polling)
     */
    public function getQueueData($centreId)
    {
        // Sécurité : Vérifier l'accès au centre
        if (!Auth::user()->canAccessCentre($centreId)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        // Dernier appelé (pour la TV)
        $lastCalled = Ticket::select('id', 'numero', 'guichet_id', 'updated_at', 'type', 'statut')
            ->pourCentre($centreId)
            ->appelé()
            ->whereDate('updated_at', Carbon::today())
            ->with('guichet:id,nom')
            ->orderBy('called_at', 'desc')
            ->first();

        // Historique des appelés (3 derniers)
        $history = Ticket::select('id', 'numero', 'guichet_id', 'updated_at', 'statut')
            ->pourCentre($centreId)
            ->whereIn('statut', [Ticket::STATUT_APPELÉ, Ticket::STATUT_EN_COURS, Ticket::STATUT_TERMINÉ])
            ->whereDate('updated_at', Carbon::today())
            ->where('id', '!=', $lastCalled ? $lastCalled->id : 0)
            ->with('guichet:id,nom')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        // File d'attente (pour l'agent)
        $waitingQuery = Ticket::select('id', 'numero', 'service_id', 'type', 'created_at', 'priorite', 'updated_at')
            ->pourCentre($centreId)
            ->with('service:id,nom')
            ->parPriorite();

        if (Auth::user()->is_agent_biometrie) {
            $waitingQuery->where('statut', Ticket::STATUT_EN_ATTENTE_BIOMETRIE)
                         ->orderBy('updated_at', 'asc');
        } else {
            $waitingQuery->enAttente()
                         ->orderBy('created_at', 'asc');
        }

        $waiting = $waitingQuery->get();

        // Tickets actuellement en cours de traitement (par guichet) pour le widget
        $activeTickets = Ticket::select('id', 'numero', 'guichet_id', 'service_id', 'type', 'statut')
            ->where('centre_id', $centreId)
            ->where('statut', 'appelé')
            ->whereDate('updated_at', Carbon::today())
            ->with(['service:id,nom', 'guichet:id,nom'])
            ->get();

        // Calculer le statut d'occupation pour le slider TV
        $centre = Centre::findOrFail($centreId);
        
        // Guichets ouverts (avec un agent actif assigné)
        $activeGuichetsCount = Guichet::where('centre_id', $centreId)
            ->where('statut', 'ouvert')
            ->count();
            
        // Tickets en cours de traitement (appelé ou en cours)
        $processingTicketsCount = Ticket::where('centre_id', $centreId)
            ->whereIn('statut', [Ticket::STATUT_APPELÉ, Ticket::STATUT_EN_COURS, Ticket::STATUT_EN_COURS_BIOMETRIE])
            ->whereDate('updated_at', Carbon::today())
            ->count();
            
        // Tous les guichets sont occupés si:
        // 1. Il y a au moins un guichet ouvert
        // 2. Le nombre de tickets en traitement >= nombre de guichets ouverts
        $allBusy = $activeGuichetsCount > 0 && $processingTicketsCount >= $activeGuichetsCount;
        
        // Ou si la file d'attente est vide
        $queueEmpty = $waiting->count() === 0;
        
        $shouldShowSlider = $allBusy || $queueEmpty;
        
        $tvStatus = [
            'should_show_slider' => $shouldShowSlider,
            'slider_config' => $centre->options_tv ?? [
                'enabled' => false,
                'images' => [],
                'interval' => 4000
            ]
        ];

        return response()->json([
            'last_called' => $lastCalled,
            'active_tickets' => $activeTickets, // Pour le widget agent
            'history' => $history,
            'waiting' => $waiting,
            'waiting_count' => $waiting->count(),
            'tv_status' => $tvStatus
        ]);
    }
}
