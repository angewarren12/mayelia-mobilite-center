@extends('layouts.dashboard')

@section('title', 'Modifier la formule')
@section('subtitle', 'Modifiez les informations de la formule: ' . $formule->nom)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('services.formules.update', $formule) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la formule *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           value="{{ old('nom', $formule->nom) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nom') border-red-500 @enderror"
                           placeholder="Ex: Standard, VIP, VVIP"
                           required>
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="prix" class="block text-sm font-medium text-gray-700 mb-2">
                        Prix (€) *
                    </label>
                    <input type="number" 
                           id="prix" 
                           name="prix" 
                           value="{{ old('prix', $formule->prix) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('prix') border-red-500 @enderror"
                           placeholder="0.00"
                           required>
                    @error('prix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="couleur" class="block text-sm font-medium text-gray-700 mb-2">
                        Couleur *
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="color" 
                               id="couleur" 
                               name="couleur" 
                               value="{{ old('couleur', $formule->couleur) }}"
                               class="w-16 h-10 border border-gray-300 rounded-lg cursor-pointer @error('couleur') border-red-500 @enderror">
                        <input type="text" 
                               id="couleur_text" 
                               value="{{ old('couleur', $formule->couleur) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('couleur') border-red-500 @enderror"
                               placeholder="#007bff"
                               readonly>
                    </div>
                    @error('couleur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Cette couleur sera utilisée pour identifier la formule dans le calendrier.</p>
                </div>
                
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut *
                    </label>
                    <select id="statut" 
                            name="statut"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('statut') border-red-500 @enderror"
                            required>
                        <option value="actif" {{ old('statut', $formule->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $formule->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('statut')
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
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('couleur').addEventListener('input', function() {
    document.getElementById('couleur_text').value = this.value;
});

document.getElementById('couleur_text').addEventListener('input', function() {
    document.getElementById('couleur').value = this.value;
});
</script>
@endsection



