@extends('booking.layout')

@section('title', 'Suivi de rendez-vous - ' . $rendezVous->numero_suivi)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Suivi de rendez-vous</h1>
                    <p class="mt-1 text-sm text-gray-500">Détails de votre réservation</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('client.tracking.login') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour
                    </a>
                    <a href="{{ route('receipt.download', $rendezVous->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger le reçu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Carte principale du rendez-vous -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Détails du rendez-vous</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations complètes de votre réservation</p>
            </div>
            
            <div class="border-t border-gray-200">
                <dl>
                    <!-- Numéro de suivi -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Numéro de suivi</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $rendezVous->numero_suivi }}
                            </span>
                        </dd>
                    </div>

                    <!-- Statut -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($rendezVous->statut == 'confirme') bg-green-100 text-green-800
                                @elseif($rendezVous->statut == 'annule') bg-red-100 text-red-800
                                @else bg-purple-100 text-purple-800 @endif">
                                @if($rendezVous->statut == 'confirme') Confirmé
                                @elseif($rendezVous->statut == 'annule') Annulé
                                @else Terminé @endif
                            </span>
                        </dd>
                    </div>

                    <!-- Date et heure -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Date et heure</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ \Carbon\Carbon::parse($rendezVous->date_rendez_vous)->format('l d F Y') }} à {{ $rendezVous->tranche_horaire }}
                        </dd>
                    </div>

                    <!-- Centre -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Centre</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->centre->nom }}
                        </dd>
                    </div>

                    <!-- Service -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Service</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->service->nom }}
                        </dd>
                    </div>

                    <!-- Formule -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Formule</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->formule->nom }}
                        </dd>
                    </div>

                    <!-- Montant -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Montant</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="text-lg font-semibold text-green-600">
                                {{ number_format($rendezVous->formule->prix, 0, ',', ' ') }} FCFA
                            </span>
                        </dd>
                    </div>

                    <!-- Notes -->
                    @if($rendezVous->notes)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->notes }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Informations client -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations client</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Vos informations personnelles</p>
            </div>
            
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->client->nom }} {{ $rendezVous->client->prenom }}
                        </dd>
                    </div>

                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->client->telephone }}
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $rendezVous->client->email }}
                        </dd>
                    </div>

                    @if($rendezVous->client->date_naissance)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Date de naissance</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ \Carbon\Carbon::parse($rendezVous->client->date_naissance)->format('d/m/Y') }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('client.tracking.login') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-search mr-2"></i>
                Rechercher un autre rendez-vous
            </a>
            <a href="{{ route('booking.wizard') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Nouveau rendez-vous
            </a>
        </div>
    </div>
</div>
@endsection
