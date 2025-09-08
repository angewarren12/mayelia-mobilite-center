@extends('creneaux.layout')

@section('title', 'Jours Ouvrables')
@section('subtitle', 'Configurez les jours d\'ouverture et les horaires de travail')

@section('creneaux_content')

<!-- Contenu de l'onglet Jours ouvrables -->
<div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Configuration des jours de travail</h3>
                    <p class="text-sm text-gray-600 mt-1">Définissez les jours d'ouverture et les horaires de travail du centre</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-50 px-4 py-2 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium text-blue-900">{{ $joursOuverts }} jours ouverts</span>
                    </div>
                    <button onclick="testDatabase()" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium text-gray-700">
                        <i class="fas fa-database mr-2"></i>
                        Tester la base
                    </button>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $joursSemaine = [
                        1 => ['nom' => 'Lundi', 'lettre' => 'L'],
                        2 => ['nom' => 'Mardi', 'lettre' => 'M'],
                        3 => ['nom' => 'Mercredi', 'lettre' => 'M'],
                        4 => ['nom' => 'Jeudi', 'lettre' => 'J'],
                        5 => ['nom' => 'Vendredi', 'lettre' => 'V'],
                        6 => ['nom' => 'Samedi', 'lettre' => 'S'],
                        7 => ['nom' => 'Dimanche', 'lettre' => 'D']
                    ];
                @endphp

                @foreach($joursSemaine as $numero => $jour)
                    @php
                        $jourTravail = $joursTravail->where('jour_semaine', $numero)->first();
                        $actif = $jourTravail ? $jourTravail->actif : false;
                        // Debug: afficher les données pour Lundi
                        if($numero == 1) {
                            \Log::info('Jour travail Lundi:', [
                                'jourTravail' => $jourTravail ? $jourTravail->toArray() : 'null',
                                'actif' => $actif
                            ]);
                        }
                    @endphp
                    
                    <div class="border border-gray-200 rounded-lg p-4 {{ $actif ? 'bg-white border-blue-200' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $actif ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }}">
                                    <span class="font-semibold">{{ $jour['lettre'] }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $jour['nom'] }}</h4>
                                    <p class="text-sm {{ $actif ? 'text-blue-600' : 'text-gray-500' }}">
                                        {{ $actif ? 'Jour ouvrable' : 'Fermé' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Toggle d'activation -->
                            <div class="flex items-center space-x-4">
                                @if($actif)
                                    <div class="text-sm text-gray-600">
                                        <div class="flex items-center space-x-4">
                                            <span><i class="fas fa-clock mr-1"></i>{{ $jourTravail->heure_debut }} - {{ $jourTravail->heure_fin }}</span>
                                            @if($jourTravail->pause_debut && $jourTravail->pause_fin)
                                                <span><i class="fas fa-pause mr-1"></i>{{ $jourTravail->pause_debut }} - {{ $jourTravail->pause_fin }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $actif ? 'checked' : '' }} 
                                           onchange="toggleJour({{ $jourTravail ? $jourTravail->id : 'null' }}, {{ $numero }})">
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Formulaire des horaires (visible seulement si actif) -->
                        @if($actif && $jourTravail)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <form id="form-horaires-{{ $jourTravail->id }}" onsubmit="updateHoraires(event, {{ $jourTravail->id }})">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Horaires de travail</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Ouverture</label>
                                                    <input type="time" name="heure_debut" value="{{ \Carbon\Carbon::parse($jourTravail->heure_debut)->format('H:i') }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Fermeture</label>
                                                    <input type="time" name="heure_fin" value="{{ \Carbon\Carbon::parse($jourTravail->heure_fin)->format('H:i') }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Intervalle des créneaux</label>
                                            <div class="flex items-center space-x-2">
                                                <select name="intervalle_minutes" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="15" {{ $jourTravail->intervalle_minutes == 15 ? 'selected' : '' }}>15 minutes</option>
                                                    <option value="30" {{ $jourTravail->intervalle_minutes == 30 ? 'selected' : '' }}>30 minutes</option>
                                                    <option value="45" {{ $jourTravail->intervalle_minutes == 45 ? 'selected' : '' }}>45 minutes</option>
                                                    <option value="60" {{ $jourTravail->intervalle_minutes == 60 ? 'selected' : '' }}>1 heure</option>
                                                    <option value="90" {{ $jourTravail->intervalle_minutes == 90 ? 'selected' : '' }}>1h30</option>
                                                    <option value="120" {{ $jourTravail->intervalle_minutes == 120 ? 'selected' : '' }}>2 heures</option>
                                                </select>
                                                <button type="button" onclick="updateIntervalle({{ $jourTravail->id }})" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                                                    <i class="fas fa-save mr-1"></i>
                                                    Mettre à jour
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Changer l'intervalle peut affecter les templates existants
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Pause déjeuner (optionnel)</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Début pause</label>
                                                    <input type="time" name="pause_debut" value="{{ $jourTravail->pause_debut ? \Carbon\Carbon::parse($jourTravail->pause_debut)->format('H:i') : '' }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Fin pause</label>
                                                    <input type="time" name="pause_fin" value="{{ $jourTravail->pause_fin ? \Carbon\Carbon::parse($jourTravail->pause_fin)->format('H:i') : '' }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                            <i class="fas fa-save mr-2"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal pour créer une exception -->
    <div id="exceptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- En-tête du modal -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Créer une exception</h3>
                    <button onclick="closeExceptionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Formulaire -->
                <form id="exceptionForm" onsubmit="createException(event)" class="mt-6">
                    <div class="space-y-4">
                        <!-- Date de l'exception -->
                        <div>
                            <label for="date_exception" class="block text-sm font-medium text-gray-700 mb-2">
                                Date de l'exception <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="date_exception" name="date_exception" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Type d'exception -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type d'exception <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required onchange="toggleExceptionFields()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionnez un type</option>
                                <option value="ferme">Centre fermé</option>
                                <option value="capacite_reduite">Capacité réduite</option>
                                <option value="horaires_modifies">Horaires modifiés</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description (optionnel)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Décrivez la raison de cette exception..."></textarea>
                        </div>

                        <!-- Horaires modifiés (visible seulement si type = horaires_modifies) -->
                        <div id="horairesFields" class="hidden space-y-4">
                            <h4 class="text-md font-medium text-gray-900">Horaires modifiés</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-2">
                                        Heure d'ouverture
                                    </label>
                                    <input type="time" id="heure_debut" name="heure_debut"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                        Heure de fermeture
                                    </label>
                                    <input type="time" id="heure_fin" name="heure_fin"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="pause_debut" class="block text-sm font-medium text-gray-700 mb-2">
                                        Début de pause (optionnel)
                                    </label>
                                    <input type="time" id="pause_debut" name="pause_debut"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="pause_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fin de pause (optionnel)
                                    </label>
                                    <input type="time" id="pause_fin" name="pause_fin"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Capacité réduite (visible seulement si type = capacite_reduite) -->
                        <div id="capaciteField" class="hidden">
                            <label for="capacite_reduite" class="block text-sm font-medium text-gray-700 mb-2">
                                Nouvelle capacité (1-20 personnes)
                            </label>
                            <input type="number" id="capacite_reduite" name="capacite_reduite" min="1" max="20"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeExceptionModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            <i class="fas fa-save mr-2"></i>
                            Créer l'exception
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des exceptions existantes -->
    <div id="exceptionsList" class="bg-white rounded-lg shadow mt-6 hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Exceptions existantes</h3>
                <button onclick="refreshExceptionsList()" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Actualiser
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="exceptionsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>
@endsection

<script>
// Fonction pour toggle un jour
function toggleJour(jourTravailId, jourSemaine) {
    if (jourTravailId === null) {
        // Créer un nouveau jour de travail
        fetch(`/creneaux/jours-travail`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                jour_semaine: jourSemaine,
                actif: true,
                heure_debut: '08:00',
                heure_fin: '15:00',
                pause_debut: '12:00',
                pause_fin: '13:00'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    } else {
        // Toggle un jour existant
        fetch(`/creneaux/jours-travail/${jourTravailId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Fonction pour mettre à jour les horaires
function updateHoraires(event, jourTravailId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    fetch(`/creneaux/jours-travail/${jourTravailId}/horaires`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher un message de succès
            showNotification('Horaires mis à jour avec succès', 'success');
        }
    });
}

// Fonction pour tester la base de données
function testDatabase() {
    fetch('/creneaux/test-database', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    });
}

// Fonction pour mettre à jour l'intervalle
function updateIntervalle(jourTravailId) {
    const form = document.getElementById(`form-horaires-${jourTravailId}`);
    const formData = new FormData(form);
    const intervalle = formData.get('intervalle_minutes');
    
    if (!intervalle) {
        alert('Veuillez sélectionner un intervalle');
        return;
    }
    
    // Vérifier les conflits avant de changer
    fetch(`{{ url('creneaux/jours-travail') }}/${jourTravailId}/intervalle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Intervalle mis à jour avec succès !', 'success');
            location.reload();
        } else if (data.warning && data.requires_confirmation) {
            // Afficher une modal de confirmation pour les warnings
            showConfirmationModal(
                'Avertissement',
                data.message,
                data.warnings,
                () => forceMigration(jourTravailId, { intervalle_minutes: parseInt(intervalle) })
            );
        } else if (data.error) {
            // Afficher les erreurs avec option de migration forcée
            showErrorModalWithMigration('Conflits détectés', data.message, data.errors, data.warnings, () => forceMigration(jourTravailId, { intervalle_minutes: parseInt(intervalle) }));
        } else {
            showNotification('Erreur: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la mise à jour de l\'intervalle', 'error');
    });
}

// Fonction pour migrer les templates vers un nouvel intervalle
function migrateIntervalle(jourTravailId, intervalle) {
    const formData = new FormData();
    formData.append('intervalle_minutes', intervalle);
    
    fetch(`{{ url('creneaux/jours-travail') }}/${jourTravailId}/migrate-intervalle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = `Migration terminée !\n\nTemplates migrés: ${data.migrated}`;
            if (data.errors && data.errors.length > 0) {
                message += `\n\nTemplates supprimés:\n${data.errors.join('\n')}`;
            }
            alert(message);
            location.reload();
        } else {
            showNotification('Erreur lors de la migration: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la migration', 'error');
    });
}

// Fonction pour afficher les notifications
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function showConfirmationModal(title, message, warnings, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">${title}</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">${message}</p>
                    ${warnings && warnings.length > 0 ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Avertissements :</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                ${warnings.map(warning => `<li>• ${warning}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Annuler
                    </button>
                    <button onclick="confirmAction()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Continuer
                    </button>
                </div>
            </div>
        </div>
    `;
    
    function confirmAction() {
        onConfirm();
        modal.remove();
    }
    
    document.body.appendChild(modal);
}

function showErrorModal(title, message, errors, warnings) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-red-600">${title}</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">${message}</p>
                    ${errors && errors.length > 0 ? `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            <h4 class="text-sm font-medium text-red-800 mb-2">Erreurs :</h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                ${errors.map(error => `<li>• ${error}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    ${warnings && warnings.length > 0 ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Avertissements :</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                ${warnings.map(warning => `<li>• ${warning}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
                
                <div class="flex justify-end">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function showErrorModalWithMigration(title, message, errors, warnings, onForceMigration) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-red-600">${title}</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">${message}</p>
                    ${errors && errors.length > 0 ? `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            <h4 class="text-sm font-medium text-red-800 mb-2">Erreurs :</h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                ${errors.map(error => `<li>• ${error}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    ${warnings && warnings.length > 0 ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Avertissements :</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                ${warnings.map(warning => `<li>• ${warning}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Vous pouvez forcer la migration pour supprimer les templates incompatibles.
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Annuler
                    </button>
                    <button onclick="forceMigrationAction()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Forcer la migration
                    </button>
                </div>
            </div>
        </div>
    `;
    
    function forceMigrationAction() {
        onForceMigration();
        modal.remove();
    }
    
    document.body.appendChild(modal);
}

function forceMigration(jourTravailId, nouvelleConfig) {
    fetch(`/creneaux/jours-travail/${jourTravailId}/force-migration`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            nouvelle_config: nouvelleConfig
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = 'Migration terminée avec succès !';
            if (data.deleted && data.deleted.length > 0) {
                message += `\n\nTemplates supprimés : ${data.deleted.length}`;
            }
            if (data.migrated && data.migrated.length > 0) {
                message += `\n\nTemplates conservés : ${data.migrated.length}`;
            }
            showNotification(message, 'success');
            location.reload();
        } else {
            showNotification('Erreur lors de la migration : ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la migration', 'error');
    });
}

// Fonctions pour gérer le modal des exceptions
function openExceptionModal() {
    document.getElementById('exceptionModal').classList.remove('hidden');
    document.getElementById('exceptionsList').classList.remove('hidden');
    refreshExceptionsList();
}

function closeExceptionModal() {
    document.getElementById('exceptionModal').classList.add('hidden');
    document.getElementById('exceptionForm').reset();
    toggleExceptionFields();
}

function toggleExceptionFields() {
    const type = document.getElementById('type').value;
    const horairesFields = document.getElementById('horairesFields');
    const capaciteField = document.getElementById('capaciteField');
    
    // Masquer tous les champs conditionnels
    horairesFields.classList.add('hidden');
    capaciteField.classList.add('hidden');
    
    // Afficher les champs selon le type sélectionné
    if (type === 'horaires_modifies') {
        horairesFields.classList.remove('hidden');
    } else if (type === 'capacite_reduite') {
        capaciteField.classList.remove('hidden');
    }
}

function createException(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    // Validation côté client
    if (!data.date_exception || !data.type) {
        showNotification('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    if (data.type === 'horaires_modifies' && (!data.heure_debut || !data.heure_fin)) {
        showNotification('Veuillez spécifier les horaires modifiés', 'error');
        return;
    }
    
    if (data.type === 'capacite_reduite' && !data.capacite_reduite) {
        showNotification('Veuillez spécifier la nouvelle capacité', 'error');
        return;
    }
    
    fetch('/creneaux/exceptions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Exception créée avec succès !', 'success');
            closeExceptionModal();
            refreshExceptionsList();
        } else {
            showNotification('Erreur: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la création de l\'exception', 'error');
    });
}

function refreshExceptionsList() {
    fetch('/creneaux/exceptions/list')
    .then(response => response.json())
    .then(data => {
        const content = document.getElementById('exceptionsContent');
        
        if (data.exceptions && data.exceptions.length > 0) {
            let html = '<div class="space-y-4">';
            
            data.exceptions.forEach(exception => {
                const date = new Date(exception.date_exception).toLocaleDateString('fr-FR');
                const typeLabels = {
                    'ferme': 'Centre fermé',
                    'capacite_reduite': 'Capacité réduite',
                    'horaires_modifies': 'Horaires modifiés'
                };
                
                html += `
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar-times text-red-500"></i>
                                    <span class="font-medium text-gray-900">${date}</span>
                                </div>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-tag"></i>
                                        <span>${typeLabels[exception.type] || exception.type}</span>
                                    </div>
                                    
                                    ${exception.heure_debut && exception.heure_fin ? `
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-clock"></i>
                                            <span>${exception.heure_debut} - ${exception.heure_fin}</span>
                                        </div>
                                    ` : ''}
                                    
                                    ${exception.capacite_reduite ? `
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-users"></i>
                                            <span>Capacité: ${exception.capacite_reduite}</span>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Exception
                                </span>
                                
                                <button onclick="deleteException(${exception.id})" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        ${exception.description ? `
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">${exception.description}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune exception configurée</h3>
                    <p class="text-gray-600">Créez des exceptions pour gérer les jours spéciaux.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors du chargement des exceptions', 'error');
    });
}

function deleteException(exceptionId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette exception ?')) {
        return;
    }
    
    fetch(`/creneaux/exceptions/${exceptionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Exception supprimée avec succès !', 'success');
            refreshExceptionsList();
        } else {
            showNotification('Erreur: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la suppression de l\'exception', 'error');
    });
}
</script>
