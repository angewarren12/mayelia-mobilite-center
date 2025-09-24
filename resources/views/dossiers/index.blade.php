@extends('layouts.dashboard')

@section('title', 'Gestion des Dossiers')
@section('subtitle', 'Liste des dossiers clients')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dossiers</h2>
            <p class="text-gray-600">Gérez les dossiers des clients</p>
        </div>
        <a href="{{ route('dossiers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Dossier
        </a>
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
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="complet" {{ request('statut') === 'complet' ? 'selected' : '' }}>Complet</option>
                    <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-1">Rendez-vous</label>
                <select id="rendez_vous_id" name="rendez_vous_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les rendez-vous</option>
                    @foreach(\App\Models\RendezVous::with('client')->get() as $rdv)
                    <option value="{{ $rdv->id }}" {{ request('rendez_vous_id') == $rdv->id ? 'selected' : '' }}>
                        {{ $rdv->client->nom_complet ?? 'Client supprimé' }} - {{ $rdv->date_rendez_vous->format('d/m/Y') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
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
                                    <div class="text-sm font-medium text-gray-900">{{ $dossier->numero_dossier }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->created_at->format('d/m/Y') }}</div>
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
                                                {{ $dossier->rendezVous->client->nom_complet ?? 'Client supprimé' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $dossier->rendezVous->client->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous->formule->nom ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $dossier->rendezVous->tranche_horaire }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($dossier->statut === 'complet') bg-green-100 text-green-800
                                        @elseif($dossier->statut === 'rejete') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $dossier->statut)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($dossier->statut_paiement)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($dossier->statut_paiement === 'paye') bg-green-100 text-green-800
                                                @elseif($dossier->statut_paiement === 'en_attente') bg-yellow-100 text-yellow-800
                                                @elseif($dossier->statut_paiement === 'partiel') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $dossier->statut_paiement)) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Non défini</span>
                                        @endif
                                    </div>
                                    @if($dossier->montant_paye)
                                        <div class="text-sm text-gray-500">{{ number_format($dossier->montant_paye, 0, ',', ' ') }} FCFA</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('dossiers.show', $dossier) }}" class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dossiers.edit', $dossier) }}" class="text-indigo-600 hover:text-indigo-900" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('dossiers.destroy', $dossier) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                <i class="fas fa-trash"></i>
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
                <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier trouvé</h3>
                <p class="text-gray-500 mb-4">Commencez par créer votre premier dossier.</p>
                <a href="{{ route('dossiers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un dossier
                </a>
            </div>
        @endif
    </div>
</div>
@endsection