<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequis;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentRequisController extends Controller
{
    public function index()
    {
        $documents = DocumentRequis::with('service')->paginate(15);
        return view('document-requis.index', compact('documents'));
    }

    public function create()
    {
        $services = Service::where('actif', true)->get();
        return view('document-requis.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type_demande' => 'required|string|max:255',
            'nom_document' => 'required|string|max:255',
            'description' => 'nullable|string',
            'obligatoire' => 'boolean',
            'ordre' => 'required|integer|min:1'
        ]);

        try {
            DocumentRequis::create($request->all());

            return redirect()->route('document-requis.index')
                ->with('success', 'Document requis créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du document requis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du document requis')
                ->withInput();
        }
    }

    public function edit(DocumentRequis $documentRequis)
    {
        $services = Service::where('actif', true)->get();
        return view('document-requis.edit', compact('documentRequis', 'services'));
    }

    public function update(Request $request, DocumentRequis $documentRequis)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type_demande' => 'required|string|max:255',
            'nom_document' => 'required|string|max:255',
            'description' => 'nullable|string',
            'obligatoire' => 'boolean',
            'ordre' => 'required|integer|min:1'
        ]);

        try {
            $documentRequis->update($request->all());

            return redirect()->route('document-requis.index')
                ->with('success', 'Document requis modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du document requis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du document requis')
                ->withInput();
        }
    }

    public function destroy(DocumentRequis $documentRequis)
    {
        try {
            $documentRequis->delete();
            return redirect()->route('document-requis.index')
                ->with('success', 'Document requis supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du document requis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du document requis');
        }
    }
}


