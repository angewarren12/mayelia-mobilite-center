@extends('layouts.dashboard')

@section('title', 'Templates de créneaux')
@section('subtitle', 'Gérez les templates de créneaux pour générer automatiquement les disponibilités')

@section('header-actions')
<div class="flex space-x-2">
    <a href="{{ route('templates.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
        <i class="fas fa-plus"></i>
        <span>Nouveau template</span>
    </a>
    <form method="POST" action="{{ route('templates.generate-creneaux') }}" class="inline">
        @csrf
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
            <i class="fas fa-magic"></i>
            <span>Générer créneaux</span>
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Informations du centre -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-building text-blue-600 text-xl mr-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">Centre: {{ $centre->nom }}</h3>
        </div>
        <p class="text-gray-600">{{ $centre->adresse }}</p>
    </div>

    <!-- Templates existants -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Templates configurés</h3>
            <p class="text-sm text-gray-600 mt-1">Configurez les templates pour générer automatiquement les créneaux</p>
        </div>
        
        <div class="p-6">
            @if($templates->count() > 0)
                <div class="space-y-4">
                    @foreach($templates as $template)
                        <div class="border border-gray-200 rounded-lg p-4 {{ $template->statut === 'actif' ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $template->formule->couleur }}"></div>
                                        <span class="font-medium text-gray-900">{{ $template->service->nom }} - {{ $template->formule->nom }}</span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-calendar-day"></i>
                                            <span>{{ $template->nom_jour }}</span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $template->tranche_horaire }}</span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $template->capacite }} personnes</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $template->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $template->statut === 'actif' ? 'Actif' : 'Inactif' }}
                                    </span>
                                    
                                    <a href="{{ route('templates.edit', $template) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('templates.destroy', $template) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce template ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun template configuré</h3>
                    <p class="text-gray-600 mb-6">Commencez par créer des templates pour générer automatiquement les créneaux.</p>
                    <a href="{{ route('templates.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Créer un template</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start space-x-3">
            <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
            <div>
                <h4 class="text-lg font-medium text-blue-900 mb-2">Comment ça marche ?</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Créez des templates pour chaque combinaison service/formule/jour</li>
                    <li>• Définissez les tranches horaires et la capacité pour chaque template</li>
                    <li>• Utilisez "Générer créneaux" pour créer automatiquement les créneaux pour 6 mois</li>
                    <li>• Les créneaux respectent les jours de travail et les pauses configurés</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection



