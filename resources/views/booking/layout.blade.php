<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Réserver un rendez-vous') - Mayelia</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo.png') }}">
    
    <!-- Tailwind CSS Local -->
    <script src="{{ asset('js/tailwind.js') }}?v={{ time() }}"></script>
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}?v={{ time() }}">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mayelia': {
                            50: '#f2faf5',
                            100: '#e6f4ec',
                            200: '#c0e4cf',
                            300: '#9ad3b2',
                            400: '#4eb279',
                            500: '#02913F',
                            600: '#028339',
                            700: '#01662c',
                            800: '#014920',
                            900: '#012c13',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .progress-step {
            transition: all 0.3s ease;
        }
        .progress-step.active {
            background-color: #2563eb;
            color: white;
        }
        .progress-step.completed {
            background-color: #10b981;
            color: white;
        }
        .progress-line {
            transition: all 0.3s ease;
        }
        .progress-line.completed {
            background-color: #10b981;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia Mobilité" class="h-12 w-auto">
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Réservation sécurisée
                    </div>
                    @if(session('booking_pays_nom'))
                        <div class="text-sm text-mayelia-600">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ session('booking_pays_nom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Progress Bar -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center py-4">
                @php
                    $steps = [
                        ['number' => 1, 'name' => 'Service', 'route' => 'booking.wizard'],
                        ['number' => 2, 'name' => 'Vérification', 'route' => 'booking.verification'],
                        ['number' => 3, 'name' => 'Pays', 'route' => 'booking.index'],
                        ['number' => 4, 'name' => 'Ville', 'route' => 'booking.villes'],
                        ['number' => 5, 'name' => 'Centre', 'route' => 'booking.centres'],
                        ['number' => 6, 'name' => 'Calendrier', 'route' => 'booking.calendrier'],
                        ['number' => 7, 'name' => 'Informations', 'route' => 'booking.client'],
                        ['number' => 8, 'name' => 'Confirmation', 'route' => 'booking.confirmation'],
                    ];
                    $currentStep = $currentStep ?? 1;
                @endphp

                @foreach($steps as $index => $step)
                    <div class="flex items-center">
                        <div class="progress-step flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                            @if($step['number'] < $currentStep) completed
                            @elseif($step['number'] == $currentStep) active
                            @else bg-gray-200 text-gray-500 @endif">
                            @if($step['number'] < $currentStep)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $step['number'] }}
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium
                            @if($step['number'] < $currentStep) text-green-600
                            @elseif($step['number'] == $currentStep) text-mayelia-600
                            @else text-gray-500 @endif">
                            {{ $step['name'] }}
                        </span>
                    </div>
                    
                    @if($index < count($steps) - 1)
                        <div class="progress-line flex-1 h-0.5 mx-4
                            @if($step['number'] < $currentStep) completed
                            @else bg-gray-200 @endif"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Features Section (only on first steps) -->
    @if($currentStep <= 3)
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-green-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Rapide</h4>
                        <p class="text-gray-600">Réservation en quelques minutes</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-mayelia-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-mayelia-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Sécurisé</h4>
                        <p class="text-gray-600">Données protégées et sécurisées</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-alt text-purple-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Mobile</h4>
                        <p class="text-gray-600">Compatible mobile et tablette</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Mayelia</h3>
                    <p class="text-gray-300 text-sm">
                        Votre partenaire de confiance pour toutes vos démarches administratives.
                    </p>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">Services</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li>• Cartes d'identité</li>
                        <li>• Cartes de résident</li>
                        <li>• Visas</li>
                        <li>• Autres démarches</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p><i class="fas fa-phone mr-2"></i> +225 XX XX XX XX</p>
                        <p><i class="fas fa-envelope mr-2"></i> contact@mayelia.ci</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> Abidjan, Côte d'Ivoire</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 Mayelia. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                <i class="fas fa-spinner fa-spin text-2xl text-mayelia-600"></i>
                <span class="text-lg font-medium">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Fonctions utilitaires globales
        window.showLoading = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        };

        window.hideLoading = function() {
            document.getElementById('loading-overlay').classList.add('hidden');
        };

        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-mayelia-500';
            
            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 max-w-sm`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        };

        // Gestion des erreurs AJAX
        window.handleAjaxError = function(xhr) {
            let message = 'Une erreur est survenue';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).flat().join(', ');
            } else if (xhr.status === 404) {
                message = 'Ressource non trouvée';
            } else if (xhr.status === 500) {
                message = 'Erreur serveur';
            }
            
            showToast(message, 'error');
        };

        // Fonction pour naviguer vers l'étape suivante
        window.goToNextStep = function(url, data = {}) {
            showLoading();
            
            // Stocker les données en session
            if (data) {
                Object.keys(data).forEach(key => {
                    sessionStorage.setItem(`booking_${key}`, data[key]);
                });
            }
            
            window.location.href = url;
        };

        // Fonction pour revenir à l'étape précédente
        window.goToPreviousStep = function(url) {
            showLoading();
            window.location.href = url;
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Afficher les messages de session
            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showToast('{{ session('error') }}', 'error');
            @endif
            
            @if(session('warning'))
                showToast('{{ session('warning') }}', 'warning');
            @endif
        });
    </script>

    @yield('scripts')
</body>
</html>
