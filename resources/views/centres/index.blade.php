@extends('layouts.dashboard')

@section('title', 'Configuration des services & formules')
@section('subtitle', 'Gérez l\'activation des services et formules pour votre centre')

@section('header-actions')
<div class="flex items-center space-x-4">
    <div class="relative">
        <input type="text" id="searchServices" placeholder="Rechercher un service..." 
               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-transparent">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Informations du centre -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
            <div class="flex items-center">
                <i class="fas fa-building text-mayelia-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">{{ $centre->nom }}</h3>
            </div>
            @isAdmin
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('qms.display', $centre) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-tv mr-2"></i>
                    Écran TV
                </a>
                <a href="{{ route('admin.centres.qms.edit', $centre) }}" 
                   class="inline-flex items-center px-4 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-lg transition-colors text-sm">
                    <i class="fas fa-users-cog mr-2"></i>
                    Paramètres QMS
                </a>
            </div>
            @endisAdmin
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
                <i class="fas fa-map-marker-alt mr-2"></i>
                {{ $centre->adresse }}
            </div>
            <div>
                <i class="fas fa-phone mr-2"></i>
                {{ $centre->telephone }}
            </div>
            <div>
                <i class="fas fa-envelope mr-2"></i>
                {{ $centre->email }}
            </div>
            <div>
                <i class="fas fa-city mr-2"></i>
                {{ $centre->ville->nom }}
            </div>
        </div>
    </div>

    <!-- Services avec formules -->
    @if($servicesGlobaux->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="servicesContainer">
            @foreach($servicesGlobaux as $service)
                @php
                    $serviceActive = $servicesActives->contains('id', $service->id);
                    $formulesService = $formulesGlobales->where('service_id', $service->id);
                @endphp
                
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 service-card" data-service-name="{{ strtolower($service->nom) }}">
                    <!-- En-tête de la carte service -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->nom }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ $service->description }}</p>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span><i class="fas fa-clock mr-1"></i>{{ $service->duree_rdv }} min</span>
                                </div>
                            </div>
                            
                            <!-- Toggle principal du service -->
                            <div class="ml-4">
                                @isAdmin
                                <form method="POST" action="{{ route('centres.toggle-service', $service) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="actif" value="{{ $serviceActive ? '0' : '1' }}">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" {{ $serviceActive ? 'checked' : '' }} 
                                               onchange="this.form.submit()">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-mayelia-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-mayelia-600"></div>
                                    </label>
                                </form>
                                @else
                                <div class="px-3 py-1 text-xs font-medium rounded-full {{ $serviceActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $serviceActive ? 'Actif' : 'Inactif' }}
                                </div>
                                @endisAdmin
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formules du service -->
                    <div class="p-6">
                        @if($formulesService->count() > 0)
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Formules disponibles :</h4>
                            <div class="space-y-3">
                                @foreach($formulesService as $formule)
                                    @php
                                        $formuleActive = $formulesActives->contains('id', $formule->id);
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $formule->couleur }}"></div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $formule->nom }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ number_format($formule->prix, 2) }} €</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Toggle de la formule -->
                                        @isAdmin
                                        <form method="POST" action="{{ route('centres.toggle-formule', $formule) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="actif" value="{{ $formuleActive ? '0' : '1' }}">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only peer" {{ $formuleActive ? 'checked' : '' }} 
                                                       onchange="this.form.submit()" {{ !$serviceActive ? 'disabled' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-mayelia-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-mayelia-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                                            </label>
                                        </form>
                                        @else
                                        <div class="px-2 py-1 text-xs font-medium rounded-full {{ $formuleActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $formuleActive ? 'Actif' : 'Inactif' }}
                                        </div>
                                        @endisAdmin
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">Aucune formule disponible pour ce service</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-concierge-bell text-gray-300 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun service disponible</h3>
            <p class="text-gray-600">Contactez l'administrateur général pour ajouter des services.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchServices');
    const serviceCards = document.querySelectorAll('.service-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        serviceCards.forEach(card => {
            const serviceName = card.getAttribute('data-service-name');
            if (serviceName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
