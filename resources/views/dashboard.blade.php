@extends('layouts.dashboard')

@section('title', 'Tableau de bord')
@section('subtitle', 'Bienvenue, ' . Auth::user()->nom . '. Voici un aperçu de l\'activité de votre centre.')

@section('header-actions')
<button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
    <i class="fas fa-plus"></i>
    <span>Nouveau rendez-vous</span>
</button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rendez-vous aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rdv_aujourdhui'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Utilisateurs actifs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['utilisateurs_actifs'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Documents traités</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['documents_traites'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Croissance mensuelle</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['croissance_mensuelle'] ?? 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du centre et calendrier -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations du centre -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-building text-blue-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Informations du centre</h3>
            </div>
            
            @if(Auth::user()->centre)
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ Auth::user()->centre->nom }}</h4>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->adresse }}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->email }}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->telephone }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4 pt-4 border-t">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->users->count() }} utilisateurs</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-cogs w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->services->count() }} services</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-600">Aucun centre assigné</p>
            @endif
        </div>
        
        <!-- Calendrier des rendez-vous -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-calendar text-blue-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Calendrier des rendez-vous</h3>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Visualisez tous les rendez-vous pris dans votre centre.
            </p>
            
            <!-- Widget calendrier simple -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Septembre 2025</h4>
                    <div class="flex space-x-2">
                        <button class="p-1 hover:bg-gray-200 rounded">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </button>
                        <button class="p-1 hover:bg-gray-200 rounded">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    <div class="p-2 text-gray-500">Su</div>
                    <div class="p-2 text-gray-500">Mo</div>
                    <div class="p-2 text-gray-500">Tu</div>
                    <div class="p-2 text-gray-500">We</div>
                    <div class="p-2 text-gray-500">Th</div>
                    <div class="p-2 text-gray-500">Fr</div>
                    <div class="p-2 text-gray-500">Sa</div>
                    
                    <!-- Jours du mois -->
                    @for($i = 1; $i <= 30; $i++)
                        <div class="p-2 {{ $i == 2 ? 'bg-blue-100 text-blue-600 rounded' : '' }}">
                            {{ $i }}
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('services.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-cogs"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Gérer les services</h4>
                    <p class="text-sm text-gray-600">Configurer les services et formules</p>
                </div>
            </a>
            
            <a href="{{ route('jours-travail.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Jours de travail</h4>
                    <p class="text-sm text-gray-600">Configurer les horaires</p>
                </div>
            </a>
            
            <a href="{{ route('creneaux.templates') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg mr-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Templates</h4>
                    <p class="text-sm text-gray-600">Gérer les créneaux</p>
                </div>
            </a>
            
            <a href="{{ route('rendez-vous.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg mr-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Rendez-vous</h4>
                    <p class="text-sm text-gray-600">Voir les RDV</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection