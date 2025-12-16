<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Formule;
use App\Models\Centre;

class ServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        // Récupérer tous les services globaux
        $services = Service::actif()
            ->with('formules')
            ->get();
            
        return view('services.index', compact('services', 'centre'));
    }

    // Les services sont maintenant globaux et gérés par l'admin général
    // Les admins de centre ne peuvent que les activer/désactiver via /centres

    // Les formules sont maintenant globales et gérées par l'admin général
    // Les admins de centre ne peuvent que les activer/désactiver via /centres
}

