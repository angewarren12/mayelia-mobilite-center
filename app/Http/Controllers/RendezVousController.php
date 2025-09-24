<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Centre;
use App\Models\Service;
use App\Models\Formule;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RendezVousController extends Controller
{
    public function index()
    {
        $query = RendezVous::with(['client', 'service', 'formule', 'centre', 'dossierOuvert.agent']);
        
        // Filtres
        if (request('centre_id')) {
            $query->where('centre_id', request('centre_id'));
        }
        
        if (request('date')) {
            $query->whereDate('date_rendez_vous', request('date'));
        }
        
        if (request('statut')) {
            $query->where('statut', request('statut'));
        }
        
        $rendezVous = $query->orderBy('date_rendez_vous', 'desc')->paginate(15);
        $centres = Centre::all();
        
        return view('rendez-vous.index', compact('rendezVous', 'centres'));
    }

    public function show(RendezVous $rendezVous)
    {
        $rendezVous->load(['client', 'service', 'formule', 'centre']);
        return view('rendez-vous.show', compact('rendezVous'));
    }

    public function create()
    {
        $centres = Centre::where('statut', 'actif')->get();
        $services = Service::where('statut', 'actif')->get();
        $formules = Formule::where('statut', 'actif')->get();
        $clients = Client::where('actif', true)->get();
        
        return view('rendez-vous.create', compact('centres', 'services', 'formules', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'date_rendez_vous' => 'required|date|after:today',
            'tranche_horaire' => 'required|string',
            'statut' => 'required|in:confirme,annule,termine',
            'notes' => 'nullable|string'
        ]);

        try {
            // Générer un numéro de suivi unique
            $numeroSuivi = 'RDV' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $rendezVous = RendezVous::create([
                'client_id' => $request->client_id,
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'date_rendez_vous' => $request->date_rendez_vous,
                'tranche_horaire' => $request->tranche_horaire,
                'statut' => $request->statut,
                'numero_suivi' => $numeroSuivi,
                'notes' => $request->notes,
            ]);

            return redirect()->route('rendez-vous.index')
                ->with('success', 'Rendez-vous créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du rendez-vous: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du rendez-vous')
                ->withInput();
        }
    }

    public function edit(RendezVous $rendezVous)
    {
        $rendezVous->load(['client', 'service', 'formule', 'centre']);
        $centres = Centre::where('statut', 'actif')->get();
        $services = Service::where('statut', 'actif')->get();
        $formules = Formule::where('statut', 'actif')->get();
        $clients = Client::where('actif', true)->get();
        
        return view('rendez-vous.edit', compact('rendezVous', 'centres', 'services', 'formules', 'clients'));
    }

    public function update(Request $request, RendezVous $rendezVous)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'date_rendez_vous' => 'required|date',
            'tranche_horaire' => 'required|string',
            'statut' => 'required|in:confirme,annule,termine',
            'notes' => 'nullable|string'
        ]);

        try {
            $rendezVous->update($request->all());

            return redirect()->route('rendez-vous.index')
                ->with('success', 'Rendez-vous modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du rendez-vous: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du rendez-vous')
                ->withInput();
        }
    }

    public function destroy(RendezVous $rendezVous)
    {
        try {
            $rendezVous->delete();
            return redirect()->route('rendez-vous.index')
                ->with('success', 'Rendez-vous supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rendez-vous: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du rendez-vous');
        }
    }

    /**
     * API pour récupérer les formules d'un service
     */
    public function getFormulesByService($serviceId)
    {
        try {
            $formules = Formule::where('service_id', $serviceId)
                             ->where('statut', 'actif')
                             ->get(['id', 'nom', 'prix', 'description']);

            return response()->json($formules);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des formules: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}