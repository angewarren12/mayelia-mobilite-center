@extends('booking.layout')

@section('title', 'Sélection de la ville')

@php
    $currentStep = 2;
@endphp

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Choisissez votre ville
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Sélectionnez la ville où vous souhaitez effectuer votre démarche
        </p>
    </div>

    <!-- Ville Selection -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-map-marker-alt text-6xl text-mayelia-600 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                Villes disponibles
            </h3>
            <p class="text-gray-600">
                Choisissez la ville qui vous convient le mieux
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($villes->count() > 0)
                @foreach($villes as $ville)
                    <div class="border-2 border-mayelia-200 rounded-lg p-6 hover:border-mayelia-400 hover:shadow-md transition-all cursor-pointer ville-card"
                         data-ville-id="{{ $ville->id }}"
                         data-ville-nom="{{ $ville->nom }}">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-mayelia-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-city text-2xl text-mayelia-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $ville->nom }}
                            </h4>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ $ville->centres ? $ville->centres->count() : 0 }} centre(s) disponible(s)
                            </p>
                            <div class="flex items-center justify-center text-sm text-mayelia-600">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Continuer
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucune ville disponible pour ce pays</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex justify-between mt-8">
        <button onclick="goToPreviousStep('/booking')" 
                class="flex items-center px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour
        </button>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter les event listeners pour les cartes de ville
        document.querySelectorAll('.ville-card').forEach(card => {
            card.addEventListener('click', function() {
                const villeId = this.dataset.villeId;
                const villeNom = this.dataset.villeNom;
                
                // Stocker la sélection
                sessionStorage.setItem('booking_ville_id', villeId);
                sessionStorage.setItem('booking_ville_nom', villeNom);
                
                // Rediriger vers la sélection des centres
                window.location.href = `/booking/centres/${villeId}`;
            });
        });
    });
</script>
@endsection
