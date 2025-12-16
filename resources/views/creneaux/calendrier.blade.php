@extends('creneaux.layout')

@section('title', 'Calendrier des Créneaux')
@section('subtitle', 'Visualisez et gérez la disponibilité des créneaux')

@section('creneaux_content')
<!-- Données pour JavaScript -->
<script>
    window.centreId = {{ auth()->user()->centre->id }};
    window.disponibiliteData = @json($disponibiliteData ?? []);
</script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Calendrier principal -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Calendrier des disponibilités
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button onclick="previousMonth()" class="p-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="currentMonth" class="text-lg font-medium text-gray-900"></span>
                        <button onclick="nextMonth()" class="p-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Calendrier -->
                <div id="calendarContainer" class="grid grid-cols-7 gap-1">
                    <!-- En-têtes des jours -->
                    <div class="text-center font-medium text-gray-500 py-2">Lun</div>
                    <div class="text-center font-medium text-gray-500 py-2">Mar</div>
                    <div class="text-center font-medium text-gray-500 py-2">Mer</div>
                    <div class="text-center font-medium text-gray-500 py-2">Jeu</div>
                    <div class="text-center font-medium text-gray-500 py-2">Ven</div>
                    <div class="text-center font-medium text-gray-500 py-2">Sam</div>
                    <div class="text-center font-medium text-gray-500 py-2">Dim</div>
                    
                    <!-- Jours du mois -->
                    <div id="calendarDays" class="col-span-7 grid grid-cols-7 gap-1">
                        <!-- Les jours seront générés dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte de disponibilité -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow sticky top-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2"></i>
                    Disponibilité
                </h3>
            </div>
            
            <div id="availabilityCard" class="p-6">
                <!-- Contenu dynamique -->
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-calendar-day text-4xl mb-4"></i>
                    <p>Sélectionnez une date dans le calendrier</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Légende -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h4 class="text-md font-semibold text-gray-900 mb-4">
        <i class="fas fa-info mr-2"></i>
        Légende
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h5 class="text-sm font-medium text-gray-700 mb-2">Statut des créneaux</h5>
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-sm text-gray-600">Disponible</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                    <span class="text-sm text-gray-600">Limité</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span class="text-sm text-gray-600">Complet</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-400 rounded"></div>
                    <span class="text-sm text-gray-600">Fermé</span>
                </div>
            </div>
        </div>
        <div>
            <h5 class="text-sm font-medium text-gray-700 mb-2">Indicateurs du calendrier</h5>
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-xs text-white font-bold">12</div>
                    <span class="text-sm text-gray-600">Créneaux disponibles</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center text-xs text-white font-bold">5</div>
                    <span class="text-sm text-gray-600">Capacité réduite</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-xs text-white font-bold">8</div>
                    <span class="text-sm text-gray-600">Horaires modifiés</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center text-xs text-white font-bold">0</div>
                    <span class="text-sm text-gray-600">Centre fermé</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDate = new Date();
let selectedDate = null;
let availabilityCache = new Map(); // Cache pour les données de disponibilité
let loadingStates = new Set(); // États de chargement pour éviter les appels multiples

// Initialiser le calendrier
document.addEventListener('DOMContentLoaded', function() {
    updateCalendar();
});

// Mettre à jour le calendrier
async function updateCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Mettre à jour le titre
    document.getElementById('currentMonth').textContent = 
        new Date(year, month).toLocaleDateString('fr-FR', { 
            year: 'numeric', 
            month: 'long' 
        });
    
    // Générer les jours du mois
    generateCalendarDays(year, month);
    
    // Charger toutes les disponibilités du mois en une seule fois
    await loadMonthAvailability(year, month + 1); // +1 car les mois JS commencent à 0
}

