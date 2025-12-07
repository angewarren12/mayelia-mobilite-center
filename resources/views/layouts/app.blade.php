<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Mayelia Mobilité - Votre partenaire mobilité en Côte d\'Ivoire')</title>
    <meta name="description" content="@yield('description', 'Mayelia Mobilité vous accompagne dans vos démarches de mobilité : visa, transport VIP, assistance aéroport et formalités administratives. Service professionnel et fiable en Côte d\'Ivoire.')">
    
    <!-- Favicon -->
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        /* Mayelia Colors */
        :root {
            --mayelia-turquoise: #11B49A;
            --mayelia-sky-blue: #1DA0DB;
        }
    </style>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mayelia': {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#11B49A',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="font-inter antialiased">
    <!-- Header with White Background -->
    <header class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-4 group">
                        <div class="relative">
                            <img src="{{ asset('img/logo.png') }}" 
                                 alt="Mayelia Mobilité" 
                                 class="h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                        </div>
                    </a>
                </div>

                <!-- Right Side - Account and Notifications -->
                <div class="flex items-center space-x-4">
                    <!-- Account Icon -->
                    <button class="relative p-3 text-gray-600 hover:text-turquoise-600 hover:bg-turquoise-50 rounded-full transition-all duration-300 group">
                        <i class="fas fa-user text-xl"></i>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-turquoise-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>

                    <!-- Notifications Icon -->
                    <button class="relative p-3 text-gray-600 hover:text-turquoise-600 hover:bg-turquoise-50 rounded-full transition-all duration-300 group">
                        <i class="fas fa-bell text-xl"></i>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full">
                            <span class="absolute inset-0 bg-red-500 rounded-full animate-ping"></span>
                        </div>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-bold">3</span>
                    </button>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden">
                        <button type="button" class="text-gray-700 hover:text-turquoise-600 focus:outline-none focus:text-turquoise-600 transition-colors p-2" id="mobile-menu-button">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="lg:hidden hidden bg-white shadow-lg border-t border-gray-100" id="mobile-menu">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-turquoise-600 block px-4 py-3 rounded-lg text-base font-medium transition-all duration-300 hover:bg-turquoise-50">Accueil</a>
                <a href="/about" class="text-gray-700 hover:text-turquoise-600 block px-4 py-3 rounded-lg text-base font-medium transition-all duration-300 hover:bg-turquoise-50">À propos</a>
                <a href="/services" class="text-gray-700 hover:text-turquoise-600 block px-4 py-3 rounded-lg text-base font-medium transition-all duration-300 hover:bg-turquoise-50">Services</a>
                <a href="/contact" class="text-gray-700 hover:text-turquoise-600 block px-4 py-3 rounded-lg text-base font-medium transition-all duration-300 hover:bg-turquoise-50">Contact</a>
                <div class="pt-4">
                    <a href="{{ route('booking.wizard') }}" class="bg-turquoise-500 hover:bg-turquoise-600 text-white text-sm px-6 py-3 w-full text-center block rounded-lg transition-all duration-300">
                        Prendre RDV
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="{{ asset('img/logo.png') }}" 
                             alt="Mayelia Mobilité" 
                             class="h-12 w-auto">
                        <div>
                            <div class="text-2xl font-bold bg-gradient-to-r from-turquoise-400 to-sky-blue-400 bg-clip-text text-transparent">
                                Mayelia Mobilité
                            </div>
                            <div class="text-sm text-gray-400">Centre de mobilité</div>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed max-w-md">
                        Votre partenaire de confiance pour tous vos besoins de mobilité en Côte d'Ivoire. 
                        Visa, transport, assistance et formalités administratives avec excellence.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center hover:scale-110 transition-transform duration-300">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center hover:scale-110 transition-transform duration-300">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center hover:scale-110 transition-transform duration-300">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center hover:scale-110 transition-transform duration-300">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Liens rapides</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-turquoise-400 transition-colors duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Accueil
                        </a></li>
                        <li><a href="/about" class="text-gray-300 hover:text-turquoise-400 transition-colors duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            À propos
                        </a></li>
                        <li><a href="/services" class="text-gray-300 hover:text-turquoise-400 transition-colors duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Services
                        </a></li>
                        <li><a href="{{ route('booking.wizard') }}" class="text-gray-300 hover:text-turquoise-400 transition-colors duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Prendre RDV
                        </a></li>
                        <li><a href="/contact" class="text-gray-300 hover:text-turquoise-400 transition-colors duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Contact
                        </a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-white">Contact</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-white text-sm"></i>
                            </div>
                            <span class="text-gray-300">+225 XX XX XX XX</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-white text-sm"></i>
                            </div>
                            <span class="text-gray-300">contact@mayelia-mobilite.ci</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <span class="text-gray-300">Abidjan, Côte d'Ivoire</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-center md:text-left mb-4 md:mb-0">
                        &copy; {{ date('Y') }} Mayelia Mobilité. Tous droits réservés.
                    </p>
                    <div class="flex space-x-6">
                        <a href="/privacy" class="text-gray-400 hover:text-turquoise-400 transition-colors duration-300">Politique de confidentialité</a>
                        <a href="/terms" class="text-gray-400 hover:text-turquoise-400 transition-colors duration-300">Conditions d'utilisation</a>
                    </div>
                </div>
            </div>
    </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white/95', 'backdrop-blur-sm');
            } else {
                navbar.classList.remove('bg-white/95', 'backdrop-blur-sm');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>