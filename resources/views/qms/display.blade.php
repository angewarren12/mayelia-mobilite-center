<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Écran d'Affichage - {{ $centre->nom }}</title>
    
    <!-- Tailwind CSS Local -->
    <script src="{{ asset('js/tailwind.js') }}?v={{ time() }}"></script>
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}?v={{ time() }}">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mayelia: {
                            50: '#f2faf5',
                            100: '#e6f4ec',
                            200: '#c0e4cf',
                            300: '#9ad3b2',
                            400: '#4eb279',
                            500: '#02913F',
                            600: '#028339',
                            700: '#01662c',
                            800: '#014920',
                            900: '#012c13',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #012c13 0%, #014920 50%, #01662c 100%);
        }
        
        .ticket-pulse {
            animation: pulse-scale 2s ease-in-out infinite;
        }
        
        @keyframes pulse-scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .glow {
            box-shadow: 0 0 40px rgba(2, 145, 63, 0.4);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen overflow-hidden">
    <div class="h-screen flex flex-col p-8" x-data="tvDisplay()">
        
        <!-- Header Minimal -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-white rounded-2xl p-3 shadow-xl">
                    <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia Mobilité" class="h-12 w-auto">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $centre->nom }}</h1>
                    <p class="text-mayelia-200 text-sm">Système de gestion de file d'attente</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-5xl font-bold text-white font-mono" x-text="currentTime"></div>
                <div class="text-mayelia-200 text-sm mt-1" x-text="currentDate"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 grid grid-cols-3 gap-8">
            
            <!-- Zone d'Appel Principale (2/3) -->
            <div :class="flash ? 'bg-mayelia-600/30 scale-105 ring-8 ring-yellow-400' : 'bg-white/10'" 
                 class="col-span-2 backdrop-blur-lg rounded-3xl p-12 flex flex-col justify-center items-center border border-white/20 shadow-2xl transition-all duration-300 transform">
                
                <!-- Ticket Appelé -->
                <div x-show="currentTicket" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center w-full">
                    
                    <div class="mb-8">
                        <span class="text-3xl text-white/70 uppercase tracking-[0.3em] font-semibold">
                            Numéro Appelé
                        </span>
                    </div>
                    
                    <!-- Numéro de Ticket -->
                    <div class="bg-gradient-to-br from-white to-mayelia-50 rounded-3xl p-16 mb-12 shadow-2xl glow ticket-pulse inline-block min-w-[500px]">
                        <h1 class="text-[8rem] md:text-[10rem] font-black leading-none flex items-center justify-center text-mayelia-700">
                            <span x-show="currentTicket?.type === 'rdv'" class="text-yellow-400 mr-4 animate-pulse"><i class="fas fa-star"></i></span>
                            <span class="text-transparent bg-clip-text bg-gradient-to-br from-mayelia-600 to-mayelia-800" x-text="currentTicket?.numero"></span>
                        </h1>
                    </div>
                    
                    <!-- Direction Guichet -->
                    <div class="flex items-center justify-center space-x-8 bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                        <div class="text-center">
                            <div class="text-2xl text-white/80 mb-2 uppercase tracking-wider">Veuillez vous rendre au</div>
                            <div class="text-7xl font-black text-white" x-text="currentTicket?.guichet?.nom"></div>
                        </div>
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-arrow-right text-6xl text-white animate-pulse"></i>
                            <i class="fas fa-arrow-right text-6xl text-white/60 animate-pulse" style="animation-delay: 0.2s"></i>
                            <i class="fas fa-arrow-right text-6xl text-white/30 animate-pulse" style="animation-delay: 0.4s"></i>
                        </div>
                    </div>
                </div>

                <!-- État d'Attente -->
                <div x-show="!currentTicket" class="text-center">
                    <div class="mb-8">
                        <i class="fas fa-clock text-8xl text-white/30 animate-pulse"></i>
                    </div>
                    <h2 class="text-4xl text-white/70 font-semibold">En attente du prochain appel...</h2>
                    <p class="text-xl text-white/50 mt-4">Merci de patienter</p>
                </div>
            </div>

            <!-- Historique (1/3) -->
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 shadow-2xl flex flex-col">
                
                <!-- Titre -->
                <div class="mb-6 pb-4 border-b border-white/20">
                    <h3 class="text-2xl font-bold text-white uppercase tracking-wider flex items-center">
                        <i class="fas fa-history mr-3 text-mayelia-300"></i>
                        Derniers Appels
                    </h3>
                </div>
                
                <!-- Liste des Tickets -->
                <div class="flex-1 space-y-4 overflow-hidden">
                    <template x-for="(ticket, index) in history" :key="ticket.id">
                        <div class="backdrop-blur-sm rounded-2xl p-6 border transition-all slide-in"
                             :class="ticket.statut === 'appelé' ? 'bg-green-600/30 border-green-400 shadow-[0_0_15px_rgba(74,222,128,0.3)]' : 'bg-white/10 border-white/20 hover:bg-white/20'"
                             :style="`animation-delay: ${index * 0.1}s`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                         :class="ticket.statut === 'appelé' ? 'bg-green-500 text-white' : 'bg-mayelia-500/30 text-mayelia-200'">
                                        <i class="fas" :class="ticket.statut === 'appelé' ? 'fa-sync-alt fa-spin' : 'fa-check'"></i>
                                    </div>
                                    <div>
                                        <div class="text-3xl font-black text-white" x-text="ticket.numero"></div>
                                        <div class="text-sm text-white/60" x-text="ticket.statut === 'appelé' ? 'En cours' : formatTime(ticket.updated_at)"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-white/50 uppercase tracking-wider mb-1">Guichet</div>
                                    <div class="text-3xl font-bold" :class="ticket.statut === 'appelé' ? 'text-green-300' : 'text-mayelia-300'" x-text="ticket.guichet?.nom"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Message si vide -->
                    <div x-show="history.length === 0" class="text-center py-12">
                        <i class="fas fa-inbox text-4xl text-white/20 mb-4"></i>
                        <p class="text-white/50">Aucun appel récent</p>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-6 pt-6 border-t border-white/20 text-center">
                    <div class="flex items-center justify-center space-x-2 text-white/70 mb-2">
                        <i class="fas fa-users"></i>
                        <span class="text-sm font-medium">Personnes en attente:</span>
                        <span class="text-xl font-bold text-mayelia-300" x-text="waitingCount"></span>
                    </div>
                    <p class="text-xs text-white/50">Merci de votre patience</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Local -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function tvDisplay() {
            return {
                currentTime: '',
                currentDate: '',
                currentTicket: null,
                history: [],
                waitingCount: 0,
                lastTicketId: null,
                lastCallTime: null,
                centreId: {{ $centre->id }},
                flash: false,
                
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    
                    this.fetchData();
                    setInterval(() => this.fetchData(), 1000);
                },
                
                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('fr-FR', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        second: '2-digit'
                    });
                    this.currentDate = now.toLocaleDateString('fr-FR', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },
                
                fetchData() {
                    fetch(`/qms/api/queue/${this.centreId}`)
                        .then(res => res.json())
                        .then(data => {
                            this.currentTicket = data.last_called;
                            this.history = data.history || [];
                            this.waitingCount = data.waiting_count || 0;
                            
                            // Détecter un nouvel appel
                            if (this.currentTicket && 
                                (this.currentTicket.id !== this.lastTicketId)) {
                                this.lastTicketId = this.currentTicket.id;
                                this.triggerFlash();
                            }
                        })
                        .catch(error => console.error('Erreur:', error));
                },

                triggerFlash() {
                    this.flash = true;
                    setTimeout(() => this.flash = false, 800);
                },
                
                formatTime(dateString) {
                    if (!dateString) return '';
                    return new Date(dateString).toLocaleTimeString('fr-FR', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
            }
        }
    </script>
</body>
</html>
