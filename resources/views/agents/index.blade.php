@extends('layouts.dashboard')

@section('title', 'Gestion des Agents')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-tie text-purple-500 mr-2"></i>
                    Agents du centre
                </h3>
                <p class="text-sm text-gray-600 mt-1">Gérez les agents de votre centre</p>
            </div>
            <a href="{{ route('agents.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouvel Agent
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" id="searchAgent" placeholder="Nom, email ou téléphone..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Centre</label>
                <select id="filterCentre" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les centres</option>
                    @foreach(\App\Models\Centre::all() as $centre)
                        <option value="{{ $centre->id }}">{{ $centre->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="filterStatut" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
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
                        Agent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Centre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dernière Connexion
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($agents as $agent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-user-tie text-purple-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $agent->nom_complet }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $agent->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $agent->email }}</div>
                            <div class="text-sm text-gray-500">{{ $agent->telephone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $agent->centre->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $agent->actif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $agent->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $agent->derniere_connexion ? $agent->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('agents.show', $agent) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('agents.edit', $agent) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-yellow-50">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="toggle-status {{ $agent->actif ? 'text-gray-600 hover:text-gray-900' : 'text-green-600 hover:text-green-900' }} p-2 rounded-md hover:bg-gray-50"
                                        data-id="{{ $agent->id }}">
                                    <i class="fas fa-{{ $agent->actif ? 'pause' : 'play' }}"></i>
                                </button>
                                <form action="{{ route('agents.destroy', $agent) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet agent ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-user-tie text-4xl mb-4"></i>
                                <p class="text-lg">Aucun agent trouvé</p>
                                <p class="text-sm">Commencez par ajouter votre premier agent</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($agents->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $agents->links() }}
            </div>
        </div>
    @endif
</div>

@include('components.toast')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status des agents
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.dataset.id;
            const button = this;
            
            fetch(`/agents/${agentId}/toggle-status`, {
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
                    // Recharger la page pour mettre à jour l'affichage
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors du changement de statut', 'error');
            });
        });
    });
});
</script>
@endpush
