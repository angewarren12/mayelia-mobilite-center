<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Centre;
use App\Models\Service;
use App\Models\Formule;
use App\Models\CentreService;
use App\Models\CentreFormule;

class CentreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Récupérer tous les services globaux
        $servicesGlobaux = Service::where('statut', 'actif')->get();
        
        // Récupérer les services activés pour ce centre
        $servicesActives = $centre->services()->wherePivot('actif', true)->get();
        
        // Récupérer toutes les formules globales
        $formulesGlobales = Formule::where('statut', 'actif')->get();
        
        // Récupérer les formules activées pour ce centre
        $formulesActives = $centre->formules()->wherePivot('actif', true)->get();
        
        return view('centres.index', compact('centre', 'servicesGlobaux', 'servicesActives', 'formulesGlobales', 'formulesActives'));
    }

    /**
     * Activer/désactiver un service pour le centre
     */
    public function toggleService(Request $request, Service $service)
    {
        $user = auth()->user();
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

        return redirect()->route('centres.index')->with('success', $message);
    }

    /**
     * Activer/désactiver une formule pour le centre
     */
    public function toggleFormule(Request $request, Formule $formule)
    {
        $user = auth()->user();
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

        return redirect()->route('centres.index')->with('success', $message);
    }
}
