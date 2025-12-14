<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\Guichet;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\Ticket;
use App\Services\QmsPriorityService;
use App\Services\ThermalPrintService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Pour l'instant, on suppose que l'agent est lié à un guichet ou peut en choisir un
        $guichets = Guichet::all(); // À filtrer par centre de l'agent si applicable
        
        return view('qms.agent', compact('guichets'));
    }

    /**
     * Création d'un ticket (depuis la borne)
     */
    public function storeTicket(Request $request)
    {
        $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'nullable|exists:services,id',
            'type' => 'required|in:rdv,sans_rdv',
            'numero_rdv' => 'nullable|string' // Si c'est un RDV
        ]);

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
            if ($request->type === 'rdv' && $request->numero_rdv) {
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
                'statut' => 'en_attente'
            ]);

            // Calculer la priorité selon le mode du centre
            $priorite = $this->priorityService->calculatePriority($centre, $ticket);
            $ticket->update(['priorite' => $priorite]);

            DB::commit();

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
        // Si un ticket spécifique est demandé (rappel)
        if ($ticket) {
            $ticket->update([
                'statut' => 'appelé',
                'called_at' => now(),
                'guichet_id' => $request->guichet_id
            ]);
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }

        $centre = Centre::findOrFail($request->centre_id);
        
        // NETTOYAGE: Terminer automatiquement tout ticket encore en "appelé" pour ce guichet
        // Cela évite les "tickets zombies" qui réapparaissent sur la TV si l'agent a oublié de terminer
        Ticket::where('guichet_id', $request->guichet_id)
              ->where('statut', 'appelé')
              ->update(['statut' => 'terminé', 'completed_at' => now()]);

        // Recalculer les priorités selon le mode actif du centre
        $this->priorityService->updateAllPriorities($centre);

        // Sélectionner le prochain ticket
        $nextTicket = Ticket::where('centre_id', $centre->id)
            ->where('statut', 'en_attente')
            ->orderBy('priorite', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextTicket) {
            $nextTicket->update([
                'statut' => 'appelé',
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
    public function completeTicket(Ticket $ticket)
    {
        $ticket->update([
            'statut' => 'terminé',
            'completed_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Annuler un ticket (Client absent)
     */
    public function cancelTicket(Ticket $ticket)
    {
        $ticket->update([
            'statut' => 'absent',
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
            'statut' => 'appelé',
            'called_at' => now() // Met à jour l'heure d'appel pour le faire remonter sur la TV
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Vérifier un numéro de RDV (Borne)
     */
    public function checkRdv(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'centre_id' => 'required|exists:centres,id'
        ]);

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
     */
    public function getCentreInfo($centreId)
    {
        $centre = Centre::findOrFail($centreId);
        
        return response()->json([
            'id' => $centre->id,
            'nom' => $centre->nom,
            'qms_mode' => $centre->qms_mode,
            'qms_fenetre_minutes' => $centre->qms_fenetre_minutes,
        ]);
    }

    /**
     * Récupérer les services d'un centre (pour sélection dans le kiosk)
     */
    public function getServices($centreId)
    {
        $centre = Centre::findOrFail($centreId);
        // Utiliser servicesActives() qui filtre déjà par actif dans la table pivot
        // et aussi filtrer par statut actif du service
        $services = $centre->servicesActives()
            ->where('statut', 'actif')
            ->get(['services.id', 'services.nom']);
        
        return response()->json($services);
    }

    /**
     * Données pour l'affichage TV et Agent (Polling)
     */
    public function getQueueData($centreId)
    {
        // Dernier appelé (pour la TV)
        $lastCalled = Ticket::select('id', 'numero', 'guichet_id', 'updated_at', 'type', 'statut')
            ->where('centre_id', $centreId)
            ->where('statut', 'appelé')
            ->whereDate('updated_at', Carbon::today())
            ->with('guichet:id,nom')
            ->orderBy('called_at', 'desc')
            ->first();

        // Historique des appelés (3 derniers)
        $history = Ticket::select('id', 'numero', 'guichet_id', 'updated_at', 'statut')
            ->where('centre_id', $centreId)
            ->whereIn('statut', ['appelé', 'en_cours', 'terminé'])
            ->whereDate('updated_at', Carbon::today())
            ->where('id', '!=', $lastCalled ? $lastCalled->id : 0)
            ->with('guichet:id,nom')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        // File d'attente (pour l'agent)
        $waiting = Ticket::select('id', 'numero', 'service_id', 'type', 'created_at', 'priorite')
            ->where('centre_id', $centreId)
            ->where('statut', 'en_attente')
            ->with('service:id,nom')
            ->orderBy('priorite', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Tickets actuellement en cours de traitement (par guichet) pour le widget
        $activeTickets = Ticket::select('id', 'numero', 'guichet_id', 'service_id', 'type', 'statut')
            ->where('centre_id', $centreId)
            ->where('statut', 'appelé')
            ->whereDate('updated_at', Carbon::today())
            ->with('service:id,nom')
            ->get();

        return response()->json([
            'last_called' => $lastCalled,
            'active_tickets' => $activeTickets, // Pour le widget agent
            'history' => $history,
            'waiting' => $waiting,
            'waiting_count' => $waiting->count()
        ]);
    }
}
