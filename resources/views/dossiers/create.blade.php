@extends('layouts.dashboard')

@section('title', 'Nouveau Dossier')
@section('subtitle', 'Créer un nouveau dossier client')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nouveau Dossier</h2>
            <p class="text-gray-600">Créer un nouveau dossier pour un client</p>
        </div>
        <a href="{{ route('dossiers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('dossiers.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Rendez-vous -->
                <div class="lg:col-span-2">
                    <label for="rendez_vous_id" class="block text-sm font-medium text-gray-700 mb-2">Rendez-vous *</label>
                    <select id="rendez_vous_id" name="rendez_vous_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un rendez-vous</option>
                        @foreach($rendezVousList as $rdv)
                        <option value="{{ $rdv->id }}" {{ old('rendez_vous_id', $rendezVous?->id) == $rdv->id ? 'selected' : '' }}>
                            {{ $rdv->client->nom_complet }} - {{ $rdv->date_rendez_vous->format('d/m/Y') }} {{ $rdv->tranche_horaire }} - {{ $rdv->service->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('rendez_vous_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">Statut du dossier *</label>
                    <select id="statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="en_cours" {{ old('statut', 'en_cours') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="complet" {{ old('statut') == 'complet' ? 'selected' : '' }}>Complet</option>
                        <option value="rejete" {{ old('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                    @error('statut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Notes additionnelles sur le dossier...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations du rendez-vous sélectionné -->
            @if($rendezVous)
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du rendez-vous sélectionné</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Client</label>
                        <p class="text-gray-900">{{ $rendezVous->client->nom_complet }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $rendezVous->client->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Téléphone</label>
                        <p class="text-gray-900">{{ $rendezVous->client->telephone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date</label>
                        <p class="text-gray-900">{{ $rendezVous->date_rendez_vous->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Heure</label>
                        <p class="text-gray-900">{{ $rendezVous->tranche_horaire }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Centre</label>
                        <p class="text-gray-900">{{ $rendezVous->centre->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Service</label>
                        <p class="text-gray-900">{{ $rendezVous->service->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Formule</label>
                        <p class="text-gray-900">{{ $rendezVous->formule->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Prix</label>
                        <p class="text-gray-900 font-semibold">{{ number_format($rendezVous->formule->prix, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dossiers.index') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Créer le dossier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Mise à jour des informations du rendez-vous sélectionné
document.getElementById('rendez_vous_id').addEventListener('change', function() {
    const rendezVousId = this.value;
    
    if (rendezVousId) {
        // Charger les informations du rendez-vous
        fetch(`/api/rendez-vous/${rendezVousId}`)
            .then(response => response.json())
            .then(data => {
                // Mettre à jour l'affichage des informations
                updateRendezVousInfo(data);
            })
            .catch(error => {
                console.error('Erreur lors du chargement du rendez-vous:', error);
            });
    }
});

function updateRendezVousInfo(rendezVous) {
    // Cette fonction pourrait être étendue pour mettre à jour dynamiquement
    // les informations affichées si nécessaire
    console.log('Rendez-vous sélectionné:', rendezVous);
}
</script>
@endsection
