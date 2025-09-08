@extends('booking.layout')

@section('title', 'Sélection du service')

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Choisissez votre service
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Sélectionnez le type de démarche que vous souhaitez effectuer
        </p>
    </div>

    <!-- Service Selection -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-clipboard-list text-6xl text-blue-600 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                Services disponibles
            </h3>
            <p class="text-gray-600">
                Choisissez le service qui correspond à votre besoin
            </p>
        </div>

        <div id="services-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Les services seront chargés via AJAX -->
            <div class="col-span-full text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                <p class="text-gray-600">Chargement des services...</p>
            </div>
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
        loadServices();
    });

    function loadServices() {
        const centreId = sessionStorage.getItem('booking_centre_id');
        
        if (!centreId) {
            showToast('Veuillez d\'abord sélectionner un centre', 'error');
            goToPreviousStep('/booking');
            return;
        }
        
        fetch(`/booking/services/${centreId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayServices(data.services);
                } else {
                    showToast('Erreur lors du chargement des services', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors du chargement des services', 'error');
            });
    }

    function displayServices(services) {
        const container = document.getElementById('services-container');
        
        if (services.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucun service disponible dans ce centre</p>
                </div>
            `;
            return;
        }

        container.innerHTML = services.map(service => `
            <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer service-card"
                 data-service-id="${service.id}"
                 data-service-nom="${service.nom}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-purple-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        ${service.nom}
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">
                        ${service.description || 'Service de démarche administrative'}
                    </p>
                    <div class="text-sm text-gray-500 mb-4">
                        <i class="fas fa-tags mr-1"></i>
                        ${service.formules ? service.formules.length : 0} formule(s) disponible(s)
                    </div>
                    <div class="flex items-center justify-center text-sm text-blue-600">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Continuer
                    </div>
                </div>
            </div>
        `).join('');

        // Ajouter les event listeners
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                const serviceNom = this.dataset.serviceNom;
                
                // Stocker la sélection
                sessionStorage.setItem('booking_service_id', serviceId);
                sessionStorage.setItem('booking_service_nom', serviceNom);
                
                // Rediriger vers la sélection des formules
                goToNextStep(`/booking/formules/${sessionStorage.getItem('booking_centre_id')}/${serviceId}`, {
                    service_id: serviceId,
                    service_nom: serviceNom
                });
            });
        });
    }
</script>
@endsection
