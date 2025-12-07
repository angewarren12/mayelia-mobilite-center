@extends('layouts.dashboard')

@section('title', 'Modifier un Agent')
@section('subtitle', 'Modifiez les informations et permissions de l\'agent')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Modifier un Agent</h2>
            <p class="text-gray-600 mt-1">Modifiez les informations et permissions de l'agent</p>
        </div>
        <a href="{{ route('agents.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-lg shadow p-6">
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <h4 class="text-sm font-medium text-red-800">Erreurs de validation</h4>
                </div>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agents.update', $agent) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nom et Prénom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom *
                        </label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $agent->nom) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('nom') border-red-500 @enderror"
                               placeholder="Ex: KOUASSI"
                               required>
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom *
                        </label>
                        <input type="text" 
                               id="prenom" 
                               name="prenom" 
                               value="{{ old('prenom', $agent->prenom) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('prenom') border-red-500 @enderror"
                               placeholder="Ex: Jean"
                               required>
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email et Téléphone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $agent->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="Ex: jean.kouassi@example.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone *
                        </label>
                        <input type="text" 
                               id="telephone" 
                               name="telephone" 
                               value="{{ old('telephone', $agent->telephone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('telephone') border-red-500 @enderror"
                               placeholder="Ex: +225 07 12 34 56 78"
                               required>
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Centre (affichage en lecture seule) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Centre
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 flex items-center">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        <span>{{ $agent->centre->nom ?? 'Non assigné' }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Le centre ne peut pas être modifié
                    </p>
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut
                    </label>
                    <select id="statut" 
                            name="statut" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('statut') border-red-500 @enderror">
                        <option value="actif" {{ old('statut', $agent->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $agent->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe (optionnel) -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau mot de passe (optionnel)
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-mayelia-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="Laisser vide pour ne pas modifier">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Laissez vide si vous ne souhaitez pas modifier le mot de passe</p>
                </div>

                <!-- Permissions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Permissions *
                    </label>
                    <p class="text-xs text-gray-500 mb-4">
                        Sélectionnez les permissions que cet agent pourra utiliser. Les permissions déterminent les actions que l'agent peut effectuer dans le système.
                    </p>
                    
                    @php
                        $agentPermissionIds = $agent->permissions->pluck('id')->toArray();
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-3 capitalize">
                                    <i class="fas fa-folder mr-2 text-mayelia-500"></i>
                                    {{ ucfirst($module) }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($modulePermissions as $permission)
                                        <label class="flex items-start space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   class="mt-1 h-4 w-4 text-mayelia-600 focus:ring-mayelia-500 border-gray-300 rounded"
                                                   {{ in_array($permission->id, old('permissions', $agentPermissionIds)) ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $permission->name }}</span>
                                                @if($permission->description)
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @error('permissions')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t">
                <a href="{{ route('agents.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white rounded-lg transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

