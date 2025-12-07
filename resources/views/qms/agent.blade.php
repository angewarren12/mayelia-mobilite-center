@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 p-6" x-data="agentDashboard()">
    <!-- Header / Sélection Guichet -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex justify-between items-center">
        <div class="flex items-center">
            <div class="h-10 w-10 bg-mayelia-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user-tie text-mayelia-600"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Espace Agent</h1>
                <p class="text-sm text-gray-500">{{ Auth::user()->name }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <select x-model="selectedGuichet" class="form-select rounded-lg border-gray-300 focus:border-mayelia-500 focus:ring focus:ring-mayelia-200 transition duration-200">
                <option value="">Sélectionner un guichet</option>
                @foreach($guichets as $guichet)
                <option value="{{ $guichet->id }}">{{ $guichet->nom }} ({{ $guichet->centre->nom }})</option>
                @endforeach
            </select>
            
            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center" x-show="selectedGuichet">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                En ligne
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-show="selectedGuichet">
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
                        <button @click="recallTicket()" class="flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-bold transition-colors shadow-md">
                            <i class="fas fa-bell mr-2"></i> Rappeler
                        </button>
                        <button @click="completeTicket()" class="flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition-colors shadow-md">
                            <i class="fas fa-check mr-2"></i> Terminer
                        </button>
                        <button @click="cancelTicket()" class="flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold transition-colors shadow-md">
                            <i class="fas fa-user-slash mr-2"></i> Absent
                        </button>
                    </div>
                    
                    <div class="mt-6 text-sm text-gray-400">
                        Appelé à <span x-text="formatTime(currentTicket?.called_at)"></span>
                    </div>
                </div>

                <div class="p-12 text-center" x-show="!currentTicket">
                    <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-coffee text-4xl text-gray-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">En attente</h2>
                    <p class="text-gray-500 mb-8">Aucun ticket en cours de traitement</p>
                    
                    <button @click="callNext()" class="px-8 py-4 bg-mayelia-600 hover:bg-mayelia-700 text-white rounded-xl font-bold text-xl transition-all transform hover:scale-105 shadow-lg flex items-center mx-auto">
                        <i class="fas fa-bullhorn mr-3"></i> Appeler le suivant
                    </button>
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
                    <div class="text-2xl font-bold text-green-600">12</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-gray-500 text-sm mb-1">Temps moyen</div>
                    <div class="text-2xl font-bold text-blue-600">5m</div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite: File d'attente -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">
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
    
    <div x-show="!selectedGuichet" class="text-center py-20">
        <div class="bg-white p-10 rounded-xl shadow-lg inline-block max-w-md">
            <i class="fas fa-store-alt text-6xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Bienvenue</h2>
            <p class="text-gray-500 mb-6">Veuillez sélectionner votre guichet pour commencer à travailler.</p>
        </div>
    </div>
</div>

<!-- Alpine.js Logic -->
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function agentDashboard() {
        return {
            selectedGuichet: '',
            currentTicket: null,
            waitingList: [],
            waitingCount: 0,
            centreId: 1, // À dynamiser selon l'agent connecté
            
            init() {
                // Polling toutes les 5 secondes
                setInterval(() => {
                    if (this.selectedGuichet) {
                        this.fetchQueueData();
                    }
                }, 5000);
            },
            
            fetchQueueData() {
                fetch(`/qms/api/queue/${this.centreId}`)
                    .then(res => res.json())
                    .then(data => {
                        this.waitingList = data.waiting;
                        this.waitingCount = data.waiting_count;
                    });
            },
            
            callNext() {
                if (!this.selectedGuichet) return alert('Sélectionnez un guichet');
                
                fetch('{{ route("qms.tickets.call", ["ticket" => ""]) }}'.replace('/call', '') + '/call', {
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
                        this.currentTicket = data.ticket;
                        this.fetchQueueData();
                    } else {
                        alert(data.message);
                    }
                });
            },
            
            completeTicket() {
                if (!this.currentTicket) return;
                
                fetch(`/qms/tickets/${this.currentTicket.id}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.currentTicket = null;
                        this.fetchQueueData();
                    }
                });
            },
            
            cancelTicket() {
                if (!this.currentTicket) return;
                
                fetch(`/qms/tickets/${this.currentTicket.id}/cancel`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.currentTicket = null;
                        this.fetchQueueData();
                    }
                });
            },
            
            recallTicket() {
                if (!this.currentTicket) return;
                
                fetch(`/qms/tickets/${this.currentTicket.id}/recall`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Feedback visuel (sonnette ?)
                    }
                });
            },
            
            formatTime(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>
@endsection