// Générer les jours du calendrier
function generateCalendarDays(year, month) {
    const container = document.getElementById('calendarDays');
    container.innerHTML = '';
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay() + 1); // Commencer par lundi
    
    const endDate = new Date(lastDay);
    endDate.setDate(endDate.getDate() + (7 - lastDay.getDay())); // Finir par dimanche
    
    console.log('=== GÉNÉRATION CALENDRIER ===');
    console.log('Année:', year, 'Mois:', month);
    console.log('Premier jour du mois:', firstDay);
    console.log('Dernier jour du mois:', lastDay);
    console.log('Date de début (lundi):', startDate);
    console.log('Date de fin (dimanche):', endDate);
    
    const today = new Date();
    const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    
    // Utiliser une boucle plus sûre pour éviter les problèmes de référence
    let currentDate = new Date(startDate);
    while (currentDate <= endDate) {
        const dayElement = document.createElement('div');
        dayElement.className = 'aspect-square flex items-center justify-center text-sm cursor-pointer rounded-lg transition-colors';
        
        const isCurrentMonth = currentDate.getMonth() === month;
        const isToday = currentDate.getTime() === todayDate.getTime();
        const isSelected = selectedDate && currentDate.getTime() === selectedDate.getTime();
        
        // Classes de base
        if (!isCurrentMonth) {
            dayElement.className += ' text-gray-300';
        } else {
            dayElement.className += ' text-gray-900 hover:bg-mayelia-50';
        }
        
        if (isToday) {
            dayElement.className += ' bg-mayelia-100 font-semibold';
        }
        
        if (isSelected) {
            dayElement.className += ' bg-mayelia-500 text-white';
        }
        
        // Ajouter un identifiant unique pour chaque jour
        dayElement.id = `day-${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
        
        dayElement.textContent = currentDate.getDate();
        
        // Créer une copie de la date pour éviter les problèmes de référence
        const dateCopy = new Date(currentDate.getTime());
        console.log('Génération calendrier - Date créée:', dateCopy, 'ID:', dayElement.id);
        
        dayElement.onclick = () => {
            console.log('CLIC sur élément:', dayElement.id, 'Date stockée:', dateCopy);
            selectDate(dateCopy);
        };
        
        container.appendChild(dayElement);
        
        // Passer au jour suivant
        currentDate.setDate(currentDate.getDate() + 1);
    }
}

// Charger toutes les disponibilités du mois en une seule requête
async function loadMonthAvailability(year, month) {
    const cacheKey = `${window.centreId}-${year}-${month}`;
    
    // Vérifier le cache d'abord
    if (availabilityCache.has(cacheKey)) {
        const cachedData = availabilityCache.get(cacheKey);
        updateAllDayIndicators(cachedData);
        return;
    }
    
    // Éviter les appels multiples
    if (loadingStates.has(cacheKey)) {
        return;
    }
    
    loadingStates.add(cacheKey);
    
    try {
        const response = await fetch(`/api/disponibilite-mois/${window.centreId}/${year}/${month}`);
        const data = await response.json();
        
        if (data.success) {
            // Mettre en cache pour le mois
            availabilityCache.set(cacheKey, data.data);
            
            // Mettre en cache chaque jour individuellement aussi
            Object.keys(data.data).forEach(dateStr => {
                const dayCacheKey = `${window.centreId}-${dateStr}`;
                availabilityCache.set(dayCacheKey, data.data[dateStr]);
            });
            
            // Mettre à jour tous les indicateurs
            updateAllDayIndicators(data.data);
        }
    } catch (error) {
        console.error('Erreur lors du chargement des disponibilités du mois:', error);
    } finally {
        loadingStates.delete(cacheKey);
    }
}

// Mettre à jour tous les indicateurs des jours du calendrier
function updateAllDayIndicators(disponibilites) {
    Object.keys(disponibilites).forEach(dateStr => {
        const date = new Date(dateStr + 'T00:00:00');
        const elementId = `day-${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        const element = document.getElementById(elementId);
        
        if (element) {
            const data = disponibilites[dateStr];
            updateDayIndicator(element, data);
        }
    });
}

// Charger la disponibilité pour un jour (conservé pour la compatibilité si nécessaire)
async function loadDayAvailability(date, element) {
    if (date < new Date().setHours(0, 0, 0, 0)) {
        element.className += ' opacity-50';
        return;
    }
    
    const dateStr = date.toISOString().split('T')[0];
    const cacheKey = `${window.centreId}-${dateStr}`;
    
    // Vérifier le cache d'abord
    if (availabilityCache.has(cacheKey)) {
        const cachedData = availabilityCache.get(cacheKey);
        updateDayIndicator(element, cachedData);
        return;
    }
    
    // Éviter les appels multiples
    if (loadingStates.has(cacheKey)) {
        return;
    }
    
    loadingStates.add(cacheKey);
    
    try {
        const response = await fetch(`/api/disponibilite/${window.centreId}/${dateStr}`);
        const data = await response.json();
        
        if (data.success) {
            // Mettre en cache
            availabilityCache.set(cacheKey, data.data);
            
            // Mettre à jour l'indicateur
            updateDayIndicator(element, data.data);
        }
    } catch (error) {
        console.error('Erreur lors du chargement de la disponibilité:', error);
    } finally {
        loadingStates.delete(cacheKey);
    }
}

// Fonction pour mettre à jour l'indicateur d'un jour
function updateDayIndicator(element, data) {
    const status = data.statut;
    let indicatorClass = '';
    let badgeText = '';
    
    switch (status) {
        case 'ouvert':
            const totalCreneaux = countTotalCreneaux(data.services);
            indicatorClass = 'bg-green-500';
            badgeText = totalCreneaux.toString();
            break;
        case 'ferme':
            indicatorClass = 'bg-gray-400';
            badgeText = '0';
            break;
        case 'capacite_reduite':
            const creneauxReduits = countTotalCreneaux(data.services);
            indicatorClass = 'bg-yellow-500';
            badgeText = creneauxReduits.toString();
            break;
        case 'horaires_modifies':
            const creneauxModifies = countTotalCreneaux(data.services);
            indicatorClass = 'bg-orange-500';
            badgeText = creneauxModifies.toString();
            break;
    }
    
    // Supprimer l'ancien indicateur s'il existe
    const oldIndicator = element.querySelector('.day-indicator');
    if (oldIndicator) {
        oldIndicator.remove();
    }
    
    // Ajouter le nouvel indicateur
    const indicator = document.createElement('div');
    indicator.className = `day-indicator absolute top-1 right-1 w-6 h-6 rounded-full ${indicatorClass} flex items-center justify-center text-xs text-white font-bold`;
    indicator.textContent = badgeText;
    element.style.position = 'relative';
    element.appendChild(indicator);
    
    // Stocker les données pour la sélection
    element.dataset.availabilityData = JSON.stringify(data);
}

// Compter le nombre total de créneaux disponibles
function countTotalCreneaux(services) {
    let total = 0;
    if (services) {
        Object.values(services).forEach(service => {
            Object.values(service.formules).forEach(formule => {
                total += formule.creneaux.length;
            });
        });
    }
    return total;
}

