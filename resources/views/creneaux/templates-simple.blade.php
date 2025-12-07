@extends('creneaux.layout')

@section('title', 'Templates de Créneaux')
@section('subtitle', 'Configurez les templates de créneaux pour chaque service et formule')

@section('creneaux_content')
<!-- Données des formules pour JavaScript -->
<script>
    window.formulesData = @json($formulesData);
    @php
        $authService = app(\App\Services\AuthService::class);
        $isAdmin = $authService->isAdmin();
        $canDeleteTemplate = $authService->hasPermission('creneaux', 'templates.delete');
        $canUpdateTemplate = $authService->hasPermission('creneaux', 'templates.update');
    @endphp
    window.isAdmin = @json($isAdmin);
    window.canDeleteTemplate = @json($canDeleteTemplate);
    window.canUpdateTemplate = @json($canUpdateTemplate);
</script>

<!-- Contenu de l'onglet Templates -->
<div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Configuration des templates de créneaux</h3>
                    <p class="text-sm text-gray-600 mt-1">Définissez les créneaux horaires et les formules pour chaque service</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="toggleTemplatesVisibility()" 
                            id="toggleTemplatesBtn"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-eye-slash"></i>
                        <span>Masquer les templates</span>
                    </button>
                    @userCan('creneaux', 'templates.create')
                    <button onclick="openBulkTemplateModal()" class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Créer en masse</span>
                    </button>
                    <form method="POST" action="{{ route('templates.generate-creneaux') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                            <i class="fas fa-magic"></i>
                            <span>Générer créneaux</span>
                        </button>
                    </form>
                    @enduserCan
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if($servicesActives->count() > 0)
                <div id="templatesContainer" class="space-y-6">
                    @foreach($servicesActives as $service)
                        <div class="border border-gray-200 rounded-lg">
                            <!-- En-tête du service -->
                            <div class="p-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-lg bg-mayelia-100 flex items-center justify-center">
                                            <i class="fas fa-concierge-bell text-mayelia-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">{{ $service->nom }}</h4>
                                            <p class="text-sm text-gray-600">{{ $service->description }}</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                                                <span><i class="fas fa-clock mr-1"></i>{{ $service->duree_rdv }} min</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                                            {{ $service->formules->count() }} formules disponibles
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenu du service -->
                            <div class="p-6">
                                <!-- Onglets des jours -->
                                <div class="border-b border-gray-200 mb-6">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
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
                                            @endphp
                                            
                                            <button onclick="showDayTab('{{ $service->id }}', {{ $numero }})" 
                                                    class="day-tab py-3 px-4 border-b-2 font-medium text-sm transition-all duration-200 {{ $loop->first ? 'border-mayelia-500 text-mayelia-600 bg-mayelia-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} {{ !$actif ? 'opacity-50' : '' }}"
                                                    data-service="{{ $service->id }}"
                                                    data-jour="{{ $numero }}"
                                                    {{ !$actif ? 'disabled' : '' }}>
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $loop->first ? 'bg-mayelia-500 text-white' : ($actif ? 'bg-mayelia-100 text-mayelia-600' : 'bg-gray-200 text-gray-500') }}">
                                                        {{ $jour['lettre'] }}
                                                    </div>
                                                    <div class="flex flex-col items-start">
                                                        <span class="font-medium">{{ $jour['nom'] }}</span>
                                                        @if($loop->first)
                                                            <span class="text-xs text-mayelia-600 font-semibold">ACTUEL</span>
                                                        @elseif(!$actif)
                                                            <span class="text-xs text-gray-400">(Fermé)</span>
                                                        @else
                                                            <span class="text-xs text-gray-500">Cliquez pour voir</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>
                                
                                <!-- Contenu des onglets jours -->
                                @foreach($joursSemaine as $numero => $jour)
                                    @php
                                        $jourTravail = $joursTravail->where('jour_semaine', $numero)->first();
                                        $actif = $jourTravail ? $jourTravail->actif : false;
                                        $templatesJour = $templates->where('jour_semaine', $numero)->where('service_id', $service->id);
                                    @endphp
                                    
                                    <div id="day-{{ $service->id }}-{{ $numero }}" class="day-content {{ $loop->first ? '' : 'hidden' }}">
                                        @if($actif)
                                            <div class="mb-4 p-4 bg-mayelia-50 rounded-lg border-l-4 border-mayelia-500">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4 text-sm">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-mayelia-100 text-mayelia-800">
                                                                <i class="fas fa-calendar-day mr-1"></i>
                                                                {{ $jour['nom'] }}
                                                            </span>
                                                            @if($loop->first)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                                    <i class="fas fa-eye mr-1"></i>
                                                                    ACTUEL
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <span class="text-mayelia-600">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Horaires: {{ $jourTravail->heure_debut }} - {{ $jourTravail->heure_fin }}
                                                        </span>
                                                        @if($jourTravail->pause_debut && $jourTravail->pause_fin)
                                                            <span class="text-red-600">
                                                                <i class="fas fa-pause mr-1"></i>
                                                                Pause: {{ $jourTravail->pause_debut }} - {{ $jourTravail->pause_fin }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="relative">
                                                        <button onclick="showTrancheSelector('{{ $service->id }}', {{ $numero }})" 
                                                                class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-3 py-1 rounded text-sm">
                                                            <i class="fas fa-plus mr-1"></i>
                                                            Ajouter template
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Tableau des créneaux -->
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Tranche horaire
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Formules configurées
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Actions
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @php
                                                            // Utiliser le service pour générer les tranches avec l'intervalle configuré
                                                            $trancheService = app(\App\Services\TrancheHoraireService::class);
                                                            $tranches = $trancheService->generateTranchesForDay($jourTravail);
                                                            
                                                            // Séparer les tranches normales et de pause
                                                            $tranchesNormales = collect($tranches)->where('est_pause', false);
                                                            $tranchesPause = collect($tranches)->where('est_pause', true);
                                                            
                                                            // Créer un tableau avec toutes les tranches
                                                            $toutesTranches = collect();
                                                            
                                                            // Ajouter toutes les tranches normales
                                                            foreach($tranchesNormales as $tranche) {
                                                                $toutesTranches->push($tranche);
                                                            }
                                                            
                                                            // Ajouter la pause fusionnée si elle existe
                                                            if($tranchesPause->isNotEmpty()) {
                                                                $pauseDebut = $tranchesPause->first()['tranche_horaire'] ?? null;
                                                                $pauseFin = $tranchesPause->last()['tranche_horaire'] ?? null;
                                                                $pauseDuree = $tranchesPause->sum('duree_minutes');
                                                                
                                                                // Extraire juste les heures pour l'affichage
                                                                $pauseDebutHeure = substr($pauseDebut, 0, 5); // "12:00"
                                                                $pauseFinHeure = substr($pauseFin, 0, 5); // "13:00"
                                                                
                                                                $toutesTranches->push([
                                                                    'tranche_horaire' => $pauseDebutHeure . ' - ' . $pauseFinHeure,
                                                                    'est_pause' => true,
                                                                    'duree_minutes' => $pauseDuree,
                                                                    'pause_debut' => $pauseDebut,
                                                                    'pause_fin' => $pauseFin,
                                                                    'heure_debut' => $pauseDebut // Pour le tri
                                                                ]);
                                                            }
                                                            
                                                            // Trier chronologiquement par heure de début
                                                            $tranchesOrdonnees = $toutesTranches->sortBy(function($tranche) {
                                                                if($tranche['est_pause']) {
                                                                    return $tranche['pause_debut'];
                                                                } else {
                                                                    // Extraire l'heure de début de la tranche normale
                                                                    return substr($tranche['tranche_horaire'], 0, 8); // "08:30:00"
                                                                }
                                                            });
                                                        @endphp
                                                        
                                                        @foreach($tranchesOrdonnees as $tranche)
                                                            @php
                                                                $templatesTranche = $tranche['est_pause'] ? collect() : $templatesJour->where('tranche_horaire', $tranche['tranche_horaire']);
                                                            @endphp
                                                            
                                                            <tr class="{{ $tranche['est_pause'] ? 'bg-orange-50 border-t-2 border-orange-200' : '' }}">
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    <div class="flex items-center">
                                                                        @if($tranche['est_pause'])
                                                                            <i class="fas fa-pause text-orange-500 mr-2"></i>
                                                                            <span class="font-medium text-orange-700">{{ $tranche['tranche_horaire'] }}</span>
                                                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                                                <i class="fas fa-coffee mr-1"></i>
                                                                                Pause
                                                                            </span>
                                                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                                                {{ $tranche['duree_minutes'] }}min
                                                                            </span>
                                                                        @else
                                                                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                                                                            {{ $tranche['tranche_horaire'] }}
                                                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-mayelia-100 text-mayelia-800">
                                                                                {{ $tranche['duree_minutes'] }}min
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    @if($tranche['est_pause'])
                                                                        <span class="text-sm text-gray-500 italic">Pause - Aucun service</span>
                                                                    @else
                                                                        <div class="flex flex-wrap gap-2">
                                                                            @foreach($templatesTranche as $template)
                                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                                                      style="background-color: {{ $template->formule->couleur }}20; color: {{ $template->formule->couleur }};">
                                                                                    @if($template->formule->nom === 'Standard')
                                                                                        <i class="fas fa-star mr-1"></i>
                                                                                    @elseif($template->formule->nom === 'VIP')
                                                                                        <i class="fas fa-crown mr-1"></i>
                                                                                    @elseif($template->formule->nom === 'VVIP')
                                                                                        <i class="fas fa-gem mr-1"></i>
                                                                                    @endif
                                                                                    {{ $template->formule->nom }} ({{ $template->capacite }})
                                                                                </span>
                                                                            @endforeach
                                                                            
                                                                            @if($templatesTranche->isEmpty())
                                                                                <span class="text-sm text-gray-400 italic">Aucune formule configurée</span>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    @if(!$tranche['est_pause'])
                                                                        @if($templatesTranche->isNotEmpty())
                                                                            <div class="flex space-x-2">
                                                                                @userCan('creneaux', 'templates.update')
                                                                                <button onclick="modifyTemplate('{{ $service->id }}', {{ $numero }}, '{{ $tranche['tranche_horaire'] }}')" 
                                                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-mayelia-700 bg-mayelia-100 border border-mayelia-300 rounded-md hover:bg-mayelia-200 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                                                                    <i class="fas fa-edit mr-1"></i>
                                                                                    Modifier
                                                                                </button>
                                                                                @enduserCan
                                                                                @userCan('creneaux', 'templates.create')
                                                                                <button onclick="addTemplate('{{ $service->id }}', {{ $numero }}, '{{ $tranche['tranche_horaire'] }}')" 
                                                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border border-green-300 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                                                    <i class="fas fa-plus mr-1"></i>
                                                                                    Ajouter
                                                                                </button>
                                                                                @enduserCan
                                                                            </div>
                                                                        @else
                                                                            @userCan('creneaux', 'templates.create')
                                                                            <button onclick="addTemplate('{{ $service->id }}', {{ $numero }}, '{{ $tranche['tranche_horaire'] }}')" 
                                                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-mayelia-700 bg-mayelia-100 border border-mayelia-300 rounded-md hover:bg-mayelia-200 focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                                                                <i class="fas fa-plus mr-1"></i>
                                                                                Ajouter
                                                                            </button>
                                                                            @enduserCan
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                                <p>Ce jour est fermé. Activez-le d'abord dans "Jours ouvrables".</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-concierge-bell text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun service activé</h3>
                    <p class="text-gray-600">Activez d'abord des services dans "Gestion du centre".</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour ajouter un template -->
<div id="templateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un template</h3>
                <button onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Affichage de la tranche horaire sélectionnée -->
            <div id="selectedTrancheInfo" class="mb-4 p-3 bg-mayelia-50 rounded-lg border border-mayelia-200" style="display: none;">
                <div class="flex items-center">
                    <i class="fas fa-clock text-mayelia-600 mr-2"></i>
                    <span class="text-sm font-medium text-mayelia-900">Tranche horaire sélectionnée :</span>
                    <span id="selectedTrancheText" class="ml-2 text-sm text-mayelia-700 font-semibold"></span>
                </div>
            </div>
            
            <!-- Section des templates existants -->
            <div id="existingTemplates" class="mb-4 p-4 bg-gray-50 rounded-lg" style="display: none;">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Templates existants :</h4>
                <div id="templatesList" class="space-y-2">
                    <!-- Les templates existants seront chargés ici -->
                </div>
            </div>
            
            <form id="templateForm">
                <input type="hidden" id="template_service_id" name="service_id">
                <input type="hidden" id="template_jour_semaine" name="jour_semaine">
                <input type="hidden" id="template_tranche_horaire" name="tranche_horaire">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formule</label>
                    <select id="template_formule_id" name="formule_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                        <option value="">Sélectionnez une formule</option>
                        <!-- Les options seront chargées dynamiquement via JavaScript -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capacité</label>
                    <input type="number" id="template_capacite" name="capacite" min="1" value="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                </div>
                
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="closeTemplateModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" id="templateSubmitBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-mayelia-600 border border-transparent rounded-md hover:bg-mayelia-700">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour création en masse de templates -->
<div id="bulkTemplateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Créer des templates en masse</h3>
                <button onclick="closeBulkTemplateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="bulkTemplateForm">
                <div class="space-y-4">
                    <!-- Sélection des jours -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Jours de la semaine</label>
                            <div class="flex space-x-2">
                                <button type="button" onclick="selectAllDays()" class="text-xs text-mayelia-600 hover:text-mayelia-800">Tout sélectionner</button>
                                <button type="button" onclick="deselectAllDays()" class="text-xs text-gray-600 hover:text-gray-800">Tout désélectionner</button>
                            </div>
                        </div>
                        <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            @php
                                $joursLabels = ['1' => 'Lundi', '2' => 'Mardi', '3' => 'Mercredi', '4' => 'Jeudi', '5' => 'Vendredi', '6' => 'Samedi', '7' => 'Dimanche'];
                                $joursActifs = $joursTravail->where('actif', true)->pluck('jour_semaine')->toArray();
                            @endphp
                            @foreach($joursLabels as $value => $label)
                                @if(in_array($value, $joursActifs))
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="jours_semaine[]" value="{{ $value }}" class="day-checkbox rounded border-gray-300 text-mayelia-600 focus:ring-mayelia-500">
                                        <span class="text-sm font-medium">{{ $label }}</span>
                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Actif</span>
                                    </label>
                                @endif
                            @endforeach
                            @if(empty($joursActifs))
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">Aucun jour de travail configuré</p>
                                    <p class="text-xs text-gray-400">Configurez d'abord vos jours de travail</p>
                                </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Sélectionnez un ou plusieurs jours</p>
                    </div>
                    
                    <!-- Sélection du service -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                        <select id="bulk_service_id" name="service_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500" onchange="loadFormulesForBulk()">
                            <option value="">Sélectionnez un service</option>
                            @foreach($servicesActives as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Sélection des formules -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Formules à appliquer</label>
                        <div id="bulk_formules_container" class="space-y-2 max-h-40 overflow-y-auto">
                            <p class="text-sm text-gray-500">Sélectionnez d'abord un service</p>
                        </div>
                    </div>
                    
                    <!-- Capacité par défaut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Capacité par défaut</label>
                        <input type="number" id="bulk_capacite" name="capacite" value="1" min="1" max="20" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeBulkTemplateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-mayelia-600 hover:bg-mayelia-700 rounded-md">
                        <i class="fas fa-plus mr-1"></i>
                        Créer les templates
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDayTab(serviceId, jourSemaine) {
    // Masquer tous les contenus de jours pour ce service
    document.querySelectorAll(`[data-service="${serviceId}"]`).forEach(tab => {
        // Retirer les classes actives
        tab.classList.remove('border-mayelia-500', 'text-mayelia-600', 'bg-mayelia-50');
        tab.classList.add('border-transparent', 'text-gray-500');
        
        // Mettre à jour l'indicateur "ACTUEL"
        const currentIndicator = tab.querySelector('.text-mayelia-600.font-semibold');
        const clickIndicator = tab.querySelector('.text-gray-500');
        if (currentIndicator) {
            currentIndicator.textContent = 'Cliquez pour voir';
            currentIndicator.className = 'text-xs text-gray-500';
        }
        if (clickIndicator) {
            clickIndicator.textContent = 'Cliquez pour voir';
        }
        
        // Mettre à jour le cercle de l'onglet
        const circle = tab.querySelector('.w-7.h-7');
        if (circle) {
            circle.classList.remove('bg-mayelia-500', 'text-white');
            circle.classList.add('bg-mayelia-100', 'text-mayelia-600');
        }
    });
    
    // Masquer tous les contenus et retirer l'indicateur "ACTUEL" du contenu
    document.querySelectorAll(`[id^="day-${serviceId}-"]`).forEach(content => {
        content.classList.add('hidden');
        // Retirer l'indicateur "ACTUEL" du contenu
        const currentContentIndicator = content.querySelector('.bg-green-100.text-green-800');
        if (currentContentIndicator) {
            currentContentIndicator.style.display = 'none';
        }
    });
    
    // Afficher le contenu sélectionné
    const selectedTab = document.querySelector(`[data-service="${serviceId}"][data-jour="${jourSemaine}"]`);
    const selectedContent = document.getElementById(`day-${serviceId}-${jourSemaine}`);
    
    if (selectedTab && selectedContent) {
        // Appliquer les classes actives
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
        selectedTab.classList.add('border-mayelia-500', 'text-mayelia-600', 'bg-mayelia-50');
        
        // Mettre à jour l'indicateur "ACTUEL"
        const currentIndicator = selectedTab.querySelector('.text-gray-500');
        if (currentIndicator) {
            currentIndicator.textContent = 'ACTUEL';
            currentIndicator.className = 'text-xs text-mayelia-600 font-semibold';
        }
        
        // Mettre à jour le cercle de l'onglet actif
        const circle = selectedTab.querySelector('.w-7.h-7');
        if (circle) {
            circle.classList.remove('bg-mayelia-100', 'text-mayelia-600');
            circle.classList.add('bg-mayelia-500', 'text-white');
        }
        
        // Afficher l'indicateur "ACTUEL" dans le contenu
        const currentContentIndicator = selectedContent.querySelector('.bg-green-100.text-green-800');
        if (currentContentIndicator) {
            currentContentIndicator.style.display = 'inline-flex';
        }
        
        selectedContent.classList.remove('hidden');
    }
}

function showTrancheSelector(serviceId, jourSemaine) {
    // Récupérer toutes les tranches horaires disponibles pour ce jour
    const dayContent = document.getElementById(`day-${serviceId}-${jourSemaine}`);
    if (!dayContent) {
        alert('Impossible de trouver les tranches horaires pour ce jour');
        return;
    }
    
    // Extraire les tranches horaires depuis le tableau
    const tranches = [];
    const rows = dayContent.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const trancheCell = row.querySelector('td:first-child');
        if (trancheCell) {
            const trancheText = trancheCell.textContent.trim();
            // Ignorer les pauses
            if (!trancheText.includes('Pause') && trancheText.includes(':')) {
                // Extraire la tranche horaire (format: "08:00:00 - 09:00:00")
                const match = trancheText.match(/(\d{2}:\d{2}:\d{2})\s*-\s*(\d{2}:\d{2}:\d{2})/);
                if (match) {
                    tranches.push(match[0]); // "08:00:00 - 09:00:00"
                }
            }
        }
    });
    
    if (tranches.length === 0) {
        alert('Aucune tranche horaire disponible pour ce jour');
        return;
    }
    
    // Si une seule tranche, l'utiliser directement
    if (tranches.length === 1) {
        addTemplate(serviceId, jourSemaine, tranches[0]);
        return;
    }
    
    // Créer un modal de sélection
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sélectionnez une tranche horaire</h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    ${tranches.map((t, i) => `
                        <button onclick="selectTrancheAndClose('${serviceId}', ${jourSemaine}, '${t}')" 
                                class="w-full text-left px-4 py-2 bg-mayelia-50 hover:bg-mayelia-100 border border-mayelia-200 rounded-md text-sm">
                            ${t}
                        </button>
                    `).join('')}
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeTrancheSelector()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Fonction pour sélectionner et fermer
    window.selectTrancheAndClose = function(sId, jSemaine, tranche) {
        closeTrancheSelector();
        addTemplate(sId, jSemaine, tranche);
    };
    
    // Fonction pour fermer le modal
    window.closeTrancheSelector = function() {
        if (modal.parentNode) {
            document.body.removeChild(modal);
        }
        delete window.selectTrancheAndClose;
        delete window.closeTrancheSelector;
    };
}

function addTemplate(serviceId, jourSemaine, trancheHoraire = null) {
    console.log('=== DÉBUT addTemplate ===');
    console.log('serviceId:', serviceId);
    console.log('jourSemaine:', jourSemaine);
    console.log('trancheHoraire:', trancheHoraire);
    
    // Vérifier que trancheHoraire est fourni
    if (!trancheHoraire) {
        alert('Veuillez sélectionner une tranche horaire');
        return;
    }
    
    document.getElementById('template_service_id').value = serviceId;
    document.getElementById('template_jour_semaine').value = jourSemaine;
    document.getElementById('template_tranche_horaire').value = trancheHoraire;
    
    // Afficher la tranche horaire sélectionnée
    document.getElementById('selectedTrancheText').textContent = trancheHoraire;
    document.getElementById('selectedTrancheInfo').style.display = 'block';
    
    // Charger les formules du service sélectionné
    loadFormulesForService(serviceId);
    
    // Masquer la section des templates existants pour l'ajout
    document.getElementById('existingTemplates').style.display = 'none';
    
    // Changer le titre du modal
    document.querySelector('#templateModal h3').textContent = 'Ajouter un template';
    
    // Changer le texte du bouton
    document.getElementById('templateSubmitBtn').textContent = 'Ajouter';
    document.getElementById('templateSubmitBtn').className = 'px-4 py-2 text-sm font-medium text-white bg-mayelia-600 border border-transparent rounded-md hover:bg-mayelia-700';
    
    // Réinitialiser le formulaire
    document.getElementById('template_formule_id').value = '';
    document.getElementById('template_capacite').value = '1';
    
    document.getElementById('templateModal').classList.remove('hidden');
    console.log('Modal d\'ajout ouvert');
}

function modifyTemplate(serviceId, jourSemaine, trancheHoraire = null) {
    console.log('=== DÉBUT modifyTemplate ===');
    console.log('serviceId:', serviceId);
    console.log('jourSemaine:', jourSemaine);
    console.log('trancheHoraire:', trancheHoraire);
    
    document.getElementById('template_service_id').value = serviceId;
    document.getElementById('template_jour_semaine').value = jourSemaine;
    document.getElementById('template_tranche_horaire').value = trancheHoraire || '';
    
    // Afficher la tranche horaire sélectionnée
    if (trancheHoraire) {
        document.getElementById('selectedTrancheText').textContent = trancheHoraire;
        document.getElementById('selectedTrancheInfo').style.display = 'block';
    } else {
        document.getElementById('selectedTrancheInfo').style.display = 'none';
    }
    
    // Charger les formules du service sélectionné
    loadFormulesForService(serviceId);
    
    // Charger les templates existants pour cette tranche
    loadExistingTemplates(serviceId, jourSemaine, trancheHoraire);
    
    // Changer le titre du modal
    document.querySelector('#templateModal h3').textContent = 'Modifier les templates';
    
    // Changer le texte et la couleur du bouton
    document.getElementById('templateSubmitBtn').textContent = 'Ajouter un nouveau';
    document.getElementById('templateSubmitBtn').className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700';
    
    // Réinitialiser le formulaire
    document.getElementById('template_formule_id').value = '';
    document.getElementById('template_capacite').value = '1';
    
    document.getElementById('templateModal').classList.remove('hidden');
    console.log('Modal de modification ouvert');
}

function loadExistingTemplates(serviceId, jourSemaine, trancheHoraire) {
    console.log('=== DÉBUT loadExistingTemplates ===');
    
    const params = new URLSearchParams({
        service_id: serviceId,
        jour_semaine: jourSemaine,
        tranche_horaire: trancheHoraire
    });
    
    fetch(`{{ route("templates.for-tranche") }}?${params}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Templates existants chargés:', data);
        
        const existingTemplatesDiv = document.getElementById('existingTemplates');
        const templatesListDiv = document.getElementById('templatesList');
        
        if (data.success && data.templates && data.templates.length > 0) {
            // Afficher la section des templates existants
            existingTemplatesDiv.style.display = 'block';
            
            // Générer le HTML pour chaque template
            templatesListDiv.innerHTML = data.templates.map(template => `
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: ${template.couleur}20; color: ${template.couleur};">
                            <i class="fas fa-star mr-1"></i>
                            ${template.formule_nom}
                        </span>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-users"></i>
                            <span>Capacité: ${template.capacite}</span>
                        </div>
                    </div>
                    ${window.canDeleteTemplate ? `
                    <button onclick="deleteTemplate(${template.id})" 
                            class="inline-flex items-center px-2 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors">
                        <i class="fas fa-trash mr-1"></i>
                        Supprimer
                    </button>
                    ` : ''}
                </div>
            `).join('');
        } else {
            // Masquer la section des templates existants
            existingTemplatesDiv.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des templates existants:', error);
        document.getElementById('existingTemplates').style.display = 'none';
    });
}

function deleteTemplate(templateId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce template ?')) {
        fetch(`/templates/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Template supprimé avec succès', 'success');
                // Recharger les templates existants
                const serviceId = document.getElementById('template_service_id').value;
                const jourSemaine = document.getElementById('template_jour_semaine').value;
                const trancheHoraire = document.getElementById('template_tranche_horaire').value;
                loadExistingTemplates(serviceId, jourSemaine, trancheHoraire);
                // Recharger la page pour mettre à jour l'affichage principal
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur lors de la suppression: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression du template', 'error');
        });
    }
}

