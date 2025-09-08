@extends('booking.layout')

@section('title', 'Dashboard - ' . $client->nom . ' ' . $client->prenom)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bonjour {{ $client->prenom }} !</h1>
                    <p class="mt-1 text-sm text-gray-500">Voici l'historique de vos rendez-vous</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('client.tracking.login') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>
                    <a href="{{ route('booking.wizard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Nouveau rendez-vous
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Confirmés</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['confirme'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Annulés</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['annule'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-double text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Terminés</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prochain rendez-vous -->
        @if($stats['prochain'])
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900">Prochain rendez-vous</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p><strong>{{ $stats['prochain']->centre->nom }}</strong></p>
                        <p>{{ \Carbon\Carbon::parse($stats['prochain']->date_rendez_vous)->format('l d F Y') }} à {{ $stats['prochain']->tranche_horaire }}</p>
                        <p>{{ $stats['prochain']->service->nom }} - {{ $stats['prochain']->formule->nom }}</p>
                        <p class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $stats['prochain']->numero_suivi }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Liste des rendez-vous -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Historique des rendez-vous</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Tous vos rendez-vous passés et à venir</p>
            </div>
            
            @if($rendezVous->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($rendezVous as $rdv)
                    <li class="hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($rdv->statut == 'confirme')
                                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                        @elseif($rdv->statut == 'annule')
                                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                        @else
                                            <i class="fas fa-check-double text-purple-500 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $rdv->centre->nom }}
                                            </p>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($rdv->statut == 'confirme') bg-green-100 text-green-800
                                                @elseif($rdv->statut == 'annule') bg-red-100 text-red-800
                                                @else bg-purple-100 text-purple-800 @endif">
                                                {{ ucfirst($rdv->statut) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            <p>{{ $rdv->service->nom }} - {{ $rdv->formule->nom }}</p>
                                            <p>{{ \Carbon\Carbon::parse($rdv->date_rendez_vous)->format('l d F Y') }} à {{ $rdv->tranche_horaire }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ number_format($rdv->formule->prix, 0, ',', ' ') }} FCFA</p>
                                        <p class="text-xs text-gray-500">{{ $rdv->numero_suivi }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('receipt.download', $rdv->id) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-download mr-1"></i>
                                            Reçu
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rendez-vous</h3>
                    <p class="text-gray-500 mb-4">Vous n'avez pas encore de rendez-vous.</p>
                    <a href="{{ route('booking.wizard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Réserver maintenant
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
