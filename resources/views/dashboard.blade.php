@extends('layouts.dashboard')

@section('title', 'Tableau de bord')
@section('subtitle', 'Bienvenue, ' . Auth::user()->nom . '. Voici un aperçu de l\'activité de votre centre.')

@section('header-actions')
<button class="bg-mayelia-600 hover:bg-mayelia-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
    <i class="fas fa-plus"></i>
    <span>Nouveau rendez-vous</span>
</button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ Auth::user()->role === 'agent' ? 'Mes dossiers finalisés' : 'Dossiers finalisés (Centre)' }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['documents_traites'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-mayelia-100 text-mayelia-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Planning aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rdv_aujourdhui'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full {{ Auth::user()->role === 'agent' ? 'bg-purple-100 text-purple-600' : 'bg-green-100 text-green-600' }}">
                    <i class="fas {{ Auth::user()->role === 'agent' ? 'fa-folder-open' : 'fa-users' }} text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ Auth::user()->role === 'agent' ? 'Mes dossiers en cours' : 'Utilisateurs actifs' }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->role === 'agent' ? ($stats['mes_dossiers_en_cours'] ?? 0) : ($stats['utilisateurs_actifs'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du centre et calendrier -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations du centre -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-building text-mayelia-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Informations du centre</h3>
            </div>
            
            @if(Auth::user()->centre)
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ Auth::user()->centre->nom }}</h4>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->adresse }}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->email }}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->telephone }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4 pt-4 border-t">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->users->count() }} utilisateurs</span>
                        </div>
                        
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-cogs w-4 h-4 mr-2"></i>
                            <span>{{ Auth::user()->centre->services->count() }} services</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-600">Aucun centre assigné</p>
            @endif
        </div>
        
        <!-- Calendrier des rendez-vous -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center mb-4">
                <i class="fas fa-calendar text-mayelia-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Calendrier des rendez-vous</h3>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Visualisez tous les rendez-vous pris dans votre centre.
            </p>
            
            <!-- Widget calendrier simple -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 id="calendarMonth" class="font-medium text-gray-900">
                        {{ $currentDate->locale('fr')->translatedFormat('F Y') }}
                    </h4>
                    <div class="flex space-x-2">
                        <button onclick="previousMonth()" class="p-1 hover:bg-gray-200 rounded">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </button>
                        <button onclick="nextMonth()" class="p-1 hover:bg-gray-200 rounded">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    <div class="p-2 text-gray-500 font-medium">Di</div>
                    <div class="p-2 text-gray-500 font-medium">Lu</div>
                    <div class="p-2 text-gray-500 font-medium">Ma</div>
                    <div class="p-2 text-gray-500 font-medium">Me</div>
                    <div class="p-2 text-gray-500 font-medium">Je</div>
                    <div class="p-2 text-gray-500 font-medium">Ve</div>
                    <div class="p-2 text-gray-500 font-medium">Sa</div>
                    
                    <!-- Jours du mois - générés dynamiquement -->
                    <div id="calendarDays" class="col-span-7 grid grid-cols-7 gap-1">
                        <!-- Le contenu sera généré par JavaScript -->
                    </div>
                </div>
                
                <!-- Légende -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-center space-x-4 text-xs">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                            <span class="text-gray-600">Avec RDV</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-300 rounded-full mr-1"></div>
                            <span class="text-gray-600">Aujourd'hui</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal pour afficher les rendez-vous d'un jour -->
            <div id="rdvModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Rendez-vous</h3>
                        <button onclick="closeRdvModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4 overflow-y-auto flex-1" id="modalContent">
                        <!-- Le contenu sera rempli dynamiquement -->
                    </div>
                </div>
            </div>
            
            <script>
            let currentCalendarDate = new Date({{ $currentDate->year }}, {{ $currentDate->month - 1 }}, 1);
            let rendezVousData = {};
            
            // Noms des mois en français
            const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            
            // Charger les rendez-vous du mois actuel
            loadRendezVous();
            
            function loadRendezVous() {
                const year = currentCalendarDate.getFullYear();
                const month = currentCalendarDate.getMonth() + 1;
                
                fetch(`/api/dashboard/rendez-vous?year=${year}&month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            rendezVousData = data.rendezVous;
                            renderCalendar();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des rendez-vous:', error);
                        renderCalendar();
                    });
            }
            
            function renderCalendar() {
                const year = currentCalendarDate.getFullYear();
                const month = currentCalendarDate.getMonth();
                
                // Mettre à jour le titre
                document.getElementById('calendarMonth').textContent = 
                    months[month] + ' ' + year;
                
                // Premier jour du mois
                const firstDay = new Date(year, month, 1);
                const firstDayOfWeek = firstDay.getDay(); // 0 = Dimanche, 1 = Lundi, etc.
                
                // Dernier jour du mois
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                
                // Aujourd'hui
                const today = new Date();
                const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
                const todayDate = isCurrentMonth ? today.getDate() : null;
                
                let html = '';
                
                // Jours vides avant le premier jour du mois
                for (let i = 0; i < firstDayOfWeek; i++) {
                    html += '<div class="p-2"></div>';
                }
                
                // Jours du mois
                for (let day = 1; day <= daysInMonth; day++) {
                    const hasRendezVous = rendezVousData[day] && rendezVousData[day].length > 0;
                    const isToday = day === todayDate;
                    const rdvCount = hasRendezVous ? rendezVousData[day].length : 0;
                    
                    let classes = 'p-2 text-center rounded transition-colors';
                    
                    if (hasRendezVous) {
                        classes += ' bg-green-100 text-green-700 font-semibold cursor-pointer hover:bg-green-200';
                    } else {
                        classes += ' cursor-default';
                    }
                    
                    if (isToday && !hasRendezVous) {
                        classes += ' bg-gray-300 font-semibold';
                    } else if (isToday && hasRendezVous) {
                        classes += ' ring-2 ring-gray-400';
                    }
                    
                    const onClick = hasRendezVous ? `onclick="showRendezVousModal(${day})"` : '';
                    const title = hasRendezVous ? `title="Cliquez pour voir les ${rdvCount} rendez-vous"` : '';
                    
                    html += `
                        <div class="${classes}" ${onClick} ${title}>
                            <div>${day}</div>
                            ${hasRendezVous ? `<div class="text-xs mt-1 font-bold">${rdvCount}</div>` : ''}
                        </div>
                    `;
                }
                
                document.getElementById('calendarDays').innerHTML = html;
            }
            
            function previousMonth() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                loadRendezVous();
            }
            
            function nextMonth() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                loadRendezVous();
            }
            
            function showRendezVousModal(day) {
                const modal = document.getElementById('rdvModal');
                const modalTitle = document.getElementById('modalTitle');
                const modalContent = document.getElementById('modalContent');
                
                const year = currentCalendarDate.getFullYear();
                const month = currentCalendarDate.getMonth() + 1;
                const monthName = months[currentCalendarDate.getMonth()];
                
                modalTitle.textContent = `Rendez-vous du ${day} ${monthName} ${year}`;
                
                // Récupérer les rendez-vous du jour depuis les données stockées
                const rendezVous = rendezVousData[day] || [];
                
                let html = '';
                if (rendezVous && rendezVous.length > 0) {
                    html = '<div class="space-y-4">';
                    rendezVous.forEach((rdv, index) => {
                        const statutColors = {
                            'confirme': 'bg-mayelia-100 text-mayelia-800',
                            'termine': 'bg-green-100 text-green-800',
                            'annule': 'bg-red-100 text-red-800',
                            'dossier_ouvert': 'bg-purple-100 text-purple-800'
                        };
                        const statutColor = statutColors[rdv.statut] || 'bg-gray-100 text-gray-800';
                        const statutText = {
                            'confirme': 'Confirmé',
                            'termine': 'Terminé',
                            'annule': 'Annulé',
                            'dossier_ouvert': 'Dossier ouvert'
                        };
                        
                        html += `
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="w-10 h-10 bg-mayelia-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-mayelia-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">${rdv.client}</h4>
                                                <p class="text-sm text-gray-600">${rdv.service}</p>
                                            </div>
                                        </div>
                                        <div class="ml-13 space-y-1">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-clock w-4 h-4 mr-2 text-gray-400"></i>
                                                <span>${rdv.tranche_horaire}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statutColor}">
                                            ${statutText[rdv.statut] || rdv.statut}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center py-8"><p class="text-gray-500">Aucun rendez-vous pour ce jour</p></div>';
                }
                
                modalContent.innerHTML = html;
                modal.classList.remove('hidden');
            }
            
            function closeRdvModal() {
                document.getElementById('rdvModal').classList.add('hidden');
            }
            
            // Fermer le modal en cliquant en dehors
            document.getElementById('rdvModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRdvModal();
                }
            });
            </script>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @isAdmin
            <a href="{{ route('centres.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-mayelia-100 text-mayelia-600 rounded-lg mr-3">
                    <i class="fas fa-cogs"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Gérer les services</h4>
                    <p class="text-sm text-gray-600">Configurer les services et formules</p>
                </div>
            </a>
            @endisAdmin
            
            @isAdmin
            <a href="{{ route('jours-travail.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Jours de travail</h4>
                    <p class="text-sm text-gray-600">Configurer les horaires</p>
                </div>
            </a>
            @endisAdmin
            
            @isAdmin
            <a href="{{ route('creneaux.templates') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg mr-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Templates</h4>
                    <p class="text-sm text-gray-600">Gérer les créneaux</p>
                </div>
            </a>
            @endisAdmin
            
            <a href="{{ route('dossiers.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Gérer les dossiers</h4>
                    <p class="text-sm text-gray-600">Suivi et traitement</p>
                </div>
            </a>

            <a href="{{ route('rendez-vous.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg mr-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Rendez-vous</h4>
                    <p class="text-sm text-gray-600">Voir les RDV</p>
                </div>
            </a>
            
            <a href="{{ route('qms.agent') }}" class="flex items-center p-4 border border-mayelia-200 bg-mayelia-50 rounded-lg hover:bg-mayelia-100 transition-colors shadow-sm">
                <div class="p-2 bg-mayelia-600 text-white rounded-lg mr-3">
                    <i class="fas fa-desktop"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Guichet Agent</h4>
                    <p class="text-sm text-gray-600">Accéder au QMS</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection