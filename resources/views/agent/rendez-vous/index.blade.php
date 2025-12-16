@extends('layouts.dashboard')

@section('title', 'Rendez-vous - Interface Agent')
@section('subtitle', 'Gestion des rendez-vous de votre centre')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Rendez-vous du Centre</h2>
            <p class="text-gray-600">Gérez les rendez-vous de votre centre</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
                Connecté en tant que : <strong>{{ Auth::user()->nom }}</strong>
            </span>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('agent.rendez-vous.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nom client, email, numéro de suivi..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les statuts</option>
                    <option value="confirme" {{ request('statut') === 'confirme' ? 'selected' : '' }}>Confirmé</option>
                    <option value="dossier_ouvert" {{ request('statut') === 'dossier_ouvert' ? 'selected' : '' }}>Dossier ouvert</option>
                    <option value="documents_verifies" {{ request('statut') === 'documents_verifies' ? 'selected' : '' }}>Documents vérifiés</option>
                    <option value="documents_manquants" {{ request('statut') === 'documents_manquants' ? 'selected' : '' }}>Documents manquants</option>
                    <option value="paiement_effectue" {{ request('statut') === 'paiement_effectue' ? 'selected' : '' }}>Paiement effectué</option>
                    <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('agent.rendez-vous.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des rendez-vous -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($rendezVous->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rendezVous as $rdv)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $rdv->client->nom_complet ?? 'Client supprimé' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $rdv->client->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $rdv->service->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $rdv->formule->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $rdv->date_rendez_vous->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $rdv->tranche_horaire }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($rdv->statut === 'confirme') bg-mayelia-100 text-mayelia-800
                                        @elseif($rdv->statut === 'dossier_ouvert') bg-yellow-100 text-yellow-800
                                        @elseif($rdv->statut === 'documents_verifies') bg-green-100 text-green-800
                                        @elseif($rdv->statut === 'documents_manquants') bg-orange-100 text-orange-800
                                        @elseif($rdv->statut === 'paiement_effectue') bg-purple-100 text-purple-800
                                        @elseif($rdv->statut === 'annule') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $rdv->statut_formate }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rdv->dossierOuvert)
                                        <div class="text-sm text-gray-900">{{ $rdv->dossierOuvert->agent->nom ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $rdv->dossierOuvert->date_ouverture->format('d/m/Y H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($rdv->dossierOuvert)
                                            @if($rdv->dossierOuvert->canBeManagedBy(Auth::user()))
                                                <a href="{{ route('agent.dossier.workflow', $rdv->dossierOuvert) }}" 
                                                   class="text-mayelia-600 hover:text-mayelia-900" title="Gérer le dossier">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                            @else
                                                <span class="text-gray-400" title="Dossier géré par {{ $rdv->dossierOuvert->agent->nom }}">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        @endif
                                        
                                        <a href="{{ route('rendez-vous.show', $rdv) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $rendezVous->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rendez-vous trouvé</h3>
                <p class="text-gray-500">Aucun rendez-vous ne correspond aux critères de recherche.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation d'ouverture de dossier -->
<div id="openDossierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Confirmer l'ouverture du dossier</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">
                    Êtes-vous sûr de vouloir ouvrir le dossier de <strong id="clientName"></strong> ?
                </p>
                <p class="text-sm text-red-600 mb-4">
                    ⚠️ Une fois ouvert, aucun autre agent ne pourra prendre ce dossier.
                </p>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                    <textarea id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500" placeholder="Notes sur l'ouverture du dossier..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeOpenDossierModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button onclick="confirmOpenDossier()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-folder-open mr-2"></i>
                    Ouvrir le dossier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRendezVousId = null;

function openDossierModal(rendezVousId, clientName) {
    currentRendezVousId = rendezVousId;
    document.getElementById('clientName').textContent = clientName;
    document.getElementById('openDossierModal').classList.remove('hidden');
}

function closeOpenDossierModal() {
    document.getElementById('openDossierModal').classList.add('hidden');
    currentRendezVousId = null;
    document.getElementById('notes').value = '';
}

function confirmOpenDossier() {
    if (!currentRendezVousId) return;

    const notes = document.getElementById('notes').value;
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ouverture...';
    button.disabled = true;

    fetch(`/agent/rendez-vous/${currentRendezVousId}/open-dossier`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher un message de succès
            showSuccessToast(data.message);
            // Recharger la page
            window.location.reload();
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showErrorToast('Erreur lors de l\'ouverture du dossier');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        closeOpenDossierModal();
    });
}

// Fonctions de notification (utilisent le composant toast unifié du layout)
// Ces fonctions sont définies dans components/toast.blade.php qui est inclus dans layouts/dashboard.blade.php

// Fermer le modal en cliquant à l'extérieur
document.getElementById('openDossierModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOpenDossierModal();
    }
});
</script>
@endsection
