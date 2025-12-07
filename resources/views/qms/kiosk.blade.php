@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-4xl">
        <!-- Header -->
        <div class="bg-mayelia-600 p-8 text-center text-white">
            <h1 class="text-4xl font-bold mb-2">Bienvenue au {{ $centre->nom }}</h1>
            <p class="text-xl opacity-90">Veuillez sélectionner une option pour prendre votre ticket</p>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Option 1: J'ai un Rendez-vous -->
                <button onclick="showRdvInput()" class="flex flex-col items-center justify-center p-12 bg-blue-50 border-2 border-blue-200 rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-all transform hover:scale-105 group">
                    <div class="h-24 w-24 bg-blue-100 rounded-full flex items-center justify-center mb-6 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-calendar-check text-4xl text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">J'ai un Rendez-vous</h2>
                    <p class="text-gray-600 text-center">Scanner votre QR code ou entrer votre numéro</p>
                </button>

                <!-- Option 2: Sans Rendez-vous -->
                <button onclick="showServices()" class="flex flex-col items-center justify-center p-12 bg-mayelia-50 border-2 border-mayelia-200 rounded-xl hover:bg-mayelia-100 hover:border-mayelia-400 transition-all transform hover:scale-105 group">
                    <div class="h-24 w-24 bg-mayelia-100 rounded-full flex items-center justify-center mb-6 group-hover:bg-mayelia-200 transition-colors">
                        <i class="fas fa-ticket-alt text-4xl text-mayelia-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Prendre un Ticket</h2>
                    <p class="text-gray-600 text-center">Sélectionnez un service pour la file d'attente</p>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Services -->
    <div id="services-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="bg-gray-50 p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800">Sélectionnez un service</h3>
                <button onclick="closeModal('services-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6 grid grid-cols-1 gap-4 max-h-[60vh] overflow-y-auto">
                @foreach($services as $service)
                <button onclick="createTicket({{ $service->id }}, 'sans_rdv')" class="flex items-center p-6 bg-white border border-gray-200 rounded-lg hover:bg-mayelia-50 hover:border-mayelia-500 transition-all text-left group">
                    <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mr-4 group-hover:bg-mayelia-100">
                        <i class="fas fa-concierge-bell text-gray-600 group-hover:text-mayelia-600"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-800 group-hover:text-mayelia-700">{{ $service->nom }}</h4>
                        <p class="text-gray-500">{{ $service->description }}</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-mayelia-500"></i>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal RDV Input -->
    <div id="rdv-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gray-50 p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800">Votre Rendez-vous</h3>
                <button onclick="closeModal('rdv-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Numéro de RDV</label>
                    <input type="text" id="rdv-input" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-mayelia-500 focus:outline-none text-xl text-center uppercase" placeholder="RDV-XXXX-XXXX">
                </div>
                <button onclick="validateRdv()" class="w-full bg-mayelia-600 text-white font-bold py-4 rounded-lg hover:bg-mayelia-700 transition-colors text-lg">
                    Valider
                </button>
            </div>
        </div>
    </div>

    <!-- Ticket Print Preview (Hidden) -->
    <div id="ticket-print" class="hidden print:block print:fixed print:inset-0 print:bg-white print:z-[100] print:flex print:items-center print:justify-center">
        <div class="text-center p-8 border-2 border-black max-w-[80mm] mx-auto">
            <h2 class="text-2xl font-bold mb-2">{{ $centre->nom }}</h2>
            <p class="text-sm mb-4">{{ now()->format('d/m/Y H:i') }}</p>
            
            <div class="my-6 border-t-2 border-b-2 border-black py-4">
                <p class="text-xl font-bold mb-2">Votre Numéro</p>
                <h1 id="print-numero" class="text-6xl font-black"></h1>
                <p id="print-service" class="text-lg mt-2 font-bold"></p>
            </div>
            
            <p class="text-sm">Veuillez patienter, nous allons vous appeler.</p>
            <div id="print-footer" class="mt-4 text-xs"></div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #ticket-print, #ticket-print * {
            visibility: visible;
        }
        #ticket-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
    }
</style>

<script>
    function showServices() {
        document.getElementById('services-modal').classList.remove('hidden');
        document.getElementById('services-modal').classList.add('flex');
    }

    function showRdvInput() {
        document.getElementById('rdv-modal').classList.remove('hidden');
        document.getElementById('rdv-modal').classList.add('flex');
        setTimeout(() => document.getElementById('rdv-input').focus(), 100);
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function createTicket(serviceId, type, rdvNumero = null) {
        // Afficher loader...
        
        fetch('{{ route("qms.tickets.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                centre_id: {{ $centre->id }},
                service_id: serviceId,
                type: type,
                numero_rdv: rdvNumero
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                printTicket(data.ticket);
                closeModal('services-modal');
                closeModal('rdv-modal');
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }

    function validateRdv() {
        const numero = document.getElementById('rdv-input').value;
        if (!numero) return;
        // Ici on pourrait vérifier le RDV via une API avant de créer le ticket
        // Pour l'instant on crée un ticket de type RDV
        createTicket(null, 'rdv', numero);
    }

    function printTicket(ticket) {
        document.getElementById('print-numero').textContent = ticket.numero;
        document.getElementById('print-service').textContent = ticket.type === 'rdv' ? 'Rendez-vous' : 'Service Client';
        
        window.print();
        
        // Reset après impression
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
</script>
@endsection
