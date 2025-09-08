@extends('layouts.dashboard')

@section('title', 'Documents Requis')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-alt text-indigo-500 mr-2"></i>
                    Documents requis
                </h3>
                <p class="text-sm text-gray-600 mt-1">Configurez les documents requis par service et type de demande</p>
            </div>
            <a href="{{ route('document-requis.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Document
            </a>
        </div>
    </div>

    <!-- Onglets par service -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button onclick="switchTab('all')" 
                    id="tab-all"
                    class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                <i class="fas fa-list mr-2"></i>
                Tous les services
                <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs">{{ $documents->count() }}</span>
            </button>
            @foreach(\App\Models\Service::where('actif', true)->get() as $service)
                @php
                    $serviceCount = $documents->where('service_id', $service->id)->count();
                @endphp
                <button onclick="switchTab('{{ $service->id }}')" 
                        id="tab-{{ $service->id }}"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-cogs mr-2"></i>
                    {{ $service->nom }}
                    <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $serviceCount }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de demande</label>
                <input type="text" id="filterType" placeholder="Type de demande..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Obligatoire</label>
                <select id="filterObligatoire" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="1">Obligatoire</option>
                    <option value="0">Optionnel</option>
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

    <!-- Contenu des onglets -->
    <div id="tab-content">
        <!-- Onglet Tous les services -->
        <div id="content-all" class="tab-content">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type de Demande
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Document
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Obligatoire
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ordre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($documents as $document)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-cogs text-indigo-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $document->service->nom ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $document->type_demande }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $document->nom_document }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $document->description }}">
                                        {{ $document->description ? Str::limit($document->description, 50) : 'Aucune description' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($document->obligatoire)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Obligatoire
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-info-circle mr-1"></i> Optionnel
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $document->ordre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('document-requis.edit', $document) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-yellow-50">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('document-requis.destroy', $document) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
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
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                                        <p class="text-lg">Aucun document requis trouvé</p>
                                        <p class="text-sm">Commencez par configurer les documents requis</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Onglets par service -->
        @foreach(\App\Models\Service::where('actif', true)->get() as $service)
            <div id="content-{{ $service->id }}" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type de Demande
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Document
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Obligatoire
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ordre
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $serviceDocuments = $documents->where('service_id', $service->id);
                            @endphp
                            @forelse($serviceDocuments as $document)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $document->type_demande }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->nom_document }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $document->description }}">
                                            {{ $document->description ? Str::limit($document->description, 50) : 'Aucune description' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($document->obligatoire)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Obligatoire
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-info-circle mr-1"></i> Optionnel
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $document->ordre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('document-requis.edit', $document) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-2 rounded-md hover:bg-yellow-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('document-requis.destroy', $document) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
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
                                            <i class="fas fa-file-alt text-4xl mb-4"></i>
                                            <p class="text-lg">Aucun document requis pour {{ $service->nom }}</p>
                                            <p class="text-sm">Commencez par ajouter des documents pour ce service</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($documents->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $documents->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.tab-button.active {
    border-color: #4f46e5;
    color: #4f46e5;
}

.tab-content {
    display: block;
}

.tab-content.hidden {
    display: none;
}
</style>

<script>
function switchTab(serviceId) {
    // Désactiver tous les onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.classList.add('border-transparent', 'text-gray-500');
        button.classList.remove('border-indigo-500', 'text-indigo-600');
    });
    
    // Masquer tout le contenu
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Activer l'onglet sélectionné
    const selectedTab = document.getElementById('tab-' + serviceId);
    selectedTab.classList.add('active');
    selectedTab.classList.remove('border-transparent', 'text-gray-500');
    selectedTab.classList.add('border-indigo-500', 'text-indigo-600');
    
    // Afficher le contenu correspondant
    const selectedContent = document.getElementById('content-' + serviceId);
    selectedContent.classList.remove('hidden');
}

function applyFilters() {
    // Implémentation des filtres
    console.log('Filtres appliqués');
}
</script>
@endsection
