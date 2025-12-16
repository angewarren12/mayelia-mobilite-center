@extends('layouts.oneci')

@section('title', 'Dossiers ONECI')
@section('subtitle', 'Gestion des dossiers reçus')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-end items-center">
        <div class="flex space-x-2">
            <button onclick="openExportModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
            <a href="{{ route('oneci.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('oneci.dossiers') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Code-barres, nom client..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="recu" {{ request('statut') === 'recu' ? 'selected' : '' }}>Reçu</option>
                    <option value="traite" {{ request('statut') === 'traite' ? 'selected' : '' }}>Traité</option>
                    <option value="carte_prete" {{ request('statut') === 'carte_prete' ? 'selected' : '' }}>Carte prête</option>
                    <option value="recupere" {{ request('statut') === 'recupere' ? 'selected' : '' }}>Récupéré</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('oneci.dossiers') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des dossiers -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($dossiers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code-barres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Centre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date réception</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dossiers as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-900">{{ $item->code_barre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->dossierOuvert->rendezVous->service->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->transfer->centre->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->date_reception ? $item->date_reception->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'recu' => 'bg-mayelia-100 text-mayelia-800',
                                            'traite' => 'bg-indigo-100 text-indigo-800',
                                            'carte_prete' => 'bg-green-100 text-green-800',
                                            'recupere' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $color = $statutColors[$item->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $item->statut_formate }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('oneci.dossiers.workflow', $item) }}" 
                                       class="text-mayelia-600 hover:text-mayelia-900" 
                                       title="Voir le workflow complet">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $dossiers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier trouvé</h3>
                <p class="text-gray-500">Aucun dossier ne correspond à vos critères de recherche.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal d'export des dossiers -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-download text-mayelia-600 mr-2"></i>
                        Exporter les dossiers
                    </h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="exportForm" onsubmit="handleExport(event)">
                    @csrf
                    
                    <!-- Type d'export -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Période d'export
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="aujourdhui" class="mr-3" checked onchange="toggleExportFields()">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-day text-mayelia-500 mr-2"></i>
                                    Aujourd'hui
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="date" class="mr-3" onchange="toggleExportFields()">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar text-green-500 mr-2"></i>
                                    Date spécifique
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="plage" class="mr-3" onchange="toggleExportFields()">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                    Plage de dates
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date spécifique -->
                    <div id="dateField" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date
                        </label>
                        <input type="date" name="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <!-- Plage de dates -->
                    <div id="plageFields" class="mb-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date début
                                </label>
                                <input type="date" name="date_debut" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date fin
                                </label>
                                <input type="date" name="date_fin" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtres supplémentaires -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtres supplémentaires (optionnels)</h4>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut
                                </label>
                                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="recu">Reçu</option>
                                    <option value="traite">Traité</option>
                                    <option value="carte_prete">Carte prête</option>
                                    <option value="recupere">Récupéré</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeExportModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
    document.getElementById('exportForm').reset();
    toggleExportFields();
}

function toggleExportFields() {
    const typeExport = document.querySelector('input[name="type_export"]:checked').value;
    const dateField = document.getElementById('dateField');
    const plageFields = document.getElementById('plageFields');
    
    dateField.classList.add('hidden');
    plageFields.classList.add('hidden');
    
    if (typeExport === 'date') {
        dateField.classList.remove('hidden');
    } else if (typeExport === 'plage') {
        plageFields.classList.remove('hidden');
    }
}

function handleExport(event) {
    event.preventDefault();
    
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Export en cours...';
    submitButton.disabled = true;
    
    fetch('{{ route("export.dossiers") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                let errorMessage = 'Erreur lors de l\'export: ' + response.status;
                try {
                    const data = JSON.parse(text);
                    if (data.error) {
                        errorMessage = data.error;
                    }
                } catch (e) {
                    console.error('Erreur parse JSON:', e);
                }
                throw new Error(errorMessage);
            });
        }
        
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                throw new Error('Réponse inattendue du serveur');
            });
        }
        
        if (contentType && (contentType.includes('application/pdf') || contentType.includes('octet-stream'))) {
            return response.blob().then(blob => {
                const contentDisposition = response.headers.get('content-disposition');
                const filename = getFilenameFromContentDisposition(contentDisposition);
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                alert('Export PDF téléchargé avec succès !');
                closeExportModal();
            });
        }
        
        throw new Error('Format de réponse non reconnu');
    })
    .catch(error => {
        console.error('Erreur export:', error);
        alert(error.message || 'Erreur lors de l\'export');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function getFilenameFromContentDisposition(contentDisposition) {
    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    if (filenameMatch && filenameMatch[1]) {
        return filenameMatch[1].replace(/['"]/g, '');
    }
    return 'dossiers-export.pdf';
}
</script>
@endsection

