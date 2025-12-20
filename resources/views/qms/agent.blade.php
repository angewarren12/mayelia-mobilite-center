@extends('layouts.dashboard')

@section('title', 'Interface Agent QMS')
@section('subtitle', 'Gestion de la file d\'attente')

@section('content')
<div x-data="agentDashboard()" x-init="init()">

    <!-- MODE PLEIN ÉCRAN -->
    <div x-show="!isMiniMode" class="space-y-6 transition-all duration-300">
        <!-- Sélection Guichet & Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-mayelia-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user-tie text-mayelia-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                        <p class="text-sm text-gray-500">Agent de service</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-mayelia-50 text-mayelia-700 px-4 py-2 rounded-lg border border-mayelia-100 font-bold">
                        <i class="fas fa-desktop mr-2 text-mayelia-500"></i>
                        <span>{{ $assignedGuichet->nom }}</span>
                    </div>
                    
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center" x-show="selectedGuichet">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        En ligne
                    </div>

                    <!-- Bouton Réduire -->
                    <button @click="toggleMiniMode()" class="p-2 text-gray-500 hover:text-mayelia-600 hover:bg-mayelia-50 rounded-lg transition-colors" title="Réduire en widget">
                        <i class="fas fa-compress-alt text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne Gauche: Contrôles & Ticket Actuel -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ticket Actuel -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-mayelia-600">
                    <div class="p-8 text-center" x-show="currentTicket">
                        <div class="inline-block px-4 py-1 rounded-full text-sm font-bold mb-4" 
                             :class="currentTicket?.type === 'rdv' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'">
                            <span x-text="currentTicket?.type === 'rdv' ? 'RENDEZ-VOUS' : 'SANS RENDEZ-VOUS'"></span>
                        </div>
                        
                        <h2 class="text-8xl font-black text-gray-900 mb-2" x-text="currentTicket?.numero"></h2>
                        <p class="text-xl text-gray-500 mb-8" x-text="currentTicket?.service?.nom || 'Service Général'"></p>
                        
                        <div class="flex justify-center space-x-4">
                            <button @click="recallTicket()" :disabled="loading" class="flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-50 text-white rounded-lg font-bold transition-colors shadow-md transform hover:scale-105 active:scale-95">
                                <i class="fas fa-bell mr-2"></i> Rappeler
                            </button>
                            <button @click="completeTicket()" :disabled="completingTicket || loading" class="flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-lg font-bold transition-colors shadow-md transform hover:scale-105 active:scale-95">
                                <span x-show="!completingTicket"><i class="fas fa-check mr-2"></i> Terminer</span>
                                <span x-show="completingTicket"><i class="fas fa-spinner fa-spin mr-2"></i> Traitement...</span>
                            </button>
                            <button @click="cancelTicket()" :disabled="cancelingTicket || loading" class="flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white rounded-lg font-bold transition-colors shadow-md transform hover:scale-105 active:scale-95">
                                <span x-show="!cancelingTicket"><i class="fas fa-user-slash mr-2"></i> Absent</span>
                                <span x-show="cancelingTicket"><i class="fas fa-spinner fa-spin mr-2"></i> Traitement...</span>
                            </button>
                        </div>
                        
                        <div class="mt-6 text-sm text-gray-400">
                            Appelé à <span x-text="formatTime(currentTicket?.called_at)"></span>
                        </div>
                    </div>

                    <div class="p-12 text-center" x-show="!currentTicket">
                        <template x-if="waitingCount > 0">
                            <div>
                                <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-bullhorn text-4xl text-mayelia-600 animate-pulse"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Des clients attendent</h2>
                                <p class="text-gray-500 mb-8"><span x-text="waitingCount"></span> personne(s) dans la file</p>
                                
                                <button @click="callNext()" :disabled="loading" class="px-8 py-4 bg-mayelia-600 hover:bg-mayelia-700 disabled:bg-gray-400 text-white rounded-xl font-bold text-xl transition-all transform hover:scale-105 shadow-lg flex items-center mx-auto active:scale-95">
                                    <span x-show="!loading"><i class="fas fa-bullhorn mr-3"></i> Appeler le suivant</span>
                                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-3"></i> Chargement...</span>
                                </button>
                            </div>
                        </template>
                        
                        <template x-if="waitingCount === 0">
                            <div>
                                <div class="h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-coffee text-4xl text-gray-300"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">File d'attente vide</h2>
                                <p class="text-gray-500 mb-8">Aucun client pour le moment.</p>
                                <p class="text-green-600 font-medium">Vous pouvez effectuer des tâches administratives.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Statistiques Rapides -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm mb-1">En attente</div>
                        <div class="text-2xl font-bold text-gray-800" x-text="waitingCount">0</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm mb-1">Traités auj.</div>
                        <div class="text-2xl font-bold text-green-600">--</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm mb-1">Temps moyen</div>
                        <div class="text-2xl font-bold text-blue-600">--</div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: File d'attente -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col h-[600px]">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">File d'attente</h3>
                    <span class="bg-mayelia-100 text-mayelia-800 text-xs font-bold px-2 py-1 rounded-full" x-text="waitingCount + ' personnes'"></span>
                </div>
                
                <div class="overflow-y-auto flex-1 p-2 space-y-2">
                    <template x-for="ticket in waitingList" :key="ticket.id">
                        <div class="p-3 rounded-lg border border-gray-100 hover:border-mayelia-200 hover:bg-mayelia-50 transition-colors cursor-pointer group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-bold text-lg text-gray-800" x-text="ticket.numero"></span>
                                    <div class="text-xs text-gray-500" x-text="ticket.service?.nom || 'Général'"></div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full mb-1" 
                                          :class="ticket.type === 'rdv' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600'"
                                          x-text="ticket.type === 'rdv' ? 'RDV' : 'Standard'"></span>
                                    <span class="text-xs text-gray-400" x-text="formatTime(ticket.created_at)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="waitingList.length === 0" class="text-center py-10 text-gray-400">
                        <i class="fas fa-users-slash text-3xl mb-2"></i>
                        <p>File d'attente vide</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function agentDashboard() {
        return {
            selectedGuichet: {{ $assignedGuichet->id }},
            currentTicket: null,
            waitingList: [],
            waitingCount: 0,
            centreId: {{ $centreId }},
            assignedGuichetId: {{ $assignedGuichet ? $assignedGuichet->id : 'null' }},
            currentTime: '',
            isMiniMode: false,
            loading: false,
            justCompletedTicket: false,
            completingTicket: false,
            cancelingTicket: false,
            
            init() {
                this.isMiniMode = localStorage.getItem('qms_mini_mode') === 'true';
                this.fetchQueueData();
                
                // Polling pour vérifier si le mode mini a changé (par le widget global)
                setInterval(() => {
                    const mode = localStorage.getItem('qms_mini_mode') === 'true';
                    if (this.isMiniMode !== mode) {
                        this.isMiniMode = mode;
                    }
                }, 500);

                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
                setInterval(() => {
                    if (this.selectedGuichet) this.fetchQueueData();
                }, 1000);
            },

            toggleMiniMode() {
                if (!this.selectedGuichet && !this.isMiniMode) {
                    alert('Veuillez d\'abord sélectionner un guichet');
                    return;
                }
                this.isMiniMode = !this.isMiniMode;
                localStorage.setItem('qms_mini_mode', this.isMiniMode);
            },

            updateTime() {
                this.currentTime = new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
            },
            
            fetchQueueData() {
                fetch(`/qms/api/queue/${this.centreId}`)
                    .then(res => res.json())
                    .then(data => {
                        this.waitingList = data.waiting;
                        this.waitingCount = data.waiting_count;
                        
                        // Restaurer ticket actif UNIQUEMENT si on n'a pas de ticket ET qu'on n'a pas fait d'action manuelle récente
                        if (!this.currentTicket && data.active_tickets && this.selectedGuichet && !this.justCompletedTicket) {
                            this.currentTicket = data.active_tickets.find(t => t.guichet_id == this.selectedGuichet) || null;
                        }
                        
                        // Reset du flag après 2 secondes
                        if (this.justCompletedTicket) {
                            setTimeout(() => this.justCompletedTicket = false, 2000);
                        }
                    });
            },
            
            callNext() {
                if (!this.selectedGuichet || this.loading) return alert('Sélectionnez un guichet ou patientez');
                this.loading = true;
                
                // Feedback immédiat : on affiche un état "Recherche en cours"
                const tempTicket = { numero: '...', service: { nom: 'Recherche...' } };
                this.currentTicket = tempTicket;
                
                // Timeout de sécurité (10 secondes)
                const timeoutId = setTimeout(() => {
                    if (this.loading) {
                        this.loading = false;
                        this.currentTicket = null;
                        alert('Le serveur ne répond pas. Vérifiez votre connexion.');
                    }
                }, 10000);
                
                fetch('{{ route("qms.tickets.callNext") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ centre_id: this.centreId, guichet_id: this.selectedGuichet })
                })
                .then(res => {
                    clearTimeout(timeoutId);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le ticket immédiatement
                        this.currentTicket = data.ticket;
                        this.loading = false;
                        
                        // Jouer le son d'appel IMMÉDIATEMENT après la mise à jour
                        this.playCallSound();
                        
                        this.fetchQueueData();
                    } else {
                        this.currentTicket = null;
                        this.loading = false;
                        alert(data.message || 'Aucun ticket disponible');
                    }
                })
                .catch(e => {
                    clearTimeout(timeoutId);
                    this.currentTicket = null;
                    this.loading = false;
                    alert('Erreur de connexion au serveur');
                    console.error('Erreur callNext:', e);
                });
            },

            recallTicket() {
                if (!this.currentTicket || this.loading) return;
                
                this.loading = true;
                const ticketId = this.currentTicket.id;
                
                fetch(`/qms/tickets/${ticketId}/recall`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        // Mettre à jour le ticket actuel avec le ticket rappelé
                        if (data.ticket) {
                            this.currentTicket = data.ticket;
                            // Jouer le son d'appel
                            this.playCallSound();
                        }
                        // Rafraîchir les données de la file d'attente
                        this.fetchQueueData();
                    } else {
                        alert('Erreur lors du rappel du ticket.');
                    }
                })
                .catch(e => {
                    this.loading = false;
                    alert('Erreur de connexion lors du rappel.');
                    console.error('Erreur recallTicket:', e);
                });
            },

            completeTicket() {
                if (!this.currentTicket || this.completingTicket) return;
                
                // Activer le spinner
                this.completingTicket = true;
                
                // OPTIMISTIC UI: On assume que ça va marcher
                this.justCompletedTicket = true; // Empêcher la restauration automatique
                const backupTicket = this.currentTicket;
                this.currentTicket = null; // Vide l'écran immédiatement
                
                fetch(`/qms/tickets/${backupTicket.id}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    this.completingTicket = false;
                    if (!data.success) {
                        this.currentTicket = backupTicket;
                        this.justCompletedTicket = false;
                        alert('Erreur lors de la clôture du ticket.');
                    } else {
                        this.fetchQueueData();
                    }
                })
                .catch(e => {
                    this.completingTicket = false;
                    this.currentTicket = backupTicket;
                    this.justCompletedTicket = false;
                    alert('Erreur de connexion.');
                });
            },

            cancelTicket() {
                if (!this.currentTicket || this.cancelingTicket) return;
                
                // Activer le spinner
                this.cancelingTicket = true;
                
                // OPTIMISTIC UI
                this.justCompletedTicket = true; // Empêcher la restauration automatique
                const backupTicket = this.currentTicket;
                this.currentTicket = null; 
                
                fetch(`/qms/tickets/${backupTicket.id}/cancel`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    this.cancelingTicket = false;
                    if (!data.success) {
                        this.currentTicket = backupTicket;
                        this.justCompletedTicket = false;
                        alert('Erreur lors de l\'annulation.');
                    } else {
                        this.fetchQueueData();
                    }
                })
                .catch(e => {
                    this.cancelingTicket = false;
                    this.currentTicket = backupTicket;
                    this.justCompletedTicket = false;
                    alert('Erreur de connexion.');
                });
            },
            
            formatTime(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            },
            
            playCallSound() {
                try {
                    // Utiliser le fichier audio beep.wav
                    const audio = new Audio('/sounds/beep.wav');
                    audio.volume = 0.7; // Volume à 70% (ajustable entre 0 et 1)
                    audio.play().catch(err => {
                        // Si l'auto-play est bloqué, on affiche juste un message en console
                        console.log('Impossible de jouer le son automatiquement. L\'interaction utilisateur peut être requise.');
                    });
                } catch (e) {
                    console.log('Erreur lors de la lecture du son:', e);
                }
            }
        }
    }
</script>
@endsection
