<?php

namespace App\Http\Controllers;

use App\Models\Guichet;
use App\Models\User;
use App\Models\Centre;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuichetManagementController extends Controller
{
    /**
     * Liste des guichets avec filtres d'isolation
     */
    public function index()
    {
        $user = Auth::user();
        $query = Guichet::with(['agent', 'centre']);

        // Isolation par centre pour les admins de centre
        if ($user->centre_id) {
            $query->where('centre_id', $user->centre_id);
        }

        $guichets = $query->get();
        
        // Données pour les formulaires
        $centres = $user->centre_id 
            ? Centre::where('id', $user->centre_id)->get() 
            : Centre::actif()->get();

        $agents = User::agents()->actifs();
        if ($user->centre_id) {
            $agents->where('centre_id', $user->centre_id);
        }
        $agents = $agents->get();

        $services = Service::actif()->get();

        return view('admin.guichets.index', compact('guichets', 'centres', 'agents', 'services'));
    }

    /**
     * Création d'un nouveau guichet
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'centre_id' => 'required|exists:centres,id',
            'user_id' => 'nullable|exists:users,id',
            'type_services' => 'nullable|array'
        ]);

        // Sécurité isolation
        if (Auth::user()->centre_id && Auth::user()->centre_id != $request->centre_id) {
            return back()->with('error', 'Action non autorisée pour ce centre.');
        }

        Guichet::create([
            'nom' => $request->nom,
            'centre_id' => $request->centre_id,
            'user_id' => $request->user_id,
            'statut' => 'fermé',
            'type_services' => $request->type_services
        ]);

        return redirect()->route('admin.guichets.index')->with('success', 'Guichet créé avec succès.');
    }

    /**
     * Mise à jour d'un guichet
     */
    public function update(Request $request, Guichet $guichet)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'type_services' => 'nullable|array',
            'statut' => 'required|in:ouvert,fermé,pause'
        ]);

        // Sécurité isolation
        if (Auth::user()->centre_id && Auth::user()->centre_id != $guichet->centre_id) {
            abort(403);
        }

        $guichet->update([
            'nom' => $request->nom,
            'user_id' => $request->user_id,
            'statut' => $request->statut,
            'type_services' => $request->type_services
        ]);

        return redirect()->route('admin.guichets.index')->with('success', 'Guichet mis à jour avec succès.');
    }

    /**
     * Suppression d'un guichet
     */
    public function destroy(Guichet $guichet)
    {
        // Sécurité isolation
        if (Auth::user()->centre_id && Auth::user()->centre_id != $guichet->centre_id) {
            abort(403);
        }

        $guichet->delete();

        return redirect()->route('admin.guichets.index')->with('success', 'Guichet supprimé avec succès.');
    }

    /**
     * Bascule rapide du statut via AJAX
     */
    public function toggleStatus(Guichet $guichet)
    {
        if (Auth::user()->centre_id && Auth::user()->centre_id != $guichet->centre_id) {
            return response()->json(['success' => false], 403);
        }

        $newStatus = $guichet->statut === 'ouvert' ? 'fermé' : 'ouvert';
        $guichet->update(['statut' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'label' => ucfirst($newStatus)
        ]);
    }
}
