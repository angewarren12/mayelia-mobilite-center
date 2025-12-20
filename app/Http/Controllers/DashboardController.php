<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\CreneauGenere;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        // Statistiques
        $stats = [
            'rdv_aujourdhui' => 0,
            'utilisateurs_actifs' => 0,
            'documents_traites' => 0
        ];
        
        if ($centre) {
            $isAgent = $user->role === 'agent';
            
            // Rendez-vous d'aujourd'hui (planning global du centre)
            $stats['rdv_aujourdhui'] = RendezVous::where('centre_id', $centre->id)
                ->whereDate('date_rendez_vous', Carbon::today())
                ->count();
            
            // Utilisateurs actifs du centre
            $stats['utilisateurs_actifs'] = User::where('centre_id', $centre->id)
                ->where('statut', 'actif')
                ->count();
            
            // Mes dossiers / Dossiers du centre (Dossiers finalisés ce mois)
            $queryDocuments = \App\Models\DossierOuvert::where('statut', 'finalise');
            if ($isAgent) {
                $queryDocuments->where('agent_id', $user->id);
            } else {
                $queryDocuments->whereHas('rendezVous', function($q) use ($centre) {
                    $q->where('centre_id', $centre->id);
                });
            }
            $stats['documents_traites'] = $queryDocuments->whereMonth('updated_at', Carbon::now()->month)->count();

            // Statistique supplémentaire pour l'agent
            if ($isAgent) {
                $stats['mes_dossiers_en_cours'] = \App\Models\DossierOuvert::where('agent_id', $user->id)
                    ->where('statut', 'en_cours')
                    ->count();
            }
        }
        
        // Date actuelle pour le calendrier
        $currentDate = Carbon::now();
        
        return view('dashboard', compact('stats', 'currentDate', 'centre'));
    }

    /**
     * API pour récupérer les rendez-vous d'un mois donné
     */
    public function getRendezVousByMonth(Request $request)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return response()->json(['success' => false, 'message' => 'Aucun centre assigné'], 403);
        }
        
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        // Récupérer tous les rendez-vous du mois pour ce centre
        // Exclure les rendez-vous finalisés (statut = 'finalise')
        $rendezVous = RendezVous::where('centre_id', $centre->id)
            ->whereYear('date_rendez_vous', $year)
            ->whereMonth('date_rendez_vous', $month)
            ->where('statut', '!=', 'finalise')
            ->whereDoesntHave('dossierOuvert', function($query) {
                $query->where('statut', 'finalise');
            })
            ->with(['client', 'service'])
            ->get();
        
        // Grouper par jour
        $rendezVousByDay = [];
        foreach ($rendezVous as $rdv) {
            $day = Carbon::parse($rdv->date_rendez_vous)->format('j');
            if (!isset($rendezVousByDay[$day])) {
                $rendezVousByDay[$day] = [];
            }
            $rendezVousByDay[$day][] = [
                'id' => $rdv->id,
                'client' => $rdv->client->nom_complet ?? 'Client supprimé',
                'service' => $rdv->service->nom ?? 'N/A',
                'tranche_horaire' => $rdv->tranche_horaire,
                'statut' => $rdv->statut
            ];
        }
        
        return response()->json([
            'success' => true,
            'rendezVous' => $rendezVousByDay,
            'total' => $rendezVous->count()
        ]);
    }
}