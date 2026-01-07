<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $centre->nom }} - Borne Tickets</title>
    
    <script src="{{ asset('js/tailwind.js') }}?v={{ time() }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}?v={{ time() }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script defer src="{{ asset('js/alpine.js') }}"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mayelia': {
                            50: '#f2faf5', 100: '#e6f4ec', 200: '#c0e4cf', 300: '#9ad3b2',
                            400: '#4eb279', 500: '#02913F', 600: '#028339', 700: '#01662c',
                            800: '#014920', 900: '#012c13',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    
    <style>
        body { -webkit-user-select: none; user-select: none; } /* Empêche sélection texte sur borne tactile */
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen overflow-hidden font-sans" x-data="kioskData()">

    <!-- Header Logo -->
    <div class="fixed top-0 left-0 w-full bg-white shadow-sm p-6 flex justify-between items-center z-10">
        <!-- Logo avec trigger caché pour menu admin (5 clics) -->
        <div @click="adminTrigger()" class="cursor-pointer select-none">
            <img src="{{ asset('img/logo-oneci.jpg') }}" alt="ONECI" class="h-16 pointer-events-none">
        </div>
        <div class="text-right">
            <h1 class="text-xl font-bold text-gray-800">{{ $centre->nom }}</h1>
            <p class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="w-full max-w-6xl px-4 mt-20">
        
        <!-- ÉCRAN 1: ACCUEIL / SÉLECTION TYPE -->
        <div x-show="step === 1" class="grid gap-8 transition-all duration-300 transform"
             :class="mode === 'fifo' ? 'grid-cols-1' : 'grid-cols-2'">
            
            <!-- OPTION 1: PRENDRE TICKET (FIFO) ou SANS RDV -->
            <button @click="selectType('sans_rdv')" :disabled="loading"
                    class="bg-white rounded-3xl shadow-xl p-12 flex flex-col items-center justify-center transform hover:scale-[1.02] active:scale-95 transition-all text-center border-b-8 border-mayelia-500 h-96 w-full disabled:opacity-50 disabled:cursor-not-allowed">
                <div x-show="!loading" class="bg-mayelia-100 rounded-full p-8 mb-6">
                    <i class="fas fa-ticket-alt text-6xl text-mayelia-600"></i>
                </div>
                <div x-show="loading" class="bg-gray-100 rounded-full p-8 mb-6">
                    <i class="fas fa-spinner fa-spin text-6xl text-gray-400"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-800 mb-2" x-text="mode === 'fifo' ? 'PRENDRE UN TICKET' : 'SANS RENDEZ-VOUS'"></h2>
                <p class="text-xl text-gray-500" x-text="mode === 'fifo' ? 'Ticket pour service standard' : 'File d\'attente standard'"></p>
            </button>

            <!-- OPTION 2: AVEC RDV -->
            <button x-show="mode !== 'fifo'" @click="startRdv()" 
                    class="bg-white rounded-3xl shadow-xl p-12 flex flex-col items-center justify-center transform hover:scale-[1.02] active:scale-95 transition-all text-center border-b-8 border-purple-500 h-96 w-full">
                <div class="bg-purple-100 rounded-full p-8 mb-6">
                    <i class="fas fa-calendar-check text-6xl text-purple-600"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-800 mb-2">J'AI UN RENDEZ-VOUS</h2>
                <p class="text-xl text-gray-500">Scanner ou saisir numéro</p>
            </button>
        </div>

        <!-- ÉCRAN 1.5: SÉLECTION SERVICE (Si Sans RDV) -->
        <div x-show="step === 1.5" class="bg-white rounded-3xl shadow-2xl p-8 max-w-5xl mx-auto" style="display: none;">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Choisissez votre service</h2>
                <p class="text-gray-500">Veuillez sélectionner le motif de votre visite</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 overflow-y-auto max-h-[60vh] p-4">
                @foreach($services as $service)
                <button @click="selectService({{ $service->id }}, '{{ addslashes($service->nom) }}')" :disabled="loading"
                        class="bg-gray-50 hover:bg-mayelia-50 border-2 border-gray-200 hover:border-mayelia-500 rounded-xl p-6 flex flex-col items-center justify-center transition-all h-40 active:scale-95 disabled:opacity-50">
                    <span x-show="!loading" class="text-4xl mb-3 text-mayelia-600 font-bold">{{ substr($service->nom, 0, 1) }}</span>
                    <span x-show="loading" class="text-4xl mb-3 text-gray-400"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="text-lg font-bold text-gray-800 text-center leading-tight">{{ $service->nom }}</span>
                </button>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <button @click="step = 1" class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </button>
            </div>
        </div>

        <!-- ÉCRAN 1.7: SÉLECTION TYPE DE PIÈCE (Si Retrait de Carte) -->
        <div x-show="step === 1.7" class="bg-white rounded-3xl shadow-2xl p-12 max-w-4xl mx-auto text-center" style="display: none;">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Type de pièce à retirer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <button @click="confirmRetrait('CNI')" class="bg-gray-50 hover:bg-mayelia-50 border-4 border-gray-100 hover:border-mayelia-500 rounded-2xl p-10 flex flex-col items-center transition-all active:scale-95">
                    <i class="fas fa-address-card text-6xl text-mayelia-600 mb-4"></i>
                    <span class="text-2xl font-bold">CARTE CNI</span>
                </button>
                <button @click="confirmRetrait('Résident')" class="bg-gray-50 hover:bg-mayelia-50 border-4 border-gray-100 hover:border-mayelia-500 rounded-2xl p-10 flex flex-col items-center transition-all active:scale-95">
                    <i class="fas fa-id-card-alt text-6xl text-mayelia-600 mb-4"></i>
                    <span class="text-2xl font-bold">CARTE DE RÉSIDENT</span>
                </button>
            </div>
            <div class="mt-12">
                <button @click="step = 1.5" class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </button>
            </div>
        </div>
        <div x-show="step === 2" class="bg-white rounded-3xl shadow-2xl p-12 max-w-2xl mx-auto text-center" style="display: none;">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Numéro de Suivi / Enrôlement</h2>
            
            <input type="text" x-model="numeroRdv" 
                   class="w-full text-center text-4xl font-mono p-6 border-4 border-gray-200 rounded-2xl focus:border-mayelia-500 focus:ring-0 mb-8 uppercase" 
                   placeholder="Ex: R123456789"
                   @keydown.enter="verifyRdv()">

            <!-- Clavier Virtuel -->
            <div class="grid grid-cols-3 gap-4 mb-8 max-w-sm mx-auto">
                <template x-for="n in [1,2,3,4,5,6,7,8,9,0]" :key="n">
                    <button @click="appendNumber(n)" class="bg-gray-100 text-2xl font-bold py-4 rounded-xl hover:bg-gray-200 active:bg-gray-300 shadow-sm" x-text="n"></button>
                </template>
                <button @click="backspace()" class="bg-red-100 text-red-600 text-xl py-4 rounded-xl shadow-sm"><i class="fas fa-backspace"></i></button>
                <button @click="verifyRdv()" class="bg-green-600 text-white text-xl py-4 rounded-xl shadow-sm col-span-1"><i class="fas fa-check"></i></button>
            </div>

            <div class="flex space-x-4">
                <button @click="step = 1; numeroRdv = ''" class="flex-1 py-4 bg-gray-200 text-gray-700 rounded-xl font-bold text-xl">Annuler</button>
                <button @click="verifyRdv()" class="flex-1 py-4 bg-mayelia-600 text-white rounded-xl font-bold text-xl shadow-lg" :disabled="loading">
                    <span x-show="!loading">Valider</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
            
            <p x-show="errorMsg" class="mt-4 text-red-600 font-bold text-lg animate-pulse" x-text="errorMsg"></p>
        </div>

        <!-- ÉCRAN 3: CONFIRMATION / IMPRESSION -->
        <div x-show="step === 3" class="text-center" style="display: none;">
            <div class="inline-block bg-white rounded-full p-8 shadow-2xl mb-8 animate-bounce">
                <i class="fas fa-print text-6xl text-mayelia-600"></i>
            </div>
            <h2 class="text-4xl font-black text-white drop-shadow-md mb-4 bg-black/20 p-4 rounded-xl inline-block">Impression en cours...</h2>
            <p class="text-xl text-white/90 drop-shadow-sm font-bold">Veuillez récupérer votre ticket.</p>
        </div>

    </div>

    <!-- MODAL CONFIG ADMIN (CACHÉ) -->
    <div x-show="showAdmin" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="bg-gray-800 text-white p-4 flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fas fa-cogs mr-2"></i> Configuration Borne</h3>
                <button @click="showAdmin = false" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Imprimante</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        La borne utilise l'imprimante par défaut du navigateur. 
                        Pour changer (Bluetooth/USB/WiFi), veuillez configurer l'imprimante dans les <strong>paramètres système</strong> de la tablette/PC.
                    </p>
                    <button @click="testPrint()" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i> Lancer une page de test
                    </button>
                </div>
                
                <div class="border-t pt-4">
                    <h4 class="font-bold text-gray-800 mb-2">Mode Actuel</h4>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $centre->qms_mode === 'fifo' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ strtoupper($centre->qms_mode) }}
                        </span>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <button @click="window.location.reload()" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                        <i class="fas fa-sync mr-1"></i> Redémarrer l'interface
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- IFRAME D'IMPRESSION CACHÉE -->
    <iframe id="printFrame" name="printFrame" style="display:none;"></iframe>

    <script>
        function kioskData() {
            return {
                step: 1,
                mode: '{{ $centre->qms_mode }}',
                numeroRdv: '',
                selectedServiceId: null,
                typeRetrait: '',
                loading: false,
                errorMsg: '',
                adminTapCount: 0,
                showAdmin: false,
                
                // Déclencher menu admin après 5 clics rapides sur logo
                adminTrigger() {
                    this.adminTapCount++;
                    if (this.adminTapCount >= 5) {
                        this.showAdmin = true;
                        this.adminTapCount = 0;
                    }
                    setTimeout(() => this.adminTapCount = 0, 2000);
                },

                testPrint() {
                    // Imprimer la page actuelle juste pour tester la connexion
                    window.print();
                },
                
                selectType(type) {
                    if (type === 'sans_rdv') {
                        // Si mode FIFO et un seul service, on passe direct, sinon choix service
                        // Pour simplification : on demande toujours le service si > 1, sinon direct
                        @if($services->count() > 1)
                            this.step = 1.5;
                        @else
                            this.selectService({{ $services->first()->id ?? 1 }});
                        @endif
                    }
                },

                selectService(serviceId, serviceNom) {
                    this.selectedServiceId = serviceId;
                    // Si c'est le service "Retrait de Carte", on demande le type
                    if (serviceNom.toLowerCase().includes('retrait')) {
                        this.step = 1.7;
                    } else {
                        this.generateTicket('sans_rdv', { service_id: serviceId });
                    }
                },

                confirmRetrait(type) {
                    this.typeRetrait = type;
                    this.generateTicket('sans_rdv', { 
                        service_id: this.selectedServiceId,
                        type_retrait: type
                    });
                },
                
                startRdv() {
                    this.step = 2;
                    this.numeroRdv = '';
                    this.errorMsg = '';
                    setTimeout(() => document.querySelector('input').focus(), 100);
                },

                appendNumber(n) { this.numeroRdv += n; },
                backspace() { this.numeroRdv = this.numeroRdv.slice(0, -1); },

                verifyRdv() {
                    if (!this.numeroRdv) return;
                    this.loading = true;
                    this.errorMsg = '';

                    fetch('{{ route("qms.check-rdv") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ numero: this.numeroRdv, centre_id: {{ $centre->id }} })
                    })
                    .then(res => {
                        if (res.status === 419) {
                            window.location.reload();
                            return null;
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        
                        this.loading = false;
                        if (data.success) {
                            this.generateTicket('rdv', { rdv_data: data.rdv, service_id: data.rdv.service_id });
                        } else {
                            this.errorMsg = data.message || 'Numéro invalide.';
                        }
                    })
                    .catch(e => {
                        this.loading = false; 
                        this.errorMsg = 'Erreur connexion.'; 
                    });
                },

                generateTicket(type, extraData = {}) {
                    this.loading = true;
                    
                    fetch('{{ route("qms.tickets.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            centre_id: {{ $centre->id }},
                            type: type,
                            service_id: extraData.service_id ?? 1,
                            numero_rdv: this.numeroRdv,
                            ...extraData
                        })
                    })
                    .then(res => {
                        if (res.status === 419) {
                            // Session expirée, on recharge pour avoir un nouveau token
                            window.location.reload();
                            return null;
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return; // Si reload déclenché
                        
                        if (data.success) {
                            this.step = 3;
                            this.printTicket(data.ticket);
                            setTimeout(() => {
                                this.step = 1;
                                this.numeroRdv = '';
                                this.loading = false;
                            }, 3000); // Retour rapide après 3 secondes
                        } else {
                            this.errorMsg = data.message;
                            if (this.step !== 1.5) this.step = 2; // Reste où on est ou va à l'erreur
                        }
                    });
                },

                printTicket(ticket) {
                    const printFrame = document.getElementById('printFrame');
                    
                    // S'assurer que l'iframe est prête avant de charger
                    printFrame.onload = function() {
                        // Attendre un peu pour que le contenu soit complètement chargé
                        setTimeout(function() {
                            try {
                                // Appeler window.print() dans l'iframe
                                printFrame.contentWindow.print();
                            } catch (e) {
                                // Si erreur (CORS ou autre), essayer d'imprimer la page principale
                                console.log('Erreur impression iframe, tentative alternative');
                                window.print();
                            }
                        }, 300); // Délai pour charger le QR code et tout le contenu
                    };
                    
                    // Charger la page d'impression dans l'iframe
                    printFrame.src = `/qms/tickets/${ticket.id}/print`;
                }
            }
        }
    </script>
</body>
</html>
