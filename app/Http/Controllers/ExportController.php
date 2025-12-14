<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use App\Models\DossierOneciItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Afficher le modal d'export
     */
    public function showExportModal()
    {
        return view('exports.modal');
    }

    /**
     * Exporter les rendez-vous selon les critères
     */
    public function exportRendezVous(Request $request)
    {
        \Log::info('=== DÉBUT EXPORT ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Is AJAX:', ['ajax' => $request->ajax()]);
        
        // Nettoyer les champs vides (convertir "" en null)
        $data = $request->all();
        foreach (['date', 'date_debut', 'date_fin', 'statut'] as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        $request->merge($data);

        try {
            $request->validate([
                'type_export' => 'required|in:aujourdhui,date,plage',
                'date' => 'required_if:type_export,date|nullable|date',
                'date_debut' => 'required_if:type_export,plage|nullable|date',
                'date_fin' => 'required_if:type_export,plage|nullable|date|after_or_equal:date_debut'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur de validation: ' . json_encode($e->errors())], 400);
            }
            return back()->withErrors($e->errors());
        }

        // Récupérer le centre de l'utilisateur connecté
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Aucun centre assigné à votre compte.'], 403);
            }
            return back()->with('error', 'Aucun centre assigné à votre compte.');
        }

        $query = RendezVous::with(['client', 'service', 'formule', 'centre']);
        
        // Filtrer par centre : si un centre_id est spécifié, l'utiliser, sinon utiliser le centre de l'utilisateur
        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        } else {
            $query->where('centre_id', $centre->id);
        }

        // Initialiser les variables
        $titre = 'Export des rendez-vous';
        $filename = 'rendez-vous-export-' . Carbon::now()->format('Y-m-d-His') . '.pdf';

        switch ($request->type_export) {
            case 'aujourdhui':
                $query->whereDate('date_rendez_vous', Carbon::today());
                $filename = 'rendez-vous-aujourdhui-' . Carbon::today()->format('Y-m-d') . '.pdf';
                $titre = 'Rendez-vous d\'aujourd\'hui (' . Carbon::today()->format('d/m/Y') . ')';
                break;
                
            case 'date':
                $query->whereDate('date_rendez_vous', $request->date);
                $filename = 'rendez-vous-' . Carbon::parse($request->date)->format('Y-m-d') . '.pdf';
                $titre = 'Rendez-vous du ' . Carbon::parse($request->date)->format('d/m/Y');
                break;
                
            case 'plage':
                $query->whereBetween('date_rendez_vous', [
                    Carbon::parse($request->date_debut)->startOfDay(),
                    Carbon::parse($request->date_fin)->endOfDay()
                ]);
                $filename = 'rendez-vous-' . 
                    Carbon::parse($request->date_debut)->format('Y-m-d') . '-' . 
                    Carbon::parse($request->date_fin)->format('Y-m-d') . '.pdf';
                $titre = 'Rendez-vous du ' . 
                    Carbon::parse($request->date_debut)->format('d/m/Y') . ' au ' . 
                    Carbon::parse($request->date_fin)->format('d/m/Y');
                break;
        }

        // Appliquer les filtres supplémentaires s'ils existent
        if ($request->filled('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }

        \Log::info('Query SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        $rendezVous = $query->orderBy('date_rendez_vous', 'asc')
                           ->orderBy('tranche_horaire', 'asc')
                           ->get();

        \Log::info('Nombre de rendez-vous trouvés:', ['count' => $rendezVous->count()]);

        if ($rendezVous->isEmpty()) {
            \Log::warning('Aucun rendez-vous trouvé avec les critères fournis');
            if ($request->ajax()) {
                return response()->json(['error' => 'Aucun rendez-vous trouvé pour les critères sélectionnés.'], 404);
            }
            return back()->with('error', 'Aucun rendez-vous trouvé pour les critères sélectionnés.');
        }

        try {
            \Log::info('Génération du PDF...');
            $pdf = Pdf::loadView('exports.rendez-vous-pdf', [
                'rendezVous' => $rendezVous,
                'titre' => $titre ?? 'Export des rendez-vous',
                'date_export' => Carbon::now()->format('d/m/Y H:i')
            ]);
            
            \Log::info('PDF généré avec succès, téléchargement...');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Erreur export PDF:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les dossiers selon les critères
     */
    public function exportDossiers(Request $request)
    {
        \Log::info('=== DÉBUT EXPORT DOSSIERS ===');
        \Log::info('Request data:', $request->all());
        
        // Nettoyer les champs vides (convertir "" en null)
        $data = $request->all();
        foreach (['date', 'date_debut', 'date_fin', 'statut'] as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        $request->merge($data);

        try {
            $request->validate([
                'type_export' => 'required|in:aujourdhui,date,plage',
                'date' => 'required_if:type_export,date|nullable|date',
                'date_debut' => 'required_if:type_export,plage|nullable|date',
                'date_fin' => 'required_if:type_export,plage|nullable|date|after_or_equal:date_debut'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur de validation: ' . json_encode($e->errors())], 400);
            }
            return back()->withErrors($e->errors());
        }

        $query = DossierOneciItem::with([
            'dossierOuvert.rendezVous.client',
            'dossierOuvert.rendezVous.service',
            'dossierOuvert.rendezVous.formule',
            'transfer.centre',
            'agentOneci'
        ]);

        // Initialiser les variables
        $titre = 'Export des dossiers';
        $filename = 'dossiers-export-' . Carbon::now()->format('Y-m-d-His') . '.pdf';

        switch ($request->type_export) {
            case 'aujourdhui':
                $query->whereDate('date_reception', Carbon::today());
                $filename = 'dossiers-aujourdhui-' . Carbon::today()->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers reçus aujourd\'hui (' . Carbon::today()->format('d/m/Y') . ')';
                break;
                
            case 'date':
                $query->whereDate('date_reception', $request->date);
                $filename = 'dossiers-' . Carbon::parse($request->date)->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers reçus le ' . Carbon::parse($request->date)->format('d/m/Y');
                break;
                
            case 'plage':
                $query->whereBetween('date_reception', [
                    Carbon::parse($request->date_debut)->startOfDay(),
                    Carbon::parse($request->date_fin)->endOfDay()
                ]);
                $filename = 'dossiers-' . 
                    Carbon::parse($request->date_debut)->format('Y-m-d') . '-' . 
                    Carbon::parse($request->date_fin)->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers reçus du ' . 
                    Carbon::parse($request->date_debut)->format('d/m/Y') . ' au ' . 
                    Carbon::parse($request->date_fin)->format('d/m/Y');
                break;
        }

        // Appliquer les filtres supplémentaires s'ils existent
        if ($request->filled('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }

        $dossiers = $query->orderBy('date_reception', 'desc')->get();

        \Log::info('Nombre de dossiers trouvés:', ['count' => $dossiers->count()]);

        if ($dossiers->isEmpty()) {
            \Log::warning('Aucun dossier trouvé avec les critères fournis');
            if ($request->ajax()) {
                return response()->json(['error' => 'Aucun dossier trouvé pour les critères sélectionnés.'], 404);
            }
            return back()->with('error', 'Aucun dossier trouvé pour les critères sélectionnés.');
        }

        try {
            \Log::info('Génération du PDF...');
            $pdf = Pdf::loadView('exports.dossiers-pdf', [
                'dossiers' => $dossiers,
                'titre' => $titre ?? 'Export des dossiers',
                'date_export' => Carbon::now()->format('d/m/Y H:i')
            ]);
            
            \Log::info('PDF généré avec succès, téléchargement...');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Erreur export PDF:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }
}