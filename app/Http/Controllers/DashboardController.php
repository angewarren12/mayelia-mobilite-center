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
        
        return view('dashboard', compact('stats'));
    }
}