<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\Client;
use App\Models\DossierOuvert;
use App\Models\RetraitCarte;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // On retire le constructeur avec closure qui cause des erreurs de routing
    
    /**
     * Écran de sélection de centre pour le Super Admin
     */
    public function selectCentre()
    {
        if (! Auth::user()->isSuperAdmin()) abort(403);
        $centres = Centre::orderBy('nom')->get();
        return view('super-admin.select-centre', compact('centres'));
    }

    /**
     * Dashboard global multi-centres
     */
    public function dashboard(Request $request)
    {
        if (! Auth::user()->isSuperAdmin()) abort(403);
        $centreId = $request->get('centre_id');
        $selectedCentre = $centreId ? Centre::find($centreId) : null;

        // ========== STATS GLOBALES ==========
        $statsGlobales = [
            'total_centres' => Centre::count(),
            'total_utilisateurs' => User::where('statut', 'actif')->count(),
            'total_clients' => Client::count(),
            'total_dossiers_ce_mois' => DossierOuvert::whereMonth('created_at', Carbon::now()->month)->count(),
            'total_retraits_ce_mois' => RetraitCarte::whereMonth('created_at', Carbon::now()->month)->count(),
            'total_tickets_aujourdhui' => Ticket::whereDate('created_at', Carbon::today())->count(),
            'total_stock_cartes' => Centre::sum('stock_cartes'),
        ];

        // ========== STATS PAR CENTRE (Si un centre est sélectionné) ==========
        $statsCentre = null;
        if ($selectedCentre) {
            $statsCentre = [
                'dossiers_mois' => DossierOuvert::whereHas('rendezVous', function($q) use ($centreId) {
                    $q->where('centre_id', $centreId);
                })->whereMonth('created_at', Carbon::now()->month)->count(),

                'retraits_mois' => RetraitCarte::where('centre_id', $centreId)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count(),

                'tickets_aujourdhui' => Ticket::where('centre_id', $centreId)
                    ->whereDate('created_at', Carbon::today())
                    ->count(),

                'clients_centre' => Client::whereHas('rendezVous', function($q) use ($centreId) {
                    $q->where('centre_id', $centreId);
                })->count(),

                'utilisateurs_actifs' => User::where('centre_id', $centreId)
                    ->where('statut', 'actif')
                    ->count(),
                'stock_cartes' => $selectedCentre->stock_cartes,
            ];
        }

        // ========== GRAPHIQUES COMPARATIFS (Top 5 Centres) ==========
        $topCentres = $this->getTopCentresStats();

        // ========== ACTIVITÉ RÉCENTE TOUS CENTRES ==========
        $activiteRecente = $this->getActiviteRecente($centreId);

        return view('super-admin.dashboard', compact(
            'statsGlobales',
            'statsCentre',
            'selectedCentre',
            'centres',
            'topCentres',
            'activiteRecente'
        ));
    }

    /**
     * Récupère les stats des 5 centres les plus actifs
     */
    private function getTopCentresStats()
    {
        return Centre::select('centres.*')
            ->withCount([
                'retraitCartes as retraits_count' => function($q) {
                    $q->whereMonth('created_at', Carbon::now()->month);
                },
                'tickets as tickets_count' => function($q) {
                    $q->whereDate('created_at', Carbon::today());
                }
            ])
            ->addSelect(['stock_cartes'])
            ->orderByDesc('retraits_count')
            ->limit(5)
            ->get();
    }

    /**
     * Récupère l'activité récente (derniers retraits, dossiers, tickets)
     */
    private function getActiviteRecente($centreId = null)
    {
        $retraitsQuery = RetraitCarte::with(['client', 'centre'])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        $dossiersQuery = DossierOuvert::with(['client', 'rendezVous.centre'])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        if ($centreId) {
            $retraitsQuery->where('centre_id', $centreId);
           
$dossiersQuery->whereHas('rendezVous', function($q) use ($centreId) {
                $q->where('centre_id', $centreId);
            });
        }

        return [
            'retraits' => $retraitsQuery->get(),
            'dossiers' => $dossiersQuery->get(),
        ];
    }

    /**
     * Export rapport Excel multi-centres
     */
    public function exportRapport(Request $request)
    {
        if (! Auth::user()->isSuperAdmin()) abort(403);
        // TODO: Implémenter l'export Excel avec Maatwebsite\Excel
        return response()->json(['success' => true, 'message' => 'Export en développement']);
    }
}
