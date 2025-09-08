<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\RendezVous;
use App\Models\Agent;
use App\Models\DocumentRequis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DossierController extends Controller
{
    public function index()
    {
        $query = Dossier::with(['rendezVous.client', 'agent', 'rendezVous.service', 'rendezVous.formule']);
        
        // Si l'utilisateur est un agent, ne montrer que ses dossiers
        if (Auth::user()->role === 'agent') {
            $query->where('agent_id', Auth::id());
        }
        
        $dossiers = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('dossiers.index', compact('dossiers'));
    }

    public function show(Dossier $dossier)
    {
        $dossier->load([
            'rendezVous.client',
            'rendezVous.service',
            'rendezVous.formule',
            'agent',
            'reprogrammations.rendezVous'
        ]);
        
        // Récupérer les documents requis pour ce service
        $documentsRequis = DocumentRequis::where('service_id', $dossier->rendezVous->service_id)
            ->orderBy('ordre')
            ->get();
            
        return view('dossiers.show', compact('dossier', 'documentsRequis'));
    }

    public function open(Request $request, RendezVous $rendezVous)
    {
        // Vérifier si le dossier n'est pas déjà ouvert par un autre agent
        $dossierExistant = Dossier::where('rendez_vous_id', $rendezVous->id)->first();
        
        if ($dossierExistant && $dossierExistant->agent_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce dossier est déjà ouvert par un autre agent'
            ], 400);
        }

        try {
            if ($dossierExistant) {
                // Mettre à jour l'agent si c'est le même dossier
                $dossierExistant->update(['agent_id' => Auth::id()]);
                $dossier = $dossierExistant;
            } else {
                // Créer un nouveau dossier
                $dossier = Dossier::create([
                    'rendez_vous_id' => $rendezVous->id,
                    'agent_id' => Auth::id(),
                    'statut' => 'ouvert',
                    'date_ouverture' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Dossier ouvert avec succès',
                'dossier_id' => $dossier->id
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ouverture du dossier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ouverture du dossier'
            ], 500);
        }
    }

    public function updateDocuments(Request $request, Dossier $dossier)
    {
        $request->validate([
            'documents_verifies' => 'required|array',
            'documents_verifies.*' => 'boolean',
            'notes_documents_manquants' => 'nullable|string'
        ]);

        try {
            $dossier->update([
                'documents_verifies' => $request->documents_verifies,
                'notes_documents_manquants' => $request->notes_documents_manquants,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documents mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des documents'
            ], 500);
        }
    }

    public function updatePayment(Request $request, Dossier $dossier)
    {
        $request->validate([
            'paiement_effectue' => 'required|boolean',
            'reference_paiement' => 'nullable|string|max:255',
            'montant_paiement' => 'nullable|numeric|min:0'
        ]);

        try {
            $dossier->update([
                'paiement_effectue' => $request->paiement_effectue,
                'reference_paiement' => $request->reference_paiement,
                'montant_paiement' => $request->montant_paiement,
                'date_paiement' => $request->paiement_effectue ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Informations de paiement mises à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du paiement'
            ], 500);
        }
    }

    public function updateBiometrie(Request $request, Dossier $dossier)
    {
        $request->validate([
            'biometrie_passee' => 'required|boolean'
        ]);

        try {
            $dossier->update([
                'biometrie_passee' => $request->biometrie_passee,
                'date_biometrie' => $request->biometrie_passee ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statut biométrie mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la biométrie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la biométrie'
            ], 500);
        }
    }

    public function validate(Request $request, Dossier $dossier)
    {
        try {
            $dossier->update([
                'statut' => 'valide',
                'date_validation' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dossier validé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation du dossier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation du dossier'
            ], 500);
        }
    }

    public function reschedule(Request $request, Dossier $dossier)
    {
        $request->validate([
            'nouveau_rendez_vous_id' => 'required|exists:rendez_vous,id',
            'raison' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            // Créer une reprogrammation
            $dossier->reprogrammations()->create([
                'nouveau_rendez_vous_id' => $request->nouveau_rendez_vous_id,
                'raison' => $request->raison,
                'notes' => $request->notes,
                'agent_id' => Auth::id(),
            ]);

            // Mettre à jour le statut du dossier
            $dossier->update(['statut' => 'reprogramme']);

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous reprogrammé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la reprogrammation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la reprogrammation'
            ], 500);
        }
    }
}


