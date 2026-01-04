<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequis;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class DocumentRequisController extends Controller
{
    /**
     * Afficher la liste des documents requis par service
     */
    public function index(Request $request)
    {
        $query = DocumentRequis::with('service');
        
        // Filtrage par service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        
        // Filtrage par type de demande
        if ($request->filled('type_demande')) {
            $query->where('type_demande', $request->type_demande);
        }
        
        // Filtrage par statut obligatoire
        if ($request->filled('obligatoire')) {
            $query->where('obligatoire', $request->boolean('obligatoire'));
        }
        
        $documentsRequis = $query->orderBy('service_id')
                                ->orderBy('type_demande')
                                ->orderBy('ordre')
                                ->paginate(20);
        
        // Récupérer tous les services pour le filtre
        $services = Service::where('statut', 'actif')->orderBy('nom')->get();
        
        // Types de demande disponibles
        $typesDemande = [
            'Première demande' => 'Première demande',
            'Renouvellement' => 'Renouvellement',
            'Renouvellement avec modification' => 'Renouvellement avec modification',
            'Modification' => 'Modification',
            'Duplicata' => 'Duplicata'
        ];
        
        return view('document-requis.index', compact('documentsRequis', 'services', 'typesDemande'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $services = Service::where('statut', 'actif')->orderBy('nom')->get();
        
        $typesDemande = [
            'Première demande' => 'Première demande',
            'Renouvellement' => 'Renouvellement',
            'Renouvellement avec modification' => 'Renouvellement avec modification',
            'Modification' => 'Modification',
            'Duplicata' => 'Duplicata'
        ];
        
        return view('document-requis.create', compact('services', 'typesDemande'));
    }

    /**
     * Enregistrer un nouveau document requis
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type_demande' => 'required|string|max:255',
            'nom_document' => 'required|string|max:255',
            'description' => 'nullable|string',
            'obligatoire' => 'boolean',
            'ordre' => 'required|integer|min:0'
        ]);

        try {
            DocumentRequis::create([
                'service_id' => $request->service_id,
                'type_demande' => $request->type_demande,
                'nom_document' => $request->nom_document,
                'description' => $request->description,
                'obligatoire' => $request->boolean('obligatoire'),
                'ordre' => $request->ordre
            ]);

            return redirect()->route('document-requis.index')
                           ->with('success', 'Document requis ajouté avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur création document requis: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erreur lors de l\'ajout du document requis');
        }
    }

    /**
     * Afficher un document requis spécifique
     */
    public function show(DocumentRequis $documentRequis)
    {
        $documentRequis->load('service');
        
        // Si c'est une requête AJAX, retourner JSON
        if (request()->ajax()) {
            return response()->json($documentRequis);
        }
        
        return view('document-requis.show', compact('documentRequis'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(DocumentRequis $documentRequis)
    {
        $services = Service::where('statut', 'actif')->orderBy('nom')->get();
        
        $typesDemande = [
            'Première demande' => 'Première demande',
            'Renouvellement' => 'Renouvellement',
            'Renouvellement avec modification' => 'Renouvellement avec modification',
            'Modification' => 'Modification',
            'Duplicata' => 'Duplicata'
        ];
        
        return view('document-requis.edit', compact('documentRequis', 'services', 'typesDemande'));
    }

    /**
     * Mettre à jour un document requis
     */
    public function update(Request $request, DocumentRequis $documentRequis)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type_demande' => 'required|string|max:255',
            'nom_document' => 'required|string|max:255',
            'description' => 'nullable|string',
            'obligatoire' => 'boolean',
            'ordre' => 'required|integer|min:0'
        ]);

        try {
            $documentRequis->update([
                'service_id' => $request->service_id,
                'type_demande' => $request->type_demande,
                'nom_document' => $request->nom_document,
                'description' => $request->description,
                'obligatoire' => $request->boolean('obligatoire'),
                'ordre' => $request->ordre
            ]);

            return redirect()->route('document-requis.index')
                           ->with('success', 'Document requis modifié avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur modification document requis: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erreur lors de la modification du document requis');
        }
    }

    /**
     * Supprimer un document requis
     */
    public function destroy(DocumentRequis $documentRequis)
    {
        try {
            $documentRequis->delete();
            
            return redirect()->route('document-requis.index')
                           ->with('success', 'Document requis supprimé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur suppression document requis: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression du document requis');
        }
    }

    /**
     * API: Récupérer les documents requis pour un service
     */
    public function getByService(Service $service)
    {
        $documents = DocumentRequis::where('service_id', $service->id)
                                 ->orderBy('type_demande')
                                 ->orderBy('ordre')
                                 ->get();
        
        return response()->json($documents);
    }

    /**
     * API: Récupérer les documents requis par type de demande
     */
    public function getByType(Request $request, Service $service)
    {
        $typeDemande = $request->input('type_demande', 'premiere_demande');
        
        $documents = DocumentRequis::where('service_id', $service->id)
                                 ->where('type_demande', $typeDemande)
                                 ->orderBy('ordre')
                                 ->get();
        
        return response()->json($documents);
    }
}
