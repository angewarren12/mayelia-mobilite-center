@extends('booking.layout')

@section('title', 'Confirmation de réservation')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de succès -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Réservation confirmée !</h1>
            <p class="text-gray-600">Votre rendez-vous a été enregistré avec succès</p>
        </div>

        <!-- Détails de la réservation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Détails de votre réservation</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Numéro de suivi</span>
                    <span class="font-medium" id="tracking-number">#MAYELIA-2025-000000</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Centre</span>
                    <span class="font-medium" id="centre-name">Centre Mayelia San Pedro</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Service</span>
                    <span class="font-medium" id="service-name">Demande de CNI</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Formule</span>
                    <span class="font-medium" id="formule-name">VIP - 15,000 FCFA</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Date</span>
                    <span class="font-medium" id="appointment-date">Lundi 8 septembre 2025</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Heure</span>
                    <span class="font-medium" id="appointment-time">09h00 - 10h00</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Client</span>
                    <span class="font-medium" id="client-name">Jean Dupont</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium" id="client-email">jean.dupont@email.com</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Téléphone</span>
                    <span class="font-medium" id="client-phone">+225 07 12 34 56 78</span>
                </div>
                
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Statut</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Confirmé
                    </span>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-mayelia-50 border border-mayelia-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-mayelia-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Instructions importantes
            </h3>
            <ul class="text-sm text-mayelia-800 space-y-2">
                <li>• Présentez-vous 15 minutes avant votre rendez-vous</li>
                <li>• Apportez une pièce d'identité valide</li>
                <li>• Conservez ce numéro de suivi pour vos démarches</li>
                <li>• Vous recevrez un SMS de rappel 24h avant le rendez-vous</li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <button onclick="downloadReceipt()" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i>
                    Télécharger le reçu
                </button>
                
                <button onclick="printReceipt()" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-print mr-2"></i>
                    Imprimer
                </button>
                
                <button onclick="newBooking()" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-md shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvelle réservation
                </button>
            </div>
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
        selectedTimeSlot: sessionStorage.getItem('selectedTimeSlot'),
        clientName: sessionStorage.getItem('clientName'),
        clientEmail: sessionStorage.getItem('clientEmail'),
        clientPhone: sessionStorage.getItem('clientPhone')
    };
}

function updateBookingInfo() {
    // Générer un numéro de suivi aléatoire
    // Numéro temporaire (sera remplacé par le vrai numéro du backend - format: MAYELIA-YYYY-XXXXXX)
    const trackingNumber = '#MAYELIA-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 1000000)).padStart(6, '0');
    document.getElementById('tracking-number').textContent = trackingNumber;
    
    // Mettre à jour les informations
    document.getElementById('centre-name').textContent = bookingData.centreName || 'Centre non sélectionné';
    document.getElementById('service-name').textContent = bookingData.serviceName || 'Service non sélectionné';
    document.getElementById('formule-name').textContent = `${bookingData.formuleName || 'Formule non sélectionnée'} - ${bookingData.formulePrix || '0'} FCFA`;
    
    if (bookingData.selectedDate) {
        const date = new Date(bookingData.selectedDate);
        document.getElementById('appointment-date').textContent = date.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    document.getElementById('appointment-time').textContent = bookingData.selectedTimeSlot || 'Créneau non sélectionné';
    document.getElementById('client-name').textContent = bookingData.clientName || 'Client non renseigné';
    document.getElementById('client-email').textContent = bookingData.clientEmail || 'Email non renseigné';
    document.getElementById('client-phone').textContent = bookingData.clientPhone || 'Téléphone non renseigné';
}

function downloadReceipt() {
    // Simuler le téléchargement du reçu
    const receiptContent = generateReceiptContent();
    const blob = new Blob([receiptContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `recu-reservation-${document.getElementById('tracking-number').textContent}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function printReceipt() {
    window.print();
}

function newBooking() {
    // Nettoyer la session
    sessionStorage.clear();
    window.location.href = '/booking/wizard';
}

function generateReceiptContent() {
    const trackingNumber = document.getElementById('tracking-number').textContent;
    const centreName = document.getElementById('centre-name').textContent;
    const serviceName = document.getElementById('service-name').textContent;
    const formuleName = document.getElementById('formule-name').textContent;
    const appointmentDate = document.getElementById('appointment-date').textContent;
    const appointmentTime = document.getElementById('appointment-time').textContent;
    const clientName = document.getElementById('client-name').textContent;
    const clientEmail = document.getElementById('client-email').textContent;
    const clientPhone = document.getElementById('client-phone').textContent;
    
    return `
RECU DE RESERVATION
==================

Numéro de suivi: ${trackingNumber}
Date d'émission: ${new Date().toLocaleDateString('fr-FR')}

DETAILS DE LA RESERVATION
========================
Centre: ${centreName}
Service: ${serviceName}
Formule: ${formuleName}
Date: ${appointmentDate}
Heure: ${appointmentTime}

INFORMATIONS CLIENT
==================
Nom: ${clientName}
Email: ${clientEmail}
Téléphone: ${clientPhone}

INSTRUCTIONS
============
- Présentez-vous 15 minutes avant votre rendez-vous
- Apportez une pièce d'identité valide
- Conservez ce numéro de suivi pour vos démarches

Merci pour votre confiance !
    `.trim();
}
</script>
@endsection
