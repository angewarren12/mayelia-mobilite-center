<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Filtrer les agents par centre de l'utilisateur connecté
        $query = User::agents()->with('centre');
        
        if ($user && $user->centre_id) {
            $query->where('centre_id', $user->centre_id);
        }
        
        $agents = $query->paginate(10);
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des agents');
        }
        
        // Vérifier que l'admin a un centre assigné
        if (!$user->centre_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun centre assigné à votre compte. Veuillez contacter le support.');
        }
        
        // Récupérer toutes les permissions groupées par module
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        
        return view('agents.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des agents');
        }
        
        // Vérifier que l'admin a un centre assigné
        if (!$user->centre_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun centre assigné à votre compte. Veuillez contacter le support.');
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $agent = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'centre_id' => $user->centre_id, // Utiliser le centre de l'admin connecté
                'role' => 'agent',
                'statut' => 'actif',
                'password' => Hash::make($request->password),
            ]);

            // Attacher les permissions sélectionnées
            if ($request->has('permissions')) {
                $agent->permissions()->sync($request->permissions);
            }

            return redirect()->route('agents.index')
                ->with('success', 'Agent créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'agent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'agent')
                ->withInput();
        }
    }

    public function show(User $agent)
    {
        // Vérifier que c'est bien un agent
        if ($agent->role !== 'agent') {
            abort(404);
        }
        
        $agent->load(['centre', 'permissions']);
        
        // Charger tous les dossiers ouverts avec pagination
        $dossiers = $agent->dossiersOuverts()
            ->with(['rendezVous.client', 'rendezVous.service', 'rendezVous.formule'])
            ->orderBy('date_ouverture', 'desc')
            ->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => $agent->dossiersOuverts()->count(),
            'en_cours' => $agent->dossiersOuverts()->where('statut', 'en_cours')->count(),
            'valides' => $agent->dossiersOuverts()->where('statut', 'finalise')->count(),
            'complets' => $agent->dossiersOuverts()->where('statut', 'finalise')->count(),
        ];
        
        // Grouper les permissions par module
        $permissionsGrouped = $agent->permissions->groupBy('module');
        
        return view('agents.show', compact('agent', 'dossiers', 'stats', 'permissionsGrouped'));
    }

    public function edit(User $agent)
    {
        // Vérifier que c'est bien un agent
        if ($agent->role !== 'agent') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier des agents');
        }
        
        // Vérifier que l'agent appartient au centre de l'admin
        if ($user->centre_id && $agent->centre_id !== $user->centre_id) {
            abort(403, 'Vous ne pouvez modifier que les agents de votre centre');
        }
        
        // Récupérer toutes les permissions groupées par module
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        
        // Charger les permissions de l'agent
        $agent->load('permissions');
        
        return view('agents.edit', compact('agent', 'permissions'));
    }

    public function update(Request $request, User $agent)
    {
        // Vérifier que c'est bien un agent
        if ($agent->role !== 'agent') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier des agents');
        }
        
        // Vérifier que l'agent appartient au centre de l'admin
        if ($user->centre_id && $agent->centre_id !== $user->centre_id) {
            abort(403, 'Vous ne pouvez modifier que les agents de votre centre');
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $agent->id,
            'telephone' => 'required|string|max:20',
            'statut' => 'required|in:actif,inactif',
            'password' => 'nullable|string|min:8',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $data = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'statut' => $request->statut,
                // Le centre_id ne peut pas être modifié
            ];
            
            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            
            $agent->update($data);

            // Mettre à jour les permissions
            if ($request->has('permissions')) {
                $agent->permissions()->sync($request->permissions);
            } else {
                // Si aucune permission n'est sélectionnée, supprimer toutes les permissions
                $agent->permissions()->sync([]);
            }

            return redirect()->route('agents.index')
                ->with('success', 'Agent modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de l\'agent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de l\'agent')
                ->withInput();
        }
    }

    public function destroy(User $agent)
    {
        // Vérifier que c'est bien un agent
        if ($agent->role !== 'agent') {
            abort(404);
        }
        
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer des agents');
        }
        
        // Vérifier que l'agent appartient au centre de l'admin
        if ($user->centre_id && $agent->centre_id !== $user->centre_id) {
            abort(403, 'Vous ne pouvez supprimer que les agents de votre centre');
        }
        
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

    public function toggleStatus(User $agent)
    {
        // Vérifier que c'est bien un agent
        if ($agent->role !== 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les administrateurs peuvent modifier le statut des agents'
            ], 403);
        }
        
        // Vérifier que l'agent appartient au centre de l'admin
        if ($user->centre_id && $agent->centre_id !== $user->centre_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez modifier que les agents de votre centre'
            ], 403);
        }
        
        try {
            $newStatus = $agent->statut === 'actif' ? 'inactif' : 'actif';
            $agent->update(['statut' => $newStatus]);
            $status = $newStatus === 'actif' ? 'activé' : 'désactivé';
            return response()->json([
                'success' => true,
                'message' => "Agent {$status} avec succès",
                'statut' => $newStatus
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
