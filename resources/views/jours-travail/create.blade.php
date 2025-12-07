@extends('layouts.dashboard')

@section('title', 'Ajouter un jour de travail')
@section('subtitle', 'Configurez un nouveau jour de travail pour votre centre')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('jours-travail.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="jour_semaine" class="block text-sm font-medium text-gray-700 mb-2">
                        Jour de la semaine *
                    </label>
                    <select id="jour_semaine" 
                            name="jour_semaine"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('jour_semaine') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionnez un jour</option>
                        <option value="1" {{ old('jour_semaine') == '1' ? 'selected' : '' }}>Lundi</option>
                        <option value="2" {{ old('jour_semaine') == '2' ? 'selected' : '' }}>Mardi</option>
                        <option value="3" {{ old('jour_semaine') == '3' ? 'selected' : '' }}>Mercredi</option>
                        <option value="4" {{ old('jour_semaine') == '4' ? 'selected' : '' }}>Jeudi</option>
                        <option value="5" {{ old('jour_semaine') == '5' ? 'selected' : '' }}>Vendredi</option>
                        <option value="6" {{ old('jour_semaine') == '6' ? 'selected' : '' }}>Samedi</option>
                        <option value="7" {{ old('jour_semaine') == '7' ? 'selected' : '' }}>Dimanche</option>
                    </select>
                    @error('jour_semaine')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="actif" 
                               value="1"
                               {{ old('actif', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-mayelia-600 focus:ring-mayelia-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Ce jour est un jour de travail</span>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-2">
                            Heure de début *
                        </label>
                        <input type="time" 
                               id="heure_debut" 
                               name="heure_debut" 
                               value="{{ old('heure_debut', '08:00') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('heure_debut') border-red-500 @enderror"
                               required>
                        @error('heure_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Heure de fin *
                        </label>
                        <input type="time" 
                               id="heure_fin" 
                               name="heure_fin" 
                               value="{{ old('heure_fin', '18:00') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('heure_fin') border-red-500 @enderror"
                               required>
                        @error('heure_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-t pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Configuration de la pause (optionnel)</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="pause_debut" class="block text-sm font-medium text-gray-700 mb-2">
                                Début de pause
                            </label>
                            <input type="time" 
                                   id="pause_debut" 
                                   name="pause_debut" 
                                   value="{{ old('pause_debut', '12:00') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('pause_debut') border-red-500 @enderror">
                            @error('pause_debut')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="pause_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                Fin de pause
                            </label>
                            <input type="time" 
                                   id="pause_fin" 
                                   name="pause_fin" 
                                   value="{{ old('pause_fin', '13:00') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('pause_fin') border-red-500 @enderror">
                            @error('pause_fin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <p class="mt-2 text-sm text-gray-500">
                        La pause sera automatiquement exclue des créneaux disponibles.
                    </p>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t">
                <a href="{{ route('jours-travail.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



