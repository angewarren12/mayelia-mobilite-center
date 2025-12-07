@extends('creneaux.layout')

@section('title', 'Gestion des Exceptions')
@section('subtitle', 'Gérez les jours exceptionnels : fermetures, capacités réduites, horaires modifiés')

@section('creneaux_content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                    Exceptions configurées
                </h3>
                <p class="text-sm text-gray-600 mt-1">Gérez les jours exceptionnels de votre centre</p>
            </div>
            @userCan('creneaux', 'exceptions.create')
            <button onclick="openCreateExceptionModal()" 
                    class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Exception
            </button>
            @enduserCan
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-6 py-4 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type d'exception</label>
                    <select id="filterType" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="">Tous les types</option>
                        <option value="ferme">Centre fermé</option>
                        <option value="capacite_reduite">Capacité réduite</option>
                        <option value="horaires_modifies">Horaires modifiés</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                    <input type="date" id="filterDateDebut" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                </div>
e               <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                    <input type="date" id="filterDateFin" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
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

    <!-- Liste des exceptions -->
    <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Horaires
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Capacité
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="exceptionsTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($exceptions as $exception)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-day text-mayelia-500 mr-2"></i>
                                    {{ $exception->date_exception->format('d/m/Y') }}
                                    <span class="ml-2 text-xs text-gray-500">
                                        ({{ $exception->date_exception->format('l') }})
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($exception->type === 'ferme')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Centre fermé
                                    </span>
                                @elseif($exception->type === 'capacite_reduite')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-users mr-1"></i>
                                        Capacité réduite
                                    </span>
                                @elseif($exception->type === 'horaires_modifies')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-mayelia-100 text-mayelia-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Horaires modifiés
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $exception->description ?? 'Aucune description' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($exception->type === 'ferme')
                                    <span class="text-gray-400 italic">Fermé</span>
                                @else
                                    <div class="space-y-1">
                                        @if($exception->heure_debut && $exception->heure_fin)
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                                {{ $exception->heure_debut->format('H:i') }} - {{ $exception->heure_fin->format('H:i') }}
                                            </div>
                                        @endif
                                        @if($exception->pause_debut && $exception->pause_fin)
                                            <div class="flex items-center text-orange-600">
                                                <i class="fas fa-pause mr-1"></i>
                                                Pause: {{ $exception->pause_debut->format('H:i') }} - {{ $exception->pause_fin->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($exception->type === 'capacite_reduite' && $exception->capacite_reduite)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $exception->capacite_reduite }} personnes
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @userCan('creneaux', 'exceptions.update')
                                    <button onclick="editException({{ $exception->id }})" 
                                            class="text-mayelia-600 hover:text-mayelia-900 transition duration-200">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @enduserCan
                                    @userCan('creneaux', 'exceptions.delete')
                                    <button onclick="deleteException({{ $exception->id }})" 
                                            class="text-red-600 hover:text-red-900 transition duration-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @enduserCan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">Aucune exception configurée</p>
                                    <p class="text-sm">Cliquez sur "Nouvelle Exception" pour commencer</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
    </div>
</div>

<!-- Modal de création/modification d'exception -->
<div id="exceptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 id="exceptionModalTitle" class="text-lg font-medium text-gray-900">
                    Nouvelle Exception
                </h3>
            </div>
            
            <form id="exceptionForm" class="p-6">
                @csrf
                <input type="hidden" id="exceptionId" name="id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date d'exception -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-1"></i>
                            Date d'exception *
                        </label>
                        <input type="date" id="dateException" name="date_exception" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <!-- Type d'exception -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Type d'exception *
                        </label>
                        <select id="typeException" name="type" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500"
                                onchange="toggleExceptionFields()">
                            <option value="">Sélectionner un type</option>
                            <option value="ferme">Centre fermé</option>
                            <option value="capacite_reduite">Capacité réduite</option>
                            <option value="horaires_modifies">Horaires modifiés</option>
                        </select>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1"></i>
                        Description
                    </label>
                    <textarea id="descriptionException" name="description" rows="3"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500"
                              placeholder="Ex: Jour férié, maintenance, événement spécial..."></textarea>
                </div>
                
                <!-- Champs conditionnels -->
                <div id="horairesFields" class="mt-6 hidden">
                    <h4 class="text-md font-medium text-gray-900 mb-4">
                        <i class="fas fa-clock mr-2"></i>
                        Horaires exceptionnels
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Heure de début</label>
                            <input type="time" id="heureDebut" name="heure_debut"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Heure de fin</label>
                            <input type="time" id="heureFin" name="heure_fin"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pause début</label>
                            <input type="time" id="pauseDebut" name="pause_debut"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pause fin</label>
                            <input type="time" id="pauseFin" name="pause_fin"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
                    </div>
                </div>
                
                <div id="capaciteField" class="mt-6 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1"></i>
                        Capacité réduite (nombre de personnes)
                    </label>
                    <input type="number" id="capaciteReduite" name="capacite_reduite" min="1"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-mayelia-500"
                           placeholder="Ex: 5">
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeExceptionModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-200">
                    Annuler
                </button>
                <button type="button" onclick="saveException()"
                        class="px-4 py-2 text-sm font-medium text-white bg-mayelia-600 hover:bg-mayelia-700 rounded-md transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentExceptionId = null;

// Fonctions de gestion des modals
function openCreateExceptionModal() {
    currentExceptionId = null;
    document.getElementById('exceptionModalTitle').textContent = 'Nouvelle Exception';
    document.getElementById('exceptionForm').reset();
    document.getElementById('horairesFields').classList.add('hidden');
    document.getElementById('capaciteField').classList.add('hidden');
    document.getElementById('exceptionModal').classList.remove('hidden');
}

function closeExceptionModal() {
    document.getElementById('exceptionModal').classList.add('hidden');
    currentExceptionId = null;
}

function toggleExceptionFields() {
    const type = document.getElementById('typeException').value;
    const horairesFields = document.getElementById('horairesFields');
    const capaciteField = document.getElementById('capaciteField');
    
    // Masquer tous les champs conditionnels
    horairesFields.classList.add('hidden');
    capaciteField.classList.add('hidden');
    
    // Afficher les champs selon le type
    if (type === 'horaires_modifies') {
        horairesFields.classList.remove('hidden');
    } else if (type === 'capacite_reduite') {
        capaciteField.classList.remove('hidden');
    }
}

// Fonctions CRUD
function saveException() {
    const form = document.getElementById('exceptionForm');
    const formData = new FormData(form);
    
    const url = currentExceptionId ? 
        `/exceptions/${currentExceptionId}` : 
        '/exceptions';
    
    const method = currentExceptionId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showSuccessToast(data.message || 'Exception sauvegardée avec succès');
            closeExceptionModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            console.error('Erreur de sauvegarde:', data);
            showErrorToast(data.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Erreur lors de la sauvegarde');
    });
}

function editException(id) {
    // Récupérer les données de l'exception via AJAX
    fetch(`/exceptions/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const exception = data.exception;
            currentExceptionId = id;
            
            document.getElementById('exceptionModalTitle').textContent = 'Modifier l\'Exception';
            document.getElementById('dateException').value = exception.date_exception;
            document.getElementById('typeException').value = exception.type;
            document.getElementById('descriptionException').value = exception.description || '';
            
            if (exception.heure_debut) {
                document.getElementById('heureDebut').value = exception.heure_debut;
            }
            if (exception.heure_fin) {
                document.getElementById('heureFin').value = exception.heure_fin;
            }
            if (exception.pause_debut) {
                document.getElementById('pauseDebut').value = exception.pause_debut;
            }
            if (exception.pause_fin) {
                document.getElementById('pauseFin').value = exception.pause_fin;
            }
            if (exception.capacite_reduite) {
                document.getElementById('capaciteReduite').value = exception.capacite_reduite;
            }
            
            toggleExceptionFields();
            document.getElementById('exceptionModal').classList.remove('hidden');
            showInfoToast('Exception chargée pour modification');
        } else {
            showErrorToast('Erreur lors du chargement de l\'exception');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Erreur lors du chargement de l\'exception');
    });
}

function deleteException(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette exception ?')) {
        fetch(`/exceptions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message || 'Exception supprimée avec succès');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showErrorToast(data.message || 'Impossible de supprimer l\'exception');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Erreur lors de la suppression');
        });
    }
}

function applyFilters() {
    // Implémentation des filtres (à développer)
    console.log('Filtres appliqués');
}
</script>
@endsection