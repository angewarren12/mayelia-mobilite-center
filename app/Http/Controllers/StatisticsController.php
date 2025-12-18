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

        // Récupérer les agents
        $query = User::where('role', 'agent')
            ->where('statut', 'actif');
        
        // Si l'utilisateur est admin mais lié à un centre spécifique (cas rare mais possible)
        // ou si on voulait filtrer par centre via request pour le super admin
        if ($user->role === 'admin' && $request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        } elseif ($user->role !== 'admin' && $user->centre_id) {
            // Si gestionnaire de centre (non implémenté pour l'instant mais au cas où)
            $query->where('centre_id', $user->centre_id);
        }
        
        $agents = $query->with('centre')->get();
        $stats = [];
        
        foreach ($agents as $agent) {
            // 1. Dossiers ouverts dans la période
            $dossiersOuverts = DossierOuvert::where('agent_id', $agent->id)
                ->whereBetween('date_ouverture', [$startDate, $endDate])
                ->count();
                
            // 2. Dossiers finalisés dans la période (via logs pour avoir la date exacte de l'action)
            $finalizedLogs = DossierActionLog::where('user_id', $agent->id)
                ->where('action', 'finalise')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('dossierOuvert')
                ->get();
                
            $finalizedCount = $finalizedLogs->count();
            
            // 3. Durée moyenne de traitement (Ouverture -> Finalisation)
            $totalMinutes = 0;
            $countWithDuration = 0;
            
            foreach ($finalizedLogs as $log) {
                $dossier = $log->dossierOuvert;
                // On a besoin de la date d'ouverture du dossier
                if ($dossier && $dossier->date_ouverture) {
                    $ouverture = $dossier->date_ouverture;
                    $finalisation = $log->created_at;
                    
                    if ($finalisation->gt($ouverture)) {
                        $totalMinutes += $ouverture->diffInMinutes($finalisation);
                        $countWithDuration++;
                    }
                }
            }
            
            $avgDuration = $countWithDuration > 0 ? round($totalMinutes / $countWithDuration) : 0;
            
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
