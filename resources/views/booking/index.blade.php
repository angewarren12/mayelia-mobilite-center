@extends('booking.layout')

@section('title', 'Réserver un rendez-vous')

@php
    $currentStep = 1;
@endphp

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Réservez votre rendez-vous en ligne
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Choisissez votre pays pour commencer la réservation
        </p>
    </div>

    <!-- Country Selection -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-globe-africa text-6xl text-blue-600 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                Sélectionnez votre pays
            </h3>
            <p class="text-gray-600">
                Choisissez le pays où vous souhaitez effectuer votre démarche
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Côte d'Ivoire -->
            <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer country-card"
                 data-pays-id="{{ $pays['id'] }}"
                 data-pays-nom="{{ $pays['nom'] }}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-flag text-2xl text-orange-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ $pays['nom'] }}
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Code: {{ $pays['code'] }}
                    </p>
                    <div class="flex items-center justify-center text-sm text-blue-600">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Continuer
                    </div>
                </div>
            </div>

            <!-- Placeholder pour d'autres pays -->
            <div class="border-2 border-gray-200 rounded-lg p-6 opacity-50">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plus text-2xl text-gray-400"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-500 mb-2">
                        Autres pays
                    </h4>
                    <p class="text-sm text-gray-400">
                        Bientôt disponible
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Gestion de la sélection du pays
    document.querySelectorAll('.country-card').forEach(card => {
        card.addEventListener('click', function() {
            const paysId = this.dataset.paysId;
            const paysNom = this.dataset.paysNom;
            
            // Stocker la sélection en session
            sessionStorage.setItem('booking_pays_id', paysId);
            sessionStorage.setItem('booking_pays_nom', paysNom);
            
            // Rediriger vers la sélection des villes
            window.location.href = `/booking/villes/${paysId}`;
        });
    });
</script>
@endsection
