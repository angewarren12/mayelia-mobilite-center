@extends('booking.layout')

@section('title', 'Suivi de mes rendez-vous')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-mayelia-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-calendar-check text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Suivi de rendez-vous</h2>
            <p class="mt-2 text-sm text-gray-600">Consultez vos rendez-vous ou suivez une réservation spécifique</p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <!-- Formulaire de connexion par téléphone -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Voir tous mes rendez-vous</h3>
                <p class="text-sm text-gray-600 mb-4">Connectez-vous avec votre numéro de téléphone pour voir l'historique complet</p>
                <form method="POST" action="{{ route('client.tracking.login.submit') }}">
                    @csrf
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">
                            Numéro de téléphone
                        </label>
                        <div class="mt-1">
                            <input id="telephone" 
                                   name="telephone" 
                                   type="tel" 
                                   required 
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-mayelia-500 focus:border-mayelia-500 sm:text-sm @error('telephone') border-red-300 @enderror"
                                   placeholder="Ex: +225 07 12 34 56 78"
                                   value="{{ old('telephone') }}">
                        </div>
                        @error('telephone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-mayelia-600 hover:bg-mayelia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Accéder à mes rendez-vous
                        </button>
                    </div>
                </form>
            </div>

            <!-- Séparateur -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">OU</span>
                </div>
            </div>

            <!-- Recherche par numéro de suivi -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Suivi d'un rendez-vous spécifique</h3>
                <p class="text-sm text-gray-600 mb-4">Entrez votre numéro de suivi pour voir les détails d'un rendez-vous</p>
                <form method="POST" action="{{ route('client.tracking.search') }}">
                    @csrf
                    <div>
                        <label for="numero_suivi" class="block text-sm font-medium text-gray-700">
                            Numéro de suivi
                        </label>
                        <div class="mt-1">
                            <input id="numero_suivi" 
                                   name="numero_suivi" 
                                   type="text" 
                                   required 
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-mayelia-500 focus:border-mayelia-500 sm:text-sm @error('numero_suivi') border-red-300 @enderror"
                                   placeholder="Ex: MAYELIA-2025-123456"
                                   value="{{ old('numero_suivi') }}">
                        </div>
                        @error('numero_suivi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mayelia-500">
                            <i class="fas fa-search mr-2"></i>
                            Voir ce rendez-vous
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lien vers la réservation -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Pas encore de rendez-vous ? 
                    <a href="{{ route('booking.wizard') }}" class="font-medium text-mayelia-600 hover:text-mayelia-500">
                        Réservez maintenant
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
