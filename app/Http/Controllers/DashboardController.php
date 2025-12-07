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
            'documents_traites' => 0,
            'croissance_mensuelle' => 0
        ];
        
        if ($centre) {
            // Rendez-vous d'aujourd'hui
            $stats['rdv_aujourdhui'] = RendezVous::where('centre_id', $centre->id)
                ->whereDate('date_rendez_vous', Carbon::today())
                ->count();
            
            // Utilisateurs actifs du centre
            $stats['utilisateurs_actifs'] = User::where('centre_id', $centre->id)
                ->where('statut', 'actif')
                ->count();
            
            // Documents traités (rendez-vous terminés ce mois)
            $stats['documents_traites'] = RendezVous::where('centre_id', $centre->id)
                ->where('statut', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
            
            // Croissance mensuelle (comparaison avec le mois dernier)
            $rdvCeMois = RendezVous::where('centre_id', $centre->id)
                ->whereMonth('created_at', Carbon::now()->month)->count();
            
            $rdvMoisDernier = RendezVous::where('centre_id', $centre->id)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
            
            if ($rdvMoisDernier > 0) {
                $stats['croissance_mensuelle'] = round((($rdvCeMois - $rdvMoisDernier) / $rdvMoisDernier) * 100);
            } else {
                $stats['croissance_mensuelle'] = $rdvCeMois > 0 ? 100 : 0;
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