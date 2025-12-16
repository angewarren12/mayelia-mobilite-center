@extends('layouts.dashboard')

@section('title', 'Gestion des Dossiers')
@section('subtitle', 'Liste des dossiers clients')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-end items-center">
        @userCan('dossiers', 'create')
        <a href="{{ route('dossiers.create-walkin') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Dossier
        </a>
        @enduserCan
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('dossiers.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nom client, email, numéro de dossier..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les statuts</option>
                    <option value="ouvert" {{ request('statut') === 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                    <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="finalise" {{ request('statut') === 'finalise' ? 'selected' : '' }}>Finalisé</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-1">Rendez-vous</label>
                <select id="rendez_vous_id" name="rendez_vous_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les rendez-vous</option>
                    @foreach(\App\Models\RendezVous::with('client')->get() as $rdv)
                    <option value="{{ $rdv->id }}" {{ request('rendez_vous_id') == $rdv->id ? 'selected' : '' }}>
                        {{ $rdv->client->nom_complet ?? 'Client supprimé' }} - {{ $rdv->date_rendez_vous->format('d/m/Y') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('dossiers.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des dossiers -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($dossiers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Dossier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date RDV</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paiement</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dossiers as $dossier)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">DOS-{{ str_pad($dossier->id, 6, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->date_ouverture->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $dossier->rendezVous?->client->nom_complet ?? 'Rendez-vous supprimé' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $dossier->rendezVous?->client->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $dossier->rendezVous?->service->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous?->formule->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $dossier->rendezVous?->date_rendez_vous?->format('d/m/Y') ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous?->tranche_horaire ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statutColors = [
                                            'ouvert' => 'bg-mayelia-100 text-mayelia-800',
                                            'en_cours' => 'bg-yellow-100 text-yellow-800',
                                            'finalise' => 'bg-green-100 text-green-800'
                                        ];
                                        $color = $statutColors[$dossier->statut] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $dossier->statut_formate }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Progression: {{ $dossier->progression }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($dossier->paiementVerification)
                                        <div class="text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Vérifié
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ number_format($dossier->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Non vérifié</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('dossier.workflow', $dossier) }}" class="text-mayelia-600 hover:text-mayelia-900" title="Voir workflow">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($dossier->statut === 'finalise')
                                        <a href="{{ route('dossier.imprimer-recu', $dossier) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-900" 
                                           title="Imprimer le reçu">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('dossiers.imprimer-etiquette', $dossier) }}" 
                                           target="_blank"
                                           class="text-purple-600 hover:text-purple-900" 
                                           title="Imprimer l'étiquette">
                                            <i class="fas fa-tag"></i>
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
                <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier trouvé</h3>
                <p class="text-gray-500 mb-4">Commencez par créer votre premier dossier.</p>
                @userCan('dossiers', 'create')
                <a href="{{ route('dossiers.create') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un dossier
                </a>
                @enduserCan
            </div>
        @endif
    </div>
</div>
@endsection