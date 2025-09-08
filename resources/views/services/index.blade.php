@extends('layouts.dashboard')

@section('title', 'Services & Formules')
@section('subtitle', 'Gérez les services et leurs formules pour votre centre')

@section('header-actions')
<a href="{{ route('services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
    <i class="fas fa-plus"></i>
    <span>Nouveau service</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    @if($services->count() > 0)
        @foreach($services as $service)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $service->nom }}</h3>
                                <p class="text-sm text-gray-600">{{ $service->description }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $service->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $service->statut === 'actif' ? 'Actif' : 'Inactif' }}
                            </span>
                            
                            <div class="flex space-x-1">
                                <a href="{{ route('services.edit', $service) }}" class="p-2 text-gray-400 hover:text-blue-600">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('services.formules.create', $service) }}" class="p-2 text-gray-400 hover:text-green-600" title="Ajouter une formule">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <form method="POST" action="{{ route('services.destroy', $service) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="text-sm">
                            <span class="text-gray-600">Durée RDV:</span>
                            <span class="font-medium">{{ $service->duree_rdv }} minutes</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Formules:</span>
                            <span class="font-medium">{{ $service->formules->count() }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Créé le:</span>
                            <span class="font-medium">{{ $service->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Formules du service -->
                    @if($service->formules->count() > 0)
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Formules disponibles</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($service->formules as $formule)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $formule->couleur }}"></div>
                                                <span class="font-medium text-gray-900">{{ $formule->nom }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $formule->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $formule->statut === 'actif' ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-sm text-gray-600 mb-2">
                                            Prix: <span class="font-medium">{{ number_format($formule->prix, 2) }} €</span>
                                        </div>
                                        
                                        <div class="flex space-x-1">
                                            <a href="{{ route('services.formules.edit', $formule) }}" class="p-1 text-gray-400 hover:text-blue-600">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <form method="POST" action="{{ route('services.formules.destroy', $formule) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formule ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="border-t pt-4">
                            <div class="text-center py-4">
                                <i class="fas fa-tags text-gray-300 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">Aucune formule configurée</p>
                                <a href="{{ route('services.formules.create', $service) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Ajouter une formule
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-cogs text-gray-300 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun service configuré</h3>
            <p class="text-gray-600 mb-6">Commencez par créer votre premier service pour votre centre.</p>
            <a href="{{ route('services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Créer un service</span>
            </a>
        </div>
    @endif
</div>
@endsection



