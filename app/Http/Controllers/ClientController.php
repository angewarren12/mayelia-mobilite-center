<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Recherche
        if ($request->has('search') && $request->search) {
            $query->recherche($request->search);
        }

        // Filtre par statut
        if ($request->has('actif') && $request->actif !== '') {
            $query->where('actif', $request->actif);
        }

        $clients = $query->orderBy('nom')->orderBy('prenom')->paginate(20);

        return view('clients.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:20',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'profession' => 'nullable|string|max:255',
            'sexe' => 'nullable|in:M,F',
            'numero_piece_identite' => 'nullable|string|max:50',
            'type_piece_identite' => 'nullable|in:CNI,Passeport,Carte de résident,Autre',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = Client::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du client:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du client'
            ], 500);
        }
    }

    /**
     * Afficher un client spécifique
     */
    public function show($id)
    {
        try {
            $client = Client::withCount('rendezVous')->findOrFail($id);

            return response()->json([
                'success' => true,
                'client' => $client
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération du client:', [
                'error' => $e->getMessage(),
                'client_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $id,
            'telephone' => 'required|string|max:20',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'profession' => 'nullable|string|max:255',
            'sexe' => 'nullable|in:M,F',
            'numero_piece_identite' => 'nullable|string|max:50',
            'type_piece_identite' => 'nullable|in:CNI,Passeport,Carte de résident,Autre',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Client modifié avec succès',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la modification du client:', [
                'error' => $e->getMessage(),
                'client_id' => $id,
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du client'
            ], 500);
        }
    }

    /**
     * Supprimer un client
     */
    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            
            // Vérifier s'il y a des rendez-vous associés
            if ($client->rendezVous()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce client car il a des rendez-vous associés'
                ], 400);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du client:', [
                'error' => $e->getMessage(),
                'client_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du client'
            ], 500);
        }
    }

    /**
     * Changer le statut d'un client
     */
    public function toggleStatus($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->actif = !$client->actif;
            $client->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut du client modifié avec succès',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du changement de statut du client:', [
                'error' => $e->getMessage(),
                'client_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de statut'
            ], 500);
        }
    }


    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }



    /**
     * API pour rechercher des clients (pour autocomplétion)
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $clients = Client::recherche($term)
            ->actifs()
            ->select('id', 'nom', 'prenom', 'email', 'telephone')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->nom_complet . ' (' . $client->email . ')',
                    'nom' => $client->nom,
                    'prenom' => $client->prenom,
                    'email' => $client->email,
                    'telephone' => $client->telephone
                ];
            });

        return response()->json($clients);
    }


    /**
     * Vérifier si un client existe par numéro de téléphone
     */
    public function checkClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Numéro de téléphone requis'
            ], 400);
        }

        $client = Client::where('telephone', $request->telephone)
                       ->where('actif', true)
                       ->first();

        if ($client) {
            return response()->json([
                'success' => true,
                'client' => $client
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aucun client trouvé avec ce numéro de téléphone'
            ]);
        }
    }

    /**
     * Créer un nouveau client
     */
    public function createClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:20|unique:clients,telephone',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:M,F',
            'adresse' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $client = Client::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'date_naissance' => $request->date_naissance,
                'sexe' => $request->sexe,
                'adresse' => $request->adresse,
                'notes' => $request->notes,
                'actif' => true
            ]);

            \Log::info('Nouveau client créé via API:', [
                'client_id' => $client->id,
                'nom' => $client->nom,
                'prenom' => $client->prenom,
                'email' => $client->email,
                'telephone' => $client->telephone
            ]);

            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'Client créé avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du client via API:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du client'
            ], 500);
        }
    }
}