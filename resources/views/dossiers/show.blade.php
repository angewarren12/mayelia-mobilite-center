@extends('layouts.dashboard')

@section('title', 'Détails du Dossier')
@section('subtitle', 'Informations complètes du dossier')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dossier {{ $dossier->numero_dossier }}</h2>
            <p class="text-gray-600">Créé le {{ $dossier->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('dossiers.edit', $dossier) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('dossiers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations du client -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-blue-600"></i>
                Informations Client
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nom complet</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->client->nom_complet ?? 'Client supprimé' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->client->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Téléphone</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->client->telephone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Date de naissance</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->client->date_naissance ? $dossier->rendezVous->client->date_naissance->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Informations du rendez-vous -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-green-600"></i>
                Rendez-vous
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Date</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Heure</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->tranche_horaire }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Centre</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->centre->nom ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Service</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Formule</label>
                    <p class="text-gray-900">{{ $dossier->rendezVous->formule->nom ?? 'N/A' }}</p>
                        </div>
                        </div>
                    </div>

                    <!-- Statut du dossier -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                Statut
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Statut du dossier</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($dossier->statut === 'complet') bg-green-100 text-green-800
                        @elseif($dossier->statut === 'rejete') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $dossier->statut)) }}
                                            </span>
                                        </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Statut de paiement</label>
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
                <div>
                    <label class="text-sm font-medium text-gray-500">Montant payé</label>
                    <p class="text-gray-900 font-semibold">{{ number_format($dossier->montant_paye, 0, ',', ' ') }} FCFA</p>
                                    </div>
                @endif
                @if($dossier->date_paiement)
                <div>
                    <label class="text-sm font-medium text-gray-500">Date de paiement</label>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($dossier->date_paiement)->format('d/m/Y') }}</p>
                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

    <!-- Documents requis -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-file-alt mr-2 text-orange-600"></i>
            Documents Requis
        </h3>
        
        @if($documentsRequis->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($documentsRequis as $docRequis)
                    @php
                        $document = $dossier->documents->where('document_requis_id', $docRequis->id)->first();
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4 {{ $document ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $docRequis->nom }}</h4>
                                @if($docRequis->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $docRequis->description }}</p>
                                @endif
                                @if($document)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Fourni
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $document->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        En attente
                                    </span>
                                @endif
                            </div>
                            @if($document)
                                <a href="{{ Storage::url($document->chemin_fichier) }}" target="_blank" class="text-blue-600 hover:text-blue-800 ml-2">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
                                                        @else
            <div class="text-center py-8">
                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Aucun document requis pour ce service</p>
            </div>
                                                        @endif
                                </div>
                                
    <!-- Notes -->
    @if($dossier->notes)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
            Notes
        </h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $dossier->notes }}</p>
        </div>
                                </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-cogs mr-2 text-gray-600"></i>
            Actions
        </h3>
        <div class="flex flex-wrap gap-3">
            <button onclick="updateDocuments()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center">
                <i class="fas fa-upload mr-2"></i>
                Mettre à jour les documents
            </button>
            <button onclick="updatePayment()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-credit-card mr-2"></i>
                Gérer le paiement
            </button>
            <form method="POST" action="{{ route('dossiers.destroy', $dossier) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Supprimer le dossier
                                </button>
                            </form>
        </div>
                        </div>
                    </div>

<!-- Modal pour la mise à jour des documents -->
<div id="documentsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mettre à jour les documents</h3>
            </div>
            <form id="documentsForm" method="POST" action="{{ route('dossiers.update-documents', $dossier) }}" enctype="multipart/form-data">
                                @csrf
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($documentsRequis as $docRequis)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $docRequis->nom }}
                                    @if($docRequis->obligatoire)
                                        <span class="text-red-500">*</span>
                                    @endif
                                            </label>
                                @if($docRequis->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ $docRequis->description }}</p>
                                @endif
                                <input type="file" 
                                       name="documents[{{ $loop->index }}][fichier]" 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="hidden" name="documents[{{ $loop->index }}][document_requis_id]" value="{{ $docRequis->id }}">
                            </div>
                        @endforeach
                                        </div>
                                    </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeDocumentsModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Mettre à jour
                    </button>
                                        </div>
            </form>
                                        </div>
                                    </div>
                                </div>

<!-- Modal pour la gestion du paiement -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Gérer le paiement</h3>
                        </div>
            <form id="paymentForm" method="POST" action="{{ route('dossiers.update-payment', $dossier) }}">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label for="statut_paiement" class="block text-sm font-medium text-gray-700 mb-2">Statut de paiement *</label>
                        <select id="statut_paiement" name="statut_paiement" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="en_attente" {{ $dossier->statut_paiement === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="paye" {{ $dossier->statut_paiement === 'paye' ? 'selected' : '' }}>Payé</option>
                            <option value="partiel" {{ $dossier->statut_paiement === 'partiel' ? 'selected' : '' }}>Partiel</option>
                            <option value="rembourse" {{ $dossier->statut_paiement === 'rembourse' ? 'selected' : '' }}>Remboursé</option>
                        </select>
                    </div>
                    <div>
                        <label for="montant_paye" class="block text-sm font-medium text-gray-700 mb-2">Montant payé (FCFA)</label>
                        <input type="number" id="montant_paye" name="montant_paye" value="{{ $dossier->montant_paye }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                    <div>
                        <label for="date_paiement" class="block text-sm font-medium text-gray-700 mb-2">Date de paiement</label>
                        <input type="date" id="date_paiement" name="date_paiement" value="{{ $dossier->date_paiement ? \Carbon\Carbon::parse($dossier->date_paiement)->format('Y-m-d') : '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-2">Mode de paiement</label>
                        <input type="text" id="mode_paiement" name="mode_paiement" value="{{ $dossier->mode_paiement }}" 
                               placeholder="Ex: Espèces, Virement, Carte bancaire..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                    <div>
                        <label for="reference_paiement" class="block text-sm font-medium text-gray-700 mb-2">Référence de paiement</label>
                        <input type="text" id="reference_paiement" name="reference_paiement" value="{{ $dossier->reference_paiement }}" 
                               placeholder="Numéro de transaction, référence..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Enregistrer
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateDocuments() {
    document.getElementById('documentsModal').classList.remove('hidden');
}

function closeDocumentsModal() {
    document.getElementById('documentsModal').classList.add('hidden');
}

function updatePayment() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Fermer les modals en cliquant à l'extérieur
document.getElementById('documentsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDocumentsModal();
    }
});

document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>
@endsection