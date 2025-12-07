@extends('layouts.dashboard')

@section('title', 'Nouvelle exception')
@section('subtitle', 'Créez une exception pour un jour spécifique')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Créer une exception</h3>
            <p class="text-sm text-gray-600 mt-1">Définissez une exception pour un jour spécifique</p>
        </div>
        
        <form method="POST" action="{{ route('exceptions.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Date de l'exception -->
                <div>
                    <label for="date_exception" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de l'exception <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="date_exception" 
                           name="date_exception" 
                           value="{{ old('date_exception', now()->addDay()->toDateString()) }}"
                           min="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500 @error('date_exception') border-red-500 @enderror"
                           required>
                    @error('date_exception')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type d'exception -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type d'exception <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500 @error('type') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionnez un type</option>
                        <option value="fermeture" {{ old('type') == 'fermeture' ? 'selected' : '' }}>Fermeture complète</option>
                        <option value="horaires_modifies" {{ old('type') == 'horaires_modifies' ? 'selected' : '' }}>Horaires modifiés</option>
                        <option value="capacite_reduite" {{ old('type') == 'capacite_reduite' ? 'selected' : '' }}>Capacité réduite</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500 @error('description') border-red-500 @enderror"
                              placeholder="Décrivez la raison de cette exception...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Horaires modifiés (conditionnel) -->
                <div id="horaires-section" class="hidden">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Horaires modifiés</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="heure_debut" class="block text-xs text-gray-500 mb-1">Heure de début</label>
                            <input type="time" 
                                   id="heure_debut" 
                                   name="heure_debut" 
                                   value="{{ old('heure_debut') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                        </div>
                        <div>
                            <label for="heure_fin" class="block text-xs text-gray-500 mb-1">Heure de fin</label>
                            <input type="time" 
                                   id="heure_fin" 
                                   name="heure_fin" 
                                   value="{{ old('heure_fin') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                        </div>
                    </div>
                </div>

                <!-- Pause modifiée (conditionnel) -->
                <div id="pause-section" class="hidden">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Pause modifiée</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="pause_debut" class="block text-xs text-gray-500 mb-1">Début de pause</label>
                            <input type="time" 
                                   id="pause_debut" 
                                   name="pause_debut" 
                                   value="{{ old('pause_debut') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                        </div>
                        <div>
                            <label for="pause_fin" class="block text-xs text-gray-500 mb-1">Fin de pause</label>
                            <input type="time" 
                                   id="pause_fin" 
                                   name="pause_fin" 
                                   value="{{ old('pause_fin') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-mayelia-500 focus:border-mayelia-500">
                        </div>
                    </div>
                </div>

                <!-- Capacité réduite (conditionnel) -->
                <div id="capacite-section" class="hidden">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="capacite_reduite" 
                               name="capacite_reduite" 
                               value="1"
                               {{ old('capacite_reduite') ? 'checked' : '' }}
                               class="h-4 w-4 text-mayelia-600 focus:ring-mayelia-500 border-gray-300 rounded">
                        <label for="capacite_reduite" class="ml-2 block text-sm text-gray-700">
                            Capacité réduite pour ce jour
                        </label>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('creneaux.exceptions') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-mayelia-600 border border-transparent rounded-md hover:bg-mayelia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500">
                    Créer l'exception
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const horairesSection = document.getElementById('horaires-section');
    const pauseSection = document.getElementById('pause-section');
    const capaciteSection = document.getElementById('capacite-section');

    function toggleSections() {
        const type = typeSelect.value;
        
        // Masquer toutes les sections
        horairesSection.classList.add('hidden');
        pauseSection.classList.add('hidden');
        capaciteSection.classList.add('hidden');
        
        // Afficher les sections selon le type
        if (type === 'horaires_modifies') {
            horairesSection.classList.remove('hidden');
            pauseSection.classList.remove('hidden');
        } else if (type === 'capacite_reduite') {
            capaciteSection.classList.remove('hidden');
        }
    }

    typeSelect.addEventListener('change', toggleSections);
    
    // Initialiser l'affichage
    toggleSections();
});
</script>
@endsection