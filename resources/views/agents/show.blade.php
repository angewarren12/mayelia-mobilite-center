@extends('layouts.dashboard')

@section('title', 'Détails de l\'Agent')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Détails de l'Agent</h2>
            <p class="text-gray-600">{{ $agent->nom_complet }}</p>
        </div>
        <div class="flex space-x-3">
            @isAdmin
            <a href="{{ route('agents.edit', $agent) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            @endisAdmin
            <a href="{{ route('agents.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-mayelia-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Dossiers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-mayelia-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-folder text-mayelia-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['en_cours'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Validés</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['valides'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Complets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['complets'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-double text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de l'agent -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informations Personnelles -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-mayelia-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-mayelia-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informations Personnelles</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-id-card text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Nom complet</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $agent->nom_complet }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Email</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->email }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Téléphone</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->telephone }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center">
                        <i class="fas fa-toggle-on text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Statut</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $agent->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($agent->statut) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Informations Professionnelles -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-briefcase text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informations Professionnelles</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-building text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Centre</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->centre->nom ?? 'N/A' }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-sign-in-alt text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Dernière Connexion</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->derniere_connexion ? $agent->derniere_connexion->format('d/m/Y à H:i') : 'Jamais' }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-plus text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Date de Création</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center">
                        <i class="fas fa-edit text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-600">Dernière Modification</span>
                    </div>
                    <span class="text-sm text-gray-900">{{ $agent->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions de l'agent -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-shield-alt text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Permissions</h3>
                        <p class="text-sm text-gray-600">Permissions accordées à cet agent</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                    {{ $agent->permissions->count() }} permission(s)
                                        </span>
            </div>
        </div>

        <div class="p-6">
            @if($permissionsGrouped->count() > 0)
                <div class="space-y-4">
                    @foreach($permissionsGrouped as $module => $permissions)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 capitalize flex items-center">
                                <i class="fas fa-folder mr-2 text-purple-500"></i>
                                {{ ucfirst($module) }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($permissions as $permission)
                                    <div class="flex items-start space-x-2 p-2 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $permission->name }}</span>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune permission assignée</h3>
                    <p class="text-gray-500">Cet agent n'a aucune permission spécifique. Les admins ont toutes les permissions par défaut.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique des dossiers -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-history text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Historique des Dossiers</h3>
                        <p class="text-sm text-gray-600">Tous les dossiers gérés par cet agent</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-mayelia-100 text-mayelia-800 rounded-full text-sm font-medium">
                    {{ $stats['total'] }} dossier(s)
                </span>
                        </div>
                    </div>

        @if($dossiers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date RDV</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Ouverture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dossiers as $dossier)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $dossier->rendezVous->client->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous->formule->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $dossier->rendezVous->tranche_horaire ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'en_cours' => 'bg-mayelia-100 text-mayelia-800',
                                            'dossier_complet' => 'bg-green-100 text-green-800',
                                            'dossier_incomplet' => 'bg-red-100 text-red-800',
                                            'valide' => 'bg-purple-100 text-purple-800',
                                            'transmis_oneci' => 'bg-indigo-100 text-indigo-800'
                                        ];
                                        $color = $statutColors[$dossier->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $dossier->statut_formate }}
                                                </span>
                                            </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $dossier->date_ouverture ? $dossier->date_ouverture->format('d/m/Y à H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($dossier->rendezVous)
                                            <a href="{{ route('rendez-vous.show', $dossier->rendezVous) }}" 
                                               class="text-mayelia-600 hover:text-mayelia-900" 
                                               title="Voir le rendez-vous">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @if($dossier->rendezVous && $dossier->rendezVous->dossierOuvert)
                                            <a href="{{ route('dossier.workflow', $dossier->rendezVous->dossierOuvert) }}" 
                                               class="text-green-600 hover:text-green-900" 
                                               title="Gérer le dossier">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        @endif
                                    </div>
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
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier trouvé</h3>
                <p class="text-gray-500">Cet agent n'a encore traité aucun dossier.</p>
            </div>
        @endif
    </div>
</div>
@endsection