function loadFormulesForService(serviceId) {
    console.log('=== DÉBUT loadFormulesForService ===');
    console.log('serviceId:', serviceId);
    console.log('formulesData:', window.formulesData);
    
    const formuleSelect = document.getElementById('template_formule_id');
    
    // Vider les options existantes
    formuleSelect.innerHTML = '<option value="">Sélectionnez une formule</option>';
    
    // Récupérer les formules du service
    const formules = window.formulesData[serviceId] || [];
    console.log('Formules trouvées pour ce service:', formules);
    
    // Ajouter les options
    formules.forEach(formule => {
        console.log('Ajout de la formule:', formule);
        const option = document.createElement('option');
        option.value = formule.id;
        option.textContent = `${formule.nom} - ${new Intl.NumberFormat('fr-FR').format(formule.prix)} FCFA`;
        option.style.color = formule.couleur;
        formuleSelect.appendChild(option);
    });
    
    // Si aucune formule disponible
    if (formules.length === 0) {
        console.log('Aucune formule disponible pour ce service');
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Aucune formule disponible pour ce service';
        option.disabled = true;
        formuleSelect.appendChild(option);
    }
    
    console.log('=== FIN loadFormulesForService ===');
}

function closeTemplateModal() {
    document.getElementById('templateModal').classList.add('hidden');
}

// Fonctions pour le modal de création en masse
function openBulkTemplateModal() {
    document.getElementById('bulkTemplateModal').classList.remove('hidden');
}

function closeBulkTemplateModal() {
    document.getElementById('bulkTemplateModal').classList.add('hidden');
}

function loadFormulesForBulk() {
    const serviceId = document.getElementById('bulk_service_id').value;
    const container = document.getElementById('bulk_formules_container');
    
    if (!serviceId) {
        container.innerHTML = '<p class="text-sm text-gray-500">Sélectionnez d\'abord un service</p>';
        return;
    }
    
    const formules = window.formulesData[serviceId] || [];
    
    if (formules.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500">Aucune formule disponible pour ce service</p>';
        return;
    }
    
    container.innerHTML = formules.map(formule => `
        <label class="flex items-center space-x-3 p-2 border border-gray-200 rounded-lg hover:bg-gray-50">
            <input type="checkbox" name="formule_ids[]" value="${formule.id}" class="rounded border-gray-300 text-mayelia-600 focus:ring-mayelia-500">
            <div class="flex-1">
                <span class="text-sm font-medium" style="color: ${formule.couleur}">${formule.nom}</span>
                <span class="text-xs text-gray-500 ml-2">${new Intl.NumberFormat('fr-FR').format(formule.prix)} FCFA</span>
            </div>
        </label>
    `).join('');
}

