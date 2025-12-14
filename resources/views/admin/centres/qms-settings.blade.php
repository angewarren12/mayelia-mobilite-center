@extends('layouts.dashboard')

@section('title', 'Paramètres QMS')
@section('subtitle', 'Configuration de la gestion de file d\'attente')

@section('header-actions')
<a href="{{ route('centres.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
    <i class="fas fa-arrow-left mr-2"></i>
    Retour aux Centres
</a>
@endsection

@section('content')
<div class="space-y-6">
        <!-- Messages de succès -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Formulaire -->
        <form method="POST" action="{{ route('admin.centres.qms.update', $centre) }}" class="bg-white rounded-lg shadow-lg p-8">
            @csrf
            @method('PUT')

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Mode de Gestion de File d'Attente
                </h2>
                <p class="text-sm text-gray-600 mb-6">
                    Choisissez comment les tickets des clients avec et sans rendez-vous seront traités.
                </p>

                <!-- Mode FIFO -->
                <div class="border-2 rounded-lg p-6 mb-4 transition-all {{ $centre->qms_mode === 'fifo' ? 'border-mayelia-500 bg-mayelia-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <label class="flex items-start cursor-pointer">
                        <input 
                            type="radio" 
                            name="qms_mode" 
                            value="fifo" 
                            {{ $centre->qms_mode === 'fifo' ? 'checked' : '' }}
                            class="mt-1 h-5 w-5 text-mayelia-600 focus:ring-mayelia-500"
                        >
                        <div class="ml-4 flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-list-ol text-mayelia-600 mr-2"></i>
                                <span class="font-semibold text-lg text-gray-900">
                                    FIFO - Premier Arrivé, Premier Servi
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">
                                Tous les clients sont traités dans l'ordre d'arrivée, sans distinction entre ceux qui ont un rendez-vous et ceux qui n'en ont pas.
                            </p>
                            <div class="bg-gray-50 rounded p-3 text-sm">
                                <div class="font-medium text-gray-700 mb-1">Exemple :</div>
                                <div class="text-gray-600">
                                    • 14:00 - Client A (sans RDV) → Ticket I001<br>
                                    • 14:10 - Client B (RDV 14:15) → Ticket I002<br>
                                    • <span class="text-mayelia-600 font-medium">Résultat : I001 passe en premier</span>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Mode Fenêtre de Tolérance -->
                <div class="border-2 rounded-lg p-6 transition-all {{ $centre->qms_mode === 'fenetre_tolerance' ? 'border-mayelia-500 bg-mayelia-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <label class="flex items-start cursor-pointer">
                        <input 
                            type="radio" 
                            name="qms_mode" 
                            value="fenetre_tolerance"
                            {{ $centre->qms_mode === 'fenetre_tolerance' ? 'checked' : '' }}
                            class="mt-1 h-5 w-5 text-mayelia-600 focus:ring-mayelia-500"
                        >
                        <div class="ml-4 flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-clock text-mayelia-600 mr-2"></i>
                                <span class="font-semibold text-lg text-gray-900">
                                    Fenêtre de Tolérance
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">
                                Les clients avec rendez-vous sont prioritaires <strong>uniquement</strong> s'ils arrivent dans leur créneau horaire (± fenêtre de tolérance).
                            </p>
                            
                            <!-- Configuration de la fenêtre -->
                            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-cog mr-1"></i>
                                    Fenêtre de tolérance (en minutes)
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input 
                                        type="number" 
                                        name="qms_fenetre_minutes" 
                                        value="{{ $centre->qms_fenetre_minutes ?? 15 }}"
                                        min="5"
                                        max="60"
                                        class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-mayelia-500 focus:border-mayelia-500"
                                    >
                                    <span class="text-sm text-gray-600">minutes</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Recommandé : 15 minutes (permet ±15 min autour de l'heure du RDV)
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded p-3 text-sm">
                                <div class="font-medium text-gray-700 mb-1">Exemple avec fenêtre de 15 min :</div>
                                <div class="text-gray-600">
                                    • 14:00 - Client A (sans RDV) → Ticket I001<br>
                                    • 14:10 - Client B (RDV 14:15) → Ticket I002 (dans la fenêtre 14:00-14:30)<br>
                                    • <span class="text-mayelia-600 font-medium">Résultat : I002 passe en premier (priorité RDV)</span><br>
                                    <br>
                                    • 14:00 - Client C (sans RDV) → Ticket I003<br>
                                    • 14:05 - Client D (RDV 15:00) → Ticket I004 (hors fenêtre 14:45-15:15)<br>
                                    • <span class="text-mayelia-600 font-medium">Résultat : I003 passe en premier (RDV hors fenêtre)</span>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-lg transition-colors"
                >
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les Paramètres
                </button>
            </div>
        </form>

        <!-- Informations supplémentaires -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Informations Importantes
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Les paramètres s'appliquent immédiatement à tous les nouveaux tickets</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Les tickets déjà en attente seront recalculés lors du prochain appel</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                    <span>Vous pouvez changer de mode à tout moment selon vos besoins</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
