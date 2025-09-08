@extends('booking.layout')

@section('title', 'Sélection du centre')

@php
    $currentStep = 3;
@endphp

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Choisissez votre centre
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Sélectionnez le centre Mayelia le plus proche de vous
        </p>
    </div>

    <!-- Centre Selection -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-building text-6xl text-blue-600 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                Centres disponibles
            </h3>
            <p class="text-gray-600">
                Choisissez le centre qui vous convient le mieux
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($centres->count() > 0)
                @foreach($centres as $centre)
                    <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer centre-card"
                         data-centre-id="{{ $centre->id }}"
                         data-centre-nom="{{ $centre->nom }}">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-building text-2xl text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $centre->nom }}
                            </h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $centre->ville ? $centre->ville->nom : 'Ville inconnue' }}
                            </p>
                            <div class="text-sm text-gray-500 mb-4">
                                <i class="fas fa-clock mr-1"></i>
                                Horaires: 08h00 - 15h00
                            </div>
                            <div class="flex items-center justify-center text-sm text-blue-600">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Continuer
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucun centre disponible dans cette ville</p>
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
        // Ajouter les event listeners pour les cartes de centre
        document.querySelectorAll('.centre-card').forEach(card => {
            card.addEventListener('click', function() {
                const centreId = this.dataset.centreId;
                const centreNom = this.dataset.centreNom;
                
                // Stocker la sélection
                sessionStorage.setItem('booking_centre_id', centreId);
                sessionStorage.setItem('booking_centre_nom', centreNom);
                
                // Rediriger vers la sélection des services
                window.location.href = `/booking/services/${centreId}`;
            });
        });
    });
</script>
@endsection