function selectAllDays() {
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllDays() {
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.checked = false;
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

document.getElementById('templateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('=== DÉBUT submit template ===');
    
    const formData = new FormData(this);
    const formuleId = formData.get('formule_id');
    const serviceId = formData.get('service_id');
    const jourSemaine = formData.get('jour_semaine');
    const trancheHoraire = formData.get('tranche_horaire');
    const capacite = formData.get('capacite');
    
    console.log('Données du formulaire:');
    console.log('- serviceId:', serviceId);
    console.log('- formuleId:', formuleId);
    console.log('- jourSemaine:', jourSemaine);
    console.log('- trancheHoraire:', trancheHoraire);
    console.log('- capacite:', capacite);
    
    // Validation côté client
    if (!formuleId) {
        console.log('Erreur: Aucune formule sélectionnée');
        alert('Veuillez sélectionner une formule');
        return;
    }
    
    if (!trancheHoraire) {
        console.log('Erreur: Aucune tranche horaire sélectionnée');
        alert('Veuillez sélectionner une tranche horaire');
        return;
    }
    
    if (!capacite || capacite < 1) {
        console.log('Erreur: Capacité invalide');
        alert('La capacité doit être d\'au moins 1');
        return;
    }
    
    console.log('Envoi de la requête...');
    
    fetch('{{ route("templates.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Réponse reçue:', response.status, response.statusText);
        return response.json();
    })
    .then(data => {
        console.log('Données de la réponse:', data);
        if (data.success) {
            showSuccessToast(data.message || 'Template ajouté avec succès !');
            closeTemplateModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            console.log('Erreur dans la réponse:', data);
            showErrorToast(data.message || 'Erreur inconnue');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la requête:', error);
        showErrorToast('Erreur lors de l\'ajout du template');
    });
    
    console.log('=== FIN submit template ===');
});

