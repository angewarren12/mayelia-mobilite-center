@extends('layouts.dashboard')

@section('title', 'Gestion des Clients')
@section('subtitle', 'Liste des clients du centre')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Clients</h2>
            <p class="text-gray-600">Gérez les clients de votre centre</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Client
        </button>
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
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-48">
                <label for="actif" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="actif" name="actif" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                        <button onclick="viewClient({{ $client->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editClient({{ $client->id }})" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="toggleClientStatus({{ $client->id }})" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-toggle-{{ $client->actif ? 'on' : 'off' }}"></i>
                                        </button>
                                        <button onclick="deleteClient({{ $client->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter un client
                </button>
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
                        <input type="text" id="nom" name="nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input type="text" id="telephone" name="telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="sexe" class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                        <select id="sexe" name="sexe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-1">Lieu de naissance</label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                        <input type="text" id="profession" name="profession" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label for="type_piece_identite" class="block text-sm font-medium text-gray-700 mb-1">Type de pièce d'identité</label>
                        <select id="type_piece_identite" name="type_piece_identite" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner</option>
                            <option value="CNI">CNI</option>
                            <option value="Passeport">Passeport</option>
                            <option value="Carte de résident">Carte de résident</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="numero_piece_identite" class="block text-sm font-medium text-gray-700 mb-1">Numéro de pièce d'identité</label>
                        <input type="text" id="numero_piece_identite" name="numero_piece_identite" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
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
    // TODO: Implémenter l'édition
    console.log('Édition du client:', id);
}

function viewClient(id) {
    // TODO: Implémenter la visualisation
    console.log('Visualisation du client:', id);
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
</script>
@endsection
