@extends('layouts.dashboard')

@section('title', 'Modifier le Document Requis')
@section('subtitle', 'Modifier les informations du document requis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Modifier le Document Requis</h1>
            <p class="text-gray-600">Modifiez les informations du document requis</p>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('document-requis.update', $documentRequis) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service -->
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Service <span class="text-red-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('service_id') border-red-500 @enderror">
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" 
                                    {{ (old('service_id', $documentRequis->service_id) == $service->id) ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type de demande -->
                <div>
                    <label for="type_demande" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de demande <span class="text-red-500">*</span>
                    </label>
                    <select name="type_demande" id="type_demande" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type_demande') border-red-500 @enderror">
                        <option value="">Sélectionner un type</option>
                        @foreach($typesDemande as $key => $label)
                            <option value="{{ $key }}" 
                                    {{ (old('type_demande', $documentRequis->type_demande) == $key) ? 'selected' : '' }}>
                                {{ $key }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_demande')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nom du document -->
            <div>
                <label for="nom_document" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom du document <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nom_document" id="nom_document" required
                       value="{{ old('nom_document', $documentRequis->nom_document) }}"
                       placeholder="Ex: Pièce d'identité, Justificatif de domicile..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nom_document') border-red-500 @enderror">
                @error('nom_document')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Description détaillée du document requis..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $documentRequis->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Statut obligatoire -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Statut du document
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="obligatoire" value="1" 
                                   {{ old('obligatoire', $documentRequis->obligatoire ? '1' : '0') == '1' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">
                                <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                                Obligatoire
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="obligatoire" value="0" 
                                   {{ old('obligatoire', $documentRequis->obligatoire ? '1' : '0') == '0' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">
                                <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                Facultatif
                            </span>
                        </label>
                    </div>
                    @error('obligatoire')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ordre d'affichage -->
                <div>
                    <label for="ordre" class="block text-sm font-medium text-gray-700 mb-2">
                        Ordre d'affichage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="ordre" id="ordre" required min="0"
                           value="{{ old('ordre', $documentRequis->ordre) }}"
                           placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('ordre') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Les documents seront affichés dans cet ordre</p>
                    @error('ordre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('document-requis.index') }}" 
                   class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-2"></i>Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Messages d'erreur -->
@if($errors->any())
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <div>
                <p class="font-medium">Erreurs de validation :</p>
                <ul class="text-sm mt-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<script>
// Auto-hide error messages
setTimeout(() => {
    const errorToast = document.querySelector('.fixed.top-4.right-4');
    if (errorToast) errorToast.remove();
}, 8000);
</script>
@endsection