<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DossierOuvert;
use App\Models\DossierActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Afficher les statistiques des agents
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Vérification des droits (Admin ou permission spécifique)
        if ($user->role !== 'admin' && !$user->hasPermission('statistics', 'view')) {
            abort(403, 'Accès non autorisé');
        }

        // Filtre de date (par défaut aujourd'hui)
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::today();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::today()->endOfDay();

        // Récupérer les agents selon le rôle et le centre de l'utilisateur
        $query = User::where('role', 'agent')
            ->where('statut', 'actif');
        
        if ($user->role === 'agent') {
            // Un agent ne voit que ses propres statistiques
            $query->where('id', $user->id);
        } elseif ($user->centre_id) {
            // Un administrateur de centre ne voit que les agents de son propre centre
            $query->where('centre_id', $user->centre_id);
        } elseif ($user->role === 'admin' && $request->filled('centre_id')) {
            // Un super-administrateur peut filtrer par n'importe quel centre
            $query->where('centre_id', $request->centre_id);
        }
        
        $agents = $query->with('centre')->get();
        
        // Liste des centres pour le filtre (seulement pour le super-admin ou limitée au centre de l'admin)
        $centres = \App\Models\Centre::all();
        if ($user->centre_id) {
            $centres = \App\Models\Centre::where('id', $user->centre_id)->get();
        }

        $stats = [];
        
        foreach ($agents as $agent) {
             // 1. Dossiers ouverts dans la période
            $dossiersOuverts = DossierOuvert::where('agent_id', $agent->id)
                ->whereBetween('date_ouverture', [$startDate, $endDate])
                ->count();
                
            // 2. Dossiers finalisés dans la période (via logs pour avoir la date exacte de l'action)
            $finalizedCount = DossierActionLog::where('user_id', $agent->id)
                ->where('action', 'finalise')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // 3. Durée moyenne de traitement (Ouverture -> Finalisation) - Optimisation SQL
            $avgDuration = \Illuminate\Support\Facades\DB::table('dossier_actions_log')
                ->join('dossier_ouvert', 'dossier_actions_log.dossier_ouvert_id', '=', 'dossier_ouvert.id')
                ->where('dossier_actions_log.user_id', $agent->id)
                ->where('dossier_actions_log.action', 'finalise')
                ->whereBetween('dossier_actions_log.created_at', [$startDate, $endDate])
                // On s'assure que la date de fin est après la date de début pour éviter des valeurs négatives aberrantes
                ->whereRaw('dossier_actions_log.created_at > dossier_ouvert.date_ouverture')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, dossier_ouvert.date_ouverture, dossier_actions_log.created_at)) as avg_duration')
                ->value('avg_duration');
            
            $avgDuration = $avgDuration ? round($avgDuration) : 0;
            
            $stats[] = (object) [
                'agent_id' => $agent->id,
                'agent_nom' => $agent->nom_complet,
                'centre_nom' => $agent->centre ? $agent->centre->nom : '-',
                'dossiers_ouverts' => $dossiersOuverts,
                'dossiers_finalises' => $finalizedCount,
                'avg_duration_formatted' => $this->formatDuration($avgDuration),
                'avg_duration_minutes' => $avgDuration
            ];
        }
        
        // Centres pour le filtre admin
        $centres = \App\Models\Centre::all();

        return view('statistics.index', compact('stats', 'startDate', 'endDate', 'centres'));
    }
    
    /**
     * Formater la durée en heures/minutes
     */
    private function formatDuration($minutes)
    {
        if ($minutes == 0) return '-';
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return "{$hours}h {$mins}m";
    }
}
