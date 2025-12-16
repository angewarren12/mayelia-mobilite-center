@extends('layouts.dashboard')

@section('title', 'Transferts ONECI')
@section('subtitle', 'Gestion des transferts vers l\'ONECI')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-end items-center">
        @userCan('oneci-transfers', 'create')
        <a href="{{ route('oneci-transfers.create') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Transfert
        </a>
        @enduserCan
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('oneci-transfers.index') }}" class="flex flex-wrap gap-4">
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="envoye" {{ request('statut') === 'envoye' ? 'selected' : '' }}>Envoyé</option>
                    <option value="recu_oneci" {{ request('statut') === 'recu_oneci' ? 'selected' : '' }}>Reçu à l'ONECI</option>
                    <option value="traite" {{ request('statut') === 'traite' ? 'selected' : '' }}>Traité</option>
                    <option value="carte_prete" {{ request('statut') === 'carte_prete' ? 'selected' : '' }}>Carte prête</option>
                    <option value="recupere" {{ request('statut') === 'recupere' ? 'selected' : '' }}>Récupéré</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('oneci-transfers.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des transferts -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($transfers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Transfert</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Envoi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Dossiers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transfers as $transfer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transfer->code_transfert }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transfer->date_envoi->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transfer->nombre_dossiers }} dossier(s)</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $transfer->statut_formate }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('oneci-transfers.show', $transfer) }}" class="text-mayelia-600 hover:text-mayelia-900" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('oneci-transfers.imprimer-etiquettes', $transfer) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Imprimer étiquettes">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if($transfer->statut === 'en_attente')
                                        <form method="POST" action="{{ route('oneci-transfers.envoyer', $transfer) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Marquer comme envoyé">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
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
                {{ $transfers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun transfert trouvé</h3>
                <p class="text-gray-500 mb-4">Créez votre premier transfert pour commencer.</p>
                @userCan('oneci-transfers', 'create')
                <a href="{{ route('oneci-transfers.create') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un transfert
                </a>
                @enduserCan
            </div>
        @endif
    </div>
</div>
@endsection


