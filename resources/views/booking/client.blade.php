@extends('booking.layout')

@section('title', 'Informations client')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec récapitulatif -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Informations client</h1>
                    <div class="mt-2 text-sm text-gray-600">
                        <span id="selected-centre-info"></span> • 
                        <span id="selected-service-info"></span> • 
                        <span id="selected-formule-info"></span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        <span id="selected-date-info"></span> à <span id="selected-time-info"></span>
                    </div>
                </div>
                <button onclick="goBack()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </button>
            </div>
        </div>

        <!-- Formulaire client -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form id="client-form" onsubmit="submitForm(event)">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="prenom" name="prenom" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="telephone" name="telephone" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>

                    <!-- Date de naissance -->
                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de naissance
                        </label>
                        <input type="date" id="date_naissance" name="date_naissance"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>

                    <!-- Sexe -->
                    <div>
                        <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                            Sexe
                        </label>
                        <select id="sexe" name="sexe"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500">
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>

                    <!-- Adresse -->
                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse
                        </label>
                        <textarea id="adresse" name="adresse" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500"></textarea>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (optionnel)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500"
                                  placeholder="Informations supplémentaires..."></textarea>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="goBack()"
                            class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-md shadow-sm">
                        <i class="fas fa-credit-card mr-2"></i>
                        Procéder au paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let bookingData = {};

document.addEventListener('DOMContentLoaded', function() {
    loadBookingData();
    updateBookingInfo();
});

function loadBookingData() {
    // Récupérer les données de la session
    bookingData = {
        centreId: sessionStorage.getItem('selectedCentreId'),
        centreName: sessionStorage.getItem('selectedCentreName'),
        serviceId: sessionStorage.getItem('selectedServiceId'),
        serviceName: sessionStorage.getItem('selectedServiceName'),
        formuleId: sessionStorage.getItem('selectedFormuleId'),
        formuleName: sessionStorage.getItem('selectedFormuleName'),
        formulePrix: sessionStorage.getItem('selectedFormulePrix'),
        selectedDate: sessionStorage.getItem('selectedDate'),
        selectedTimeSlot: sessionStorage.getItem('selectedTimeSlot')
    };
}

function updateBookingInfo() {
    document.getElementById('selected-centre-info').textContent = bookingData.centreName || 'Centre non sélectionné';
    document.getElementById('selected-service-info').textContent = bookingData.serviceName || 'Service non sélectionné';
    document.getElementById('selected-formule-info').textContent = `${bookingData.formuleName || 'Formule non sélectionnée'} - ${bookingData.formulePrix || '0'} FCFA`;
    document.getElementById('selected-date-info').textContent = bookingData.selectedDate ? new Date(bookingData.selectedDate).toLocaleDateString('fr-FR') : 'Date non sélectionnée';
    document.getElementById('selected-time-info').textContent = bookingData.selectedTimeSlot || 'Créneau non sélectionné';
}

function submitForm(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const clientData = Object.fromEntries(formData.entries());
    
    // Ajouter les données de réservation
    const bookingRequest = {
        ...bookingData,
        ...clientData
    };
    
    // Simuler le paiement
    processPayment(bookingRequest);
}

function processPayment(bookingData) {
    // Afficher un modal de simulation de paiement
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-mayelia-100 mb-4">
                    <i class="fas fa-credit-card text-mayelia-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Simulation de paiement</h3>
                <p class="text-sm text-gray-500 mb-4">Montant: ${bookingData.formulePrix} FCFA</p>
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-mayelia-600 mx-auto mb-4"></div>
                <p class="text-sm text-gray-600">Traitement en cours...</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Simuler le traitement
    setTimeout(() => {
        document.body.removeChild(modal);
        // Rediriger vers la confirmation
        window.location.href = `/booking/confirmation/${bookingData.centreId}`;
    }, 3000);
}

function goBack() {
    window.location.href = '/booking/calendrier';
}
</script>
@endsection
