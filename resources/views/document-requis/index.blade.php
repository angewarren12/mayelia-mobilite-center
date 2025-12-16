@extends('layouts.dashboard')

@section('title', 'Documents Requis')
@section('subtitle', 'Gestion des documents requis par service')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec bouton d'ajout -->
    <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4">
        @isAdmin
        <button onclick="openCreateModal()" 
                class="w-full sm:w-auto bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Ajouter un document
        </button>
        @endisAdmin
    </div>

    <!-- Système d'onglets par service -->
    <div class="bg-white rounded-lg shadow">
        <!-- Onglets -->
    <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('all')" 
                        class="tab-button active flex-shrink-0 py-4 px-6 border-b-2 border-mayelia-500 font-medium text-sm text-mayelia-600 whitespace-nowrap">
                    <i class="fas fa-list mr-2"></i>Tous les services
                </button>
                @foreach($services as $service)
                    <button onclick="switchTab('{{ $service->id }}')" 
                            class="tab-button flex-shrink-0 py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        <i class="fas fa-file-alt mr-2"></i>{{ $service->nom }}
                </button>
            @endforeach
        </nav>
    </div>

        <!-- Contenu des onglets -->
        <div class="p-4 sm:p-6">
            <!-- Filtres pour chaque onglet -->
            <div class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de demande</label>
                        <select id="filterTypeDemande" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            <option value="">Tous les types</option>
                            @foreach($typesDemande as $key => $label)
                                <option value="{{ $key }}">{{ $key }}</option>
                            @endforeach
                        </select>
            </div>
                    
            <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="filterStatut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    <option value="">Tous</option>
                    <option value="1">Obligatoire</option>
                            <option value="0">Facultatif</option>
                </select>
            </div>
                    
            <div class="flex items-end">
                        <button onclick="applyFilters()" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filtrer
                </button>
            </div>
        </div>
    </div>

            <!-- Contenu des documents par service -->
            <div id="documentsContent">
                <!-- Tous les services -->
                <div id="tab-all" class="tab-content">
                    @if($documentsRequis->count() > 0)
                        <!-- Version desktop -->
                        <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documentsRequis as $document)
                            <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $document->service->nom }}
                                </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($document->type_demande === 'Première demande') bg-mayelia-100 text-mayelia-800
                                                    @elseif($document->type_demande === 'Renouvellement') bg-green-100 text-green-800
                                                    @elseif($document->type_demande === 'Modification') bg-yellow-100 text-yellow-800
                                                    @else bg-purple-100 text-purple-800
                                                    @endif">
                                        {{ $document->type_demande }}
                                    </span>
                                </td>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                                {{ $document->nom_document }}
                                </td>
                                            <td class="px-4 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ $document->description ?? 'Aucune description' }}
                                </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                    @if($document->obligatoire)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>Obligatoire
                                        </span>
                                    @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        <i class="fas fa-info-circle mr-1"></i>Facultatif
                                        </span>
                                    @endif
                                </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $document->ordre }}
                                </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                                    <a href="{{ route('document-requis.show', $document) }}" 
                                                       class="text-mayelia-600 hover:text-mayelia-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @isAdmin
                                                    <button onclick="openEditModal({{ $document->id }})" 
                                                            class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('document-requis.destroy', $document) }}" 
                                                          method="POST" class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
                                            @csrf
                                            @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                                    @endisAdmin
                                    </div>
                                </td>
                            </tr>
                                    @endforeach
                    </tbody>
                </table>
                        </div>

                        <!-- Pagination -->
                        @if($documentsRequis->hasPages())
                            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                {{ $documentsRequis->links() }}
                            </div>
                        @endif

                        <!-- Version mobile -->
                        <div class="lg:hidden space-y-4">
                            @foreach($documentsRequis as $document)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $document->nom_document }}</h3>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('document-requis.show', $document) }}" 
                                               class="text-mayelia-600 hover:text-mayelia-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @isAdmin
                                            <button onclick="openEditModal({{ $document->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('document-requis.destroy', $document) }}" 
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endisAdmin
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span class="font-medium">Service:</span>
                                            <span>{{ $document->service->nom }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Type:</span>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($document->type_demande === 'Première demande') bg-mayelia-100 text-mayelia-800
                                                @elseif($document->type_demande === 'Renouvellement') bg-green-100 text-green-800
                                                @elseif($document->type_demande === 'Modification') bg-yellow-100 text-yellow-800
                                                @else bg-purple-100 text-purple-800
                                                @endif">
                                                {{ $document->type_demande }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Statut:</span>
                                            @if($document->obligatoire)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>Obligatoire
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    <i class="fas fa-info-circle mr-1"></i>Facultatif
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">Ordre:</span>
                                            <span>{{ $document->ordre }}</span>
                                        </div>
                                        @if($document->description)
                                            <div>
                                                <span class="font-medium">Description:</span>
                                                <p class="mt-1 text-gray-600">{{ Str::limit($document->description, 100) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination mobile -->
                        @if($documentsRequis->hasPages())
                            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-4">
                                {{ $documentsRequis->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun document requis</h3>
                            <p class="text-gray-500 mb-4">Commencez par ajouter des documents requis pour vos services.</p>
                            <button onclick="openCreateModal()" 
                                    class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Ajouter le premier document
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Documents par service -->
                @foreach($services as $service)
                    <div id="tab-{{ $service->id }}" class="tab-content hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->nom }}</h3>
                            <p class="text-sm text-gray-600">{{ $service->description }}</p>
                        </div>
                        
                        <div id="documents-{{ $service->id }}">
                            <!-- Les documents seront chargés via AJAX -->
                            <div class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500">Chargement des documents...</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Messages de succès/erreur -->
@if(session('success'))
    <div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-plus text-mayelia-600 mr-2"></i>
                        Ajouter un Document Requis
                    </h3>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="createForm" action="{{ route('document-requis.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Service <span class="text-red-500">*</span>
                            </label>
                            <select name="service_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Type de demande <span class="text-red-500">*</span>
                            </label>
                            <select name="type_demande" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                <option value="">Sélectionner un type</option>
                                @foreach($typesDemande as $key => $label)
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du document <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom_document" required
                               placeholder="Ex: Pièce d'identité, Justificatif de domicile..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  placeholder="Description détaillée du document requis..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Statut du document
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="obligatoire" value="1" checked
                                           class="mr-2 text-mayelia-600 focus:ring-mayelia-500">
                                    <span class="text-sm text-gray-700">
                                        <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                                        Obligatoire
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="obligatoire" value="0"
                                           class="mr-2 text-mayelia-600 focus:ring-mayelia-500">
                                    <span class="text-sm text-gray-700">
                                        <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                        Facultatif
                                    </span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ordre d'affichage <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="ordre" required min="0" value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeCreateModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-edit text-indigo-600 mr-2"></i>
                        Modifier le Document Requis
                    </h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="editForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Service <span class="text-red-500">*</span>
                            </label>
                            <select name="service_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Type de demande <span class="text-red-500">*</span>
                            </label>
                            <select name="type_demande" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                <option value="">Sélectionner un type</option>
                                @foreach($typesDemande as $key => $label)
                                    <option value="{{ $key }}">{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du document <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom_document" required
                               placeholder="Ex: Pièce d'identité, Justificatif de domicile..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                                  placeholder="Description détaillée du document requis..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Statut du document
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="obligatoire" value="1"
                                           class="mr-2 text-mayelia-600 focus:ring-mayelia-500">
                                    <span class="text-sm text-gray-700">
                                        <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                                        Obligatoire
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="obligatoire" value="0"
                                           class="mr-2 text-mayelia-600 focus:ring-mayelia-500">
                                    <span class="text-sm text-gray-700">
                                        <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                        Facultatif
                                    </span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ordre d'affichage <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="ordre" required min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
@php
    $authService = app(\App\Services\AuthService::class);
    $isAdmin = $authService->isAdmin();
@endphp
window.isAdmin = @json($isAdmin);

// Auto-hide toasts
setTimeout(() => {
    const successToast = document.getElementById('success-toast');
    const errorToast = document.getElementById('error-toast');
    
    if (successToast) successToast.remove();
    if (errorToast) errorToast.remove();
}, 5000);

// Gestion des onglets
function switchTab(serviceId) {
    // Masquer tous les contenus d'onglets
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Désactiver tous les boutons d'onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-mayelia-500', 'text-mayelia-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Activer l'onglet sélectionné
    const activeButton = document.querySelector(`[onclick="switchTab('${serviceId}')"]`);
    if (activeButton) {
        activeButton.classList.add('active', 'border-mayelia-500', 'text-mayelia-600');
        activeButton.classList.remove('border-transparent', 'text-gray-500');
    }
    
    // Afficher le contenu de l'onglet
    const activeContent = document.getElementById(`tab-${serviceId}`);
    if (activeContent) {
        activeContent.classList.remove('hidden');
        
        // Charger les documents du service si ce n'est pas "all"
        if (serviceId !== 'all') {
            loadServiceDocuments(serviceId);
        }
    }
}

// Charger les documents d'un service via AJAX
function loadServiceDocuments(serviceId) {
    const container = document.getElementById(`documents-${serviceId}`);
    if (container.querySelector('.loaded')) return; // Éviter les rechargements
    
    container.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Chargement des documents...</p>
        </div>
    `;
    
    fetch(`/api/services/${serviceId}/documents-requis`)
        .then(response => response.json())
        .then(documents => {
            if (documents.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun document requis</h3>
                        <p class="text-gray-500 mb-4">Aucun document requis pour ce service.</p>
                        <button onclick="openCreateModal()" 
                                class="bg-mayelia-600 text-white px-4 py-2 rounded-lg hover:bg-mayelia-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Ajouter un document
                        </button>
                    </div>
                `;
            } else {
                // Générer le HTML pour les documents
                let html = '';
                
                // Version desktop
                html += `
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                documents.forEach(doc => {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    ${doc.type_demande === 'Première demande' ? 'bg-mayelia-100 text-mayelia-800' :
                                      doc.type_demande === 'Renouvellement' ? 'bg-green-100 text-green-800' :
                                      doc.type_demande === 'Modification' ? 'bg-yellow-100 text-yellow-800' :
                                      'bg-purple-100 text-purple-800'}">
                                    ${doc.type_demande}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">${doc.nom_document}</td>
                            <td class="px-4 py-4 text-sm text-gray-500 max-w-xs truncate">${doc.description || 'Aucune description'}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                ${doc.obligatoire ? 
                                    '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Obligatoire</span>' :
                                    '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-info-circle mr-1"></i>Facultatif</span>'
                                }
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${doc.ordre}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="/document-requis/${doc.id}" class="text-mayelia-600 hover:text-mayelia-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    ${window.isAdmin ? `
                                    <button onclick="openEditModal(${doc.id})" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/document-requis/${doc.id}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Version mobile -->
                    <div class="lg:hidden space-y-4">
                `;
                
                documents.forEach(doc => {
                    html += `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-sm font-medium text-gray-900">${doc.nom_document}</h3>
                                <div class="flex space-x-2">
                                    <a href="/document-requis/${doc.id}" class="text-mayelia-600 hover:text-mayelia-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    ${window.isAdmin ? `
                                    <button onclick="openEditModal(${doc.id})" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/document-requis/${doc.id}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    ` : ''}
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span class="font-medium">Type:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        ${doc.type_demande === 'Première demande' ? 'bg-mayelia-100 text-mayelia-800' :
                                          doc.type_demande === 'Renouvellement' ? 'bg-green-100 text-green-800' :
                                          doc.type_demande === 'Modification' ? 'bg-yellow-100 text-yellow-800' :
                                          'bg-purple-100 text-purple-800'}">
                                        ${doc.type_demande}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Statut:</span>
                                    ${doc.obligatoire ? 
                                        '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Obligatoire</span>' :
                                        '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-info-circle mr-1"></i>Facultatif</span>'
                                    }
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Ordre:</span>
                                    <span>${doc.ordre}</span>
                                </div>
                                ${doc.description ? `
                                    <div>
                                        <span class="font-medium">Description:</span>
                                        <p class="mt-1 text-gray-600">${doc.description.length > 100 ? doc.description.substring(0, 100) + '...' : doc.description}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                    </div>
                `;
                
                container.innerHTML = html;
                container.classList.add('loaded');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-2"></i>
                    <p class="text-red-500">Erreur lors du chargement des documents</p>
                </div>
            `;
        });
}

// Appliquer les filtres
function applyFilters() {
    const typeDemande = document.getElementById('filterTypeDemande').value;
    const statut = document.getElementById('filterStatut').value;
    
    // Logique de filtrage à implémenter selon les besoins
    console.log('Filtres appliqués:', { typeDemande, statut });
}

// Modal functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createForm').reset();
}

function openEditModal(documentId) {
    fetch(`/document-requis/${documentId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('#editForm').action = `/document-requis/${documentId}`;
            document.querySelector('select[name="service_id"]').value = data.service_id;
            document.querySelector('select[name="type_demande"]').value = data.type_demande;
            document.querySelector('input[name="nom_document"]').value = data.nom_document;
            document.querySelector('textarea[name="description"]').value = data.description || '';
            document.querySelector(`input[name="obligatoire"][value="${data.obligatoire ? '1' : '0'}"]`).checked = true;
            document.querySelector('input[name="ordre"]').value = data.ordre;
            
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des données');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Fermer les modales en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        closeCreateModal();
        closeEditModal();
    }
});
</script>
@endsection