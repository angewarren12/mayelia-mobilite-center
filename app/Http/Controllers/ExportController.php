<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use App\Models\DossierOneciItem;
use Carbon\Carbon;
use App\Jobs\GeneratePdfJob;

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

        // Récupérer le centre cible
        $user = auth()->user();
        $targetCentreId = $user->centre_id ?? $request->centre_id;
        
        if (!$targetCentreId) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Veuillez sélectionner un centre ou aucun centre assigné à votre compte.'], 403);
            }
            return back()->with('error', 'Veuillez sélectionner un centre.');
        }

        $query = RendezVous::with(['client', 'service', 'formule', 'centre']);
        $query->where('centre_id', $targetCentreId);

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
            \Log::info('Lancement de la génération PDF asynchrone...');
            
            GeneratePdfJob::dispatch(
                'exports.rendez-vous-pdf',
                [
                    'rendezVous' => $rendezVous,
                    'titre' => $titre ?? 'Export des rendez-vous',
                    'date_export' => Carbon::now()->format('d/m/Y H:i')
                ],
                $filename,
                'public',
                'exports'
            );
            
            \Log::info('Job de génération PDF lancé avec succès');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Export en cours de génération. Le fichier sera disponible dans quelques instants.',
                    'filename' => $filename
                ]);
            }
            
            return back()->with('success', 'Export en cours de génération. Le fichier sera disponible dans quelques instants.');
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
    /**
     * Exporter les dossiers ouverts selon les critères
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

        $user = auth()->user();

        $query = \App\Models\DossierOuvert::with([
            'rendezVous.client',
            'rendezVous.service',
            'rendezVous.formule',
            'rendezVous.centre',
            'agent',
            'paiementVerification'
        ]);

        // Filtrer par centre : 
        // 1. Si l'utilisateur est rattaché à un centre, on force ce centre (Isolation)
        // 2. Si c'est un Super Admin (pas de centre), il peut choisir via centre_id ou exporter tout par défaut
        if ($user->centre_id) {
            $query->whereHas('rendezVous', function($q) use ($user) {
                $q->where('centre_id', $user->centre_id);
            });
        } elseif ($request->filled('centre_id')) {
            $query->whereHas('rendezVous', function($q) use ($request) {
                $q->where('centre_id', $request->centre_id);
            });
        }

        // Initialiser les variables
        $titre = 'Export des dossiers';
        $filename = 'dossiers-export-' . Carbon::now()->format('Y-m-d-His') . '.pdf';

        switch ($request->type_export) {
            case 'aujourdhui':
                $query->whereDate('date_ouverture', Carbon::today());
                $filename = 'dossiers-aujourdhui-' . Carbon::today()->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers ouverts aujourd\'hui (' . Carbon::today()->format('d/m/Y') . ')';
                break;
                
            case 'date':
                $query->whereDate('date_ouverture', $request->date);
                $filename = 'dossiers-' . Carbon::parse($request->date)->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers ouverts le ' . Carbon::parse($request->date)->format('d/m/Y');
                break;
                
            case 'plage':
                $query->whereBetween('date_ouverture', [
                    Carbon::parse($request->date_debut)->startOfDay(),
                    Carbon::parse($request->date_fin)->endOfDay()
                ]);
                $filename = 'dossiers-' . 
                    Carbon::parse($request->date_debut)->format('Y-m-d') . '-' . 
                    Carbon::parse($request->date_fin)->format('Y-m-d') . '.pdf';
                $titre = 'Dossiers ouverts du ' . 
                    Carbon::parse($request->date_debut)->format('d/m/Y') . ' au ' . 
                    Carbon::parse($request->date_fin)->format('d/m/Y');
                break;
        }

        // Appliquer les filtres supplémentaires s'ils existent
        if ($request->filled('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }

        $dossiers = $query->orderBy('date_ouverture', 'desc')->get();

        \Log::info('Nombre de dossiers trouvés:', ['count' => $dossiers->count()]);

        if ($dossiers->isEmpty()) {
            \Log::warning('Aucun dossier trouvé avec les critères fournis');
            if ($request->ajax()) {
                return response()->json(['error' => 'Aucun dossier trouvé pour les critères sélectionnés.'], 404);
            }
            return back()->with('error', 'Aucun dossier trouvé pour les critères sélectionnés.');
        }

        try {
            \Log::info('Lancement de la génération PDF asynchrone...');
            
            GeneratePdfJob::dispatch(
                'exports.dossiers-pdf',
                [
                    'dossiers' => $dossiers,
                    'titre' => $titre ?? 'Export des dossiers',
                    'date_export' => Carbon::now()->format('d/m/Y H:i')
                ],
                $filename,
                'public',
                'exports'
            );
            
            \Log::info('Job de génération PDF lancé avec succès');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Export en cours de génération. Le fichier sera disponible dans quelques instants.',
                    'filename' => $filename
                ]);
            }
            
            return back()->with('success', 'Export en cours de génération. Le fichier sera disponible dans quelques instants.');
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