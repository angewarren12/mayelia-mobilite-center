@extends('booking.layout')

@section('title', 'Réserver un rendez-vous')

@php
    $currentStep = 1;
@endphp

@section('content')
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Prendre un rendez-vous en ligne
        </h2>
        <p class="text-lg text-gray-600 mb-8">
            Suivez les étapes pour réserver votre créneau
        </p>
        
    </div>

    <!-- Bandeau de vérification ONECI -->
    @if(isset($oneciData) && $oneciData)
    <div class="mb-8 bg-green-50 border-2 border-green-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-3xl text-green-600"></i>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-green-900 mb-2">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Pré-enrôlement ONECI vérifié
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-green-800">
                    <div>
                        <span class="font-medium">Numéro:</span> 
                        <span class="ml-2">{{ $oneciData['numero_pre_enrolement'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Nom:</span> 
                        <span class="ml-2">{{ $oneciData['nom'] ?? '' }} {{ $oneciData['prenoms'] ?? '' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Téléphone:</span> 
                        <span class="ml-2">{{ $oneciData['telephone'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Statut:</span> 
                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                            ✓ Validé
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Wizard Container -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        
        <!-- Étape 1: Sélection du Service -->
        <div id="step-service" class="wizard-step active">
            <div class="text-center mb-8">
                <i class="fas fa-concierge-bell text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 1: Choisissez votre service
                </h3>
                <p class="text-gray-600">
                    Sélectionnez le service pour lequel vous souhaitez prendre rendez-vous
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                @php
                    $formule = $service->formules->first();
                @endphp
                <div class="border-2 border-mayelia-200 rounded-lg p-6 hover:border-mayelia-400 hover:shadow-md transition-all cursor-pointer service-card"
                     data-service-id="{{ $service->id }}" 
                     data-service-nom="{{ $service->nom }}"
                     data-formule-id="{{ $formule ? $formule->id : '' }}"
                     data-formule-nom="{{ $formule ? $formule->nom : '' }}"
                     data-formule-prix="{{ $formule ? $formule->prix : '' }}">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-2xl text-blue-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $service->nom }}
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ Str::limit($service->description, 100) }}
                        </p>
                        <div class="flex items-center justify-center text-sm text-mayelia-600">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Sélectionner
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Étape 2: Vérification ONECI -->
        <div id="step-verification" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-shield-alt text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 2: Vérification ONECI
                </h3>
                <p class="text-gray-600">
                    Veuillez vérifier votre numéro de pré-enrôlement pour continuer
                </p>
            </div>

            <div class="max-w-xl mx-auto">
                <div class="bg-gray-50 rounded-lg p-8 border border-gray-200">
                    <form id="wizard-verification-form" onsubmit="verifyOneciInWizard(event)">
                        <div class="mb-6">
                            <label for="wizard_numero_pre_enrolement" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de pré-enrôlement <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="wizard_numero_pre_enrolement" 
                                required
                                placeholder="Ex: ONECI2025001"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500 text-lg"
                            >
                        </div>

                        <div id="wizard-message-container" class="mb-6 hidden"></div>

                        <button 
                            type="submit" 
                            id="wizard-verify-btn"
                            class="w-full px-6 py-3 bg-mayelia-600 hover:bg-mayelia-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center justify-center"
                        >
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Vérifier et Continuer</span>
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-200 text-center" id="wizard-pre-enrollment-link">
                        <a href="{{ route('booking.oneci-redirect') }}" target="_blank" class="text-mayelia-600 hover:text-mayelia-700 font-medium text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Je n'ai pas encore de numéro (Pré-enrôlement)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Étape 3: Sélection du pays -->
        <div id="step-pays" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-globe-africa text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 3: Sélectionnez votre pays
                </h3>
                <p class="text-gray-600">
                    Choisissez le pays où vous souhaitez effectuer votre démarche
                </p>
            </div>

            <div id="pays-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Les pays seront chargés dynamiquement -->
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-mayelia-600 mb-4"></i>
                    <p class="text-gray-600">Chargement des pays disponibles pour ce service...</p>
                </div>
            </div>
        </div>

        <!-- Étape 4: Sélection de la ville -->
        <div id="step-ville" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-map-marker-alt text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 4: Choisissez votre ville
                </h3>
                <p class="text-gray-600">
                    Sélectionnez la ville où vous souhaitez effectuer votre démarche
                </p>
            </div>

            <div id="villes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-mayelia-600 mb-4"></i>
                    <p class="text-gray-600">Chargement des villes...</p>
                </div>
            </div>
        </div>

        <!-- Étape 5: Sélection du centre -->
        <div id="step-centre" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-building text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 5: Choisissez votre centre
                </h3>
                <p class="text-gray-600">
                    Sélectionnez le centre Mayelia le plus proche de vous
                </p>
            </div>

            <div id="centres-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Les centres seront chargés dynamiquement -->
            </div>
        </div>

        <!-- Anciennes étapes Service et Formule supprimées -->


        <!-- Étape 6: Calendrier et disponibilités -->
        <div id="step-calendrier" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-calendar-alt text-6xl text-mayelia-600 mb-4"></i>
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

        <!-- Étape 7: Informations client -->
        <div id="step-client" class="wizard-step hidden">
            <div class="text-center mb-8">
                <i class="fas fa-user text-6xl text-mayelia-600 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Étape 7: Vos informations
                </h3>
                <p class="text-gray-600">
                    Vérifiez et complétez vos informations personnelles
                </p>
            </div>

            <div class="max-w-2xl mx-auto">
                <!-- Formulaire client (pré-rempli avec données ONECI) -->
                <div id="new-client-form">
                    <form id="client-form" onsubmit="submitClientForm(event)">
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
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
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

                            <!-- Lieu de naissance -->
                            <div>
                                <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lieu de naissance
                                </label>
                                <input type="text" id="lieu_naissance" name="lieu_naissance"
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
                        

                    </form>
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
                    class="flex items-center px-6 py-3 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700 transition-colors ml-auto">
                <span>Suivant</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>

    <!-- Résumé de sélection -->
    <div id="selection-summary" class="mt-8 bg-mayelia-50 rounded-lg p-6 hidden">
        <h4 class="text-lg font-semibold text-mayelia-900 mb-4">
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
        service: null,
        formule: null, // Sera auto-sélectionné avec le service
        pays: null,
        ville: null,
        centre: null,
        date: null,
        creneau: null
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Restaurer l'état sauvegardé si disponible
        restoreWizardState();
        
        initializeWizard();
        
        // Pré-remplir les données ONECI si disponibles
        @if(isset($oneciData) && $oneciData)
        prefillOneciData({!! json_encode($oneciData) !!});
        @endif
    });

    function saveWizardState() {
        // Sauvegarder l'état actuel dans sessionStorage
        const state = {
            currentStep: document.querySelector('.wizard-step:not(.hidden)')?.id || 'step-service',
            selectedData: selectedData,
            currentStepNumber: currentStepNumber
        };
        sessionStorage.setItem('wizard_state', JSON.stringify(state));
    }

    function restoreWizardState() {
        const savedState = sessionStorage.getItem('wizard_state');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                
                // Restaurer les données sélectionnées
                if (state.selectedData) {
                    selectedData = state.selectedData;
                    // Mettre à jour l'interface si un service est déjà sélectionné
                    if (selectedData.service) {
                        updateVerificationUI();
                    }
                }

                // Restaurer les données ONECI si disponibles
                const oneciData = sessionStorage.getItem('oneci_data');
                if (oneciData) {
                    try {
                        window.oneciData = JSON.parse(oneciData);
                    } catch (e) {
                         console.error('Erreur lors de la restauration des données ONECI:', e);
                    }
                }
                
                // Restaurer le numéro d'étape
                if (state.currentStepNumber) {
                    currentStepNumber = state.currentStepNumber;
                }
                
                // Restaurer l'étape visible
                if (state.currentStep && state.currentStep !== 'step-service') {
                    // Attendre que le DOM soit prêt
                    setTimeout(() => {
                        showStep(state.currentStep);
                    }, 100);
                }
            } catch (e) {
                console.error('Erreur lors de la restauration de l\'état:', e);
                sessionStorage.removeItem('wizard_state');
            }
        }
    }

    function clearWizardState() {
        sessionStorage.removeItem('wizard_state');
    }

    function initializeWizard() {
        // Gestion de la sélection du service (Étape 1)
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                // Récupérer l'ID et le nom du service
                const serviceId = this.dataset.serviceId;
                const serviceNom = this.dataset.serviceNom;
                
                // Récupérer la formule associée
                const formuleId = this.dataset.formuleId;
                const formuleNom = this.dataset.formuleNom;
                const formulePrix = this.dataset.formulePrix;
                
                selectService(serviceId, serviceNom, formuleId, formuleNom, formulePrix);
            });
        });
    }

    function selectService(serviceId, serviceNom, formuleId, formuleNom, formulePrix) {
        selectedData.service = { id: serviceId, nom: serviceNom };
        
        // Sélectionner automatiquement la formule
        if (formuleId) {
            selectedData.formule = { 
                id: formuleId, 
                nom: formuleNom, 
                prix: formulePrix 
            };
        } else {
            console.warn('Aucune formule associée au service sélectionné');
        }

        // Adapter l'interface de vérification selon le service
        updateVerificationUI();
        
        // Sauvegarder l'état
        saveWizardState();
        
        // Passer à l'étape de vérification
        showStep('step-verification');
        updateSummary();
    }

    function updateVerificationUI() {
        if (!selectedData.service) return;

        const serviceNameLower = selectedData.service.nom.toLowerCase();
        const isCarteResident = serviceNameLower.includes('résident') || serviceNameLower.includes('resident');
        const isCNI = serviceNameLower.includes('cni') || serviceNameLower.includes('identité') || serviceNameLower.includes('identite');
        
        const label = document.querySelector('label[for="wizard_numero_pre_enrolement"]');
        const input = document.getElementById('wizard_numero_pre_enrolement');
        const preEnrollmentContainer = document.getElementById('wizard-pre-enrollment-link');
        const preEnrollmentAnchor = preEnrollmentContainer ? preEnrollmentContainer.querySelector('a') : null;
        
        if (isCarteResident) {
            label.innerHTML = 'Numéro de dossier Carte Résident <span class="text-red-500">*</span>';
            input.placeholder = 'Ex: 1686394325';
            selectedData.verificationType = 'carte_resident';
            
            // Afficher et configurer le lien pour la Carte de Résident
            if (preEnrollmentContainer && preEnrollmentAnchor) {
                preEnrollmentContainer.classList.remove('hidden');
                preEnrollmentAnchor.href = 'https://pre-enregistrement-carte-resident.oneci.ci/mayelia/Mayeli32434';
                preEnrollmentAnchor.innerHTML = '<i class="fas fa-external-link-alt mr-1"></i> Faire mon pré-enrôlement';
            }
        } else if (isCNI) {
            label.innerHTML = 'Numéro de dossier CNI <span class="text-red-500">*</span>';
            input.placeholder = 'Ex: 1736877880';
            selectedData.verificationType = 'cni';
            
            // Afficher et configurer le lien pour la CNI
            if (preEnrollmentContainer && preEnrollmentAnchor) {
                preEnrollmentContainer.classList.remove('hidden');
                preEnrollmentAnchor.href = 'https://pre-enrolement-cni.oneci.ci/formulaire?referent=mayelia';
                preEnrollmentAnchor.innerHTML = '<i class="fas fa-external-link-alt mr-1"></i> Faire mon pré-enrôlement';
            }
        } else {
            label.innerHTML = 'Numéro de pré-enrôlement <span class="text-red-500">*</span>';
            input.placeholder = 'Ex: ONECI2025001';
            selectedData.verificationType = 'oneci';
            
            // Lien par défaut pour les autres services ONECI
            if (preEnrollmentContainer && preEnrollmentAnchor) {
                preEnrollmentContainer.classList.remove('hidden');
                // Remettre le lien par défaut (si nécessaire, ou laisser tel quel)
                preEnrollmentAnchor.href = '{{ route('booking.oneci-redirect') }}';
                preEnrollmentAnchor.innerHTML = '<i class="fas fa-external-link-alt mr-1"></i> Je n\'ai pas encore de numéro (Pré-enrôlement)';
            }
        }
    }

    async function verifyOneciInWizard(event) {
        event.preventDefault();
        
        const btn = document.getElementById('wizard-verify-btn');
        const messageContainer = document.getElementById('wizard-message-container');
        const numeroInput = document.getElementById('wizard_numero_pre_enrolement');
        const numero = numeroInput.value.trim();
        
        // UI Loading
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Vérification en cours...</span>';
        messageContainer.classList.add('hidden');
        
        // Déterminer l'URL de vérification
        let verificationUrl;
        let payload;
        
        if (selectedData.verificationType === 'carte_resident') {
            verificationUrl = '{{ route('booking.verify-carte-resident') }}';
            payload = { numero_dossier: numero };
        } else if (selectedData.verificationType === 'cni') {
            verificationUrl = '{{ route('booking.verify-cni') }}';
            payload = { numero_dossier: numero };
        } else {
            verificationUrl = '{{ route('booking.verify-enrollment') }}';
            payload = { numero_pre_enrolement: numero };
        }
        
        try {
            const response = await fetch(verificationUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Succès
                sessionStorage.setItem('oneci_verified', 'true');
                sessionStorage.setItem('oneci_data', JSON.stringify(data.data));
                
                // Pré-remplir les données pour plus tard
                window.oneciData = data.data;

                // Afficher le message de succès avec le statut
                const statut = data.data.statut || data.statut || 'Vérifié';
                const container = document.getElementById('wizard-message-container');
                container.innerHTML = `
                    <div class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <span class="font-bold text-lg">Vérification réussie !</span>
                            <div class="mt-1 text-sm">
                                Statut du dossier : <span class="font-bold uppercase bg-green-200 text-green-800 px-2 py-0.5 rounded">${statut}</span>
                            </div>
                            <div class="mt-1 text-xs text-green-600">Redirection vers l'étape suivante...</div>
                        </div>
                    </div>
                `;
                container.classList.remove('hidden');
                
                // Charger les localisations pour ce service
                await loadLocationsForService(selectedData.service.id);
                
                // Passer à l'étape suivante après 2 secondes pour laisser le temps de lire
                updateSummary();
                setTimeout(() => {
                    showStep('step-pays');
                }, 2000);
                
            } else {
                // Erreur
                showWizardMessage('error', data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span>Vérifier et Continuer</span>';
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            showWizardMessage('error', 'Une erreur est survenue. Veuillez réessayer.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span>Vérifier et Continuer</span>';
        }
    }

    function showWizardMessage(type, message) {
        const container = document.getElementById('wizard-message-container');
        const className = type === 'success' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        container.className = `mb-6 border px-4 py-3 rounded-lg flex items-center ${className}`;
        container.innerHTML = `<i class="fas ${icon} mr-3"></i><span>${message}</span>`;
        container.classList.remove('hidden');
    }

    let loadedLocations = null;

    async function loadLocationsForService(serviceId) {
        const container = document.getElementById('pays-container');
        // Si le conteneur est déjà rempli et qu'on a les données, ne rien faire (évite le scintillement)
        if (loadedLocations && container.children.length > 1) {
             return;
        }

        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-mayelia-600 mb-4"></i>
                <p class="text-gray-600">Chargement des pays...</p>
            </div>
        `;
        
        try {
            const response = await fetch(`/booking/locations/${serviceId}`);
            const data = await response.json();
            
            if (data.success && data.locations.length > 0) {
                loadedLocations = data.locations; // Cache des données
                renderPays(data.locations);
            } else {
                container.innerHTML = '<div class="col-span-full text-center text-gray-500">Aucun pays disponible pour ce service.</div>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = '<div class="col-span-full text-center text-red-500">Erreur de chargement.</div>';
        }
    }

    function renderPays(locations) {
        const container = document.getElementById('pays-container');
        container.innerHTML = '';
        locations.forEach(loc => {
            const pays = loc.pays;
            const card = document.createElement('div');
            card.className = 'border-2 border-mayelia-200 rounded-lg p-6 hover:border-mayelia-400 hover:shadow-md transition-all cursor-pointer country-card';
            // On ne passe plus 'pays.villes' directement, on utilisera le cache
            card.onclick = () => selectPays(pays.id, pays.nom);
            
            card.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-flag text-2xl text-orange-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">${pays.nom}</h4>
                    <p class="text-sm text-gray-600 mb-4">Code: ${pays.code}</p>
                    <div class="flex items-center justify-center text-sm text-mayelia-600">
                        <i class="fas fa-arrow-right mr-2"></i>Continuer
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function selectPays(paysId, paysNom) {
        selectedData.pays = { id: paysId, nom: paysNom };
        saveWizardState();
        renderVilles(paysId);
        showStep('step-ville');
        updateSummary();
    }

    function renderVilles(paysId) {
        const container = document.getElementById('villes-container');
        container.innerHTML = '';
        
        let villes = [];
        if (loadedLocations) {
            const countryData = loadedLocations.find(l => l.pays.id == paysId);
            if (countryData && countryData.pays.villes) {
                villes = countryData.pays.villes;
            }
        }

        if (villes && villes.length > 0) {
            villes.forEach(ville => {
                const card = document.createElement('div');
                card.className = 'border-2 border-mayelia-200 rounded-lg p-6 hover:border-mayelia-400 hover:shadow-md transition-all cursor-pointer ville-card';
                card.onclick = () => selectVille(ville.id, ville.nom);
                
                card.innerHTML = `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-city text-2xl text-green-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">${ville.nom}</h4>
                        <div class="flex items-center justify-center text-sm text-mayelia-600">
                            <i class="fas fa-arrow-right mr-2"></i>Sélectionner
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        } else {
            // Si pas de villes chargées ou trouvées, afficher un message ou loader
            if (!loadedLocations) {
                 // Cas critique : on a perdu les données (ex: F5 sur étape ville sans repasser par le load)
                 // On doit recharger les locations
                 if (selectedData.service) {
                     container.innerHTML = `
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-spinner fa-spin text-3xl text-mayelia-600 mb-4"></i>
                            <p class="text-gray-600">Rechargement des données...</p>
                        </div>
                    `;
                    // On recharge et on rappelle renderVilles
                    loadLocationsForService(selectedData.service.id).then(() => {
                        renderVilles(paysId);
                    });
                    return;
                 }
            }
            container.innerHTML = '<div class="col-span-full text-center text-gray-500">Aucune ville disponible.</div>';
        }
    }

    function selectVille(villeId, villeNom) {
        selectedData.ville = { id: villeId, nom: villeNom };
        saveWizardState();
        loadCentres(villeId, selectedData.service.id);
        showStep('step-centre');
        updateSummary();
    }

    async function loadCentres(villeId, serviceId) {
        const container = document.getElementById('centres-container');
        
        // Si le conteneur est déjà rempli avec les données (pas un message d'erreur ou vide), on peut éventuellement éviter de recharger
        // Mais pour l'instant, on recharge pour être sûr, c'est plus simple.
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-mayelia-600 mb-4"></i>
                <p class="text-gray-600">Chargement des centres...</p>
            </div>
        `;
        
        try {
            const response = await fetch(`/booking/centres/${villeId}/${serviceId}`);
            const data = await response.json();
            
            if (data.success && data.centres.length > 0) {
                container.innerHTML = '';
                data.centres.forEach(centre => {
                    const card = document.createElement('div');
                    card.className = 'border-2 border-mayelia-200 rounded-lg p-6 hover:border-mayelia-400 hover:shadow-md transition-all cursor-pointer centre-card';
                    card.onclick = () => selectCentre(centre.id, centre.nom);
                    
                    card.innerHTML = `
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-building text-xl text-purple-600"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">${centre.nom}</h4>
                                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-map-pin mr-1"></i>${centre.adresse || 'Adresse non spécifiée'}</p>
                                <div class="flex items-center text-sm text-mayelia-600 mt-3">
                                    <span>Choisir ce centre</span>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = '<div class="col-span-full text-center text-gray-500">Aucun centre disponible.</div>';
            }
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = '<div class="col-span-full text-center text-red-500">Erreur de chargement.</div>';
        }
    }

    function selectCentre(centreId, centreNom) {
        selectedData.centre = { id: centreId, nom: centreNom };
        
        // Sauvegarder l'état
        saveWizardState();
        
        // Initialiser le calendrier
        currentDate = new Date();
        renderCalendar();
        
        showStep('step-calendrier');
        updateSummary();
    }


    function showStep(stepId) {
        // Cacher toutes les étapes
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.classList.add('hidden');
            step.classList.remove('active');
        });
        
        // Afficher l'étape sélectionnée
        const currentStepEl = document.getElementById(stepId);
        if (currentStepEl) {
            currentStepEl.classList.remove('hidden');
            currentStepEl.classList.add('active');
        }
        
        // Si on revient à l'étape service, effacer la session ONECI
        if (stepId === 'step-service') {
            clearOneciSession();
        }

        // Si on arrive à l'étape client, pré-remplir les informations
        if (stepId === 'step-client') {
            prefillClientInfo();
        }
        
        // Logique de rechargement des données dynamiques lors de la navigation (notamment retour arrière ou refresh)
        if (stepId === 'step-pays' && selectedData.service) {
             // Vérifier si besoin de recharger
             const container = document.getElementById('pays-container');
             // Si container vide ou contient seulement le spinner (cas initial ou perdu)
             if (!container.children.length || (container.children.length === 1 && container.firstElementChild.querySelector('.fa-spinner'))) {
                 loadLocationsForService(selectedData.service.id);
             }
        } else if (stepId === 'step-ville' && selectedData.pays) {
             const container = document.getElementById('villes-container');
             if (!container.children.length || (container.children.length === 1 && container.firstElementChild.querySelector('.fa-spinner'))) {
                 renderVilles(selectedData.pays.id);
             }
        } else if (stepId === 'step-centre' && selectedData.ville && selectedData.service) {
             const container = document.getElementById('centres-container');
             if (!container.children.length || (container.children.length === 1 && container.firstElementChild.querySelector('.fa-spinner'))) {
                 loadCentres(selectedData.ville.id, selectedData.service.id);
             }
        } else if (stepId === 'step-calendrier' && selectedData.centre) {
            // Le calendrier a sa propre logique de rendu, on s'assure qu'il est rendu
            // Mais renderCalendar() ne fetch rien, il affiche juste.
            // Si on change de mois, il fetch.
            // On peut appeler renderCalendar() sans risque
            setTimeout(() => renderCalendar(), 100);
        }

        // Mettre à jour le numéro d'étape
        currentStepNumber = getStepNumber(stepId);
        updateNavigation();
        updateStepIndicators();
        
        // Sauvegarder l'état
        saveWizardState();
    }

    function prefillClientInfo() {
        if (!window.oneciData) {
            console.warn('Aucune donnée ONECI disponible pour le pré-remplissage');
            return;
        }
        
        const data = window.oneciData;
        console.log('Données pour pré-remplissage:', data); // DEBUG
        
        // Mappage des champs
        const fields = {
            'nom': data.nom,
            'prenom': data.prenoms || data.prenom || '', 
            'date_naissance': data.date_naissance,
            'lieu_naissance': data.lieu_naissance,
            'telephone': data.telephone || data.numero_telephone || '', // Mappage vers l'input principal
            'email': data.email || '',
            'sexe': data.genre || data.sexe || '', // Si disponible dans l'API
            'adresse': data.adresse || ''
        };
        
        console.log('Valeur Prénom trouvée:', fields.prenom); // DEBUG

        // Remplir les champs
        for (const [id, value] of Object.entries(fields)) {
            const input = document.getElementById(id);
            if (input) {
                if (value) {
                    input.value = value;
                    input.readOnly = true; // Empêcher la modification si donnée présente
                    input.classList.add('bg-gray-100', 'cursor-not-allowed');
                } else {
                    // Si pas de valeur (ex: prénom vide), laisser le champ modifiable
                    input.readOnly = false;
                    input.classList.remove('bg-gray-100', 'cursor-not-allowed');
                }
            }
        }

        // Cas spécial pour le téléphone : si le numéro ONECI existe, on peut le pré-remplir dans le champ principal ou laisser l'utilisateur choisir
        // Ici, je suppose qu'on laisse l'utilisateur mettre son numéro de contact actuel, 
        // ou on peut le pré-remplir s'il est vide.
        const telInput = document.getElementById('telephone');
        if (telInput && !telInput.value && data.telephone) {
            telInput.value = data.telephone;
        }
    }

    function clearOneciSession() {
        // Effacer la session ONECI côté serveur
        fetch('/booking/clear-oneci-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json())
          .then(data => {
              console.log('Session ONECI effacée');
              // Réinitialiser le formulaire de vérification
              const form = document.getElementById('wizard-verification-form');
              if (form) {
                  form.reset();
              }
              const messageContainer = document.getElementById('wizard-message-container');
              if (messageContainer) {
                  messageContainer.classList.add('hidden');
              }
          })
          .catch(error => console.error('Erreur lors de l\'effacement de la session:', error));
    }

    function getStepNumber(stepId) {
        const stepMap = {
            'step-service': 1,
            'step-verification': 2,
            'step-pays': 3,
            'step-ville': 4,
            'step-centre': 5,
            'step-calendrier': 6,
            'step-client': 7,
            'step-confirmation': 8
        };
        return stepMap[stepId] || 1;
    }

    function updateStepIndicators() {
        // Mettre à jour les indicateurs du layout principal
        const progressSteps = document.querySelectorAll('.progress-step');
        
        progressSteps.forEach((step, index) => {
            const stepNumber = index + 1;
            
            if (stepNumber < currentStepNumber) {
                // Étape terminée
                step.className = 'progress-step flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium completed';
                step.innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNumber === currentStepNumber) {
                // Étape actuelle
                step.className = 'progress-step flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium active';
                step.innerHTML = stepNumber;
            } else {
                // Étape future
                step.className = 'progress-step flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium bg-gray-200 text-gray-500';
                step.innerHTML = stepNumber;
            }
        });
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

        // Gérer le bouton suivant
        // Par défaut, on le cache pour les étapes de sélection (1-6) car la navigation est automatique
        if (currentStepNumber < 7 || currentStepNumber === 8) {
            btnNext.style.display = 'none';
        } else {
            // Pour l'étape client (7), on l'affiche toujours pour valider le formulaire
            if (currentStepNumber === 7) {
                btnNext.style.display = 'flex';
                btnNext.innerHTML = `
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirmer et créer le rendez-vous
                `;
                btnNext.onclick = () => {
                    const form = document.getElementById('client-form');
                    if (form) {
                        form.dispatchEvent(new Event('submit'));
                    }
                };
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
            // Ordre correct des étapes : Service -> Vérification -> Pays -> Ville -> Centre -> Calendrier -> Client -> Confirmation
            const steps = ['step-service', 'step-verification', 'step-pays', 'step-ville', 'step-centre', 'step-calendrier', 'step-client', 'step-confirmation'];
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
        
        // Créer directement le rendez-vous
        createRendezVous();
    }

    function submitClientForm(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const clientData = Object.fromEntries(formData.entries());
        
        // Sauvegarder les données brutes du formulaire pour l'envoi au contrôleur de rendez-vous
        selectedData.clientInfo = clientData;
        
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
                createRendezVous();
            } else {
                showToast('Erreur lors de la création du client: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la création du client', 'error');
        });
    }

    function createRendezVous() {
        // Afficher un modal de chargement
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-mayelia-100 mb-4">
                        <i class="fas fa-calendar-check text-mayelia-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Création de votre rendez-vous</h3>
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-mayelia-600 mx-auto mb-4"></div>
                    <p class="text-sm text-gray-600">Traitement en cours...</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Préparer les données du rendez-vous
        // Préparer les données du rendez-vous
        const rendezVousData = {
            centre_id: selectedData.centre.id,
            service_id: selectedData.service.id,
            formule_id: selectedData.formule.id,
            client_id: selectedData.client ? selectedData.client.id : null,
            date_rendez_vous: selectedData.date.date, // Format YYYY-MM-DD déjà stocké
            tranche_horaire: selectedData.timeSlot.tranche, // Extraire la chaîne de caractères
            notes: selectedData.clientInfo ? selectedData.clientInfo.notes : '',
            
            // Infos client complètes
            ...selectedData.clientInfo,
            
            // Données ONECI
            oneci_data: window.oneciData || null
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
                
                // Nettoyer le stockage local
                clearWizardState();
                
                // Nettoyer la session ONECI côté serveur
                fetch('{{ route('booking.clear-oneci-session') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).catch(e => console.error('Erreur nettoyage session:', e));
                
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
        
        // Cacher les boutons de navigation
        const navigationButtons = document.querySelector('.flex.justify-between.mt-8');
        if (navigationButtons) {
            navigationButtons.style.display = 'none';
        }
        
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
                    <button onclick="downloadReceipt()" class="w-full bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium py-2 px-4 rounded-md">
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
                    <div class="text-mayelia-600 font-semibold">${item.label}</div>
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
        today.setHours(0, 0, 0, 0);
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
            dayDate.setHours(0, 0, 0, 0);
            
            dayElement.className = 'h-12 flex flex-col items-center justify-center text-sm cursor-pointer rounded-md hover:bg-gray-100 relative';
            
            // Styling selon le statut du jour
            if (dayDate < today) {
                dayElement.className += ' text-gray-400 cursor-not-allowed opacity-50';
                // Désactiver le clic pour les dates passées
                dayElement.onclick = null;
            } else if (dayDate.getTime() === today.getTime()) {
                dayElement.className += ' bg-mayelia-100 text-mayelia-600 font-medium';
                dayElement.onclick = () => selectDate(dayDate);
            } else {
                dayElement.className += ' text-gray-900 hover:bg-mayelia-50';
                dayElement.onclick = () => selectDate(dayDate);
            }
            
            // Ajouter le numéro du jour
            const dayNumber = document.createElement('span');
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            // Ajouter un indicateur de disponibilité (sera mis à jour par loadAvailabilityForMonth)
            // Pour les dates passées, on ne montre pas d'indicateur
            if (dayDate >= today) {
                const indicator = document.createElement('div');
                indicator.className = 'w-2 h-2 rounded-full mt-1';
                indicator.id = `availability-indicator-${day}`;
                dayElement.appendChild(indicator);
            }
            
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
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Charger toutes les disponibilités du mois en une seule requête
        fetch(`/api/disponibilite-mois/${selectedData.centre.id}/${year}/${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const serviceId = selectedData.service.id;
                    const formuleId = selectedData.formule.id;
                    
                    // Parcourir toutes les dates du mois
                    Object.keys(data.data).forEach(dateStr => {
                        const disponibilite = data.data[dateStr];
                        const date = new Date(dateStr);
                        date.setHours(0, 0, 0, 0);
                        const day = date.getDate();
                        
                        // Ne pas afficher d'indicateur pour les dates passées
                        if (date < today) {
                            updateDayIndicator(day, -1); // -1 = date passée, pas d'indicateur
                            return;
                        }
                        
                        // Vérifier si le service et la formule existent
                        if (disponibilite.services && 
                            disponibilite.services[serviceId] && 
                            disponibilite.services[serviceId].formules && 
                            disponibilite.services[serviceId].formules[formuleId]) {
                            
                            const creneaux = disponibilite.services[serviceId].formules[formuleId].creneaux;
                            const availableSlots = creneaux.filter(slot => slot.disponible > 0).length;
                            updateDayIndicator(day, availableSlots);
                        } else {
                            updateDayIndicator(day, 0);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des disponibilités du mois:', error);
                // En cas d'erreur, tous les jours sont marqués comme indisponibles
                const daysInMonth = new Date(year, month, 0).getDate();
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayDate = new Date(year, month - 1, day);
                    dayDate.setHours(0, 0, 0, 0);
                    if (dayDate < today) {
                        updateDayIndicator(day, -1);
                    } else {
                        updateDayIndicator(day, 0);
                    }
                }
            });
    }

    function updateDayIndicator(day, availableSlots) {
        const indicator = document.getElementById(`availability-indicator-${day}`);
        if (!indicator) return;
        
        // Si availableSlots est -1, c'est une date passée, on cache l'indicateur
        if (availableSlots === -1) {
            indicator.style.display = 'none';
            return;
        }
        
        // Réafficher l'indicateur s'il était caché
        indicator.style.display = 'block';
        
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
            day.classList.remove('bg-mayelia-600', 'text-white');
            day.classList.add('text-gray-900', 'hover:bg-mayelia-50');
        });
        
        event.target.classList.remove('text-gray-900', 'hover:bg-mayelia-50');
        event.target.classList.add('bg-mayelia-600', 'text-white');
        
        // Charger les créneaux disponibles
        loadAvailability(date);
    }

    function loadAvailability(date) {
        if (!selectedData.centre || !selectedData.centre.id) {
            console.error('Centre ID manquant');
            return;
        }
        
        // Vérifier que la date n'est pas dans le passé
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDate = new Date(date);
        selectedDate.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            console.log('Date passée, pas de chargement des créneaux');
            showAvailabilityError('Cette date est passée. Veuillez sélectionner une date future.');
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
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-mayelia-600 mx-auto mb-4"></div>
            <p>Chargement des disponibilités...</p>
        `;
        infoElement.classList.remove('hidden');
        detailsElement.classList.add('hidden');
    }

    function showAvailabilityError(message) {
        const detailsElement = document.getElementById('availability-details');
        const infoElement = document.getElementById('availability-info');
        
        const errorMessage = message || 'Erreur lors du chargement des disponibilités';
        
        infoElement.innerHTML = `
            <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-4"></i>
            <p>${errorMessage}</p>
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
        
        // Filtrer les créneaux passés si la date sélectionnée est aujourd'hui
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDate = new Date(date);
        selectedDate.setHours(0, 0, 0, 0);
        const isToday = selectedDate.getTime() === today.getTime();
        
        let filteredAvailability = availability;
        if (isToday) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            
            filteredAvailability = availability.filter(slot => {
                // Extraire l'heure de fin du créneau (format: "08:00:00 - 09:00:00")
                const timeRange = slot.tranche_horaire;
                if (!timeRange || !timeRange.includes(' - ')) {
                    return true; // Si le format est incorrect, on garde le créneau
                }
                
                const endTimeStr = timeRange.split(' - ')[1].trim();
                const [endHour, endMinute] = endTimeStr.split(':').map(Number);
                
                // Comparer l'heure de fin avec l'heure actuelle
                if (endHour < currentHour) {
                    return false; // Créneau déjà passé
                } else if (endHour === currentHour && endMinute <= currentMinute) {
                    return false; // Créneau déjà passé (même heure)
                }
                
                return true; // Créneau encore à venir
            });
        }
        
        if (filteredAvailability.length === 0) {
            timeSlotsElement.innerHTML = '';
            noSlotsElement.classList.remove('hidden');
            return;
        }
        
        noSlotsElement.classList.add('hidden');
        timeSlotsElement.innerHTML = '';
        
        filteredAvailability.forEach(slot => {
            const slotElement = document.createElement('button');
            slotElement.className = 'w-full p-3 text-left border border-gray-300 rounded-md hover:bg-mayelia-50 hover:border-mayelia-300 transition-colors';
            
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
            btn.classList.remove('bg-mayelia-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-900');
        });
        
        // Note: event est global dans un contexte inline onclick, mais ici nous l'appelons depuis une arrow function dans le JS.
        // Il faut s'assurer que l'élément cible est bien mis à jour visuellement avant la transition.
        // Comme nous passons directement à l'étape suivante, le changement visuel est moins critique mais bon pour l'UX instantanée.
        // Nous allons identifier le bouton cliqué différemment si 'event' n'est pas défini correctement.
        if (typeof event !== 'undefined' && event.target) {
             // Remonter au bouton si le clic était sur un enfant
            let target = event.target;
            while (target && target.tagName !== 'BUTTON') {
                target = target.parentElement;
            }
            if (target) {
                target.classList.remove('bg-white', 'text-gray-900');
                target.classList.add('bg-mayelia-600', 'text-white');
            }
        }
        
        // Passer automatiquement à l'étape suivante
        confirmBooking();
    }

    /* Fonction showConfirmationButton supprimée car plus utilisée */

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
    
    /**
     * Pré-remplit le formulaire client avec les données ONECI
     */
    function prefillOneciData(oneciData) {
        console.log('Pré-remplissage des données ONECI:', oneciData);
        
        // Attendre que le formulaire client soit affiché
        const observer = new MutationObserver(function(mutations, obs) {
            const clientForm = document.getElementById('new-client-form');
            if (clientForm && !clientForm.classList.contains('hidden')) {
                // Pré-remplir les champs
                if (oneciData.nom) {
                    const nomField = document.getElementById('nom');
                    if (nomField) nomField.value = oneciData.nom;
                }
                
                if (oneciData.prenoms) {
                    const prenomField = document.getElementById('prenom');
                    if (prenomField) prenomField.value = oneciData.prenoms;
                }
                
                if (oneciData.email) {
                    const emailField = document.getElementById('email');
                    if (emailField) emailField.value = oneciData.email;
                }
                
                if (oneciData.telephone) {
                    const telephoneField = document.getElementById('telephone');
                    if (telephoneField) telephoneField.value = oneciData.telephone;
                }
                
                if (oneciData.date_naissance) {
                    const dateField = document.getElementById('date_naissance');
                    if (dateField) dateField.value = oneciData.date_naissance;
                }
                
                console.log('Données ONECI pré-remplies avec succès');
                
                // Arrêter l'observation une fois les données remplies
                obs.disconnect();
            }
        });
        
        // Observer les changements dans le DOM
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
        
        // Stocker les données ONECI pour utilisation ultérieure
        window.oneciData = oneciData;
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
