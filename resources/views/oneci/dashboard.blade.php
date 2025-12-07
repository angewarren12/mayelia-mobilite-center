@extends('layouts.oneci')

@section('title', 'Dashboard ONECI')
@section('subtitle', 'Vue d\'ensemble des dossiers')

@section('content')
<div class="space-y-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-mayelia-100 rounded-lg p-3">
                    <i class="fas fa-inbox text-mayelia-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Reçus aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['recus_aujourdhui'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-cog text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En traitement</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['en_traitement'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cartes prêtes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['cartes_prete'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-lg p-3">
                    <i class="fas fa-archive text-gray-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Récupérés aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['recuperes'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('oneci.dossiers') }}" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                <i class="fas fa-list mr-2"></i>
                Voir tous les transferts et dossiers
            </a>
        </div>
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Mode consultation uniquement :</strong> Les dossiers sont envoyés par email avec PDF. 
                Appelez le centre Mayelia lorsque les cartes sont prêtes.
            </p>
        </div>
    </div>

    <!-- Transferts récents -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Transferts récents</h3>
            <a href="{{ route('oneci.dossiers') }}" class="text-sm text-mayelia-600 hover:text-mayelia-800">
                Voir tous les transferts
            </a>
        </div>
        @if($transfertsRecents->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code transfert</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Centre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre dossiers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date envoi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transfertsRecents as $transfer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $transfer->code_transfert }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transfer->centre->nom ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-mayelia-100 text-mayelia-800">
                                        {{ $transfer->nombre_dossiers }} dossier(s)
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transfer->date_envoi->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $transfer->statut_formate }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('oneci.transferts.detail', $transfer) }}" class="text-mayelia-600 hover:text-mayelia-900">
                                        <i class="fas fa-eye"></i> Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-truck text-4xl mb-4"></i>
                <p>Aucun transfert récent</p>
            </div>
        @endif
    </div>
</div>
@endsection

