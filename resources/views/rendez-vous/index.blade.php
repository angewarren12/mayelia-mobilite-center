@extends('layouts.dashboard')

@section('title', 'Gestion des Rendez-vous')
@section('subtitle', 'Liste des rendez-vous du centre')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec boutons d'action -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-3">
            <button onclick="openExportModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
            @userCan('rendez-vous', 'create')
            <a href="{{ route('rendez-vous.create') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Rendez-vous
            </a>
            @enduserCan
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('rendez-vous.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nom client, email, numéro de suivi..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="min-w-48">
                <label for="centre_id" class="block text-sm font-medium text-gray-700 mb-1">Centre</label>
                <select id="centre_id" name="centre_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les centres</option>
                    @foreach($centres as $centre)
                    <option value="{{ $centre->id }}" {{ request('centre_id') == $centre->id ? 'selected' : '' }}>
                        {{ $centre->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-48">
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous les statuts</option>
                    <option value="confirme" {{ request('statut') === 'confirme' ? 'selected' : '' }}>Confirmé</option>
                    <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
                    <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('rendez-vous.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Effacer
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des rendez-vous -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($rendezVous->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rendezVous as $rdv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $rdv->client->nom_complet ?? 'Client supprimé' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $rdv->client->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $rdv->service->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $rdv->formule->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $rdv->date_rendez_vous->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $rdv->tranche_horaire }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $rdv->centre->nom ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $rdv->centre->ville->nom ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($rdv->statut === 'confirme') bg-green-100 text-green-800
                                        @elseif($rdv->statut === 'annule') bg-red-100 text-red-800
                                        @else bg-mayelia-100 text-mayelia-800 @endif">
                                        {{ $rdv->statut_formate }}
                                    </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                        @if($rdv->statut === 'confirme' && !$rdv->dossierOuvert)
                                            <button onclick="openDossierModal({{ $rdv->id }}, '{{ $rdv->client->nom_complet }}')" 
                                                    class="text-green-600 hover:text-green-900" title="Ouvrir le dossier">
                                                <i class="fas fa-folder-open"></i>
                                            </button>
                                        @elseif($rdv->dossierOuvert)
                                            @if($rdv->dossierOuvert->canBeManagedBy(Auth::user()))
                                                <a href="{{ route('dossier.workflow', $rdv->dossierOuvert) }}" 
                                                   class="text-mayelia-600 hover:text-mayelia-900" title="Gérer le dossier">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                            @else
                                                <span class="text-gray-400" title="Dossier géré par {{ $rdv->dossierOuvert->agent->nom }}">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        @endif
                                        
                                        <a href="{{ route('rendez-vous.show', $rdv) }}" class="text-mayelia-600 hover:text-mayelia-900" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                        @userCan('rendez-vous', 'update')
                                        <a href="{{ route('rendez-vous.edit', $rdv) }}" class="text-indigo-600 hover:text-indigo-900" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                        @enduserCan
                                        <a href="{{ route('dossiers.index', ['rendez_vous_id' => $rdv->id]) }}" class="text-orange-600 hover:text-orange-900" title="Gérer dossiers">
                                            <i class="fas fa-folder"></i>
                                        </a>
                                        @userCan('rendez-vous', 'delete')
                                        <form method="POST" action="{{ route('rendez-vous.destroy', $rdv) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                    </button>
                                        </form>
                                        @enduserCan
                            </div>
                        </td>
                    </tr>
                        @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $rendezVous->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rendez-vous trouvé</h3>
                <p class="text-gray-500 mb-4">Commencez par créer votre premier rendez-vous.</p>
                @userCan('rendez-vous', 'create')
                <a href="{{ route('rendez-vous.create') }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700">
                    <i class="fas fa-plus mr-2"></i>
                    Créer un rendez-vous
                </a>
                @enduserCan
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation d'ouverture de dossier -->
<div id="openDossierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Confirmer l'ouverture du dossier</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">
                    Êtes-vous sûr de vouloir ouvrir le dossier de <strong id="clientName"></strong> ?
                </p>
                <p class="text-sm text-red-600 mb-4">
                    ⚠️ Une fois ouvert, aucun autre agent ne pourra prendre ce dossier.
                </p>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                    <textarea id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500" placeholder="Notes sur l'ouverture du dossier..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeOpenDossierModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button onclick="confirmOpenDossier()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-folder-open mr-2"></i>
                    Ouvrir le dossier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRendezVousId = null;

function openDossierModal(rendezVousId, clientName) {
    currentRendezVousId = rendezVousId;
    document.getElementById('clientName').textContent = clientName;
    document.getElementById('openDossierModal').classList.remove('hidden');
}

function closeOpenDossierModal() {
    document.getElementById('openDossierModal').classList.add('hidden');
    currentRendezVousId = null;
    document.getElementById('notes').value = '';
}

function confirmOpenDossier() {
    if (!currentRendezVousId) return;

    const notes = document.getElementById('notes').value;
    
    console.log('=== DÉBUT OUVERTURE DOSSIER (CLIENT) ===');
    console.log('Rendez-vous ID:', currentRendezVousId);
    console.log('Notes:', notes);
    
    // Afficher un indicateur de chargement
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ouverture...';
    button.disabled = true;

    const url = `/rendez-vous/${currentRendezVousId}/open-dossier`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    console.log('URL:', url);
    console.log('CSRF Token:', csrfToken);

    fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            notes: notes
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text(); // Récupérer d'abord en texte pour voir le contenu
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed JSON:', data);
            
                    if (data.success) {
                // Afficher un message de succès
                showSuccessToast(data.message);
                // Rediriger vers la page de workflow
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
                    } else {
                showErrorToast(data.message);
            }
        } catch (e) {
            console.error('Erreur parsing JSON:', e);
            console.error('Contenu reçu:', text);
            showErrorToast('Erreur de réponse du serveur');
                    }
                })
                .catch(error => {
        console.error('Erreur fetch:', error);
        showErrorToast('Erreur lors de l\'ouverture du dossier');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        closeOpenDossierModal();
    });
}

// Fonctions de notification (utilisent le composant toast unifié du layout)
// Ces fonctions sont définies dans components/toast.blade.php qui est inclus dans layouts/dashboard.blade.php

// Fermer le modal en cliquant à l'extérieur
document.getElementById('openDossierModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOpenDossierModal();
    }
});

