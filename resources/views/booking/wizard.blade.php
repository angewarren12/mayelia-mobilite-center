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
            Suivez les étapes pour réserver votre créneau
        </p>
        
        <!-- Barre de progression -->
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div id="step-indicator-1" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">1</div>
                    <span class="ml-2 text-sm font-medium text-gray-700">Pays</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div id="progress-bar-1" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex items-center">
                    <div id="step-indicator-2" class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">2</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Ville</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div id="progress-bar-2" class="h-full bg-gray-200 transition-all duration-300"></div>
                </div>
                <div class="flex items-center">
                    <div id="step-indicator-3" class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Centre</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div id="progress-bar-3" class="h-full bg-gray-200 transition-all duration-300"></div>
                </div>
                <div class="flex items-center">
                    <div id="step-indicator-4" class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">4</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Service</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div id="progress-bar-4" class="h-full bg-gray-200 transition-all duration-300"></div>
                </div>
                <div class="flex items-center">
                    <div id="step-indicator-5" class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">5</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Formule</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div id="progress-bar-5" class="h-full bg-gray-200 transition-all duration-300"></div>
                </div>
                <div class="flex items-center">
                    <div id="step-indicator-6" class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">6</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Créneau</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Container -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        
        <!-- Étape 1: Sélection du pays -->
        <div id="step-pays" class="wizard-step active">
            <div class="text-center mb-8">
                <i class="fas fa-globe-africa text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 1: Sélectionnez votre pays
                </h3>
                <p class="text-gray-600">
                    Choisissez le pays où vous souhaitez effectuer votre démarche
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Côte d'Ivoire -->
                <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer country-card"
                     data-pays-id="1" data-pays-nom="Côte d'Ivoire">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-flag text-2xl text-orange-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            Côte d'Ivoire
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">
                            Code: CI
                        </p>
                        <div class="flex items-center justify-center text-sm text-blue-600">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Continuer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Étape 2: Sélection de la ville -->
        <div id="step-ville" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-map-marker-alt text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 2: Choisissez votre ville
                </h3>
                <p class="text-gray-600">
                    Sélectionnez la ville où vous souhaitez effectuer votre démarche
                </p>
            </div>

            <div id="villes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Chargement des villes...</p>
                </div>
            </div>
        </div>

        <!-- Étape 3: Sélection du centre -->
        <div id="step-centre" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-building text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 3: Choisissez votre centre
                </h3>
                <p class="text-gray-600">
                    Sélectionnez le centre Mayelia le plus proche de vous
                </p>
            </div>

            <div id="centres-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Les centres seront chargés dynamiquement -->
            </div>
        </div>

        <!-- Étape 4: Sélection du service -->
        <div id="step-service" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-clipboard-list text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 4: Choisissez votre service
                </h3>
                <p class="text-gray-600">
                    Sélectionnez le type de démarche que vous souhaitez effectuer
                </p>
            </div>

            <div id="services-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Les services seront chargés dynamiquement -->
            </div>
        </div>

        <!-- Étape 5: Sélection de la formule -->
        <div id="step-formule" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-star text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 5: Choisissez votre formule
                </h3>
                <p class="text-gray-600">
                    Sélectionnez la formule qui correspond à vos besoins
                </p>
            </div>

            <div id="formules-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Les formules seront chargées dynamiquement -->
            </div>
        </div>

        <!-- Étape 6: Calendrier et disponibilités -->
        <div id="step-calendrier" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-calendar-alt text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 6: Choisissez votre créneau
                </h3>
                <p class="text-gray-600">
                    Sélectionnez la date et l'heure qui vous conviennent
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Calendrier -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-gray-900">Calendrier</h4>
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
                    
                    <!-- Légende des indicateurs -->
                    <div class="mt-4 flex items-center justify-center space-x-6 text-xs">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                            <span>Disponible (5+ créneaux)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></div>
                            <span>Peu disponible (1-4 créneaux)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                            <span>Indisponible</span>
                        </div>
                    </div>
                </div>

                <!-- Carte de disponibilité -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Disponibilités</h4>
                    
                    <div id="availability-info" class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-plus text-3xl mb-4"></i>
                        <p>Sélectionnez une date pour voir les créneaux disponibles</p>
                    </div>

                    <div id="availability-details" class="hidden">
                        <div class="mb-4">
                            <h5 class="font-medium text-gray-900" id="selected-date-title"></h5>
                            <p class="text-sm text-gray-600" id="selected-date-info"></p>
                        </div>

                        <div id="time-slots" class="space-y-2 max-h-96 overflow-y-auto">
                            <!-- Les créneaux seront chargés dynamiquement -->
                        </div>

                        <div id="no-slots" class="text-center py-8 text-gray-500 hidden">
                            <i class="fas fa-times-circle text-3xl mb-4"></i>
                            <p>Aucun créneau disponible pour cette date</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Étape 7: Vérification client -->
        <div id="step-client" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-user text-6xl text-blue-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 7: Vérification client
                </h3>
                <p class="text-gray-600">
                    Avez-vous déjà pris un rendez-vous chez Mayelia ?
                </p>
            </div>

            <div class="max-w-md mx-auto">
                <!-- Question initiale -->
                <div id="client-question" class="space-y-4">
                    <div class="flex space-x-4 justify-center">
                        <button onclick="checkExistingClient(true)" 
                                class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Oui, j'ai déjà un compte
                        </button>
                        <button onclick="checkExistingClient(false)" 
                                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>
                            Non, je suis nouveau
                        </button>
                    </div>
                </div>

                <!-- Formulaire de vérification par téléphone -->
                <div id="phone-verification" class="hidden">
                    <form id="phone-form" onsubmit="verifyPhoneNumber(event)">
                        <div class="mb-6">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de téléphone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required
                                   placeholder="Ex: +225 07 12 34 56 78"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Le numéro de téléphone est votre identifiant</p>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="button" onclick="goBackToQuestion()" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                                <i class="fas fa-search mr-2"></i>
                                Vérifier
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Formulaire d'inscription pour nouveaux clients -->
                <div id="new-client-form" class="hidden">
                    <form id="client-form" onsubmit="submitClientForm(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom -->
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nom" name="nom" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Prénom -->
                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="prenom" name="prenom" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="telephone" name="telephone" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Date de naissance -->
                            <div>
                                <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de naissance
                                </label>
                                <input type="date" id="date_naissance" name="date_naissance"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Sexe -->
                            <div>
                                <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sexe
                                </label>
                                <select id="sexe" name="sexe"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes (optionnel)
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Informations supplémentaires..."></textarea>
                            </div>
                        </div>
                        
                        <div class="flex space-x-4 mt-6">
                            <button type="button" onclick="goBackToQuestion()" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer et continuer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Affichage des informations client existant -->
                <div id="existing-client-info" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                            <h4 class="text-lg font-semibold text-green-900">Client trouvé !</h4>
                        </div>
                        <div id="client-details" class="text-sm text-green-800">
                            <!-- Les détails du client seront affichés ici -->
                        </div>
                        <div class="mt-4">
                            <button onclick="proceedWithExistingClient()" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Continuer avec ce compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-8">
            <button id="btn-previous" onclick="previousStep()" 
                    class="flex items-center px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors hidden">
                <i class="fas fa-arrow-left mr-2"></i>
                Précédent
            </button>
            
            <button id="btn-next" onclick="nextStep()" 
                    class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors ml-auto">
                <span>Suivant</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>

    <!-- Résumé de sélection -->
    <div id="selection-summary" class="mt-8 bg-blue-50 rounded-lg p-6 hidden">
        <h4 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            Votre sélection
        </h4>
        <div id="summary-content" class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">
            <!-- Le résumé sera généré dynamiquement -->
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentStepNumber = 1;
    let selectedData = {
        pays: null,
        ville: null,
        centre: null,
        service: null,
        formule: null
    };

    document.addEventListener('DOMContentLoaded', function() {
        initializeWizard();
    });

    function initializeWizard() {
        // Gestion de la sélection du pays
        document.querySelectorAll('.country-card').forEach(card => {
            card.addEventListener('click', function() {
                selectPays(this.dataset.paysId, this.dataset.paysNom);
            });
        });
    }

    function selectPays(paysId, paysNom) {
        selectedData.pays = { id: paysId, nom: paysNom };
        loadVilles(paysId);
        showStep('step-ville');
        updateSummary();
    }

    function selectVille(villeId, villeNom) {
        selectedData.ville = { id: villeId, nom: villeNom };
        loadCentres(villeId);
        showStep('step-centre');
        updateSummary();
    }

    function selectCentre(centreId, centreNom) {
        selectedData.centre = { id: centreId, nom: centreNom };
        loadServices(centreId);
        showStep('step-service');
        updateSummary();
    }

    function selectService(serviceId, serviceNom) {
        selectedData.service = { id: serviceId, nom: serviceNom };
        loadFormules(selectedData.centre.id, serviceId);
        showStep('step-formule');
        updateSummary();
    }

    function selectFormule(formuleId, formuleNom, formulePrix) {
        selectedData.formule = { id: formuleId, nom: formuleNom, prix: formulePrix };
        updateSummary();
        
        // Afficher l'étape calendrier
        showStep('step-calendrier');
        initializeCalendar();
    }

    function loadVilles(paysId) {
        fetch(`/booking/villes/${paysId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayVilles(data.villes);
                } else {
                    showToast('Erreur lors du chargement des villes', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors du chargement des villes', 'error');
            });
    }

    function displayVilles(villes) {
        const container = document.getElementById('villes-container');
        
        if (villes.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucune ville disponible pour ce pays</p>
                </div>
            `;
            return;
        }

        container.innerHTML = villes.map(ville => `
            <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer ville-card"
                 data-ville-id="${ville.id}" data-ville-nom="${ville.nom}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-city text-2xl text-blue-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">${ville.nom}</h4>
                    <p class="text-sm text-gray-600 mb-4">${ville.centres ? ville.centres.length : 0} centre(s)</p>
                    <div class="flex items-center justify-center text-sm text-blue-600">
                        <i class="fas fa-arrow-right mr-2"></i>Continuer
                    </div>
                </div>
            </div>
        `).join('');

        // Ajouter les event listeners
        document.querySelectorAll('.ville-card').forEach(card => {
            card.addEventListener('click', function() {
                selectVille(this.dataset.villeId, this.dataset.villeNom);
            });
        });
    }

    function loadCentres(villeId) {
        fetch(`/booking/centres/${villeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCentres(data.centres);
                } else {
                    showToast('Erreur lors du chargement des centres', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors du chargement des centres', 'error');
            });
    }

    function displayCentres(centres) {
        const container = document.getElementById('centres-container');
        
        if (centres.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600">Aucun centre disponible dans cette ville</p>
                </div>
            `;
            return;
        }

        container.innerHTML = centres.map(centre => `
            <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-md transition-all cursor-pointer centre-card"
                 data-centre-id="${centre.id}" data-centre-nom="${centre.nom}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-2xl text-green-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">${centre.nom}</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-map-marker-alt mr-1"></i>${centre.ville ? centre.ville.nom : 'Ville inconnue'}
                    </p>
                    <div class="text-sm text-gray-500 mb-4">
                        <i class="fas fa-clock mr-1"></i>Horaires: 08h00 - 15h00
                    </div>
                    <div class="flex items-center justify-center text-sm text-blue-600">
                        <i class="fas fa-arrow-right mr-2"></i>Continuer
                    </div>
                </div>
            </div>
        `).join('');

        // Ajouter les event listeners
        document.querySelectorAll('.centre-card').forEach(card => {
            card.addEventListener('click', function() {
                selectCentre(this.dataset.centreId, this.dataset.centreNom);
            });
        });
    }

    function loadServices(centreId) {
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
                 data-service-id="${service.id}" data-service-nom="${service.nom}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-purple-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">${service.nom}</h4>
                    <p class="text-sm text-gray-600 mb-4">${service.description || 'Service de démarche administrative'}</p>
                    <div class="text-sm text-gray-500 mb-4">
                        <i class="fas fa-tags mr-1"></i>${service.formules ? service.formules.length : 0} formule(s)
                    </div>
                    <div class="flex items-center justify-center text-sm text-blue-600">
                        <i class="fas fa-arrow-right mr-2"></i>Continuer
                    </div>
                </div>
            </div>
        `).join('');

        // Ajouter les event listeners
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                selectService(this.dataset.serviceId, this.dataset.serviceNom);
            });
        });
    }

    function loadFormules(centreId, serviceId) {
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
                     data-formule-id="${formule.id}" data-formule-nom="${formule.nom}" data-formule-prix="${formule.prix}">
                    <div class="text-center">
                        <div class="w-16 h-16 ${bgColor} rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas ${isVip ? 'fa-crown' : 'fa-check-circle'} text-2xl ${iconColor}"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">${formule.nom}</h4>
                        <p class="text-sm text-gray-600 mb-4">${formule.description || 'Formule de service'}</p>
                        <div class="text-2xl font-bold text-gray-900 mb-4">${parseFloat(formule.prix).toLocaleString()} FCFA</div>
                        <div class="flex items-center justify-center text-sm text-blue-600">
                            <i class="fas fa-arrow-right mr-2"></i>Continuer
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = serviceInfo.outerHTML + formulesHtml;

        // Ajouter les event listeners
        document.querySelectorAll('.formule-card').forEach(card => {
            card.addEventListener('click', function() {
                selectFormule(this.dataset.formuleId, this.dataset.formuleNom, this.dataset.formulePrix);
            });
        });
    }

    function showStep(stepId) {
        // Cacher toutes les étapes
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.classList.add('hidden');
            step.classList.remove('active');
        });
        
        // Afficher l'étape sélectionnée
        document.getElementById(stepId).classList.remove('hidden');
        document.getElementById(stepId).classList.add('active');
        
        // Mettre à jour le numéro d'étape
        currentStepNumber = parseInt(stepId.split('-')[1].replace('step', ''));
        updateNavigation();
        updateStepIndicators();
    }

    function updateStepIndicators() {
        // Mettre à jour tous les indicateurs
        for (let i = 1; i <= 6; i++) {
            const indicator = document.getElementById(`step-indicator-${i}`);
            const progressBar = document.getElementById(`progress-bar-${i}`);
            
            // Vérifier que les éléments existent
            if (!indicator || !progressBar) {
                console.warn(`Élément manquant pour l'étape ${i}`);
                continue;
            }
            
            // Trouver le label (span qui suit l'indicateur)
            const label = indicator.parentElement.querySelector('span');
            
            if (i < currentStepNumber) {
                // Étapes complétées
                indicator.className = 'w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-medium';
                progressBar.className = 'h-full bg-green-600 transition-all duration-300';
                if (label) label.className = 'ml-2 text-sm font-medium text-green-600';
            } else if (i === currentStepNumber) {
                // Étape actuelle
                indicator.className = 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium';
                progressBar.className = 'h-full bg-blue-600 transition-all duration-300';
                if (label) label.className = 'ml-2 text-sm font-medium text-blue-600';
            } else {
                // Étapes futures
                indicator.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium';
                progressBar.className = 'h-full bg-gray-200 transition-all duration-300';
                if (label) label.className = 'ml-2 text-sm font-medium text-gray-500';
            }
        }
    }

    function updateNavigation() {
        const btnPrevious = document.getElementById('btn-previous');
        const btnNext = document.getElementById('btn-next');
        
        // Afficher/masquer le bouton précédent
        if (currentStepNumber > 1) {
            btnPrevious.classList.remove('hidden');
        } else {
            btnPrevious.classList.add('hidden');
        }
        
        // Gérer le bouton suivant
        if (currentStepNumber === 7) { // Dernière étape
            btnNext.style.display = 'none';
        } else {
            btnNext.style.display = 'flex';
            
            // Changer le texte du bouton suivant selon l'étape
            if (currentStepNumber === 6) {
                btnNext.innerHTML = `
                    <i class="fas fa-check mr-2"></i>
                    Confirmer la réservation
                `;
                btnNext.onclick = confirmBooking;
            } else if (currentStepNumber === 7) {
                btnNext.innerHTML = `
                    <i class="fas fa-credit-card mr-2"></i>
                    Procéder au paiement
                `;
                btnNext.onclick = () => {
                    const form = document.getElementById('client-form');
                    if (form) {
                        form.dispatchEvent(new Event('submit'));
                    }
                };
            } else {
                btnNext.innerHTML = `
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                `;
                btnNext.onclick = nextStep;
            }
        }
    }

    function nextStep() {
        if (currentStepNumber === 5) {
            // Finaliser la sélection
            if (selectedData.formule) {
                window.location.href = `/booking/calendrier/${selectedData.centre.id}/${selectedData.service.id}/${selectedData.formule.id}`;
            }
        }
    }

    function previousStep() {
        if (currentStepNumber > 1) {
            const steps = ['step-pays', 'step-ville', 'step-centre', 'step-service', 'step-formule', 'step-calendrier', 'step-client'];
            showStep(steps[currentStepNumber - 2]);
        }
    }

    function checkExistingClient(isExisting) {
        if (isExisting) {
            // Afficher le formulaire de vérification par téléphone
            document.getElementById('client-question').classList.add('hidden');
            document.getElementById('phone-verification').classList.remove('hidden');
        } else {
            // Afficher le formulaire d'inscription
            document.getElementById('client-question').classList.add('hidden');
            document.getElementById('new-client-form').classList.remove('hidden');
        }
    }

    function goBackToQuestion() {
        // Cacher tous les formulaires et afficher la question initiale
        document.getElementById('client-question').classList.remove('hidden');
        document.getElementById('phone-verification').classList.add('hidden');
        document.getElementById('new-client-form').classList.add('hidden');
        document.getElementById('existing-client-info').classList.add('hidden');
    }

    function verifyPhoneNumber(event) {
        event.preventDefault();
        
        const phone = document.getElementById('phone').value;
        
        // Vérifier le numéro de téléphone
        fetch('/api/check-client', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ telephone: phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.client) {
                // Client trouvé
                displayExistingClient(data.client);
            } else {
                // Client non trouvé
                showToast('Aucun client trouvé avec ce numéro de téléphone', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la vérification', 'error');
        });
    }

    function displayExistingClient(client) {
        // Afficher les informations du client
        document.getElementById('client-details').innerHTML = `
            <div class="space-y-2">
                <div><strong>Nom:</strong> ${client.prenom} ${client.nom}</div>
                <div><strong>Email:</strong> ${client.email}</div>
                <div><strong>Téléphone:</strong> ${client.telephone}</div>
                ${client.date_naissance ? `<div><strong>Date de naissance:</strong> ${new Date(client.date_naissance).toLocaleDateString('fr-FR')}</div>` : ''}
                ${client.adresse ? `<div><strong>Adresse:</strong> ${client.adresse}</div>` : ''}
            </div>
        `;
        
        // Cacher le formulaire de vérification et afficher les infos client
        document.getElementById('phone-verification').classList.add('hidden');
        document.getElementById('existing-client-info').classList.remove('hidden');
        
        // Stocker les données du client
        selectedData.client = client;
    }

    function proceedWithExistingClient() {
        // Mettre à jour le résumé
        updateSummary();
        
        // Passer directement au paiement
        processPayment();
    }

    function submitClientForm(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const clientData = Object.fromEntries(formData.entries());
        
        // Vérifier si le numéro existe déjà
        fetch('/api/check-client', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ telephone: clientData.telephone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.client) {
                // Le numéro existe déjà
                showToast('Ce numéro de téléphone est déjà utilisé. Veuillez utiliser un autre numéro ou vous connecter avec ce numéro.', 'error');
            } else {
                // Créer le nouveau client
                createNewClient(clientData);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la vérification', 'error');
        });
    }

    function createNewClient(clientData) {
        // Créer le nouveau client
        fetch('/api/create-client', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(clientData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Client créé avec succès
                selectedData.client = data.client;
                updateSummary();
                processPayment();
            } else {
                showToast('Erreur lors de la création du client: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la création du client', 'error');
        });
    }

    function processPayment() {
        // Afficher un modal de simulation de paiement
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Simulation de paiement</h3>
                    <p class="text-sm text-gray-500 mb-4">Montant: ${selectedData.formule.prix} FCFA</p>
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-sm text-gray-600">Traitement en cours...</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Préparer les données du rendez-vous
        const rendezVousData = {
            centre_id: selectedData.centre.id,
            service_id: selectedData.service.id,
            formule_id: selectedData.formule.id,
            client_id: selectedData.client.id,
            date_rendez_vous: selectedData.date.date, // Format YYYY-MM-DD déjà stocké
            tranche_horaire: selectedData.timeSlot.tranche, // Extraire la chaîne de caractères
            notes: ''
        };
        
        console.log('Données du rendez-vous:', rendezVousData);
        
        // Appeler l'API pour créer le rendez-vous
        fetch('/api/create-rendez-vous', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(rendezVousData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Réponse API:', data);
            
            if (data.success) {
                // Stocker le numéro de suivi
                selectedData.numeroSuivi = data.numero_suivi;
                selectedData.rendezVous = data.rendez_vous;
                
                // Retirer le modal
                document.body.removeChild(modal);
                
                // Afficher la confirmation
                showConfirmation();
            } else {
                throw new Error(data.message || 'Erreur lors de la création du rendez-vous');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            
            // Retirer le modal
            document.body.removeChild(modal);
            
            // Afficher une erreur
            showToast('Erreur lors de la création du rendez-vous: ' + error.message, 'error');
        });
    }

    function showConfirmation() {
        // Utiliser le numéro de suivi généré par l'API
        const trackingNumber = selectedData.numeroSuivi || '#RDV-' + new Date().getFullYear() + '-' + Math.random().toString(36).substr(2, 6).toUpperCase();
        
        // Afficher la confirmation
        const confirmationHtml = `
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Réservation confirmée !</h3>
                <p class="text-gray-600 mb-6">Votre rendez-vous a été enregistré avec succès</p>
                
                <div class="bg-gray-50 rounded-lg p-6 text-left max-w-md mx-auto">
                    <h4 class="font-semibold text-gray-900 mb-4">Détails de votre réservation</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Numéro de suivi:</span>
                            <span class="font-medium">${trackingNumber}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Centre:</span>
                            <span class="font-medium">${selectedData.centre.nom}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service:</span>
                            <span class="font-medium">${selectedData.service.nom}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Formule:</span>
                            <span class="font-medium">${selectedData.formule.nom} - ${selectedData.formule.prix} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">${selectedData.date.display}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Heure:</span>
                            <span class="font-medium">${selectedData.timeSlot.tranche}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Client:</span>
                            <span class="font-medium">${selectedData.client.prenom} ${selectedData.client.nom}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    <button onclick="downloadReceipt()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger le reçu
                    </button>
                    <button onclick="newBooking()" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle réservation
                    </button>
                </div>
            </div>
        `;
        
        // Remplacer le contenu du wizard
        document.querySelector('.bg-white.rounded-lg.shadow-lg.p-8').innerHTML = confirmationHtml;
    }

    function downloadReceipt() {
        // Télécharger le reçu PDF depuis le serveur
        if (selectedData.rendezVous && selectedData.rendezVous.id) {
            const url = `/receipt/${selectedData.rendezVous.id}/download`;
            window.open(url, '_blank');
        } else {
            showToast('Erreur: Impossible de télécharger le reçu', 'error');
        }
    }

    function newBooking() {
        // Recharger la page pour recommencer
        window.location.reload();
    }

    function generateReceiptContent() {
        return `
RECU DE RESERVATION
==================

Numéro de suivi: #RDV-${new Date().getFullYear()}-${Math.random().toString(36).substr(2, 6).toUpperCase()}
Date d'émission: ${new Date().toLocaleDateString('fr-FR')}

DETAILS DE LA RESERVATION
========================
Centre: ${selectedData.centre.nom}
Service: ${selectedData.service.nom}
Formule: ${selectedData.formule.nom} - ${selectedData.formule.prix} FCFA
Date: ${selectedData.date.display}
Heure: ${selectedData.timeSlot.tranche}

INFORMATIONS CLIENT
==================
Nom: ${selectedData.client.prenom} ${selectedData.client.nom}
Email: ${selectedData.client.email}
Téléphone: ${selectedData.client.telephone}

INSTRUCTIONS
============
- Présentez-vous 15 minutes avant votre rendez-vous
- Apportez une pièce d'identité valide
- Conservez ce numéro de suivi pour vos démarches

Merci pour votre confiance !
        `.trim();
    }

    function updateSummary() {
        const summary = document.getElementById('selection-summary');
        const content = document.getElementById('summary-content');
        
        if (selectedData.pays) {
            summary.classList.remove('hidden');
            
            let summaryItems = [
                { label: 'Pays', value: selectedData.pays.nom },
                { label: 'Ville', value: selectedData.ville ? selectedData.ville.nom : 'Non sélectionnée' },
                { label: 'Centre', value: selectedData.centre ? selectedData.centre.nom : 'Non sélectionné' },
                { label: 'Service', value: selectedData.service ? selectedData.service.nom : 'Non sélectionné' },
                { label: 'Formule', value: selectedData.formule ? `${selectedData.formule.nom} - ${selectedData.formule.prix} FCFA` : 'Non sélectionnée' }
            ];
            
            if (selectedData.date) {
                summaryItems.push({ label: 'Date', value: selectedData.date.display });
            }
            
            if (selectedData.timeSlot) {
                summaryItems.push({ label: 'Heure', value: selectedData.timeSlot.tranche });
            }
            
            if (selectedData.client) {
                summaryItems.push({ label: 'Client', value: `${selectedData.client.prenom} ${selectedData.client.nom}` });
            }
            
            content.innerHTML = summaryItems.map(item => `
                <div class="text-center">
                    <div class="text-blue-600 font-semibold">${item.label}</div>
                    <div class="text-sm text-gray-600">${item.value}</div>
                </div>
            `).join('');
        }
    }

    // Variables pour le calendrier
    let currentDate = new Date();
    let selectedDate = null;
    let selectedTimeSlot = null;

    function initializeCalendar() {
        renderCalendar();
    }

    function renderCalendar() {
        const calendarGrid = document.getElementById('calendar-grid');
        const currentMonthElement = document.getElementById('current-month');
        
        // S'assurer que currentDate est au début du mois
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        
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
            
            dayElement.className = 'h-12 flex flex-col items-center justify-center text-sm cursor-pointer rounded-md hover:bg-gray-100 relative';
            
            // Styling selon le statut du jour
            if (dayDate < today) {
                dayElement.className += ' text-gray-400 cursor-not-allowed';
            } else if (dayDate.toDateString() === today.toDateString()) {
                dayElement.className += ' bg-blue-100 text-blue-600 font-medium';
            } else {
                dayElement.className += ' text-gray-900 hover:bg-blue-50';
            }
            
            // Ajouter le numéro du jour
            const dayNumber = document.createElement('span');
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            // Ajouter un indicateur de disponibilité (sera mis à jour par loadAvailabilityForMonth)
            const indicator = document.createElement('div');
            indicator.className = 'w-2 h-2 rounded-full mt-1';
            indicator.id = `availability-indicator-${day}`;
            dayElement.appendChild(indicator);
            
            dayElement.onclick = () => selectDate(dayDate);
            
            calendarGrid.appendChild(dayElement);
        }
        
        // Charger les disponibilités pour le mois entier
        loadAvailabilityForMonth();
    }

    function loadAvailabilityForMonth() {
        if (!selectedData.centre || !selectedData.centre.id) {
            return;
        }
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;
        const daysInMonth = new Date(year, month, 0).getDate();
        
        // Charger les disponibilités pour chaque jour du mois
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month - 1, day);
            const dateStr = date.toISOString().split('T')[0];
            
            fetch(`/api/disponibilite/${selectedData.centre.id}/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.services) {
                        const serviceId = selectedData.service.id;
                        const formuleId = selectedData.formule.id;
                        
                        if (data.data.services[serviceId] && data.data.services[serviceId].formules && data.data.services[serviceId].formules[formuleId]) {
                            const creneaux = data.data.services[serviceId].formules[formuleId].creneaux;
                            const availableSlots = creneaux.filter(slot => slot.disponible > 0).length;
                            updateDayIndicator(day, availableSlots);
                        } else {
                            updateDayIndicator(day, 0);
                        }
                    } else {
                        updateDayIndicator(day, 0);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement de la disponibilité pour le jour', day, error);
                    updateDayIndicator(day, 0);
                });
        }
    }

    function updateDayIndicator(day, availableSlots) {
        const indicator = document.getElementById(`availability-indicator-${day}`);
        if (!indicator) return;
        
        if (availableSlots === 0) {
            indicator.className = 'w-2 h-2 rounded-full mt-1 bg-red-500';
        } else if (availableSlots < 5) {
            indicator.className = 'w-2 h-2 rounded-full mt-1 bg-yellow-500';
        } else {
            indicator.className = 'w-2 h-2 rounded-full mt-1 bg-green-500';
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
        if (!selectedData.centre || !selectedData.centre.id) {
            console.error('Centre ID manquant');
            return;
        }
        
        const dateStr = date.toISOString().split('T')[0];
        const url = `/api/disponibilite/${selectedData.centre.id}/${dateStr}`;
        
        // Afficher le loading
        showAvailabilityLoading();
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // L'API retourne data.data.disponibilite, mais on a besoin des créneaux pour le service et formule sélectionnés
                    const serviceId = selectedData.service.id;
                    const formuleId = selectedData.formule.id;
                    
                    if (data.data && data.data.services && data.data.services[serviceId] && data.data.services[serviceId].formules && data.data.services[serviceId].formules[formuleId]) {
                        const creneaux = data.data.services[serviceId].formules[formuleId].creneaux;
                        displayAvailability(creneaux, date);
                    } else {
                        console.log('Aucun créneau trouvé pour ce service et cette formule');
                        displayAvailability([], date);
                    }
                } else {
                    console.error('Erreur lors du chargement de la disponibilité:', data.message);
                    showAvailabilityError();
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showAvailabilityError();
            });
    }

    function showAvailabilityLoading() {
        const detailsElement = document.getElementById('availability-details');
        const infoElement = document.getElementById('availability-info');
        
        infoElement.innerHTML = `
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p>Chargement des disponibilités...</p>
        `;
        infoElement.classList.remove('hidden');
        detailsElement.classList.add('hidden');
    }

    function showAvailabilityError() {
        const detailsElement = document.getElementById('availability-details');
        const infoElement = document.getElementById('availability-info');
        
        infoElement.innerHTML = `
            <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-4"></i>
            <p>Erreur lors du chargement des disponibilités</p>
        `;
        infoElement.classList.remove('hidden');
        detailsElement.classList.add('hidden');
    }

    function displayAvailability(availability, date) {
        const detailsElement = document.getElementById('availability-details');
        const infoElement = document.getElementById('availability-info');
        const timeSlotsElement = document.getElementById('time-slots');
        const noSlotsElement = document.getElementById('no-slots');
        
        
        // Cacher l'info et afficher les détails
        infoElement.classList.add('hidden');
        detailsElement.classList.remove('hidden');
        
        // Mettre à jour les informations de date
        const dateTitle = date.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('selected-date-title').textContent = dateTitle;
        document.getElementById('selected-date-info').textContent = `Créneaux disponibles pour ${selectedData.centre.nom}`;
        
        if (!availability || availability.length === 0) {
            timeSlotsElement.innerHTML = '';
            noSlotsElement.classList.remove('hidden');
            return;
        }
        
        noSlotsElement.classList.add('hidden');
        timeSlotsElement.innerHTML = '';
        
        availability.forEach(slot => {
            const slotElement = document.createElement('button');
            slotElement.className = 'w-full p-3 text-left border border-gray-300 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors';
            
            if (slot.disponible > 0) {
                slotElement.className += ' bg-white text-gray-900';
                slotElement.onclick = () => selectTimeSlot(slot);
            } else {
                slotElement.className += ' bg-gray-100 text-gray-400 cursor-not-allowed';
            }
            
            slotElement.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium">${slot.tranche_horaire}</div>
                        <div class="text-xs text-gray-500">${slot.disponible > 0 ? 'Disponible' : 'Indisponible'}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium">${slot.disponible || 0} place(s)</div>
                        <div class="text-xs text-gray-500">sur ${slot.capacite_totale || 0}</div>
                    </div>
                </div>
            `;
            
            timeSlotsElement.appendChild(slotElement);
        });
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
        showConfirmationButton();
    }

    function showConfirmationButton() {
        const btnNext = document.getElementById('btn-next');
        btnNext.innerHTML = `
            <i class="fas fa-check mr-2"></i>
            Confirmer la réservation
        `;
        btnNext.onclick = confirmBooking;
        btnNext.style.display = 'flex';
    }

    function confirmBooking() {
        if (!selectedDate || !selectedTimeSlot) {
            showToast('Veuillez sélectionner une date et un créneau', 'error');
            return;
        }
        
        // Sauvegarder les données de réservation
        selectedData.date = {
            date: selectedDate.toISOString().split('T')[0],
            display: selectedDate.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })
        };
        selectedData.timeSlot = {
            tranche: selectedTimeSlot.tranche_horaire,
            disponible: selectedTimeSlot.disponible,
            capacite_totale: selectedTimeSlot.capacite_totale
        };
        
        // Mettre à jour le résumé
        updateSummary();
        
        // Passer à l'étape suivante (formulaire client)
        showStep('step-client');
    }

    function previousMonth() {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        renderCalendar();
    }
</script>

<style>
    .wizard-step {
        transition: all 0.3s ease;
    }
    
    .wizard-step.active {
        display: block;
    }
    
    .wizard-step.hidden {
        display: none;
    }
</style>
@endsection
