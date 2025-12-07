@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white overflow-hidden" x-data="tvDisplay()">
    <!-- Top Bar -->
    <div class="bg-gray-800 p-4 flex justify-between items-center shadow-md">
        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 mr-4">
            <h1 class="text-2xl font-bold">{{ $centre->nom }}</h1>
        </div>
        <div class="text-2xl font-mono" x-text="currentTime"></div>
    </div>

    <div class="grid grid-cols-3 h-[calc(100vh-80px)]">
        <!-- Main Display (Left 2/3) -->
        <div class="col-span-2 p-8 flex flex-col justify-center items-center border-r border-gray-700 relative">
            
            <!-- Current Ticket -->
            <div class="text-center w-full" x-show="currentTicket" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <div class="mb-8">
                    <span class="text-4xl text-gray-400 uppercase tracking-widest">Numéro Appelé</span>
                </div>
                
                <div class="bg-white text-gray-900 rounded-3xl p-12 mb-12 shadow-[0_0_50px_rgba(255,255,255,0.3)] inline-block min-w-[600px]">
                    <h1 class="text-[12rem] font-black leading-none" x-text="currentTicket?.numero"></h1>
                </div>
                
                <div class="flex justify-center items-center space-x-8">
                    <div class="text-right">
                        <div class="text-3xl text-gray-400 mb-1">Veuillez vous rendre au</div>
                        <div class="text-6xl font-bold text-mayelia-400" x-text="currentTicket?.guichet?.nom"></div>
                    </div>
                    <i class="fas fa-arrow-right text-6xl text-mayelia-400 animate-pulse"></i>
                </div>
            </div>

            <!-- Idle State -->
            <div class="text-center" x-show="!currentTicket">
                <i class="fas fa-clock text-8xl text-gray-700 mb-6 animate-pulse"></i>
                <h2 class="text-4xl text-gray-500">En attente du prochain appel...</h2>
            </div>
        </div>

        <!-- Sidebar (Right 1/3) -->
        <div class="bg-gray-800 p-6 flex flex-col">
            <h3 class="text-xl text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-700 pb-4">
                Derniers Appels
            </h3>
            
            <div class="space-y-4 flex-1 overflow-hidden">
                <template x-for="ticket in history" :key="ticket.id">
                    <div class="bg-gray-700 rounded-xl p-6 flex justify-between items-center opacity-80">
                        <div>
                            <div class="text-4xl font-bold" x-text="ticket.numero"></div>
                            <div class="text-sm text-gray-400" x-text="formatTime(ticket.updated_at)"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400 uppercase">Guichet</div>
                            <div class="text-2xl font-bold text-mayelia-400" x-text="ticket.guichet?.nom"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Info -->
            <div class="mt-auto pt-6 border-t border-gray-700 text-center text-gray-500 text-sm">
                <p>Bienvenue chez Mayelia Mobilite Center</p>
                <p>Pour votre sécurité, merci de respecter les consignes.</p>
            </div>
        </div>
    </div>
    
    <!-- Audio Element -->
    <audio id="bell-sound" src="{{ asset('sounds/bell.mp3') }}" preload="auto"></audio>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function tvDisplay() {
        return {
            currentTime: '',
            currentTicket: null,
            history: [],
            lastTicketId: null,
            centreId: {{ $centre->id }},
            
            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
                
                this.fetchData();
                setInterval(() => this.fetchData(), 3000); // Polling rapide (3s)
            },
            
            updateTime() {
                this.currentTime = new Date().toLocaleTimeString('fr-FR');
            },
            
            fetchData() {
                fetch(`/qms/api/queue/${this.centreId}`)
                    .then(res => res.json())
                    .then(data => {
                        this.currentTicket = data.last_called;
                        this.history = data.history;
                        
                        // Détecter un nouvel appel ou un rappel
                        if (this.currentTicket && 
                            (this.currentTicket.id !== this.lastTicketId || 
                             new Date(this.currentTicket.called_at) > new Date(this.lastCallTime))) {
                            
                            this.playBell();
                            this.lastTicketId = this.currentTicket.id;
                            this.lastCallTime = this.currentTicket.called_at;
                        }
                    });
            },
            
            playBell() {
                const audio = document.getElementById('bell-sound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
            },
            
            formatTime(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>
@endsection