// Fonctions pour le modal d'export
function openExportModal() {
    console.log('Ouverture du modal d\'export');
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    console.log('Fermeture du modal d\'export');
    document.getElementById('exportModal').classList.add('hidden');
}

// Gestion des champs conditionnels pour l'export
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type_export"]');
    const dateField = document.getElementById('dateField');
    const plageFields = document.getElementById('plageFields');
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Masquer tous les champs
            dateField.classList.add('hidden');
            plageFields.classList.add('hidden');
            
            // Afficher le champ approprié
            if (this.value === 'date') {
                dateField.classList.remove('hidden');
            } else if (this.value === 'plage') {
                plageFields.classList.remove('hidden');
            }
        });
    });
    
    // Gestion de la soumission du formulaire d'export
    const exportForm = document.getElementById('exportForm');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleExport();
        });
    }
});

// Fonction pour gérer l'export en AJAX
function handleExport() {
    console.log('=== DÉBUT EXPORT AJAX ===');
    
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    // Afficher un indicateur de chargement
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Export en cours...';
    submitButton.disabled = true;
    
    console.log('FormData:', Object.fromEntries(formData));
    
    fetch('{{ route("export.rendez-vous") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response content-type:', response.headers.get('content-type'));
        
        if (!response.ok) {
            // Gérer les erreurs HTTP
            return response.text().then(text => {
                let errorMessage = 'Erreur lors de l\'export: ' + response.status;
                try {
                    const data = JSON.parse(text);
                    if (data.error) {
                        errorMessage = data.error;
                    }
                } catch (e) {
                    console.error('Erreur parse JSON:', e);
                }
                throw new Error(errorMessage);
            });
        }
        
        const contentType = response.headers.get('content-type');
        
        // Si c'est un JSON, c'est probablement une erreur
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                throw new Error('Réponse inattendue du serveur');
            });
        }
        
        // Si c'est un PDF (application/pdf ou octet-stream), télécharger
        if (contentType && (contentType.includes('application/pdf') || contentType.includes('octet-stream'))) {
            return response.blob().then(blob => {
                const contentDisposition = response.headers.get('content-disposition');
                const filename = getFilenameFromContentDisposition(contentDisposition);
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                showSuccessToast('Export PDF téléchargé avec succès !');
                closeExportModal();
            });
        }
        
        // Si on arrive ici, on ne sait pas quoi faire
        throw new Error('Format de réponse non reconnu');
    })
    .catch(error => {
        console.error('Erreur export:', error);
        showErrorToast(error.message || 'Erreur lors de l\'export');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

// Fonction pour extraire le nom de fichier du Content-Disposition
function getFilenameFromContentDisposition(contentDisposition) {
    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    if (filenameMatch && filenameMatch[1]) {
        return filenameMatch[1].replace(/['"]/g, '');
    }
    return 'rendez-vous-export.pdf';
}
</script>

<!-- Modal d'export des rendez-vous -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-download text-mayelia-600 mr-2"></i>
                        Exporter les rendez-vous
                    </h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="exportForm">
                    @csrf
                    
                    <!-- Type d'export -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Période d'export
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="aujourdhui" class="mr-3" checked>
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-day text-mayelia-500 mr-2"></i>
                                    Aujourd'hui
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="date" class="mr-3">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar text-green-500 mr-2"></i>
                                    Date spécifique
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="plage" class="mr-3">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                    Plage de dates
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date spécifique -->
                    <div id="dateField" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date
                        </label>
                        <input type="date" name="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <!-- Plage de dates -->
                    <div id="plageFields" class="mb-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date début
                                </label>
                                <input type="date" name="date_debut" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date fin
                                </label>
                                <input type="date" name="date_fin" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtres supplémentaires -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtres supplémentaires (optionnels)</h4>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut
                                </label>
                                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                    <option value="">Tous les statuts</option>
                                    <option value="confirme">Confirmé</option>
                                    <option value="dossier_ouvert">Dossier ouvert</option>
                                    <option value="documents_verifies">Documents vérifiés</option>
                                    <option value="paiement_effectue">Paiement effectué</option>
                                    <option value="dossier_oneci">Dossier ONECI</option>
                                    <option value="carte_mayelia">Carte Mayelia</option>
                                    <option value="carte_prete">Carte prête</option>
                                    <option value="termine">Terminé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeExportModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection