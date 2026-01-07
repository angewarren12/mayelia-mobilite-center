@extends('layouts.dashboard')

@section('title', 'Retraits de carte')
@section('subtitle', 'Gestion de la remise physique des pièces CNI et Résident')

@section('content')
<div class="space-y-6" x-data="{ showFinalModal: false, ticketId: null, ticketNumero: '' }">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-mayelia-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tickets en attente</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $tickets->where('statut', 'en_attente')->count() }}</p>
                </div>
                <div class="bg-mayelia-50 p-3 rounded-lg text-mayelia-600">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">En cours de traitement</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $tickets->where('statut', 'en_cours')->count() }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-b-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Attente remise physique</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $tickets->filter(fn($t) => optional($t->retraitCarte)->numero_recepisse)->count() }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg text-orange-600">
                    <i class="fas fa-hand-holding text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 overflow-hidden">
        <form action="{{ route('retraits.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Numéro, nom ou téléphone..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:outline-none text-sm transition-all">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Statut</label>
                    <select name="statut" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:outline-none text-sm transition-all">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="appelé" {{ request('statut') == 'appelé' ? 'selected' : '' }}>Appelé</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminé" {{ request('statut') == 'terminé' ? 'selected' : '' }}>Récupéré</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-mayelia-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm hover:bg-mayelia-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('retraits.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm flex items-center justify-center hover:bg-gray-200 shadow-sm transition-colors border border-gray-200" title="Réinitialiser">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border-t pt-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date début</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:outline-none text-sm transistion-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date fin</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-mayelia-500 focus:outline-none text-sm transistion-all">
                </div>
                <div class="md:col-start-4 flex items-end">
                    <a href="{{ route('retraits.export-pdf', request()->all()) }}" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm hover:bg-red-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-file-pdf mr-2"></i> Exporter PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center text-sm md:text-base">
            <h3 class="font-bold text-gray-800">Liste des retraits</h3>
            <div class="flex items-center space-x-4">
                <form action="{{ route('retraits.create-manual') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-mayelia-600 text-white px-4 py-2 rounded-lg font-bold text-xs md:text-sm shadow-sm hover:bg-mayelia-700 transition-colors">
                        <i class="fas fa-plus mr-1 md:mr-2"></i> Nouveau retrait
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-[10px] md:text-xs uppercase tracking-wider font-semibold border-b border-gray-100">
                        <th class="px-6 py-4">Numéro / Client</th>
                        <th class="px-6 py-4">Type / Récépissé</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="px-2 py-0.5 bg-mayelia-50 text-mayelia-700 rounded-md font-bold text-[11px] w-max mb-1 border border-mayelia-100">
                                    {{ $ticket->numero }}
                                </span>
                                @if($ticket->retraitCarte && $ticket->retraitCarte->client)
                                    <span class="text-sm font-bold text-gray-800 leading-tight">{{ $ticket->retraitCarte->client->nom_complet }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $ticket->retraitCarte->client->telephone }}</span>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Client non identifié</span>
                                @endif
                                <span class="text-[10px] text-gray-400 mt-1">{{ $ticket->created_at->format('d/m H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                @php
                                    $typeRetrait = optional($ticket->retraitCarte)->type_piece ?? 'CNI';
                                    $icon = $typeRetrait === 'CNI' ? 'fa-id-card' : 'fa-id-badge';
                                @endphp
                                <div class="flex items-center text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas {{ $icon }} mr-2 text-gray-400 text-xs text-center w-4"></i>
                                    <span>{{ $typeRetrait }}</span>
                                </div>
                                <span class="text-[11px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded w-max">
                                    {{ optional($ticket->retraitCarte)->numero_recepisse ?? '---' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->statut === 'en_attente')
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tracking-tight">En attente</span>
                            @elseif($ticket->statut === 'appelé')
                                <span class="px-2 py-0.5 bg-mayelia-500 text-white rounded text-[10px] font-bold uppercase tracking-tight">Appelé</span>
                            @elseif($ticket->statut === 'terminé')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase tracking-tight">Récupéré</span>
                            @elseif($ticket->statut === 'en_cours')
                                @if(optional($ticket->retraitCarte)->numero_recepisse)
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-[10px] font-bold uppercase tracking-tight">Prêt pour remise</span>
                                @else
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-tight">En cours</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-2">
                                @if($ticket->statut === 'terminé')
                                     <span class="text-[10px] text-gray-400 italic">le {{ $ticket->completed_at?->format('H:i') }}</span>
                                @elseif(optional($ticket->retraitCarte)->numero_recepisse)
                                    <button @click="showFinalModal = true; ticketId = '{{ $ticket->id }}'; ticketNumero = '{{ $ticket->numero }}'" class="px-3 py-1.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-bold text-[10px] shadow-sm flex items-center transition-all">
                                        <i class="fas fa-check-circle mr-1.5"></i> Remise
                                    </button>
                                @else
                                    <a href="{{ route('retraits.traitement', $ticket) }}" class="px-3 py-1.5 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700 font-bold text-[10px] shadow-sm transition-all">
                                        Traiter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-4">
                                    <i class="fas fa-search text-3xl text-gray-200"></i>
                                </div>
                                <p class="text-gray-400 font-medium">Aucun retrait trouvé.</p>
                                <p class="text-xs text-gray-300 mt-1">Essayez de modifier vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

    <!-- Finalisation Modal -->
    <div x-show="showFinalModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showFinalModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="`/retraits/${ticketId}/finaliser`" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-id-card text-orange-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Finaliser le retrait - Ticket <span x-text="ticketNumero"></span>
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de la pièce finale <span class="text-red-500">*</span></label>
                                        <input type="text" name="numero_piece" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none" placeholder="Numéro CNI / Résident">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration <span class="text-red-500">*</span></label>
                                        <input type="date" name="date_expiration" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-mayelia-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2 bg-mayelia-600 text-base font-bold text-white hover:bg-mayelia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmer la remise
                        </button>
                        <button type="button" @click="showFinalModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
