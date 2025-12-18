@extends('layouts.dashboard')

@section('title', __('Mon Profil'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- En-tête du profil -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-mayelia-600 to-mayelia-800 h-32 relative">
                <div class="absolute -bottom-10 left-8">
                    <div class="w-24 h-24 rounded-full bg-white p-1 shadow-lg">
                        <div class="w-full h-full rounded-full bg-mayelia-100 flex items-center justify-center text-mayelia-700 text-3xl font-bold">
                            {{ substr(Auth::user()->prenom, 0, 1) }}{{ substr(Auth::user()->nom, 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-12 pb-6 px-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</h1>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                            <span class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                {{ Auth::user()->email }}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                {{ Auth::user()->telephone }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @php
                            $roleColors = match(Auth::user()->role) {
                                'admin' => 'bg-red-100 text-red-800 border-red-200',
                                'agent' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'oneci' => 'bg-purple-100 text-purple-800 border-purple-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                            $roleLabel = match(Auth::user()->role) {
                                'admin' => 'Administrateur',
                                'agent' => 'Agent',
                                'oneci' => 'Agent ONECI',
                                default => 'Utilisateur'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium border {{ $roleColors }}">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </div>
                
                @if(Auth::user()->centre)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 inline-block">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-mayelia-100 flex items-center justify-center text-mayelia-600 mr-4">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Centre Assigné</p>
                            <p class="text-gray-900 font-semibold">{{ Auth::user()->centre->nom }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations personnelles -->
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-100 h-full">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Sécurité -->
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-100 h-full">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
