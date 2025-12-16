@extends('layouts.dashboard')

@section('title', 'Nouveau Dossier - Sur Place')
@section('subtitle', 'Créer un dossier pour un client présent sur place')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Nouveau Dossier - Sur Place</h2>
            <p class="text-gray-600">Créer un dossier pour un client présent au centre</p>
        </div>
        <a href="{{ route('dossiers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour
        </a>
    </div>

    <!-- Indicateur de progression -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4 flex-1">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-mayelia-600 text-white font-semibold step-indicator" data-step="1">
                        1
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Client</p>
                        <p class="text-xs text-gray-500">Créer ou sélectionner le client</p>
                    </div>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-4">
                    <div class="h-full bg-mayelia-600 progress-bar" style="width: 0%"></div>
                </div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="2">
                        2
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Service</p>
                        <p class="text-xs text-gray-500">Sélectionner service et formule</p>
                    </div>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-4">
                    <div class="h-full bg-gray-200 progress-bar" style="width: 0%"></div>
                </div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="3">
                        3
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Confirmation</p>
                        <p class="text-xs text-gray-500">Créer le dossier</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire multi-étapes -->
    <form id="walkinForm" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Étape 1: Client -->
        <div id="step-1" class="wizard-step">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    <i class="fas fa-user mr-2 text-mayelia-600"></i>
                    Étape 1: Client
                </h3>
                <p class="text-gray-600">Recherchez un client existant ou créez-en un nouveau</p>
            </div>

            <!-- Recherche de client existant -->
            <div class="mb-6 p-4 bg-mayelia-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2"></i>Rechercher un client existant
                </label>
                <div class="flex space-x-2">
                    <input type="text" 
                           id="clientSearch" 
                           placeholder="Nom, email ou téléphone..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    <button type="button" 
                            onclick="searchClient()" 
                            class="bg-mayelia-600 text-white px-6 py-2 rounded-lg hover:bg-mayelia-700">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                </div>
                <div id="clientSearchResults" class="mt-4 hidden">
                    <!-- Résultats de recherche -->
                </div>
            </div>

            <div class="text-center my-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">OU</span>
                    </div>
                </div>
            </div>

            <!-- Formulaire de création de client -->
            <div id="newClientForm">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Créer un nouveau client</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" 
                               name="client_nom" 
                               id="client_nom"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                        <input type="text" 
                               name="client_prenom" 
                               id="client_prenom"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" 
                               name="client_email" 
                               id="client_email"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                        <input type="text" 
                               name="client_telephone" 
                               id="client_telephone"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                        <input type="date" 
                               name="client_date_naissance" 
                               id="client_date_naissance"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lieu de naissance</label>
                        <input type="text" 
                               name="client_lieu_naissance" 
                               id="client_lieu_naissance"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                        <input type="text" 
                               name="client_adresse" 
                               id="client_adresse"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profession</label>
                        <input type="text" 
                               name="client_profession" 
                               id="client_profession"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sexe</label>
                        <select name="client_sexe" 
                                id="client_sexe"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de pièce d'identité</label>
                        <select name="client_type_piece_identite" 
                                id="client_type_piece_identite"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                            <option value="">Sélectionner</option>
                            <option value="CNI">CNI</option>
                            <option value="PASSEPORT">Passeport</option>
                            <option value="PERMIS">Permis de conduire</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de pièce d'identité</label>
                        <input type="text" 
                               name="client_numero_piece_identite" 
                               id="client_numero_piece_identite"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                    </div>
                </div>
            </div>

            <input type="hidden" name="client_id" id="client_id">

            <div class="mt-6 flex justify-end">
                <button type="button" 
                        onclick="nextStep(2)" 
                        class="bg-mayelia-600 text-white px-6 py-2 rounded-lg hover:bg-mayelia-700">
                    Suivant <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Étape 2: Service et Formule -->
        <div id="step-2" class="wizard-step hidden">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    <i class="fas fa-clipboard-list mr-2 text-mayelia-600"></i>
                    Étape 2: Service et Formule
                </h3>
                <p class="text-gray-600">Sélectionnez le service et la formule souhaités</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service *</label>
                    <select name="service_id" 
                            id="service_id"
                            required
                            onchange="loadFormules()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formule *</label>
                    <select name="formule_id" 
                            id="formule_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:border-mayelia-500">
                        <option value="">Sélectionner d'abord un service</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" 
                        onclick="previousStep(1)" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Précédent
                </button>
                <button type="button" 
                        onclick="nextStep(3)" 
                        class="bg-mayelia-600 text-white px-6 py-2 rounded-lg hover:bg-mayelia-700">
                    Suivant <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Étape 3: Confirmation -->
        <div id="step-3" class="wizard-step hidden">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                    Étape 3: Confirmation
                </h3>
                <p class="text-gray-600">Vérifiez les informations avant de créer le dossier</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h4>
                <div id="summaryContent" class="space-y-3">
                    <!-- Le contenu sera rempli dynamiquement -->
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" 
                        onclick="previousStep(2)" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Précédent
                </button>
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Créer le dossier
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let currentStep = 1;
let selectedClient = null;

function validateStep(step) {
    if (step === 1) {
        // Validation étape 1 : Client
        const clientId = document.getElementById('client_id').value;
        const clientNom = document.getElementById('client_nom').value.trim();
        const clientPrenom = document.getElementById('client_prenom').value.trim();
        const clientEmail = document.getElementById('client_email').value.trim();
        const clientTelephone = document.getElementById('client_telephone').value.trim();
        
        // Si un client est sélectionné, c'est bon
        if (clientId) {
            return { valid: true };
        }
        
        // Sinon, vérifier que tous les champs obligatoires sont remplis
        const errors = [];
        
        if (!clientNom) {
            errors.push('Le nom est obligatoire');
            document.getElementById('client_nom').classList.add('border-red-500');
        } else {
            document.getElementById('client_nom').classList.remove('border-red-500');
        }
        
        if (!clientPrenom) {
            errors.push('Le prénom est obligatoire');
            document.getElementById('client_prenom').classList.add('border-red-500');
        } else {
            document.getElementById('client_prenom').classList.remove('border-red-500');
        }
        
        if (!clientEmail) {
            errors.push('L\'email est obligatoire');
            document.getElementById('client_email').classList.add('border-red-500');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(clientEmail)) {
            errors.push('L\'email n\'est pas valide');
            document.getElementById('client_email').classList.add('border-red-500');
        } else {
            document.getElementById('client_email').classList.remove('border-red-500');
        }
        
        if (!clientTelephone) {
            errors.push('Le téléphone est obligatoire');
            document.getElementById('client_telephone').classList.add('border-red-500');
        } else {
            document.getElementById('client_telephone').classList.remove('border-red-500');
        }
        
        if (errors.length > 0) {
            return { 
                valid: false, 
                message: 'Veuillez corriger les erreurs suivantes :\n' + errors.join('\n') 
            };
        }
        
        return { valid: true };
        
    } else if (step === 2) {
        // Validation étape 2 : Service et Formule
        const serviceId = document.getElementById('service_id').value;
        const formuleId = document.getElementById('formule_id').value;
        const errors = [];
        
        if (!serviceId) {
            errors.push('Veuillez sélectionner un service');
            document.getElementById('service_id').classList.add('border-red-500');
        } else {
            document.getElementById('service_id').classList.remove('border-red-500');
        }
        
        if (!formuleId) {
            errors.push('Veuillez sélectionner une formule');
            document.getElementById('formule_id').classList.add('border-red-500');
        } else {
            document.getElementById('formule_id').classList.remove('border-red-500');
        }
        
        if (errors.length > 0) {
            return { 
                valid: false, 
                message: errors.join('\n') 
            };
        }
        
        return { valid: true };
    }
    
    return { valid: true };
}

function nextStep(step) {
    // Validation de l'étape actuelle avant de passer à la suivante
    const validation = validateStep(currentStep);
    
    if (!validation.valid) {
        if (typeof showErrorToast === 'function') {
            showErrorToast(validation.message);
        } else {
            alert(validation.message);
        }
        
        // Faire défiler vers le premier champ en erreur
        const firstErrorField = document.querySelector('.border-red-500');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorField.focus();
        }
        return;
    }
    
    // Si on passe à l'étape 3, afficher le récapitulatif
    if (step === 3) {
        showSummary();
    }
    
    // Masquer l'étape actuelle
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    
    // Afficher la nouvelle étape
    document.getElementById(`step-${step}`).classList.remove('hidden');
    
    // Mettre à jour les indicateurs
    updateProgress(step);
    currentStep = step;
}

function previousStep(step) {
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateProgress(step);
    currentStep = step;
}

function updateProgress(step) {
    // Mettre à jour les indicateurs visuels
    for (let i = 1; i <= 3; i++) {
        const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
        if (i < step) {
            indicator.classList.remove('bg-gray-300', 'text-gray-600');
            indicator.classList.add('bg-green-600', 'text-white');
        } else if (i === step) {
            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-green-600', 'text-white');
            indicator.classList.add('bg-mayelia-600', 'text-white');
        } else {
            indicator.classList.remove('bg-mayelia-600', 'text-white', 'bg-green-600', 'text-white');
            indicator.classList.add('bg-gray-300', 'text-gray-600');
        }
    }
    
    // Mettre à jour les barres de progression
    const progress = ((step - 1) / 2) * 100;
    document.querySelectorAll('.progress-bar').forEach((bar, index) => {
        if (index < step - 1) {
            bar.style.width = '100%';
            bar.classList.remove('bg-gray-200');
            bar.classList.add('bg-mayelia-600');
        } else if (index === step - 2) {
            bar.style.width = progress + '%';
        } else {
            bar.style.width = '0%';
            bar.classList.remove('bg-mayelia-600');
            bar.classList.add('bg-gray-200');
        }
    });
}

async function searchClient() {
    const searchTerm = document.getElementById('clientSearch').value.trim();
    if (searchTerm.length < 2) {
        alert('Veuillez saisir au moins 2 caractères pour la recherche.');
        return;
    }
    
    try {
        const response = await fetch(`/api/clients/search?q=${encodeURIComponent(searchTerm)}`);
        const clients = await response.json();
        
        const resultsDiv = document.getElementById('clientSearchResults');
        if (clients.length === 0) {
            resultsDiv.innerHTML = '<p class="text-gray-500 text-center py-4">Aucun client trouvé</p>';
            resultsDiv.classList.remove('hidden');
            return;
        }
        
        let html = '<div class="space-y-2">';
        clients.forEach(client => {
            html += `
                <div class="p-3 border border-gray-200 rounded-lg hover:bg-mayelia-50 cursor-pointer client-result" 
                     data-client-id="${client.id}"
                     onclick="selectClient(${client.id}, '${client.nom}', '${client.prenom}', '${client.email}', '${client.telephone}')">
                    <div class="font-medium text-gray-900">${client.nom} ${client.prenom}</div>
                    <div class="text-sm text-gray-500">${client.email} - ${client.telephone}</div>
                </div>
            `;
        });
        html += '</div>';
        
        resultsDiv.innerHTML = html;
        resultsDiv.classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
        alert('Erreur lors de la recherche de clients.');
    }
}

