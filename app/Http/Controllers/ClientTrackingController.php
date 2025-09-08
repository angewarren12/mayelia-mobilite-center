<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\RendezVous;
use Carbon\Carbon;

class ClientTrackingController extends Controller
{
    /**
     * Afficher la page de connexion client
     */
    public function showLogin()
    {
        return view('client-tracking.login');
    }

    /**
     * Traiter la connexion client
     */
    public function login(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string|min:10'
        ]);

        // Rechercher le client par numéro de téléphone
        $client = Client::where('telephone', $request->telephone)
                       ->where('actif', true)
                       ->first();

        if (!$client) {
            return back()->withErrors([
                'telephone' => 'Aucun client trouvé avec ce numéro de téléphone.'
            ])->withInput();
        }

        // Rediriger vers le dashboard avec l'ID du client
        return redirect()->route('client.dashboard', ['clientId' => $client->id]);
    }

    /**
     * Afficher le dashboard de suivi du client
     */
    public function dashboard($clientId)
    {
        $client = Client::findOrFail($clientId);

        // Récupérer tous les rendez-vous du client
        $rendezVous = RendezVous::with(['centre', 'service', 'formule'])
                               ->where('client_id', $clientId)
                               ->orderBy('date_rendez_vous', 'desc')
                               ->orderBy('tranche_horaire', 'desc')
                               ->get();

        // Séparer les rendez-vous par statut
        $rendezVousConfirme = $rendezVous->where('statut', 'confirme');
        $rendezVousAnnule = $rendezVous->where('statut', 'annule');
        $rendezVousCompleted = $rendezVous->where('statut', 'completed');

        // Statistiques
        $stats = [
            'total' => $rendezVous->count(),
            'confirme' => $rendezVousConfirme->count(),
            'annule' => $rendezVousAnnule->count(),
            'completed' => $rendezVousCompleted->count(),
            'prochain' => $rendezVousConfirme->where('date_rendez_vous', '>=', now()->toDateString())->first()
        ];

        return view('client-tracking.dashboard', compact('client', 'rendezVous', 'stats'));
    }

    /**
     * Rechercher un rendez-vous par numéro de suivi
     */
    public function searchByTracking(Request $request)
    {
        $request->validate([
            'numero_suivi' => 'required|string'
        ]);

        $rendezVous = RendezVous::with(['centre', 'service', 'formule', 'client'])
                               ->where('numero_suivi', $request->numero_suivi)
                               ->first();

        if (!$rendezVous) {
            return back()->withErrors([
                'numero_suivi' => 'Aucun rendez-vous trouvé avec ce numéro de suivi.'
            ])->withInput();
        }

        // Afficher la vue d'un seul rendez-vous
        return view('client-tracking.single-rendez-vous', compact('rendezVous'));
    }
}