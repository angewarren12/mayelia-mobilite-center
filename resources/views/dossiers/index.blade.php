@extends('layouts.dashboard')

@section('title', 'Gestion des Dossiers')
@section('subtitle', 'Liste des dossiers clients')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex justify-end items-center">
        @userCan('dossiers', 'create')
        <button onclick="openExportModal()" class="mr-3 bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center">
            <i class="fas fa-file-export mr-2 text-gray-500"></i>
            Exporter PDF
        </button>
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
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-1">Rendez-vous</label>
                <select id="rendez_vous_id" name="rendez_vous_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les rendez-vous</option>
                    @foreach(\App\Models\RendezVous::with('client')->latest()->take(50)->get() as $rdv)
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
                                            'finalise' => 'bg-green-100 text-green-800',
                                            'annulé' => 'bg-red-100 text-red-800'
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
                                        <a href="{{ route('dossier.imprimer-etiquette', $dossier) }}" 
                                           target="_blank"
                                           class="text-purple-600 hover:text-purple-900" 
                                           title="Imprimer l'étiquette">
                                            <i class="fas fa-tag"></i>
                                        </a>
                                        @endif
                                    </div>
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
                                        <form action="{{ route('dossiers.destroy', $dossier) }}" method="POST" class="ml-2" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ? Cette action est irréversible.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer le dossier">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
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

<!-- Modal Export -->
<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeExportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-file-export text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Exporter les dossiers</h3>
                        <div class="mt-4">
                            <form id="exportForm" action="{{ route('export.dossiers') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'export</label>
                                        <select name="type_export" id="type_export" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500" onchange="toggleDateInputs()">
                                            <option value="aujourdhui">Aujourd'hui</option>
                                            <option value="date">Date spécifique</option>
                                            <option value="plage">Plage de dates</option>
                                        </select>
                                    </div>
                                    
                                    <div id="date_input" class="hidden">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                        <input type="date" name="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
                                    </div>
                                    
                                    <div id="plage_input" class="hidden grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                                            <input type="date" name="date_debut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                                            <input type="date" name="date_fin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par statut (optionnel)</label>
                                        <select name="statut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
                                            <option value="">Tous les statuts</option>
                                            <option value="ouvert">Ouvert</option>
                                            <option value="en_cours">En cours</option>
                                            <option value="finalise">Finalisé</option>
                                            <option value="annulé">Rejeté</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitExport()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Télécharger PDF
                </button>
                <button type="button" onclick="closeExportModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

function toggleDateInputs() {
    const type = document.getElementById('type_export').value;
    document.getElementById('date_input').classList.add('hidden');
    document.getElementById('plage_input').classList.add('hidden');
    document.getElementById('plage_input').classList.remove('grid');
    
    if (type === 'date') {
        document.getElementById('date_input').classList.remove('hidden');
    } else if (type === 'plage') {
        document.getElementById('plage_input').classList.remove('hidden');
        document.getElementById('plage_input').classList.add('grid');
    }
}

function submitExport() {
    document.getElementById('exportForm').submit();
    closeExportModal();
}
</script>