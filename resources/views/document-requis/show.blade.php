@extends('layouts.dashboard')

@section('title', 'Détail du Document Requis')
@section('subtitle', 'Informations détaillées du document requis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- En-tête -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $documentRequis->nom_document }}</h1>
                <p class="text-gray-600">Détails du document requis</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('document-requis.edit', $documentRequis) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('document-requis.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Service -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Service</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $documentRequis->service->nom }}</p>
                <p class="text-sm text-gray-600">{{ $documentRequis->service->description }}</p>
            </div>

            <!-- Type de demande -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Type de demande</h3>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    @if($documentRequis->type_demande === 'Première demande') bg-blue-100 text-blue-800
                    @elseif($documentRequis->type_demande === 'Renouvellement') bg-green-100 text-green-800
                    @elseif($documentRequis->type_demande === 'Modification') bg-yellow-100 text-yellow-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    @if($documentRequis->type_demande === 'Première demande')
                        <i class="fas fa-plus-circle mr-1"></i>Première demande
                    @elseif($documentRequis->type_demande === 'Renouvellement')
                        <i class="fas fa-sync-alt mr-1"></i>Renouvellement
                    @elseif($documentRequis->type_demande === 'Modification')
                        <i class="fas fa-edit mr-1"></i>Modification
                    @else
                        <i class="fas fa-copy mr-1"></i>Duplicata
                    @endif
                </span>
            </div>
        </div>

        <!-- Détails du document -->
        <div class="space-y-6">
            <!-- Nom du document -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nom du document</h3>
                <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $documentRequis->nom_document }}</p>
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                @if($documentRequis->description)
                    <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $documentRequis->description }}</p>
                @else
                    <p class="text-gray-500 italic bg-gray-50 p-3 rounded-lg">Aucune description fournie</p>
                @endif
            </div>

            <!-- Statut et ordre -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Statut obligatoire -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Statut du document</h3>
                    @if($documentRequis->obligatoire)
                        <div class="flex items-center bg-red-50 p-3 rounded-lg">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="text-red-800 font-medium">Obligatoire</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Ce document est requis pour compléter la demande</p>
                    @else
                        <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                            <span class="text-gray-800 font-medium">Facultatif</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Ce document est optionnel</p>
                    @endif
                </div>

                <!-- Ordre d'affichage -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Ordre d'affichage</h3>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="text-2xl font-bold text-blue-600">{{ $documentRequis->ordre }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Position dans la liste des documents</p>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations système</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Créé le :</span>
                    {{ $documentRequis->created_at->format('d/m/Y à H:i') }}
                </div>
                <div>
                    <span class="font-medium">Modifié le :</span>
                    {{ $documentRequis->updated_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
            <form action="{{ route('document-requis.destroy', $documentRequis) }}" 
                  method="POST" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document requis ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
            </form>
            
            <div class="flex space-x-2">
                <a href="{{ route('document-requis.edit', $documentRequis) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('document-requis.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>Voir tous les documents
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
