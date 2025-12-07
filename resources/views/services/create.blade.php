@extends('layouts.dashboard')

@section('title', 'Nouveau service')
@section('subtitle', 'Créez un nouveau service pour votre centre')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('services.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom du service *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           value="{{ old('nom') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('nom') border-red-500 @enderror"
                           placeholder="Ex: Carte Nationale d'Identité"
                           required>
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Description du service...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="duree_rdv" class="block text-sm font-medium text-gray-700 mb-2">
                        Durée du rendez-vous (minutes) *
                    </label>
                    <select id="duree_rdv" 
                            name="duree_rdv"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('duree_rdv') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionnez une durée</option>
                        <option value="15" {{ old('duree_rdv') == '15' ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('duree_rdv') == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('duree_rdv') == '45' ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('duree_rdv') == '60' ? 'selected' : '' }}>1 heure</option>
                        <option value="90" {{ old('duree_rdv') == '90' ? 'selected' : '' }}>1h30</option>
                        <option value="120" {{ old('duree_rdv') == '120' ? 'selected' : '' }}>2 heures</option>
                    </select>
                    @error('duree_rdv')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t">
                <a href="{{ route('services.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white rounded-lg transition-colors">
                    Créer le service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



