@extends('layouts.dashboard')

@section('title', 'Cartes Prêtes à Récupérer')
@section('subtitle', 'Dossiers avec cartes prêtes à récupérer de l\'ONECI')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dossiers à récupérer</h2>
            <p class="text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Après appel ONECI, scannez les codes-barres pour confirmer la récupération
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('oneci-recuperation.scanner') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-barcode mr-2"></i>
                Scanner individuel
            </a>
            <a href="{{ route('oneci-recuperation.scanner-lot') }}" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                <i class="fas fa-list-check mr-2"></i>
                Scanner en lot
            </a>
        </div>
    </div>

    <!-- Alerte informative -->
    <div class="bg-mayelia-50 border border-mayelia-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-mayelia-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-mayelia-800">Workflow de récupération</h3>
                <div class="mt-2 text-sm text-mayelia-700">
                    <p><strong>Après l'appel de l'ONECI</strong> indiquant que les cartes sont prêtes :</p>
                    <ol class="list-decimal list-inside mt-1 space-y-1">
                        <li>Récupérer physiquement les dossiers avec les cartes à l'ONECI</li>
                        <li>Scanner le code-barres sur chaque enveloppe pour confirmer la récupération</li>
                        <li>Le système met automatiquement à jour le statut et envoie un SMS au client</li>
                        <li>Le client peut venir récupérer sa carte au centre le jour suivant</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('oneci-recuperation.cartes-prete') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Code-barres, nom client..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('oneci-recuperation.cartes-prete') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des cartes prêtes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($dossiers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code-barres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date envoi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dossiers as $item)
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->transfer->date_envoi->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('oneci-recuperation.scanner') }}?code={{ $item->code_barre }}" 
                                           class="text-mayelia-600 hover:text-mayelia-900" 
                                           title="Scanner ce code-barres">
                                            <i class="fas fa-barcode mr-1"></i>
                                            Scanner
                                        </a>
                                        <form method="POST" action="{{ route('oneci-recuperation.confirmer', $item) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Confirmer récupération (sans scan)"
                                                    onclick="return confirm('Confirmer la récupération de cette carte ? Un SMS sera envoyé au client.')">
                                                <i class="fas fa-check mr-1"></i>
                                                Confirmer
                                            </button>
                                        </form>
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
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier à récupérer</h3>
                <p class="text-gray-500 mb-4">Tous les dossiers envoyés ont été récupérés ou aucun transfert n'a été envoyé à l'ONECI.</p>
                <a href="{{ route('oneci-recuperation.scanner') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-barcode mr-2"></i>
                    Scanner un code-barres
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

