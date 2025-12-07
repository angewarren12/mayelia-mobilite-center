@extends('layouts.dashboard')

@section('title', 'Modifier le Dossier')
@section('subtitle', 'Modifier les informations du dossier')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Modifier le Dossier</h2>
            <p class="text-gray-600">Numéro: {{ $dossier->numero_dossier }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('dossiers.show', $dossier) }}" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 flex items-center">
                <i class="fas fa-eye mr-2"></i>
                Voir détails
            </a>
            <a href="{{ route('dossiers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('dossiers.update', $dossier) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">Statut du dossier *</label>
                    <select id="statut" name="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        <option value="en_cours" {{ $dossier->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="complet" {{ $dossier->statut == 'complet' ? 'selected' : '' }}>Complet</option>
                        <option value="rejete" {{ $dossier->statut == 'rejete' ? 'selected' : '' }}>Rejeté</option>
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"
                          placeholder="Notes additionnelles sur le dossier...">{{ old('notes', $dossier->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations du rendez-vous (lecture seule) -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du rendez-vous</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Client</label>
                        <p class="text-gray-900">{{ $dossier->rendezVous->client->nom_complet }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $dossier->rendezVous->client->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Téléphone</label>
                        <p class="text-gray-900">{{ $dossier->rendezVous->client->telephone }}</p>
                    </div>
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
                        <p class="text-gray-900">{{ $dossier->rendezVous->centre->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Service</label>
                        <p class="text-gray-900">{{ $dossier->rendezVous->service->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Formule</label>
                        <p class="text-gray-900">{{ $dossier->rendezVous->formule->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Prix</label>
                        <p class="text-gray-900 font-semibold">{{ number_format($dossier->rendezVous->formule->prix, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dossiers.show', $dossier) }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
