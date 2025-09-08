<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Centre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::with('centre')->paginate(10);
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $centres = Centre::where('actif', true)->get();
        return view('agents.create', compact('centres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email',
            'telephone' => 'required|string|max:20',
            'centre_id' => 'required|exists:centres,id',
            'actif' => 'boolean'
        ]);

        try {
            $agent = Agent::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'centre_id' => $request->centre_id,
                'actif' => $request->has('actif') ? true : false,
                'password' => Hash::make('password123'), // Mot de passe par défaut
            ]);

            return redirect()->route('agents.index')
                ->with('success', 'Agent créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'agent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'agent')
                ->withInput();
        }
    }

    public function show(Agent $agent)
    {
        $agent->load('centre', 'dossiers.rendezVous.client');
        return view('agents.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        $centres = Centre::where('actif', true)->get();
        return view('agents.edit', compact('agent', 'centres'));
    }

    public function update(Request $request, Agent $agent)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . $agent->id,
            'telephone' => 'required|string|max:20',
            'centre_id' => 'required|exists:centres,id',
            'actif' => 'boolean'
        ]);

        try {
            $agent->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'centre_id' => $request->centre_id,
                'actif' => $request->has('actif') ? true : false,
            ]);

            return redirect()->route('agents.index')
                ->with('success', 'Agent modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l\'agent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de l\'agent')
                ->withInput();
        }
    }

    public function destroy(Agent $agent)
    {
        try {
            $agent->delete();
            return redirect()->route('agents.index')
                ->with('success', 'Agent supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'agent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de l\'agent');
        }
    }

    public function toggleStatus(Agent $agent)
    {
        try {
            $agent->update(['actif' => !$agent->actif]);
            $status = $agent->actif ? 'activé' : 'désactivé';
            return response()->json([
                'success' => true,
                'message' => "Agent {$status} avec succès",
                'actif' => $agent->actif
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut de l\'agent: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut'
            ], 500);
        }
    }
}


