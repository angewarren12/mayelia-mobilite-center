@extends('layouts.dashboard')

@section('title', 'Gestion des créneaux')
@section('subtitle', 'Configurez les créneaux de rendez-vous pour votre centre')

@section('content')
<div class="space-y-6">
    <!-- Onglets de navigation -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('creneaux.index') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.index') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Jours ouvrables
                </a>
                <a href="{{ route('creneaux.templates') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.templates') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Templates
                </a>
                <a href="{{ route('creneaux.exceptions') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.exceptions') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Exceptions
                </a>
                <a href="{{ route('creneaux.calendrier') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.calendrier') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar mr-2"></i>
                    Calendrier
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenu de l'onglet Templates -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Configurez les templates de créneaux pour chaque jour de travail</h3>
                    <p class="text-sm text-gray-600 mt-1">Définissez les créneaux horaires et les formules pour chaque jour ouvrable</p>
                </div>
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('templates.generate-creneaux') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                            <i class="fas fa-magic"></i>
                            <span>Générer créneaux</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="p-6">
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

            <div class="space-y-6">
                @foreach($joursSemaine as $numero => $jour)
                    @php
                        $jourTravail = $joursTravail->where('jour_semaine', $numero)->first();
                        $actif = $jourTravail ? $jourTravail->actif : false;
                        $templatesJour = $templates->where('jour_semaine', $numero);
                        $nbCreneaux = $templatesJour->count();
                    @endphp
                    
                    <div class="border border-gray-200 rounded-lg">
                        <!-- En-tête du jour -->
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $actif ? 'bg-mayelia-100 text-mayelia-600' : 'bg-gray-200 text-gray-500' }}">
                                        <span class="font-semibold">{{ $jour['lettre'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $jour['nom'] }}</h4>
                                        @if($actif && $jourTravail)
                                            <div class="flex items-center space-x-4 text-sm">
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
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    @if($actif)
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                            {{ $nbCreneaux }} créneaux configurés
                                        </span>
                                        <button onclick="toggleDay({{ $numero }})" class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                                            <i class="fas fa-edit"></i>
                                            <span>Édition globale</span>
                                        </button>
                                    @else
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                                            Jour fermé
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($actif)
                            <!-- Contenu du jour -->
                            <div class="p-6">
                                <!-- Onglets des services -->
                                <div class="border-b border-gray-200 mb-6">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                        @foreach($servicesActives as $service)
                                            <button onclick="showServiceTab('{{ $service->id }}', {{ $numero }})" 
                                                    class="service-tab py-2 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                                    data-service="{{ $service->id }}"
                                                    data-jour="{{ $numero }}">
                                                {{ $service->nom }}
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>
                                
                                <!-- Contenu des onglets -->
                                @foreach($servicesActives as $service)
                                    <div id="service-{{ $service->id }}-{{ $numero }}" class="service-content {{ $loop->first ? '' : 'hidden' }}">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Tranche horaire
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Formules liées
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @php
                                                        $heureDebut = \Carbon\Carbon::parse($jourTravail->heure_debut);
                                                        $heureFin = \Carbon\Carbon::parse($jourTravail->heure_fin);
                                                        $pauseDebut = $jourTravail->pause_debut ? \Carbon\Carbon::parse($jourTravail->pause_debut) : null;
                                                        $pauseFin = $jourTravail->pause_fin ? \Carbon\Carbon::parse($jourTravail->pause_fin) : null;
                                                    @endphp
                                                    
                                                    @for($heure = $heureDebut->copy(); $heure->lt($heureFin); $heure->addHour())
                                                        @php
                                                            $heureSuivante = $heure->copy()->addHour();
                                                            $estPause = $pauseDebut && $pauseFin && $heure->between($pauseDebut, $pauseFin->subHour());
                                                            $trancheHoraire = $heure->format('H:i') . ':00 - ' . $heureSuivante->format('H:i') . ':00';
                                                            $templatesTranche = $templatesJour->where('service_id', $service->id)->where('tranche_horaire', $trancheHoraire);
                                                        @endphp
                                                        
                                                        <tr class="{{ $estPause ? 'bg-orange-50' : '' }}">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                                                    {{ $trancheHoraire }}
                                                                    @if($estPause)
                                                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                            Pause déjeuner
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                @if($estPause)
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
                                                                                {{ $template->formule->nom }} (capacité: {{ $template->capacite }})
                                                                            </span>
                                                                        @endforeach
                                                                        
                                                                        @if($templatesTranche->isEmpty())
                                                                            <button onclick="addTemplate('{{ $service->id }}', {{ $numero }}, '{{ $trancheHoraire }}')" 
                                                                                    class="text-mayelia-600 hover:text-mayelia-800 text-sm">
                                                                                <i class="fas fa-plus mr-1"></i>
                                                                                Ajouter une formule
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function toggleDay(jourSemaine) {
    // Fonction pour l'édition globale d'un jour
    console.log('Édition globale pour le jour:', jourSemaine);
    // TODO: Implémenter l'édition globale
}

function showServiceTab(serviceId, jourSemaine) {
    // Masquer tous les contenus de services pour ce jour
    document.querySelectorAll(`[data-jour="${jourSemaine}"]`).forEach(tab => {
        tab.classList.remove('border-mayelia-500', 'text-mayelia-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.querySelectorAll(`#service-${serviceId}-${jourSemaine}`).forEach(content => {
        content.classList.add('hidden');
    });
    
    // Afficher le contenu sélectionné
    const selectedTab = document.querySelector(`[data-service="${serviceId}"][data-jour="${jourSemaine}"]`);
    const selectedContent = document.getElementById(`service-${serviceId}-${jourSemaine}`);
    
    if (selectedTab && selectedContent) {
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
        selectedTab.classList.add('border-mayelia-500', 'text-mayelia-600');
        selectedContent.classList.remove('hidden');
    }
}

function addTemplate(serviceId, jourSemaine, trancheHoraire) {
    // Fonction pour ajouter un template
    console.log('Ajouter template:', serviceId, jourSemaine, trancheHoraire);
    // TODO: Implémenter l'ajout de template
}
</script>
@endsection


