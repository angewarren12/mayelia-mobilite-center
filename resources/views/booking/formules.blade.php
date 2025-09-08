@extends('booking.layout')

@section('title', 'Sélection de la formule')

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Choisissez votre formule
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Sélectionnez la formule qui correspond à vos besoins
        </p>
    </div>

    <!-- Formule Selection -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-star text-6xl text-blue-600 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                Formules disponibles
            </h3>
            <p class="text-gray-600">
                Choisissez la formule qui vous convient le mieux
            </p>
        </div>

        <div id="formules-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Les formules seront chargées via AJAX -->
            <div class="col-span-full text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                <p class="text-gray-600">Chargement des formules...</p>
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
        loadFormules();
    });

    function loadFormules() {
        const centreId = sessionStorage.getItem('booking_centre_id');
        const serviceId = sessionStorage.getItem('booking_service_id');
        
        if (!centreId || !serviceId) {
            showToast('Veuillez d\'abord sélectionner un centre et un service', 'error');
            goToPreviousStep('/booking');
            return;
        }
        
        fetch(`/booking/formules/${centreId}/${serviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayFormules(data.formules, data.service);
                } else {
                    showToast('Erreur lors du chargement des formules', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors du chargement des formules', 'error');
            });
    }

    function displayFormules(formules, service) {
        const container = document.getElementById('formules-container');
        
        if (formules.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucune formule disponible pour ce service</p>
                </div>
            `;
            return;
        }

        // Afficher le service sélectionné
        const serviceInfo = document.createElement('div');
        serviceInfo.className = 'col-span-full bg-blue-50 rounded-lg p-4 mb-6';
        serviceInfo.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-file-alt text-2xl text-blue-600 mr-4"></i>
                <div>
                    <h4 class="text-lg font-semibold text-blue-900">${service.nom}</h4>
                    <p class="text-sm text-blue-700">${service.description || 'Service de démarche administrative'}</p>
                </div>
            </div>
        `;
        container.appendChild(serviceInfo);

        // Afficher les formules
        const formulesHtml = formules.map(formule => {
            const isVip = formule.nom.toLowerCase().includes('vip');
            const bgColor = isVip ? 'bg-yellow-100' : 'bg-green-100';
            const iconColor = isVip ? 'text-yellow-600' : 'text-green-600';
            const borderColor = isVip ? 'border-yellow-300' : 'border-green-300';
            
            return `
                <div class="border-2 ${borderColor} rounded-lg p-6 hover:shadow-md transition-all cursor-pointer formule-card ${bgColor}"
                     data-formule-id="${formule.id}"
                     data-formule-nom="${formule.nom}"
                     data-formule-prix="${formule.prix}">
                    <div class="text-center">
                        <div class="w-16 h-16 ${bgColor} rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas ${isVip ? 'fa-crown' : 'fa-check-circle'} text-2xl ${iconColor}"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            ${formule.nom}
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">
                            ${formule.description || 'Formule de service'}
                        </p>
                        <div class="text-2xl font-bold text-gray-900 mb-4">
                            ${parseFloat(formule.prix).toLocaleString()} FCFA
                        </div>
                        <div class="flex items-center justify-center text-sm text-blue-600">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Continuer
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = serviceInfo.outerHTML + formulesHtml;

        // Ajouter les event listeners
        document.querySelectorAll('.formule-card').forEach(card => {
            card.addEventListener('click', function() {
                const formuleId = this.dataset.formuleId;
                const formuleNom = this.dataset.formuleNom;
                const formulePrix = this.dataset.formulePrix;
                
                // Stocker la sélection
                sessionStorage.setItem('booking_formule_id', formuleId);
                sessionStorage.setItem('booking_formule_nom', formuleNom);
                sessionStorage.setItem('booking_formule_prix', formulePrix);
                
                // Rediriger vers le calendrier
                goToNextStep(`/booking/calendrier/${sessionStorage.getItem('booking_centre_id')}/${sessionStorage.getItem('booking_service_id')}/${formuleId}`, {
                    formule_id: formuleId,
                    formule_nom: formuleNom,
                    formule_prix: formulePrix
                });
            });
        });
    }
</script>
@endsection
