@extends('layouts.dashboard')

@section('title', 'Gestion des Clients')
@section('subtitle', 'Liste des clients du centre')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-end items-center">
        @userCan('clients', 'create')
        <button onclick="openCreateModal()" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Client
        </button>
        @enduserCan
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nom, prénom, email ou téléphone..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="actif" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="actif" name="actif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous</option>
                    <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                    <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des clients -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($clients->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Informations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $client->nom_complet }}</div>
                                            <div class="text-sm text-gray-500">{{ $client->profession ?? 'Non renseigné' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $client->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $client->telephone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($client->date_naissance)
                                            {{ $client->date_naissance->format('d/m/Y') }}
                                        @else
                                            Non renseigné
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $client->sexe_formate ?? 'Non renseigné' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $client->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewClient({{ $client->id }})" class="text-mayelia-600 hover:text-mayelia-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @userCan('clients', 'update')
                                        <button onclick="editClient({{ $client->id }})" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="toggleClientStatus({{ $client->id }})" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-toggle-{{ $client->actif ? 'on' : 'off' }}"></i>
                                        </button>
                                        @enduserCan
                                        @userCan('clients', 'delete')
                                        <button onclick="deleteClient({{ $client->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @enduserCan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $clients->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun client trouvé</h3>
                <p class="text-gray-500 mb-4">Commencez par ajouter votre premier client.</p>
                @userCan('clients', 'create')
                <button onclick="openCreateModal()" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter un client
                </button>
                @enduserCan
            </div>
        @endif
    </div>
</div>

<!-- Modal de création/édition -->
<div id="clientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Nouveau Client</h3>
            </div>
            <form id="clientForm" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" id="nom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input type="text" id="telephone" name="telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                        <select id="sexe" name="sexe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-1">Lieu de naissance</label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div>
                        <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                        <input type="text" id="profession" name="profession" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"></textarea>
                    </div>
                    <div>
                        <label for="type_piece_identite" class="block text-sm font-medium text-gray-700 mb-1">Type de pièce d'identité</label>
                        <select id="type_piece_identite" name="type_piece_identite" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            <option value="">Sélectionner</option>
                            <option value="CNI">CNI</option>
                            <option value="Passeport">Passeport</option>
                            <option value="Carte de résident">Carte de résident</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="numero_piece_identite" class="block text-sm font-medium text-gray-700 mb-1">Numéro de pièce d'identité</label>
                        <input type="text" id="numero_piece_identite" name="numero_piece_identite" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de détails du client -->
<div id="clientDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-screen overflow-y-auto">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-mayelia-600"></i>
                        Détails du Client
                    </h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div id="clientDetailContent" class="p-8">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="px-8 py-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Informations mises à jour en temps réel
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeDetailModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentClientId = null;

function openCreateModal() {
    currentClientId = null;
    document.getElementById('modalTitle').textContent = 'Nouveau Client';
    document.getElementById('clientForm').reset();
    document.getElementById('clientModal').classList.remove('hidden');
}

function editClient(id) {
    currentClientId = id;
    
    // Charger les données du client
    fetch(`/clients/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const client = data.client;
                
                // Remplir le formulaire
                document.getElementById('nom').value = client.nom || '';
                document.getElementById('prenom').value = client.prenom || '';
                document.getElementById('email').value = client.email || '';
                document.getElementById('telephone').value = client.telephone || '';
                document.getElementById('date_naissance').value = client.date_naissance || '';
                document.getElementById('sexe').value = client.sexe || '';
                document.getElementById('lieu_naissance').value = client.lieu_naissance || '';
                document.getElementById('profession').value = client.profession || '';
                document.getElementById('adresse').value = client.adresse || '';
                document.getElementById('type_piece_identite').value = client.type_piece_identite || '';
                document.getElementById('numero_piece_identite').value = client.numero_piece_identite || '';
                document.getElementById('notes').value = client.notes || '';
                
                // Ouvrir le modal
                document.getElementById('modalTitle').textContent = 'Modifier le Client';
                document.getElementById('clientModal').classList.remove('hidden');
            } else {
                showErrorToast('Erreur lors du chargement du client');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorToast('Erreur lors du chargement du client');
        });
}

function viewClient(id) {
    // Charger les détails du client
    fetch(`/clients/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const client = data.client;
                
                // Créer le contenu HTML
                const content = `
                    <div class="space-y-8">
                        <!-- En-tête du client -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-xl text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-2xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold">${client.nom} ${client.prenom}</h2>
                                        <p class="text-mayelia-100">${client.profession || 'Profession non renseignée'}</p>
                                        <div class="flex items-center mt-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${client.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                <i class="fas fa-circle text-xs mr-2"></i>
                                                ${client.actif ? 'Actif' : 'Inactif'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-mayelia-100 text-sm">Membre depuis</p>
                                    <p class="text-lg font-semibold">${new Date(client.created_at).toLocaleDateString('fr-FR')}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informations principales -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Informations personnelles -->
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-user mr-3 text-mayelia-600"></i>
                                    Informations personnelles
                                </h4>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Nom complet</label>
                                            <p class="text-gray-900 font-medium">${client.nom} ${client.prenom}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Email</label>
                                            <p class="text-gray-900">${client.email}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Téléphone</label>
                                            <p class="text-gray-900">${client.telephone}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Date de naissance</label>
                                            <p class="text-gray-900">${client.date_naissance ? new Date(client.date_naissance).toLocaleDateString('fr-FR') : 'Non renseigné'}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Sexe</label>
                                            <p class="text-gray-900">${client.sexe === 'M' ? 'Masculin' : (client.sexe === 'F' ? 'Féminin' : 'Non renseigné')}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500 block mb-1">Lieu de naissance</label>
                                            <p class="text-gray-900">${client.lieu_naissance || 'Non renseigné'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations professionnelles -->
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-briefcase mr-3 text-green-600"></i>
                                    Informations professionnelles
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Profession</label>
                                        <p class="text-gray-900">${client.profession || 'Non renseigné'}</p>
                                    </div>
                                    ${client.adresse ? `
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Adresse</label>
                                        <p class="text-gray-900">${client.adresse}</p>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Pièce d'identité et Notes -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Pièce d'identité -->
                            ${client.type_piece_identite ? `
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-id-card mr-3 text-purple-600"></i>
                                    Pièce d'identité
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Type</label>
                                        <p class="text-gray-900">${client.type_piece_identite}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Numéro</label>
                                        <p class="text-gray-900 font-mono">${client.numero_piece_identite}</p>
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            <!-- Notes -->
                            ${client.notes ? `
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-sticky-note mr-3 text-yellow-600"></i>
                                    Notes
                                </h4>
                                <p class="text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">${client.notes}</p>
                            </div>
                            ` : ''}
                        </div>

                        <!-- Rendez-vous et statistiques -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-calendar-alt mr-3 text-indigo-600"></i>
                                Rendez-vous et statistiques
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center p-4 bg-mayelia-50 rounded-lg">
                                    <i class="fas fa-calendar-check text-2xl text-mayelia-600 mb-2"></i>
                                    <p class="text-2xl font-bold text-mayelia-900">${client.rendez_vous_count || 0}</p>
                                    <p class="text-sm text-mayelia-700">Rendez-vous total</p>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <i class="fas fa-user-check text-2xl text-green-600 mb-2"></i>
                                    <p class="text-2xl font-bold text-green-900">${client.actif ? 'Actif' : 'Inactif'}</p>
                                    <p class="text-sm text-green-700">Statut du client</p>
                                </div>
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <i class="fas fa-clock text-2xl text-purple-600 mb-2"></i>
                                    <p class="text-2xl font-bold text-purple-900">${new Date(client.updated_at).toLocaleDateString('fr-FR')}</p>
                                    <p class="text-sm text-purple-700">Dernière mise à jour</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('clientDetailContent').innerHTML = content;
                document.getElementById('clientDetailModal').classList.remove('hidden');
            } else {
                showErrorToast('Erreur lors du chargement des détails du client');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorToast('Erreur lors du chargement des détails du client');
        });
}

function toggleClientStatus(id) {
    if (confirm('Êtes-vous sûr de vouloir changer le statut de ce client ?')) {
        fetch(`/clients/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                location.reload();
            } else {
                showErrorToast(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorToast('Erreur lors du changement de statut');
        });
    }
}

function deleteClient(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
        fetch(`/clients/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                location.reload();
            } else {
                showErrorToast(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorToast('Erreur lors de la suppression');
        });
    }
}

function closeModal() {
    document.getElementById('clientModal').classList.add('hidden');
}

function closeDetailModal() {
    document.getElementById('clientDetailModal').classList.add('hidden');
}

// Gestion du formulaire
document.getElementById('clientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentClientId ? `/clients/${currentClientId}` : '/clients';
    const method = currentClientId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(data.message);
            closeModal();
            location.reload();
        } else {
            if (data.errors) {
                // Afficher les erreurs de validation
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('border-red-500');
                        // TODO: Afficher le message d'erreur
                    }
                });
            }
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de l\'enregistrement');
    });
});

// Fonctions de notification (utilisent le composant toast unifié du layout)
// Ces fonctions sont définies dans components/toast.blade.php qui est inclus dans layouts/dashboard.blade.php
</script>
@endsection
