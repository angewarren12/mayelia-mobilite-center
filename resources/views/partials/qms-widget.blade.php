<div x-data="globalQmsWidget()" x-init="init()" 
     x-show="isMiniMode && isVisible" 
     class="fixed z-50 w-80 bg-white rounded-xl shadow-2xl overflow-hidden border-t-[6px] border-mayelia-600 font-sans"
     :style="`top: ${pos.y}px; left: ${pos.x}px; cursor: default; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);`"
     @mousemove.window="onDrag($event)" 
     @mouseup.window="stopDrag()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     style="display: none;">
    
    <!-- Drag Handle Header -->
    <div class="bg-gray-50 px-4 py-2 flex justify-between items-center border-b border-gray-100 cursor-move"
         @mousedown="startDrag">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">GUID : <span x-text="selectedGuichet"></span></span>
        </div>
        <button @click="expandInterface()" class="text-gray-400 hover:text-mayelia-600 transition-colors" title="Agrandir l'interface">
            <i class="fas fa-expand-alt"></i>
        </button>
    </div>

    <!-- Widget Content -->
    <div class="p-5 text-center">
        <!-- État Actif -->
        <div x-show="currentTicket" class="space-y-4">
            <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Ticket en cours</div>
            <div class="text-6xl font-black text-gray-800 font-mono tracking-tighter" x-text="currentTicket?.numero"></div>
            
            <div class="grid grid-cols-3 gap-2 pt-2">
                <button @click="recallTicket()" class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors" title="Rappeler">
                    <i class="fas fa-bell"></i>
                </button>
                <button @click="completeTicket()" class="p-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors" title="Terminer">
                    <i class="fas fa-check"></i>
                </button>
                <button @click="cancelTicket()" class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" title="Absent">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- État Attente -->
        <div x-show="!currentTicket" class="py-2">
            <template x-if="waitingCount > 0">
                <div>
                    <div class="text-gray-400 mb-4 flex justify-center">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm mb-4"><span x-text="waitingCount"></span> personne(s) en attente</p>
                    <button @click="callNext()" :disabled="loading" class="w-full py-3 bg-mayelia-600 hover:bg-mayelia-700 disabled:bg-gray-400 text-white rounded-lg font-bold shadow-md transition-transform active:scale-95 flex items-center justify-center">
                        <span x-show="!loading"><i class="fas fa-bullhorn mr-2"></i> SUIVANT</span>
                        <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> ...</span>
                    </button>
                </div>
            </template>
            <template x-if="waitingCount === 0">
                <div class="text-center py-2">
                    <div class="text-gray-300 mb-2 flex justify-center">
                        <i class="fas fa-coffee text-3xl"></i>
                    </div>
                    <p class="text-gray-400 text-xs italic">Aucun client en attente</p>
                    <p class="text-green-600 text-xs font-medium mt-1">Vous pouvez souffler !</p>
                </div>
            </template>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-400">
            <span>En attente: <span class="font-bold text-mayelia-600" x-text="waitingCount"></span></span>
            <span x-text="currentTime"></span>
        </div>
    </div>
</div>

