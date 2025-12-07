@extends('layouts.oneci')

@section('title', 'Détail du Transfert')
@section('subtitle', 'Liste des dossiers du transfert')

@section('content')
<div class="space-y-6">
    <!-- En-tête du transfert -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-mayelia-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Transfert {{ $transfer->code_transfert }}</h1>
                        <p class="text-gray-600">Envoyé le {{ $transfer->date_envoi->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-building text-mayelia-600"></i>
                            <span class="font-semibold text-gray-700">Centre Mayelia</span>
                        </div>
                        <p class="text-gray-900">{{ $transfer->centre->nom ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-folder text-green-600"></i>
                            <span class="font-semibold text-gray-700">Nombre de dossiers</span>
                        </div>
                        <p class="text-gray-900">{{ $transfer->nombre_dossiers }} dossier(s)</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-user text-purple-600"></i>
                            <span class="font-semibold text-gray-700">Agent Mayelia</span>
                        </div>
                        <p class="text-gray-900">{{ $transfer->agentMayelia->nom_complet ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="ml-6 text-right">
                <div class="mb-4">
                    @php
                        $statutColors = [
                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                            'envoye' => 'bg-mayelia-100 text-mayelia-800',
                            'recu_oneci' => 'bg-indigo-100 text-indigo-800',
                            'traite' => 'bg-purple-100 text-purple-800',
                            'carte_prete' => 'bg-green-100 text-green-800',
                            'recupere' => 'bg-gray-100 text-gray-800'
                        ];
                        $color = $statutColors[$transfer->statut] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $color }}">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        {{ $transfer->statut_formate }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des dossiers du transfert -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dossiers du transfert ({{ $transfer->items->count() }})</h3>
        </div>
        @if($transfer->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code-barres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date réception</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transfer->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-900">{{ $item->code_barre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->dossierOuvert->rendezVous->service->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'en_attente' => 'bg-yellow-100 text-yellow-800',
                                            'recu' => 'bg-mayelia-100 text-mayelia-800',
                                            'traite' => 'bg-indigo-100 text-indigo-800',
                                            'carte_prete' => 'bg-green-100 text-green-800',
                                            'recupere' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $color = $statutColors[$item->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $item->statut_formate }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->date_reception ? $item->date_reception->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('oneci.dossiers.workflow', $item) }}" 
                                       class="text-mayelia-600 hover:text-mayelia-900" 
                                       title="Voir workflow complet">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p>Aucun dossier dans ce transfert</p>
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('oneci.dashboard') }}" class="flex items-center space-x-2 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au dashboard</span>
            </a>
        </div>
    </div>
</div>
@endsection

