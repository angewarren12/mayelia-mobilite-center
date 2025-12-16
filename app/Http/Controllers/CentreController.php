<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Centre;
use App\Models\Service;
use App\Models\Formule;
use App\Models\CentreService;
use App\Models\CentreFormule;
use App\Services\AuthService;
use App\Http\Controllers\Concerns\ChecksPermissions;
use Illuminate\Support\Facades\Cache;

class CentreController extends Controller
{
    use ChecksPermissions;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Vérifier la permission de lecture
        $this->checkPermission('centres', 'view');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Récupérer tous les services globaux
        $servicesGlobaux = Service::actif()->get();
        
        // Récupérer les services activés pour ce centre
        $servicesActives = $centre->servicesActives()->actif()->get();
        
        // Récupérer toutes les formules globales
        $formulesGlobales = Formule::actif()->get();
        
        // Récupérer les formules activées pour ce centre
        $formulesActives = $centre->formules()->wherePivot('actif', true)->get();
        
        return view('centres.index', compact('centre', 'servicesGlobaux', 'servicesActives', 'formulesGlobales', 'formulesActives'));
    }

    /**
     * Activer/désactiver un service pour le centre
     */
    public function toggleService(Request $request, Service $service)
    {
        // Bloquer pour les agents (admin uniquement)
        if (!$this->authService->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent modifier les services du centre.');
        }

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        $actif = $request->boolean('actif');
        
        if ($actif) {
            // Activer le service
            $centre->services()->syncWithoutDetaching([
                $service->id => ['actif' => true]
            ]);
            $message = "Service '{$service->nom}' activé pour votre centre.";
        } else {
            // Désactiver le service
            $centre->services()->updateExistingPivot($service->id, ['actif' => false]);
            $message = "Service '{$service->nom}' désactivé pour votre centre.";
        }

        // Invalider le cache des services de ce centre
        Cache::forget("centre_services_{$centre->id}");

        return redirect()->route('centres.index')->with('success', $message);
    }

    /**
     * Activer/désactiver une formule pour le centre
     */
    public function toggleFormule(Request $request, Formule $formule)
    {
        // Bloquer pour les agents (admin uniquement)
        if (!$this->authService->isAdmin()) {
            abort(403, 'Seuls les administrateurs peuvent modifier les formules du centre.');
        }

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        $actif = $request->boolean('actif');
        
        if ($actif) {
            // Activer la formule
            $centre->formules()->syncWithoutDetaching([
                $formule->id => ['actif' => true]
            ]);
            $message = "Formule '{$formule->nom}' activée pour votre centre.";
        } else {
            // Désactiver la formule
            $centre->formules()->updateExistingPivot($formule->id, ['actif' => false]);
            $message = "Formule '{$formule->nom}' désactivée pour votre centre.";
        }

        // Invalider le cache (même si les formules ne sont pas en cache actuellement, on prépare l'avenir)
        Cache::forget("centre_formules_{$centre->id}");

        return redirect()->route('centres.index')->with('success', $message);
    }
    /**
     * API pour récupérer les services actifs d'un centre
     */
    public function getServicesByCentre(Centre $centre)
    {
        try {
            $services = $centre->services()
                             ->wherePivot('actif', true)
                             ->where('statut', 'actif')
                             ->get(['services.id', 'services.nom']);

            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
