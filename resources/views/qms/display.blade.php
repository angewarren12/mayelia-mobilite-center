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
<body class="gradient-bg min-h-screen overflow-hidden" x-data="tvDisplay()">
    <div class="h-screen flex flex-col p-8">
        
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
                
                <!-- Titre -->
                <div class="mb-8 w-full text-center">
                    <span class="text-3xl text-white/70 uppercase tracking-[0.3em] font-semibold">
                        Numéros Appelés
                    </span>
                </div>
                
                <!-- Liste de tous les tickets actifs -->
                <div x-show="activeTickets && activeTickets.length > 0" 
                     class="w-full space-y-6">
                    <template x-for="(ticket, index) in activeTickets" :key="ticket.id">
                        <div x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="bg-gradient-to-br from-white to-mayelia-50 rounded-3xl p-8 shadow-2xl glow ticket-pulse"
                             :style="`animation-delay: ${index * 0.1}s`">
                            <div class="flex items-center justify-between">
                                <!-- Numéro de Ticket -->
                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <div class="text-5xl font-black leading-none flex items-center justify-center text-mayelia-700 mb-2">
                                            <span x-show="ticket.type === 'rdv'" class="text-yellow-400 mr-3 animate-pulse"><i class="fas fa-star text-3xl"></i></span>
                                            <span class="text-transparent bg-clip-text bg-gradient-to-br from-mayelia-600 to-mayelia-800" x-text="ticket.numero"></span>
                                        </div>
                                        <div class="text-sm text-mayelia-600 font-medium" x-text="ticket.service?.nom || 'Service Général'"></div>
                                    </div>
                                </div>
                                
                                <!-- Direction Guichet -->
                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <div class="text-xl text-mayelia-700/80 mb-2 uppercase tracking-wider font-semibold">Veuillez vous rendre au</div>
                                        <div class="text-6xl font-black text-mayelia-700" x-text="ticket.guichet?.nom"></div>
                                    </div>
                                    <div class="flex flex-col items-center space-y-1">
                                        <i class="fas fa-arrow-right text-4xl text-mayelia-600 animate-pulse"></i>
                                        <i class="fas fa-arrow-right text-4xl text-mayelia-600/60 animate-pulse" style="animation-delay: 0.2s"></i>
                                        <i class="fas fa-arrow-right text-4xl text-mayelia-600/30 animate-pulse" style="animation-delay: 0.4s"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Affichage single ticket (compatibilité/fallback) -->
                <div x-show="currentTicket && (!activeTickets || activeTickets.length === 0)" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center w-full">
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
                <div x-show="!currentTicket && (!activeTickets || activeTickets.length === 0)" class="text-center">
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
                             :class="ticket.statut === 'appelé' ? 'bg-green-600/30 border-green-400 shadow-[0_0_15px_rgba(74,222,128,0.3)]' : ticket.statut === 'absent' ? 'bg-red-600/30 border-red-400 shadow-[0_0_15px_rgba(239,68,68,0.3)]' : 'bg-white/10 border-white/20 hover:bg-white/20'"
                             :style="`animation-delay: ${index * 0.1}s`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                         :class="ticket.statut === 'appelé' ? 'bg-green-500 text-white' : ticket.statut === 'absent' ? 'bg-red-500 text-white' : 'bg-mayelia-500/30 text-mayelia-200'">
                                        <i class="fas" :class="ticket.statut === 'appelé' ? 'fa-sync-alt fa-spin' : ticket.statut === 'absent' ? 'fa-user-slash' : 'fa-check'"></i>
                                    </div>
                                    <div>
                                        <div class="text-3xl font-black text-white" x-text="ticket.numero"></div>
                                        <div class="text-sm text-white/60" x-text="ticket.statut === 'appelé' ? 'En cours' : ticket.statut === 'absent' ? 'Absent' : formatTime(ticket.updated_at)"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-white/50 uppercase tracking-wider mb-1">Guichet</div>
                                    <div class="text-3xl font-bold" :class="ticket.statut === 'appelé' ? 'text-green-300' : ticket.statut === 'absent' ? 'text-red-300' : 'text-mayelia-300'" x-text="ticket.guichet?.nom"></div>
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

    <!-- Bouton d'activation audio (requis par les navigateurs modernes) -->
    <div x-show="!audioEnabled" 
         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center backdrop-blur-sm transition-opacity duration-500"
         @click="enableAudio()">
        <div class="bg-white rounded-2xl p-12 text-center max-w-lg cursor-pointer transform hover:scale-105 transition-transform">
            <div class="mb-6 bg-mayelia-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-volume-up text-5xl text-mayelia-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Activer les Annonces Vocales</h2>
            <p class="text-xl text-gray-600 mb-8">Cliquez ici pour démarrer l'affichage avec la voix de synthèse.</p>
            <button class="bg-mayelia-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-mayelia-700 transition-colors">
                DÉMARRER
            </button>
        </div>
    </div>

    <!-- Slider Overlay -->
    <div x-show="showSlider" 
         x-transition:enter="transition ease-in duration-1000"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black flex items-center justify-center overflow-hidden">
        
        <template x-for="(image, index) in sliderImages" :key="index">
            <img :src="image" 
                 x-show="currentSlideIndex === index"
                 x-transition:enter="transition ease-in duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute inset-0 w-full h-full object-contain bg-black">
        </template>
        
        <!-- Indicateur de chargement si pas d'images -->
        <div x-show="sliderImages.length === 0" class="text-white text-2xl animate-pulse">
            Chargement des diapositives...
        </div>
    </div>

    <!-- Alpine.js Local -->
    <script defer src="{{ asset('js/alpine.js') }}"></script>
    <script>
        function tvDisplay() {
            return {
                currentTime: '',
                currentDate: '',
                currentTicket: null,
                activeTickets: [],
                history: [],
                waitingCount: 0,
                lastTicketId: null,
                lastCallTime: null,
                centreId: {{ $centre->id }},
                flash: false,
                audioEnabled: false,
                voicesLoaded: false,
                audioContext: null, // Contexte audio débloqué
                
                // Slider Logic
                showSlider: false,
                sliderImages: [],
                currentSlideIndex: 0,
                sliderIntervalTime: 4000,
                sliderTimer: null,
                sliderConfig: { enabled: false },
                allBusy: false,
                gracePeriodTimer: null,
                
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    
                    // On commence le fetch uniquement après interaction (optionnel, mais ici on fetch direct pour l'visuel)
                    this.fetchData();
                    setInterval(() => this.fetchData(), 2000); // Polling toutes les 2s pour soulager le serveur

                    // Tenter de restaurer l'état audio si déjà activé précédemment dans la session
                    if (sessionStorage.getItem('audioEnabled') === 'true') {
                        this.audioEnabled = true;
                        // Précharger les voix si audio déjà activé
                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.getVoices();
                            window.speechSynthesis.addEventListener('voiceschanged', () => {
                                this.voicesLoaded = true;
                                console.log('Voix préchargées:', window.speechSynthesis.getVoices().length);
                            }, { once: true });
                        }
                    }
                    
                    // Précharger les voix au démarrage (même si audio pas encore activé)
                    if ('speechSynthesis' in window) {
                        // Forcer le chargement initial des voix
                        window.speechSynthesis.getVoices();
                        window.speechSynthesis.addEventListener('voiceschanged', () => {
                            this.voicesLoaded = true;
                            console.log('Voix chargées au démarrage:', window.speechSynthesis.getVoices().length);
                        }, { once: true });
                    }
                },
                
                enableAudio() {
                    this.audioEnabled = true;
                    sessionStorage.setItem('audioEnabled', 'true');
                    
                    // Créer un contexte audio débloqué pour permettre la lecture automatique
                    try {
                        // Créer un contexte audio si pas déjà créé
                        if (!this.audioContext) {
                            const AudioContext = window.AudioContext || window.webkitAudioContext;
                            if (AudioContext) {
                                this.audioContext = new AudioContext();
                            }
                        }
                        
                        // Débloquer le contexte audio avec une interaction utilisateur
                        if (this.audioContext && this.audioContext.state === 'suspended') {
                            this.audioContext.resume().then(() => {
                                console.log('Contexte audio débloqué');
                            });
                        }
                    } catch (e) {
                        console.log('Erreur création contexte audio:', e);
                    }
                    
                    // Jouer un son test silencieux pour débloquer l'audio context
                    const audio = new Audio('/sounds/beep.wav');
                    audio.volume = 0;
                    audio.play().then(() => {
                        console.log('Audio débloqué avec succès');
                        audio.pause();
                        audio.currentTime = 0;
                    }).catch((e) => {
                        console.log('Erreur déblocage audio (non bloquant):', e);
                    });
                    
                    // Initialiser la synthèse vocale et charger les voix
                    if ('speechSynthesis' in window) {
                        // Forcer le chargement des voix
                        window.speechSynthesis.getVoices();
                        
                        // Écouter l'événement voiceschanged pour s'assurer que les voix sont chargées
                        if (!this.voicesLoaded) {
                            window.speechSynthesis.addEventListener('voiceschanged', () => {
                                this.voicesLoaded = true;
                                console.log('Voix chargées:', window.speechSynthesis.getVoices().length);
                            }, { once: true });
                        }
                        
                        // Test initial pour activer la synthèse vocale
                        const utterance = new SpeechSynthesisUtterance('');
                        window.speechSynthesis.speak(utterance);
                    }
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
                    // Si un call est déjà en cours de traitement (animation), on attend un peu ? Non.
                    
                    fetch(`/qms/api/queue/${this.centreId}`)
                        .then(res => res.json())
                        .then(data => {
                            // Garder last_called pour compatibilité
                            this.currentTicket = data.last_called;
                            
                            // Stocker TOUS les tickets actifs
                            this.activeTickets = (data.active_tickets || []).sort((a, b) => {
                                const dateA = new Date(a.called_at || a.updated_at);
                                const dateB = new Date(b.called_at || b.updated_at);
                                return dateB - dateA;
                            });
                            
                            this.history = data.history || [];
                            this.waitingCount = data.waiting_count || 0;
                            
                            // Gérer le Slider
                            if (data.tv_status) {
                                this.processTvStatus(data.tv_status);
                            }

                            // Détecter un nouvel appel (basé sur le TIMESTAMP du dernier appelé)
                            if (this.currentTicket) {
                                // Utiliser called_at s'il existe (prioritaire), sinon updated_at
                                const ticketTime = this.currentTicket.called_at || this.currentTicket.updated_at;
                                
                                // Si c'est la première fois qu'on charge (lastCallTime est null)
                                if (!this.lastCallTime) {
                                    this.lastCallTime = ticketTime;
                                } 
                                // Si le timestamp a changé (nouveau ticket OU rappel du même ticket)
                                else if (ticketTime !== this.lastCallTime) {
                                    this.lastCallTime = ticketTime;
                                    this.triggerFlash(this.currentTicket);
                                }
                            }
                        })
                        .catch(error => console.error('Erreur:', error));
                },

                processTvStatus(status) {
                    this.allBusy = status.should_show_slider; // On garde le nom de variable interne allBusy pour pas tout casser, mais elle porte mal son nom mtn
                    this.sliderConfig = status.slider_config || { enabled: false, images: [], interval: 4000 };
                    
                    if (this.sliderConfig.images && this.sliderConfig.images.length > 0) {
                        this.sliderImages = this.sliderConfig.images;
                    }

                    if (this.sliderConfig.interval) {
                        this.sliderIntervalTime = this.sliderConfig.interval;
                    }

                    // Logique d'activation du slider
                    if (this.sliderConfig.enabled && this.allBusy && !this.flash) {
                        // Si pas déjà affiché et pas de timer de grâce en cours
                        if (!this.showSlider && !this.gracePeriodTimer) {
                            // Démarrer la période de grâce (5 secondes) avant d'afficher
                            this.gracePeriodTimer = setTimeout(() => {
                                if (this.allBusy && !this.flash) {
                                    this.startSlider();
                                }
                                this.gracePeriodTimer = null;
                            }, 5000);
                        }
                    } else {
                        // Si conditions non réunies, arrêter immédiatement
                        this.stopSlider();
                        if (this.gracePeriodTimer) {
                            clearTimeout(this.gracePeriodTimer);
                            this.gracePeriodTimer = null;
                        }
                    }
                },

                startSlider() {
                    if (this.showSlider) return;
                    this.showSlider = true;
                    this.currentSlideIndex = 0;
                    
                    if (this.sliderTimer) clearInterval(this.sliderTimer);
                    
                    this.sliderTimer = setInterval(() => {
                        if (this.sliderImages.length > 0) {
                            this.currentSlideIndex = (this.currentSlideIndex + 1) % this.sliderImages.length;
                        }
                    }, this.sliderIntervalTime);
                },

                stopSlider() {
                    if (!this.showSlider) return;
                    this.showSlider = false;
                    if (this.sliderTimer) {
                        clearInterval(this.sliderTimer);
                        this.sliderTimer = null;
                    }
                },

                triggerFlash(ticket) {
                    // Arrêter immédiatement le slider lors d'un appel
                    this.stopSlider();
                    if (this.gracePeriodTimer) {
                        clearTimeout(this.gracePeriodTimer);
                        this.gracePeriodTimer = null;
                    }

                    this.flash = true;
                    setTimeout(() => this.flash = false, 800);
                    
                    // Séquence d'annonce
                    this.playAnnouncement(ticket);
                },
                
                playAnnouncement(ticket) {
                    if (!this.audioEnabled) {
                        console.log('Audio désactivé, annonce vocale ignorée');
                        return;
                    }

                    console.log('Début de l\'annonce pour le ticket:', ticket.numero);

                    let speechTriggered = false;
                    
                    const triggerSpeech = () => {
                        if (!speechTriggered) {
                            speechTriggered = true;
                            console.log('Déclenchement de la synthèse vocale pour:', ticket.numero);
                            this.speakTicket(ticket);
                        }
                    };

                    // 1. Son de notification (Ding-Dong)
                    const audio = new Audio('/sounds/beep.wav');
                    audio.volume = 0.6;
                    
                    // Réactiver le contexte audio si suspendu
                    if (this.audioContext && this.audioContext.state === 'suspended') {
                        this.audioContext.resume().catch(e => {
                            console.log('Erreur réactivation contexte audio:', e);
                        });
                    }
                    
                    audio.onended = () => {
                        console.log('Son terminé, déclenchement de la synthèse vocale');
                        // 2. Annonce vocale après le son
                        triggerSpeech();
                    };
                    
                    audio.onerror = (e) => {
                        console.error('Erreur lecture son:', e);
                        // Si le son échoue, on parle quand même immédiatement
                        triggerSpeech();
                    };
                    
                    // Essayer de jouer le son
                    const playPromise = audio.play();
                    
                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                console.log('Son joué avec succès');
                            })
                            .catch(e => {
                                console.log('Erreur play son (non bloquant):', e.message);
                                // Si le son ne peut pas être joué (politique navigateur), 
                                // on déclenche directement la synthèse vocale après un court délai
                                setTimeout(() => triggerSpeech(), 200);
                            });
                    } else {
                        // Fallback pour navigateurs plus anciens
                        setTimeout(() => triggerSpeech(), 200);
                    }
                    
                    // Timeout de sécurité : si le son ne se termine pas dans les 1.5 secondes, on parle quand même
                    setTimeout(() => {
                        if (!speechTriggered) {
                            console.log('Timeout sécurité, déclenchement de la synthèse vocale');
                            triggerSpeech();
                        }
                    }, 1500);
                },

                speakTicket(ticket) {
                    // 1. Préparation du texte
                    const numero = ticket.numero;
                    let guichetNom = ticket.guichet ? ticket.guichet.nom : 'au guichet';
                    
                    // Si le guichet est juste un chiffre "1", on dit "guichet 1"
                    if (/^\d+$/.test(guichetNom)) {
                        guichetNom = "guichet " + guichetNom; 
                    }

                    // Formatage: P005 -> P 5, D001 -> D 1
                    const numeroClean = numero.replace(/([A-Z])0*(\d+)/, "$1 $2"); 
                    const text = `Ticket numéro, ${numeroClean}, attendu au ${guichetNom}`;

                    // 2. Essai Synthèse Native (PC/Android)
                    if ('speechSynthesis' in window) {
                        // Forcer le chargement des voix si pas encore fait
                        window.speechSynthesis.getVoices();
                        
                        // Fonction pour essayer de parler avec les voix disponibles
                        const trySpeak = () => {
                            const voices = window.speechSynthesis.getVoices();
                            
                            // Si des voix sont détectées, on utilise le natif
                            if (voices.length > 0) {
                                window.speechSynthesis.cancel();
                                
                                const utterance = new SpeechSynthesisUtterance(text);
                                utterance.lang = 'fr-FR';
                                utterance.rate = 0.9;
                                utterance.pitch = 1;
                                utterance.volume = 1.0;

                                // Chercher une voix française
                                const frenchVoice = voices.find(v => v.lang === 'fr-FR' && !v.name.includes('Compact')) || 
                                                    voices.find(v => v.lang.startsWith('fr')) ||
                                                    voices.find(v => v.lang.includes('fr'));
                                
                                if (frenchVoice) {
                                    utterance.voice = frenchVoice;
                                    console.log('Utilisation de la voix:', frenchVoice.name);
                                } else {
                                    console.log('Aucune voix française trouvée, utilisation de la voix par défaut');
                                }

                                // Gestion des erreurs
                                utterance.onerror = (e) => {
                                    console.error('Erreur synthèse vocale native:', e);
                                    // Fallback vers TTS online en cas d'erreur
                                    this.playOnlineTTS(text);
                                };

                                window.speechSynthesis.speak(utterance);
                                return true;
                            }
                            return false;
                        };

                        // Essayer immédiatement
                        if (trySpeak()) {
                            return;
                        }

                        // Si pas de voix disponibles, attendre l'événement voiceschanged
                        const voicesChangedHandler = () => {
                            if (trySpeak()) {
                                window.speechSynthesis.removeEventListener('voiceschanged', voicesChangedHandler);
                            } else {
                                // Si toujours pas de voix après chargement, utiliser fallback
                                setTimeout(() => {
                                    window.speechSynthesis.removeEventListener('voiceschanged', voicesChangedHandler);
                                    if (!trySpeak()) {
                                        console.log("TTS Natif indisponible après chargement des voix, utilisation du fallback online");
                                        this.playOnlineTTS(text);
                                    }
                                }, 500);
                            }
                        };

                        window.speechSynthesis.addEventListener('voiceschanged', voicesChangedHandler, { once: true });

                        // Timeout de sécurité : si pas de voix après 1 seconde, utiliser fallback
                        setTimeout(() => {
                            window.speechSynthesis.removeEventListener('voiceschanged', voicesChangedHandler);
                            if (!trySpeak()) {
                                console.log("TTS Natif indisponible (timeout), utilisation du fallback online");
                                this.playOnlineTTS(text);
                            }
                        }, 1000);
                        
                        return;
                    }

                    // 3. Fallback Online (TV TCL / Samsung etc.)
                    // Si pas de support natif
                    console.log("TTS Natif non supporté, utilisation du fallback online");
                    this.playOnlineTTS(text);
                },

                playOnlineTTS(text) {
                    try {
                        // Utilisation du endpoint public Google TTS (client=tw-ob)
                        const encodedText = encodeURIComponent(text);
                        const url = `https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&q=${encodedText}&tl=fr`;
                        
                        const audio = new Audio(url);
                        audio.volume = 1.0;
                        
                        // Gestion des erreurs
                        audio.onerror = (e) => {
                            console.error("Erreur lecture TTS Online:", e);
                        };
                        
                        audio.onended = () => {
                            console.log("Annonce vocale terminée");
                        };
                        
                        audio.play().catch(e => {
                            console.error("Erreur lecture TTS Online (play):", e);
                            // Essayer une alternative si Google TTS échoue
                            this.playAlternativeTTS(text);
                        });
                    } catch (e) {
                        console.error("Erreur construction TTS Online:", e);
                        // Essayer une alternative
                        this.playAlternativeTTS(text);
                    }
                },
                
                playAlternativeTTS(text) {
                    // Alternative : utiliser l'API ResponsiveVoice ou autre service
                    // Pour l'instant, on log juste l'erreur
                    console.warn("Toutes les méthodes TTS ont échoué pour:", text);
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
