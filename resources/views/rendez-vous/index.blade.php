@extends('layouts.dashboard')

@section('title', 'Gestion des Rendez-vous')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                    Rendez-vous programmés
                </h3>
                <p class="text-sm text-gray-600 mt-1">Gérez les rendez-vous de vos clients</p>
            </div>
            <a href="{{ route('rendez-vous.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Rendez-vous
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Centre</label>
                <select id="filter-centre" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les centres</option>
                    @foreach(\App\Models\Centre::all() as $centre)
                        <option value="{{ $centre->id }}">{{ $centre->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" id="filter-date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="filter-statut" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="confirme">Confirmé</option>
                    <option value="annule">Annulé</option>
                    <option value="termine">Terminé</option>
                </select>
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
                        Centre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date & Heure
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Suivi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rendezVous as $rdv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $rdv->client->nom_complet ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $rdv->client->telephone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $rdv->service->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $rdv->formule->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $rdv->centre->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $rdv->date_rendez_vous->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $rdv->tranche_horaire }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rdv->statut === 'confirme' ? 'bg-green-100 text-green-800' : ($rdv->statut === 'annule' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($rdv->statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-mono">{{ $rdv->numero_suivi ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('rendez-vous.show', $rdv) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('rendez-vous.edit', $rdv) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-yellow-50">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($rdv->statut === 'confirme')
                                    <button type="button" 
                                            class="open-dossier text-green-600 hover:text-green-900 p-2 rounded-md hover:bg-green-50"
                                            data-rendez-vous-id="{{ $rdv->id }}"
                                            title="Ouvrir Dossier">
                                        <i class="fas fa-folder-open"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-calendar-check text-4xl mb-4"></i>
                                <p class="text-lg">Aucun rendez-vous trouvé</p>
                                <p class="text-sm">Commencez par créer votre premier rendez-vous</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($rendezVous->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $rendezVous->links() }}
            </div>
        </div>
    @endif
</div>

@include('components.toast')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ouvrir un dossier
    document.querySelectorAll('.open-dossier').forEach(button => {
        button.addEventListener('click', function() {
            const rendezVousId = this.dataset.rendezVousId;
            
            if (confirm('Êtes-vous sûr de vouloir ouvrir un dossier pour ce rendez-vous ?')) {
                fetch(`/dossiers/open/${rendezVousId}`, {
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
                        // Rediriger vers la page du dossier
                        window.location.href = `/dossiers/${data.dossier_id}`;
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur lors de l\'ouverture du dossier', 'error');
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
