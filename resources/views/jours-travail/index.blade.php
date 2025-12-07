@extends('layouts.dashboard')

@section('title', 'Configuration des jours de travail')
@section('subtitle', 'Configurez les horaires de travail pour chaque jour de la semaine')

@section('header-actions')
<a href="{{ route('jours-travail.create') }}" class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
    <i class="fas fa-plus"></i>
    <span>Ajouter un jour</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Informations du centre -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-building text-mayelia-600 text-xl mr-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">Centre: {{ $centre->nom }}</h3>
        </div>
        <p class="text-gray-600">{{ $centre->adresse }}</p>
    </div>

    <!-- Configuration des jours -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Configuration des jours de travail</h3>
            <p class="text-sm text-gray-600 mt-1">Configurez les horaires pour chaque jour de la semaine</p>
        </div>
        
        <div class="p-6">
            @if($joursTravail->count() > 0)
                <div class="space-y-4">
                    @foreach($joursTravail as $jour)
                        <div class="border border-gray-200 rounded-lg p-4 {{ $jour->actif ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-8 h-8 rounded-full {{ $jour->actif ? 'bg-green-500' : 'bg-gray-400' }} flex items-center justify-center">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </span>
                                        <span class="font-medium text-gray-900">{{ $jour->nom_jour }}</span>
                                    </div>
                                    
                                    @if($jour->actif)
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $jour->heure_debut }} - {{ $jour->heure_fin }}</span>
                                            </div>
                                            
                                            @if($jour->pause_debut && $jour->pause_fin)
                                                <div class="flex items-center space-x-1">
                                                    <i class="fas fa-coffee"></i>
                                                    <span>Pause: {{ $jour->pause_debut }} - {{ $jour->pause_fin }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Jour fermé</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @isAdmin
                                    <form method="POST" action="{{ route('jours-travail.toggle', $jour) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 text-xs font-medium rounded-full {{ $jour->actif ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} transition-colors">
                                            {{ $jour->actif ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('jours-travail.edit', $jour) }}" 
                                       class="p-2 text-gray-400 hover:text-mayelia-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('jours-travail.destroy', $jour) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette configuration ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endisAdmin
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun jour configuré</h3>
                    <p class="text-gray-600 mb-6">Commencez par configurer les jours de travail pour votre centre.</p>
                    <a href="{{ route('jours-travail.create') }}" class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Configurer les jours</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Configuration rapide -->
    @if($joursTravail->count() == 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuration rapide</h3>
            <p class="text-gray-600 mb-4">Configurez rapidement les jours de travail standards (Lundi-Vendredi, 8h-18h avec pause 12h-13h)</p>
            
            <form method="POST" action="{{ route('jours-travail.quick-setup') }}" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2">
                    <i class="fas fa-magic"></i>
                    <span>Configuration rapide</span>
                </button>
            </form>
        </div>
    @endif
</div>
@endsection



