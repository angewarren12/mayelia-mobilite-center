@extends('booking.layout')

@section('title', 'Calendrier de disponibilité')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec informations de sélection -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Calendrier de disponibilité</h1>
                    <div class="mt-2 text-sm text-gray-600">
                        <span id="selected-centre-info"></span> • 
                        <span id="selected-service-info"></span> • 
                        <span id="selected-formule-info"></span>
                    </div>
                </div>
                <button onclick="goBack()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </button>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Sélectionnez une date</h2>
                    <div class="flex items-center space-x-4">
                        <button onclick="previousMonth()" class="p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="current-month" class="text-lg font-medium text-gray-900"></span>
                        <button onclick="nextMonth()" class="p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Grille du calendrier -->
                <div class="grid grid-cols-7 gap-1 mb-4">
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Lun</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Mar</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Mer</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Jeu</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Ven</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Sam</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Dim</div>
                </div>

                <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                    <!-- Les jours seront générés dynamiquement -->
                </div>
            </div>
        </div>

        <!-- Détails de disponibilité -->
        <div id="availability-details" class="mt-6 bg-white rounded-lg shadow-sm p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Créneaux disponibles</h3>
            <div id="time-slots" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <!-- Les créneaux seront chargés dynamiquement -->
            </div>
        </div>

        <!-- Bouton de confirmation -->
        <div id="confirm-booking" class="mt-6 hidden">
            <button onclick="confirmBooking()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md">
                Confirmer la réservation
            </button>
        </div>
    </div>
</div>

<script>
let currentDate = new Date();
let selectedDate = null;
let selectedTimeSlot = null;
let bookingData = {};

// Initialiser le calendrier
document.addEventListener('DOMContentLoaded', function() {
    loadBookingData();
    renderCalendar();
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
        formulePrix: sessionStorage.getItem('selectedFormulePrix')
    };
}

function updateBookingInfo() {
    document.getElementById('selected-centre-info').textContent = bookingData.centreName || 'Centre non sélectionné';
    document.getElementById('selected-service-info').textContent = bookingData.serviceName || 'Service non sélectionné';
    document.getElementById('selected-formule-info').textContent = `${bookingData.formuleName || 'Formule non sélectionnée'} - ${bookingData.formulePrix || '0'} FCFA`;
}

function renderCalendar() {
    const calendarGrid = document.getElementById('calendar-grid');
    const currentMonthElement = document.getElementById('current-month');
    
    // Vider la grille
    calendarGrid.innerHTML = '';
    
    // Mettre à jour le mois affiché
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    currentMonthElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    
    // Obtenir le premier jour du mois et le nombre de jours
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = (firstDay.getDay() + 6) % 7; // Convertir dimanche=0 à lundi=0
    
    // Ajouter les jours vides du mois précédent
    for (let i = 0; i < startingDayOfWeek; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'h-12';
        calendarGrid.appendChild(emptyDay);
    }
    
    // Ajouter les jours du mois
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
        
        dayElement.className = 'h-12 flex items-center justify-center text-sm cursor-pointer rounded-md hover:bg-gray-100';
        
        // Styling selon le statut du jour
        if (dayDate < today) {
            dayElement.className += ' text-gray-400 cursor-not-allowed';
        } else if (dayDate.toDateString() === today.toDateString()) {
            dayElement.className += ' bg-blue-100 text-blue-600 font-medium';
        } else {
            dayElement.className += ' text-gray-900 hover:bg-blue-50';
        }
        
        dayElement.textContent = day;
        dayElement.onclick = () => selectDate(dayDate);
        
        calendarGrid.appendChild(dayElement);
    }
}

function selectDate(date) {
    // Vérifier si la date est dans le passé
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (date < today) {
        return;
    }
    
    selectedDate = date;
    
    // Mettre à jour l'affichage
    document.querySelectorAll('#calendar-grid > div').forEach(day => {
        day.classList.remove('bg-blue-600', 'text-white');
        day.classList.add('text-gray-900', 'hover:bg-blue-50');
    });
    
    event.target.classList.remove('text-gray-900', 'hover:bg-blue-50');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    // Charger les créneaux disponibles
    loadAvailability(date);
}

function loadAvailability(date) {
    if (!bookingData.centreId) {
        console.error('Centre ID manquant');
        return;
    }
    
    const dateStr = date.toISOString().split('T')[0];
    const url = `/api/disponibilite/${bookingData.centreId}/${dateStr}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAvailability(data.disponibilite);
            } else {
                console.error('Erreur lors du chargement de la disponibilité:', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

function displayAvailability(availability) {
    const detailsElement = document.getElementById('availability-details');
    const timeSlotsElement = document.getElementById('time-slots');
    const confirmElement = document.getElementById('confirm-booking');
    
    if (!availability || availability.length === 0) {
        timeSlotsElement.innerHTML = '<p class="text-gray-500 col-span-full text-center py-4">Aucun créneau disponible pour cette date</p>';
        detailsElement.classList.remove('hidden');
        confirmElement.classList.add('hidden');
        return;
    }
    
    timeSlotsElement.innerHTML = '';
    
    availability.forEach(slot => {
        const slotElement = document.createElement('button');
        slotElement.className = 'p-3 text-sm border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 text-left';
        
        if (slot.disponible) {
            slotElement.className += ' bg-white text-gray-900';
            slotElement.onclick = () => selectTimeSlot(slot);
        } else {
            slotElement.className += ' bg-gray-100 text-gray-400 cursor-not-allowed';
        }
        
        slotElement.innerHTML = `
            <div class="font-medium">${slot.tranche_horaire}</div>
            <div class="text-xs text-gray-500">${slot.disponible ? 'Disponible' : 'Indisponible'}</div>
        `;
        
        timeSlotsElement.appendChild(slotElement);
    });
    
    detailsElement.classList.remove('hidden');
}

function selectTimeSlot(slot) {
    selectedTimeSlot = slot;
    
    // Mettre à jour l'affichage
    document.querySelectorAll('#time-slots > button').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-white', 'text-gray-900');
    });
    
    event.target.classList.remove('bg-white', 'text-gray-900');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    // Afficher le bouton de confirmation
    document.getElementById('confirm-booking').classList.remove('hidden');
}

function confirmBooking() {
    if (!selectedDate || !selectedTimeSlot) {
        alert('Veuillez sélectionner une date et un créneau');
        return;
    }
    
    // Sauvegarder les données de réservation
    sessionStorage.setItem('selectedDate', selectedDate.toISOString().split('T')[0]);
    sessionStorage.setItem('selectedTimeSlot', selectedTimeSlot.tranche_horaire);
    
    // Rediriger vers le formulaire client
    window.location.href = `/booking/client/${bookingData.centreId}/${bookingData.serviceId}/${bookingData.formuleId}/${selectedDate.toISOString().split('T')[0]}/${selectedTimeSlot.tranche_horaire}`;
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function goBack() {
    window.location.href = '/booking/wizard';
}
</script>
@endsection
