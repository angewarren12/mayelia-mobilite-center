<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\Guichet;
use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QmsController extends Controller
{
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

            $ticket = Ticket::create([
                'numero' => $numero,
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'type' => $request->type,
                'priorite' => $request->type === 'rdv' ? 2 : 1,
                'statut' => 'en_attente'
            ]);

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

        // Sinon, trouver le prochain ticket
        // Priorité: RDV > Sans RDV, puis par ordre d'arrivée
        $nextTicket = Ticket::where('centre_id', $request->centre_id)
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
     * Données pour l'affichage TV et Agent (Polling)
     */
    public function getQueueData($centreId)
    {
        // Dernier appelé (pour la TV)
        $lastCalled = Ticket::where('centre_id', $centreId)
            ->where('statut', 'appelé')
            ->with('guichet')
            ->orderBy('called_at', 'desc')
            ->first();

        // Historique des appelés (3 derniers)
        $history = Ticket::where('centre_id', $centreId)
            ->whereIn('statut', ['appelé', 'en_cours', 'terminé'])
            ->whereDate('updated_at', Carbon::today())
            ->where('id', '!=', $lastCalled ? $lastCalled->id : 0)
            ->with('guichet')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        // File d'attente (pour l'agent)
        $waiting = Ticket::where('centre_id', $centreId)
            ->where('statut', 'en_attente')
            ->with('service')
            ->orderBy('priorite', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'last_called' => $lastCalled,
            'history' => $history,
            'waiting' => $waiting,
            'waiting_count' => $waiting->count()
        ]);
    }
}