<script>
    function globalQmsWidget() {
        return {
            selectedGuichet: '',
            currentTicket: null,
            loading: false,
            waitingList: [],
            waitingCount: 0,
            centreId: 1,
            currentTime: '',
            justCompletedTicket: false,
            
            // État Mini Mode
            isMiniMode: false,
            isVisible: false,
            onAgentPage: window.location.pathname.includes('/qms/agent'),
            
            pos: { x: window.innerWidth - 350, y: window.innerHeight - 350 },
            offset: { x: 0, y: 0 },
            isDragging: false,
            
            init() {
                // Initialisation état
                this.checkState();
                
                // Watcher pour les changements de localStorage par d'autres onglets/pages
                window.addEventListener('storage', () => this.checkState());
                
                // Polling état local (au cas où on change sur la page actuelle)
                setInterval(() => {
                    const mode = localStorage.getItem('qms_mini_mode') === 'true';
                    if (this.isMiniMode !== mode) {
                        this.isMiniMode = mode;
                        // Si on passe en mode mini, on refresh les données
                        if (mode) this.fetchQueueData();
                    }
                }, 1000);

                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
                
                setInterval(() => {
                    if (this.isMiniMode && this.isVisible) {
                        this.fetchQueueData();
                    }
                }, 1000);
            },

            checkState() {
                const guichet = localStorage.getItem('qms_agent_guichet');
                this.isMiniMode = localStorage.getItem('qms_mini_mode') === 'true';
                
                if (guichet) {
                    this.selectedGuichet = guichet;
                    this.isVisible = true;
                    if (this.isMiniMode && !this.onAgentPage) this.fetchQueueData();
                } else {
                    this.isVisible = false;
                }
            },

            expandInterface() {
                localStorage.setItem('qms_mini_mode', 'false');
                if (!this.onAgentPage) {
                    window.location.href = "{{ route('qms.agent') }}";
                }
            },

            // Fonctions Drag & Drop
            startDrag(e) {
                this.isDragging = true;
                this.offset.x = e.clientX - this.pos.x;
                this.offset.y = e.clientY - this.pos.y;
            },
            onDrag(e) {
                if (this.isDragging) {
                    this.pos.x = e.clientX - this.offset.x;
                    this.pos.y = e.clientY - this.offset.y;
                }
            },
            stopDrag() {
                this.isDragging = false;
            },
            
            updateTime() {
                this.currentTime = new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
            },
            
            fetchQueueData() {
                if (!this.selectedGuichet) return;
                
                fetch(`/qms/api/queue/${this.centreId}`)
                    .then(res => res.json())
                    .then(data => {
                        this.waitingList = data.waiting;
                        this.waitingCount = data.waiting_count;
                        // Trouver le ticket assigné à MON guichet (sauf si action manuelle récente)
                        if (data.active_tickets && !this.justCompletedTicket) {
                            this.currentTicket = data.active_tickets.find(t => t.guichet_id == this.selectedGuichet) || null;
                        }
                        if (this.justCompletedTicket) {
                            setTimeout(() => this.justCompletedTicket = false, 2000);
                        }
                    })
                    .catch(err => console.error('Erreur widget:', err));
            },
            
            callNext() {
                if (!this.selectedGuichet || this.loading) return;
                this.loading = true;
                
                // Feedback immédiat
                const tempTicket = { numero: '...', service: { nom: 'Recherche...' } };
                this.currentTicket = tempTicket;
                
                fetch('{{ route("qms.tickets.callNext") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        centre_id: this.centreId,
                        guichet_id: this.selectedGuichet
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        setTimeout(() => {
                            this.currentTicket = data.ticket;
                            this.loading = false;
                        }, 200);
                        this.fetchQueueData();
                    } else {
                        this.currentTicket = null;
                        this.loading = false;
                        alert(data.message);
                    }
                })
                .catch(e => {
                    this.currentTicket = null;
                    this.loading = false;
                });
            },

            completeTicket() {
                if (!this.currentTicket || this.loading) return;
                
                // OPTIMISTIC UI
                this.justCompletedTicket = true;
                const backupTicket = this.currentTicket;
                this.currentTicket = null;
                
                fetch(`/qms/tickets/${backupTicket.id}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        this.currentTicket = backupTicket;
                        this.justCompletedTicket = false;
                        alert('Erreur');
                    } else {
                        this.fetchQueueData();
                    }
                })
                .catch(e => {
                    this.currentTicket = backupTicket;
                });
            },

            cancelTicket() {
                if (!this.currentTicket || this.loading) return;
                
                // OPTIMISTIC UI
                this.justCompletedTicket = true;
                const backupTicket = this.currentTicket;
                this.currentTicket = null;
                
                fetch(`/qms/tickets/${backupTicket.id}/cancel`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        this.currentTicket = backupTicket;
                        this.justCompletedTicket = false;
                        alert('Erreur');
                    } else {
                        this.fetchQueueData();
                    }
                })
                .catch(e => {
                    this.currentTicket = backupTicket;
                });
            },

            recallTicket() {
                if (!this.currentTicket) return;
                fetch(`/qms/tickets/${this.currentTicket.id}/recall`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
            }
        }
    }
</script>