// Sélectionner une date
async function selectDate(date) {
    console.log('=== SÉLECTION DE DATE ===');
    console.log('Date sélectionnée:', date);
    console.log('Date ISO:', date.toISOString());
    console.log('Date locale:', date.toLocaleDateString('fr-FR'));
    
    selectedDate = date;
    updateCalendar();
    
    // Trouver l'élément du jour sélectionné par son ID
    const dateId = `day-${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    console.log('ID recherché:', dateId);
    
    const selectedElement = document.getElementById(dateId);
    console.log('Élément trouvé:', selectedElement);
    
    // Si on a des données stockées, les utiliser directement
    if (selectedElement && selectedElement.dataset.availabilityData) {
        console.log('Utilisation des données mises en cache');
        const data = JSON.parse(selectedElement.dataset.availabilityData);
        console.log('Données mises en cache:', data);
        displayAvailability(data, date);
    } else {
        console.log('Chargement des données depuis l\'API');
        // Sinon, charger les données
        await loadAvailabilityForDate(date);
    }
}

// Charger la disponibilité pour une date sélectionnée
async function loadAvailabilityForDate(date) {
    console.log('=== CHARGEMENT DEPUIS API ===');
    console.log('Date pour l\'API:', date);
    console.log('Date ISO pour l\'API:', date.toISOString().split('T')[0]);
    
    const card = document.getElementById('availabilityCard');
    card.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-mayelia-500"></i><p class="mt-2">Chargement...</p></div>';
    
    try {
        const url = `/api/disponibilite/${window.centreId}/${date.toISOString().split('T')[0]}`;
        console.log('URL de l\'API:', url);
        
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('Réponse de l\'API:', data);
        
        if (data.success) {
            displayAvailability(data.data, date);
        } else {
            card.innerHTML = '<div class="text-center text-red-500 py-4"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Erreur lors du chargement</p></div>';
        }
    } catch (error) {
        console.error('Erreur:', error);
        card.innerHTML = '<div class="text-center text-red-500 py-4"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Erreur de connexion</p></div>';
    }
}

// Afficher la disponibilité (optimisé)
function displayAvailability(data, date) {
    console.log('=== AFFICHAGE DISPONIBILITÉ ===');
    console.log('Date passée en paramètre:', date);
    console.log('Date des données API:', data.date);
    
    const card = document.getElementById('availabilityCard');
    
    // Afficher un indicateur de chargement
    card.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-mayelia-500"></i><p class="mt-2">Chargement des créneaux...</p></div>';
    
    // Utiliser requestAnimationFrame pour différer le rendu lourd
    requestAnimationFrame(() => {
        renderAvailabilityContent(data, date, card);
    });
}

// Fonction séparée pour le rendu du contenu
function renderAvailabilityContent(data, date, card) {
    // TOUJOURS utiliser la date passée en paramètre (date sélectionnée)
    const displayDate = date;
    console.log('Date d\'affichage finale (forcée):', displayDate);
    
    const dateStr = displayDate.toLocaleDateString('fr-FR', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    console.log('Date formatée:', dateStr);
    
    let html = `
        <div class="mb-4">
            <h4 class="font-semibold text-gray-900">${dateStr}</h4>
            <p class="text-sm text-gray-600">${data.message}</p>
        </div>
    `;
    
    // Afficher les heures de travail et de pause si disponibles
    if (data.jour_travail) {
        // Fonction pour formater les heures de manière sûre
        const formatTime = (timeString) => {
            if (!timeString) return '';
            try {
                // Gérer les formats ISO (2025-09-05T08:00:00) et HH:MM:SS
                if (timeString.includes('T')) {
                    // Format ISO : 2025-09-05T08:00:00
                    const timePart = timeString.split('T')[1];
                    const [hours, minutes] = timePart.split(':');
                    return `${hours.padStart(2, '0')}h${minutes.padStart(2, '0')}`;
                } else if (timeString.includes(':')) {
                    // Format HH:MM:SS ou HH:MM
                    const [hours, minutes] = timeString.split(':');
                    return `${hours.padStart(2, '0')}h${minutes.padStart(2, '0')}`;
                }
                return timeString;
            } catch (e) {
                console.error('Erreur formatage heure:', timeString, e);
                return timeString;
            }
        };
        
        const heureDebut = formatTime(data.jour_travail.heure_debut);
        const heureFin = formatTime(data.jour_travail.heure_fin);
        const pauseDebut = formatTime(data.jour_travail.pause_debut);
        const pauseFin = formatTime(data.jour_travail.pause_fin);
        
        if (heureDebut && heureFin) {
            html += `
                <div class="mb-4 p-3 bg-mayelia-50 rounded-lg">
                    <h5 class="text-sm font-medium text-mayelia-900 mb-2">
                        <i class="fas fa-clock mr-1"></i>
                        Horaires de travail
                    </h5>
                    <div class="text-sm text-mayelia-800">
                        <div class="flex items-center justify-between">
                            <span>Ouverture :</span>
                            <span class="font-medium">${heureDebut}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Fermeture :</span>
                            <span class="font-medium">${heureFin}</span>
                        </div>
            `;
            
            if (pauseDebut && pauseFin) {
                html += `
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-mayelia-200">
                            <span class="text-orange-700">
                                <i class="fas fa-pause mr-1"></i>
                                Pause :
                            </span>
                            <span class="font-medium text-orange-700">${pauseDebut} - ${pauseFin}</span>
                        </div>
                `;
            }
            
            html += `
                    </div>
                </div>
            `;
        }
    }
    
    if (data.statut === 'ferme') {
        html += `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-times-circle text-4xl mb-4"></i>
                <p>Centre fermé</p>
            </div>
        `;
    } else if (data.services && Object.keys(data.services).length > 0) {
        // Créer les onglets de services
        const services = Object.values(data.services);
        const serviceIds = Object.keys(data.services);
        
        html += '<div class="space-y-4">';
        
        // Onglets de services
        html += '<div class="border-b border-gray-200">';
        html += '<nav class="-mb-px flex space-x-8" aria-label="Tabs">';
        
        services.forEach((service, index) => {
            const isActive = index === 0;
            html += `
                <button onclick="switchServiceTab('${serviceIds[index]}')" 
                        id="tab-${serviceIds[index]}"
                        class="py-2 px-1 border-b-2 font-medium text-sm ${isActive ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'}">
                    ${service.nom}
                </button>
            `;
        });
        
        html += '</nav>';
        html += '</div>';
        
        // Contenu des onglets
        services.forEach((service, index) => {
            const isActive = index === 0;
            html += `
                <div id="content-${serviceIds[index]}" class="service-content ${isActive ? '' : 'hidden'}">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900 mb-2">${service.nom}</h5>
                        <p class="text-sm text-gray-600 mb-4">${service.description}</p>
            `;
            
            Object.values(service.formules).forEach(formule => {
                html += `
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-sm" style="color: ${formule.couleur}">${formule.nom}</span>
                            <span class="text-sm text-gray-600 font-semibold">${formule.prix.toLocaleString()} FCFA</span>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                `;
                
                // Afficher un indicateur de pause si elle existe
                if (data.jour_travail && data.jour_travail.pause_debut && data.jour_travail.pause_fin) {
                    const formatTime = (timeString) => {
                        if (!timeString) return '';
                        try {
                            if (timeString.includes('T')) {
                                const timePart = timeString.split('T')[1];
                                const [hours, minutes] = timePart.split(':');
                                return `${hours.padStart(2, '0')}h${minutes.padStart(2, '0')}`;
                            } else if (timeString.includes(':')) {
                                const [hours, minutes] = timeString.split(':');
                                return `${hours.padStart(2, '0')}h${minutes.padStart(2, '0')}`;
                            }
                            return timeString;
                        } catch (e) {
                            return timeString;
                        }
                    };
                    
                    const pauseDebut = formatTime(data.jour_travail.pause_debut);
                    const pauseFin = formatTime(data.jour_travail.pause_fin);
                    
                    html += `
                        <div class="text-center py-2 my-2 bg-orange-100 border border-orange-300 rounded">
                            <i class="fas fa-pause text-orange-500 mr-2"></i>
                            <span class="text-sm font-medium text-orange-700">Pause : ${pauseDebut} - ${pauseFin}</span>
                        </div>
                    `;
                }
                
                // Optimiser le rendu des créneaux avec pagination
                const creneaux = formule.creneaux;
                const creneauxPerPage = 10; // Afficher 10 créneaux à la fois
                const totalPages = Math.ceil(creneaux.length / creneauxPerPage);
                
                // Afficher seulement les premiers créneaux
                const creneauxToShow = creneaux.slice(0, creneauxPerPage);
                
                const creneauxHtml = creneauxToShow.map(creneau => {
                    const statutClass = creneau.statut === 'disponible' ? 'text-green-600 bg-green-50' : 
                                       creneau.statut === 'limite' ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50';
                    const statutIcon = creneau.statut === 'disponible' ? 'fa-check-circle' : 
                                     creneau.statut === 'limite' ? 'fa-exclamation-triangle' : 'fa-times-circle';
                    
                    // Vérifier si ce créneau est dans la période de pause
                    let pauseClass = '';
                    let pauseIcon = '';
                    
                    if (data.jour_travail && data.jour_travail.pause_debut && data.jour_travail.pause_fin) {
                        const creneauStart = creneau.tranche_horaire.split(' - ')[0];
                        const pauseStart = data.jour_travail.pause_debut;
                        const pauseEnd = data.jour_travail.pause_fin;
                        
                        if (creneauStart >= pauseStart && creneauStart < pauseEnd) {
                            pauseClass = 'bg-orange-100 border-orange-300 border-l-4';
                            pauseIcon = '<i class="fas fa-pause text-orange-500 mr-2"></i>';
                        }
                    }
                    
                    return `
                        <div class="flex items-center justify-between text-sm p-2 rounded ${statutClass} ${pauseClass}">
                            <span class="font-medium">${pauseIcon}${creneau.tranche_horaire}</span>
                            <div class="flex items-center space-x-2">
                                <i class="fas ${statutIcon}"></i>
                                <span class="font-semibold">${creneau.disponible}/${creneau.capacite_totale}</span>
                            </div>
                        </div>
                    `;
                }).join('');
                
                html += creneauxHtml;
                
                // Ajouter un bouton "Voir plus" si nécessaire
                if (totalPages > 1) {
                    html += `
                        <div class="text-center mt-3">
                            <button onclick="loadMoreCreneaux('${serviceId}', '${formuleId}', 1)" 
                                    class="text-sm text-mayelia-600 hover:text-mayelia-800 font-medium">
                                Voir plus (${creneaux.length - creneauxPerPage} autres créneaux)
                            </button>
                        </div>
                    `;
                }
                
                html += '</div></div>';
            });
            
            html += '</div></div>';
        });
        
        html += '</div>';
    } else {
        html += `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-4"></i>
                <p>Aucun service disponible</p>
            </div>
        `;
    }
    
    card.innerHTML = html;
}

// Fonction pour charger plus de créneaux (pagination)
function loadMoreCreneaux(serviceId, formuleId, page) {
    // Cette fonction sera implémentée pour charger plus de créneaux
    console.log('Chargement de plus de créneaux pour:', serviceId, formuleId, page);
}

// Navigation du calendrier
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
}

// Changer d'onglet de service
function switchServiceTab(serviceId) {
    // Masquer tous les contenus
    document.querySelectorAll('.service-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Désactiver tous les onglets
    document.querySelectorAll('[id^="tab-"]').forEach(tab => {
        tab.classList.remove('border-mayelia-500', 'text-mayelia-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Afficher le contenu sélectionné
    const content = document.getElementById('content-' + serviceId);
    if (content) {
        content.classList.remove('hidden');
    }
    
    // Activer l'onglet sélectionné
    const tab = document.getElementById('tab-' + serviceId);
    if (tab) {
        tab.classList.remove('border-transparent', 'text-gray-500');
        tab.classList.add('border-mayelia-500', 'text-mayelia-600');
    }
}
</script>
@endsection