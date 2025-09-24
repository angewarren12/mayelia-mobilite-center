<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
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
        \Log::info('Is AJAX:', $request->ajax());
        
        try {
            $request->validate([
                'type_export' => 'required|in:aujourdhui,date,plage',
                'date' => 'required_if:type_export,date|date',
                'date_debut' => 'required_if:type_export,plage|date',
                'date_fin' => 'required_if:type_export,plage|date|after_or_equal:date_debut'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur de validation: ' . implode(', ', $e->errors()['type_export'] ?? [])], 400);
            }
            return back()->withErrors($e->errors());
        }

        $query = RendezVous::with(['client', 'service', 'formule', 'centre']);

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
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $rendezVous = $query->orderBy('date_rendez_vous', 'asc')
                           ->orderBy('tranche_horaire', 'asc')
                           ->get();

        if ($rendezVous->isEmpty()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Aucun rendez-vous trouvé pour les critères sélectionnés.'], 400);
            }
            return back()->with('error', 'Aucun rendez-vous trouvé pour les critères sélectionnés.');
        }

        try {
            $pdf = Pdf::loadView('exports.rendez-vous-pdf', [
                'rendezVous' => $rendezVous,
                'titre' => $titre,
                'date_export' => Carbon::now()->format('d/m/Y H:i')
            ]);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Erreur export PDF: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }
}