function selectClient(id, nom, prenom, email, telephone) {
    selectedClient = { id, nom, prenom, email, telephone };
    document.getElementById('client_id').value = id;
    
    // Désactiver le formulaire de création
    document.getElementById('newClientForm').querySelectorAll('input, select').forEach(el => {
        el.disabled = true;
    });
    
    // Afficher un message de confirmation
    const resultsDiv = document.getElementById('clientSearchResults');
    resultsDiv.innerHTML = `
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <div>
                    <div class="font-medium text-green-900">Client sélectionné: ${nom} ${prenom}</div>
                    <div class="text-sm text-green-700">${email}</div>
                </div>
                <button type="button" 
                        onclick="clearClientSelection()" 
                        class="ml-auto text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
}

function clearClientSelection() {
    selectedClient = null;
    document.getElementById('client_id').value = '';
    document.getElementById('clientSearch').value = '';
    document.getElementById('clientSearchResults').innerHTML = '';
    document.getElementById('clientSearchResults').classList.add('hidden');
    
    // Réactiver le formulaire de création
    document.getElementById('newClientForm').querySelectorAll('input, select').forEach(el => {
        el.disabled = false;
    });
}

async function loadFormules() {
    const serviceId = document.getElementById('service_id').value;
    const formuleSelect = document.getElementById('formule_id');
    const centreId = {{ $centre->id }};
    
    if (!serviceId) {
        formuleSelect.innerHTML = '<option value="">Sélectionner d\'abord un service</option>';
        return;
    }
    
    // Afficher un indicateur de chargement
    formuleSelect.disabled = true;
    formuleSelect.innerHTML = '<option value="">Chargement...</option>';
    
    try {
        // Utiliser l'API qui prend en compte le centre
        const response = await fetch(`/booking/formules/${centreId}/${serviceId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        formuleSelect.innerHTML = '<option value="">Sélectionner une formule</option>';
        if (data.success && data.formules && data.formules.length > 0) {
            data.formules.forEach(formule => {
                const option = document.createElement('option');
                option.value = formule.id;
                option.textContent = formule.nom + (formule.prix ? ' - ' + new Intl.NumberFormat('fr-FR').format(formule.prix) + ' FCFA' : '');
                formuleSelect.appendChild(option);
            });
        } else {
            formuleSelect.innerHTML = '<option value="">Aucune formule disponible pour ce service</option>';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des formules:', error);
        formuleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        showErrorToast('Erreur lors du chargement des formules. Veuillez réessayer.');
    } finally {
        formuleSelect.disabled = false;
    }
}

function showSummary() {
    const summaryDiv = document.getElementById('summaryContent');
    let html = '';
    
    // Client
    if (selectedClient) {
        html += `
            <div class="border-b pb-3">
                <h5 class="font-semibold text-gray-900 mb-2">Client</h5>
                <p class="text-gray-700">${selectedClient.nom} ${selectedClient.prenom}</p>
                <p class="text-sm text-gray-500">${selectedClient.email} - ${selectedClient.telephone}</p>
            </div>
        `;
    } else {
        const nom = document.getElementById('client_nom').value;
        const prenom = document.getElementById('client_prenom').value;
        const email = document.getElementById('client_email').value;
        html += `
            <div class="border-b pb-3">
                <h5 class="font-semibold text-gray-900 mb-2">Nouveau Client</h5>
                <p class="text-gray-700">${nom} ${prenom}</p>
                <p class="text-sm text-gray-500">${email}</p>
            </div>
        `;
    }
    
    // Service et Formule
    const serviceSelect = document.getElementById('service_id');
    const formuleSelect = document.getElementById('formule_id');
    const serviceName = serviceSelect.options[serviceSelect.selectedIndex].text;
    const formuleName = formuleSelect.options[formuleSelect.selectedIndex].text;
    
    html += `
        <div class="border-b pb-3">
            <h5 class="font-semibold text-gray-900 mb-2">Service</h5>
            <p class="text-gray-700">${serviceName}</p>
        </div>
        <div>
            <h5 class="font-semibold text-gray-900 mb-2">Formule</h5>
            <p class="text-gray-700">${formuleName}</p>
        </div>
    `;
    
    summaryDiv.innerHTML = html;
}

// Gestion de la soumission du formulaire
document.getElementById('walkinForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création en cours...';
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('{{ route("dossiers.store-walkin") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Afficher un message de succès
            alert('Dossier créé avec succès ! Redirection vers le workflow...');
            window.location.href = data.redirect_url;
        } else {
            // Afficher les erreurs
            if (data.errors) {
                let errorMsg = 'Erreurs de validation:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMsg += `- ${data.errors[key][0]}\n`;
                });
                alert(errorMsg);
            } else {
                alert(data.message || 'Erreur lors de la création du dossier.');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la création du dossier.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>
@endsection

