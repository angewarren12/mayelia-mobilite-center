@extends('layouts.dashboard')

@section('title', $title ?? 'Gestion des Créneaux')
@section('subtitle', $subtitle ?? 'Gérez les jours de travail, les templates et les exceptions')

@section('content')
<div class="space-y-6">
    <!-- Onglets de navigation -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('creneaux.index') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.index') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Jours ouvrables
                </a>
                <a href="{{ route('creneaux.templates') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.templates') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Templates
                </a>
                <a href="{{ route('creneaux.exceptions') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.exceptions') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Exceptions
                </a>
                <a href="{{ route('creneaux.calendrier') }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('creneaux.calendrier') ? 'border-mayelia-500 text-mayelia-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-calendar mr-2"></i>
                    Calendrier
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenu spécifique à chaque page -->
    @yield('creneaux_content')
</div>
@endsection
