@extends('layouts.dashboard')

@section('title', 'Détails du Transfert')
@section('subtitle', 'Informations complètes du transfert ONECI')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Transfert {{ $transfer->code_transfert }}</h2>
            <p class="text-gray-600">Créé le {{ $transfer->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="flex space-x-2">
            @if($transfer->statut === 'en_attente')
            <form method="POST" action="{{ route('oneci-transfers.envoyer', $transfer) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700"
                        onclick="return confirm('Envoyer ce transfert à l\'ONECI ?\n\nUn email avec PDF sera envoyé automatiquement.')">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Envoyer à l'ONECI
                </button>
            </form>
            @endif
            <a href="{{ route('oneci-transfers.imprimer-etiquettes', $transfer) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-print mr-2"></i>
                Imprimer étiquettes
            </a>
            <a href="{{ route('oneci-transfers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du transfert</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Code Transfert</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transfer->code_transfert }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Date d'envoi</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transfer->date_envoi->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Statut</p>
                @php
                    $statutColors = [
                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                        'envoye' => 'bg-mayelia-100 text-mayelia-800',
                        'recu_oneci' => 'bg-purple-100 text-purple-800',
                        'traite' => 'bg-indigo-100 text-indigo-800',
                        'carte_prete' => 'bg-green-100 text-green-800',
                        'recupere' => 'bg-gray-100 text-gray-800'
                    ];
                    $color = $statutColors[$transfer->statut] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                    {{ $transfer->statut_formate }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600">Centre</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transfer->centre->nom ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Nombre de dossiers</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transfer->nombre_dossiers }} dossier(s)</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Agent Mayelia</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transfer->agentMayelia->nom_complet ?? 'N/A' }}</p>
            </div>
        </div>

        @if($transfer->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-600 mb-2">Notes</p>
            <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ $transfer->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique</h3>
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-mayelia-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-paper-plane text-mayelia-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Transfert créé</p>
                    <p class="text-sm text-gray-500">{{ $transfer->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            @if($transfer->statut !== 'en_attente')
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Envoyé à l'ONECI</p>
                    <p class="text-sm text-gray-500">{{ $transfer->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            @endif

            @if($transfer->date_reception_oneci)
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-inbox text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Reçu à l'ONECI</p>
                    <p class="text-sm text-gray-500">{{ $transfer->date_reception_oneci->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            @endif

            @if($transfer->date_carte_prete)
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Cartes prêtes</p>
                    <p class="text-sm text-gray-500">{{ $transfer->date_carte_prete->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            @endif

            @if($transfer->date_recuperation)
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-archive text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Récupéré</p>
                    <p class="text-sm text-gray-500">{{ $transfer->date_recuperation->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Liste des dossiers -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dossiers inclus ({{ $transfer->items->count() }})</h3>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date carte prête</th>
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
                                    {{ $item->date_carte_prete ? $item->date_carte_prete->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <p>Aucun dossier dans ce transfert</p>
            </div>
        @endif
    </div>
</div>
@endsection

