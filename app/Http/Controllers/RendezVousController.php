<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Centre;
use App\Models\Service;
use App\Models\Formule;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;
use App\Http\Controllers\Concerns\ChecksPermissions;

class RendezVousController extends Controller
{
    use ChecksPermissions;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function index(Request $request)
    {
        $this->checkPermission('rendez-vous', 'view');

        $query = RendezVous::with(['client', 'service', 'formule', 'centre', 'dossierOuvert.agent']);
        
        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($clientQuery) use ($search) {
                    $clientQuery->where('nom', 'like', "%{$search}%")
                              ->orWhere('prenom', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('telephone', 'like', "%{$search}%");
                })
                ->orWhere('numero_suivi', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        // Filtre par centre
        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }
        
        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_rendez_vous', $request->date);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $rendezVous = $query->orderBy('date_rendez_vous', 'desc')
                           ->orderBy('tranche_horaire', 'asc')
                           ->paginate(15)
                           ->appends($request->query());
        
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
        $this->checkPermission('rendez-vous', 'create');

        $centres = Centre::where('statut', 'actif')->get();
        $services = Service::where('statut', 'actif')->get();
        $formules = Formule::where('statut', 'actif')->get();
        $clients = Client::where('actif', true)->get();
        
        return view('rendez-vous.create', compact('centres', 'services', 'formules', 'clients'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('rendez-vous', 'create');

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
        $this->checkPermission('rendez-vous', 'update');

        $rendezVous->load(['client', 'service', 'formule', 'centre']);
        $centres = Centre::where('statut', 'actif')->get();
        $services = Service::where('statut', 'actif')->get();
        $formules = Formule::where('statut', 'actif')->get();
        $clients = Client::where('actif', true)->get();
        
        return view('rendez-vous.edit', compact('rendezVous', 'centres', 'services', 'formules', 'clients'));
    }

    public function update(Request $request, RendezVous $rendezVous)
    {
        $this->checkPermission('rendez-vous', 'update');

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
        $this->checkPermission('rendez-vous', 'delete');

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
    public function getFormulesByService(Service $service)
    {
        try {
            $formules = $service->formules()
                             ->where('statut', 'actif')
                             ->get(['id', 'nom', 'prix', 'description']);

            return response()->json($formules);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des formules: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}