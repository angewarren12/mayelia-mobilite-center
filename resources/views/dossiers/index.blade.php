@extends('layouts.dashboard')

@section('title', 'Gestion des Dossiers')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-folder-open text-orange-500 mr-2"></i>
                    Dossiers clients
                </h3>
                <p class="text-sm text-gray-600 mt-1">Gérez les dossiers de traitement des clients</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="filter-statut" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="ouvert">Ouvert</option>
                    <option value="valide">Validé</option>
                    <option value="reprogramme">Reprogrammé</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date RDV</label>
                <input type="date" id="filter-date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                <input type="text" id="filter-client" placeholder="Nom du client..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" 
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Service
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date RDV
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Agent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Paiement
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Biométrie
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dossiers as $dossier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous->client->telephone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $dossier->rendezVous->formule->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $dossier->rendezVous->tranche_horaire }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $dossier->agent->nom_complet ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $dossier->statut === 'valide' ? 'bg-green-100 text-green-800' : ($dossier->statut === 'ouvert' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($dossier->statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($dossier->paiement_effectue)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Payé
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Non payé
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($dossier->biometrie_passee)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Passée
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('dossiers.show', $dossier) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($dossier->statut === 'ouvert')
                                    <button type="button" 
                                            class="validate-dossier text-green-600 hover:text-green-900 p-2 rounded-md hover:bg-green-50"
                                            data-id="{{ $dossier->id }}"
                                            title="Valider le dossier">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-4"></i>
                                <p class="text-lg">Aucun dossier trouvé</p>
                                <p class="text-sm">Les dossiers apparaîtront ici une fois ouverts</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($dossiers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $dossiers->links() }}
            </div>
        </div>
    @endif
</div>

@include('components.toast')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation des dossiers
    document.querySelectorAll('.validate-dossier').forEach(button => {
        button.addEventListener('click', function() {
            const dossierId = this.dataset.id;
            
            if (confirm('Êtes-vous sûr de vouloir valider ce dossier ?')) {
                fetch(`/dossiers/${dossierId}/validate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        location.reload();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors de la validation', 'error');
                });
            }
        });
    });
});

function applyFilters() {
    // Implémentation des filtres côté client ou AJAX
    console.log('Filtres appliqués');
}
</script>
@endpush
