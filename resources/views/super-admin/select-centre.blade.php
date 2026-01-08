@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-mayelia-600 via-mayelia-700 to-mayelia-900 flex items-center justify-center p-4">
    <div class="w-full max-w-6xl">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-2xl mb-6">
                <i class="fas fa-crown text-4xl text-yellow-500"></i>
            </div>
            <h1 class="text-5xl font-black text-white mb-3">Espace Super Admin</h1>
            <p class="text-xl text-mayelia-100">Sélectionnez un centre pour accéder au tableau de bord</p>
        </div>

        {{-- Grille de centres --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Option : Tous les centres --}}
            <a href="{{ route('super-admin.dashboard') }}" class="group bg-gradient-to-br from-purple-600 to-purple-800 rounded-3xl p-8 shadow-2xl hover:scale-105 hover:shadow-purple-500/50 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center group-hover:bg-white/30 transition-all">
                        <i class="fas fa-globe text-4xl text-white"></i>
                    </div>
                    <i class="fas fa-arrow-right text-3xl text-white opacity-0 group-hover:opacity-100 transition-all"></i>
                </div>
                <h3 class="text-3xl font-black text-white mb-2">Tous les Centres</h3>
                <p class="text-purple-100 font-medium">Vue d'ensemble globale</p>
            </a>

            {{-- Centres individuels --}}
            @foreach($centres as $centre)
            <a href="{{ route('super-admin.dashboard', ['centre_id' => $centre->id]) }}" 
               class="group bg-white rounded-3xl p-8 shadow-2xl hover:scale-105 hover:shadow-mayelia-500/50 transition-all duration-300 border-4 border-transparent hover:border-mayelia-500">
                <div class="flex items-center justify-between mb-6">
                    <div class="w-16 h-16 bg-mayelia-100 rounded-2xl flex items-center justify-center group-hover:bg-mayelia-600 transition-all">
                        <i class="fas fa-building text-4xl text-mayelia-600 group-hover:text-white transition-all"></i>
                    </div>
                    <i class="fas fa-arrow-right text-3xl text-gray-300 opacity-0 group-hover:opacity-100 group-hover:text-mayelia-600 transition-all"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $centre->nom }}</h3>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="font-medium">{{ $centre->ville->nom }}</span>
                </div>
               
 <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">{{ $centre->users->count() }} agents</span>
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold">ACTIF</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-12 text-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au Dashboard Principal
            </a>
        </div>

    </div>
</div>
@endsection