// Gestion du formulaire de création en masse
document.getElementById('bulkTemplateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('=== DÉBUT submit bulk template ===');
    
    const formData = new FormData(this);
    const joursSemaine = formData.getAll('jours_semaine[]');
    const serviceId = formData.get('service_id');
    const capacite = formData.get('capacite');
    const formuleIds = formData.getAll('formule_ids[]');
    
    console.log('Données du formulaire bulk:');
    console.log('- joursSemaine:', joursSemaine);
    console.log('- serviceId:', serviceId);
    console.log('- capacite:', capacite);
    console.log('- formuleIds:', formuleIds);
    
    // Validation côté client
    if (joursSemaine.length === 0) {
        alert('Veuillez sélectionner au moins un jour de la semaine');
        return;
    }
    
    if (!serviceId) {
        alert('Veuillez sélectionner un service');
        return;
    }
    
    if (formuleIds.length === 0) {
        alert('Veuillez sélectionner au moins une formule');
        return;
    }
    
    if (!capacite || capacite < 1) {
        alert('La capacité doit être d\'au moins 1');
        return;
    }
    
    console.log('Envoi de la requête bulk...');
    
    fetch('{{ route("templates.store-bulk") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            jours_semaine: joursSemaine.map(jour => parseInt(jour)),
            service_id: parseInt(serviceId),
            formule_ids: formuleIds.map(id => parseInt(id)),
            capacite: parseInt(capacite)
        })
    })
    .then(response => {
        console.log('Réponse reçue:', response.status, response.statusText);
        return response.json();
    })
    .then(data => {
        console.log('Données de la réponse bulk:', data);
        if (data.success) {
            showNotification(`Templates créés avec succès ! ${data.created} templates créés.`, 'success');
            closeBulkTemplateModal();
            location.reload();
        } else {
            console.log('Erreur dans la réponse bulk:', data);
            showNotification('Erreur: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Erreur lors de la requête bulk:', error);
        showNotification('Erreur lors de la création des templates', 'error');
    });
    
    console.log('=== FIN submit bulk template ===');
});

// Fonction pour masquer/afficher les templates
function toggleTemplatesVisibility() {
    const container = document.getElementById('templatesContainer');
    const button = document.getElementById('toggleTemplatesBtn');
    const icon = button.querySelector('i');
    const text = button.querySelector('span');
    
    if (container.style.display === 'none') {
        // Afficher les templates
        container.style.display = 'block';
        icon.className = 'fas fa-eye-slash';
        text.textContent = 'Masquer les templates';
        button.className = 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2';
    } else {
        // Masquer les templates
        container.style.display = 'none';
        icon.className = 'fas fa-eye';
        text.textContent = 'Afficher les templates';
        button.className = 'bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2';
    }
}
</script>
@endsection
