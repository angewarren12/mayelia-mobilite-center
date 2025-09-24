<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\RendezVous;
use App\Models\DocumentRequis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DossierController extends Controller
{
    /**
     * Afficher la liste des dossiers
     */
    public function index(Request $request)
    {
        $query = Dossier::with(['rendezVous.client', 'rendezVous.centre', 'rendezVous.service', 'rendezVous.formule']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('rendezVous.client', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rendez_vous_id')) {
            $query->where('rendez_vous_id', $request->rendez_vous_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $dossiers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dossiers.index', compact('dossiers'));
    }

    /**
     * Afficher les détails d'un dossier
     */
    public function show(Dossier $dossier)
    {
        $dossier->load([
            'rendezVous.client', 
            'rendezVous.centre.ville', 
            'rendezVous.service', 
            'rendezVous.formule',
            'documents'
        ]);

        $documentsRequis = DocumentRequis::where('service_id', $dossier->rendezVous->service_id)->get();

        return view('dossiers.show', compact('dossier', 'documentsRequis'));
    }

    /**
     * Afficher le formulaire de création d'un dossier
     */
    public function create(Request $request)
    {
        $rendezVousId = $request->get('rendez_vous_id');
        $rendezVous = null;

        if ($rendezVousId) {
            $rendezVous = RendezVous::with(['client', 'service', 'formule', 'centre'])->findOrFail($rendezVousId);
        }

        $rendezVousList = RendezVous::with(['client', 'service', 'centre'])
            ->where('statut', 'confirme')
            ->whereDoesntHave('dossier')
            ->orderBy('date_rendez_vous', 'desc')
            ->get();

        return view('dossiers.create', compact('rendezVous', 'rendezVousList'));
    }

    /**
     * Créer un nouveau dossier
     */
    public function store(Request $request)
    {
        $request->validate([
            'rendez_vous_id' => 'required|exists:rendez_vous,id',
            'statut' => 'required|in:en_cours,complet,rejete',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Vérifier qu'il n'y a pas déjà un dossier pour ce rendez-vous
            $existingDossier = Dossier::where('rendez_vous_id', $request->rendez_vous_id)->first();
            if ($existingDossier) {
                return redirect()->back()
                    ->with('error', 'Un dossier existe déjà pour ce rendez-vous')
                    ->withInput();
            }

            $dossier = Dossier::create([
                'rendez_vous_id' => $request->rendez_vous_id,
                'statut' => $request->statut,
                'notes' => $request->notes,
                'numero_dossier' => $this->generateDossierNumber(),
            ]);

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Dossier créé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du dossier')
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire d'édition d'un dossier
     */
    public function edit(Dossier $dossier)
    {
        $dossier->load(['rendezVous.client', 'rendezVous.service', 'rendezVous.formule', 'rendezVous.centre']);
        return view('dossiers.edit', compact('dossier'));
    }

    /**
     * Mettre à jour un dossier
     */
    public function update(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut' => 'required|in:en_cours,complet,rejete',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $dossier->update([
                'statut' => $request->statut,
                'notes' => $request->notes,
            ]);

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Dossier mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du dossier')
                ->withInput();
        }
    }

    /**
     * Supprimer un dossier
     */
    public function destroy(Dossier $dossier)
    {
        try {
            // Supprimer les fichiers associés
            foreach ($dossier->documents as $document) {
                if ($document->chemin_fichier && Storage::exists($document->chemin_fichier)) {
                    Storage::delete($document->chemin_fichier);
                }
            }

            $dossier->delete();

            return redirect()->route('dossiers.index')
                ->with('success', 'Dossier supprimé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du dossier');
        }
    }

    /**
     * Mettre à jour les documents d'un dossier
     */
    public function updateDocuments(Request $request, Dossier $dossier)
    {
        $request->validate([
            'documents' => 'required|array',
            'documents.*.document_requis_id' => 'required|exists:document_requis,id',
            'documents.*.fichier' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        try {
            foreach ($request->documents as $docData) {
                $documentRequis = DocumentRequis::findOrFail($docData['document_requis_id']);
                
                if (isset($docData['fichier']) && $docData['fichier']->isValid()) {
                    $file = $docData['fichier'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('dossiers/' . $dossier->id, $filename, 'public');

                    // Créer ou mettre à jour le document
                    $dossier->documents()->updateOrCreate(
                        ['document_requis_id' => $documentRequis->id],
                        [
                            'nom_fichier' => $filename,
                            'chemin_fichier' => $path,
                            'taille_fichier' => $file->getSize(),
                            'type_mime' => $file->getMimeType(),
                        ]
                    );
                }
            }

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Documents mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des documents: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des documents');
        }
    }

    /**
     * Mettre à jour le statut de paiement
     */
    public function updatePayment(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut_paiement' => 'required|in:en_attente,paye,partiel,rembourse',
            'montant_paye' => 'nullable|numeric|min:0',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'nullable|string|max:255',
            'reference_paiement' => 'nullable|string|max:255',
        ]);

        try {
            $dossier->update([
                'statut_paiement' => $request->statut_paiement,
                'montant_paye' => $request->montant_paye,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference_paiement' => $request->reference_paiement,
            ]);

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Statut de paiement mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du paiement');
        }
    }

    /**
     * Générer un numéro de dossier unique
     */
    private function generateDossierNumber()
    {
        $prefix = 'DOS';
        $year = date('Y');
        $month = date('m');
        
        $lastDossier = Dossier::where('numero_dossier', 'like', $prefix . $year . $month . '%')
            ->orderBy('numero_dossier', 'desc')
            ->first();

        if ($lastDossier) {
            $lastNumber = (int) substr($lastDossier->numero_dossier, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}