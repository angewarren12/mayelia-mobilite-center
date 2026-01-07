<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mayelia Centers') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <!-- Tailwind CSS Local -->
    <script src="{{ asset('js/tailwind.js') }}"></script>
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'page-enter': 'pageEnter 0.4s ease-out forwards',
                    },
                    keyframes: {
                        pageEnter: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
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
    
    <!-- Alpine.js for dropdowns -->
    <script defer src="{{ asset('js/alpine.js') }}"></script>
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024 ? (localStorage.getItem('sidebarOpen') !== 'false') : false,
    mobileOpen: false 
}" 
      x-init="$watch('sidebarOpen', value => { if(window.innerWidth >= 1024) localStorage.setItem('sidebarOpen', value) });">
    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen && window.innerWidth < 1024" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <div class="bg-gradient-to-b from-mayelia-600 to-mayelia-800 shadow-lg flex flex-col transition-all duration-300 ease-in-out fixed lg:static inset-y-0 left-0 z-50 min-h-screen"
             :class="sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full lg:translate-x-0 w-64 lg:w-20'">
            <div class="p-6 flex justify-center items-center border-b border-gray-200 bg-white relative">
                <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia Mobilité" 
                     class="h-16 w-auto transition-all duration-300"
                     :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 w-0 h-0 hidden'">
                <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia Mobilité" 
                     class="h-10 w-10 rounded transition-all duration-300 object-cover"
                     :class="sidebarOpen ? 'opacity-0 w-0 h-0 hidden' : 'opacity-100 block'">
            </div>
            
            <nav class="mt-6 flex-1 overflow-y-auto">
                @php
                    $authService = app(\App\Services\AuthService::class);
                @endphp
                
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 group {{ request()->routeIs('dashboard') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Tableau de bord' : ''">
                    <i class="fas fa-home w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Tableau de bord</span>
                </a>
                
                @if($authService->isAdmin() || $authService->hasPermission('centres', 'view'))
                <a href="{{ route('centres.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('centres.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Gestion du centre' : ''">
                    <i class="fas fa-building w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Gestion du centre</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('creneaux', 'view'))
                <a href="{{ route('creneaux.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('creneaux.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Gestion des créneaux' : ''">
                    <i class="fas fa-calendar-alt w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Gestion des créneaux</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('clients', 'view'))
                <a href="{{ route('clients.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('clients.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Clients' : ''">
                    <i class="fas fa-users w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Clients</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('rendez-vous', 'view'))
                <a href="{{ route('rendez-vous.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('rendez-vous.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Rendez-vous' : ''">
                    <i class="fas fa-calendar-check w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Rendez-vous</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || in_array($authService->getAuthenticatedUser()->role, ['agent', 'agent_biometrie']))
                <a href="{{ route('qms.agent') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('qms.agent') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Guichet Agent' : ''">
                    <i class="fas fa-desktop w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Guichet Agent</span>
                </a>
                @endif
                
                @if($authService->isAdmin())
                <a href="{{ route('agents.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('agents.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Agents' : ''">
                    <i class="fas fa-user-tie w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Agents</span>
                </a>
                
                <a href="{{ route('admin.guichets.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.guichets.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Guichets' : ''">
                    <i class="fas fa-columns w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Gestion des Guichets</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('dossiers', 'view'))
                <a href="{{ route('dossiers.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dossiers.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Dossiers' : ''">
                    <i class="fas fa-folder-open w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dossiers</span>
                </a>
                @endif

                @if($authService->isAdmin() || in_array($authService->getAuthenticatedUser()->role, ['agent', 'agent_biometrie']))
                <a href="{{ route('retraits.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('retraits.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Retraits de carte' : ''">
                    <i class="fas fa-id-card w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Retraits de carte</span>
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('statistics', 'view'))
                <a href="{{ route('statistics.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('statistics.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Statistiques' : ''">
                    <i class="fas fa-chart-line w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Statistiques</span>
                </a>
                @endif
                
                @if($authService->isAdmin())
                <a href="{{ route('document-requis.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('document-requis.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Documents requis' : ''">
                    <i class="fas fa-file-alt w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Documents requis</span>
                </a>
                @endif

                {{--
                @if($authService->isAdmin() || $authService->hasPermission('oneci-transfers', 'view'))
                <a href="{{ route('oneci-transfers.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('oneci-transfers.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Transferts ONECI' : ''">
                    <i class="fas fa-paper-plane w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Transferts ONECI</span>
                </a>
                @endif

                @if($authService->isAdmin() || $authService->hasPermission('oneci-recuperation', 'view'))
                <a href="{{ route('oneci-recuperation.cartes-prete') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('oneci-recuperation.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}"
                   :title="!sidebarOpen ? 'Récupération cartes' : ''">
                    <i class="fas fa-archive w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Récupération cartes</span>
                </a>
                @endif
                --}}
            </nav>
            
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center justify-between text-white/90">
                    @php
                        $currentUser = $authService->getAuthenticatedUser();
                        $userName = $currentUser ? ($currentUser instanceof \App\Models\Agent ? $currentUser->nom_complet : $currentUser->nom) : 'Utilisateur';
                    @endphp
                    <div class="flex items-center" :class="sidebarOpen ? 'flex-row' : 'flex-col'">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center" :class="sidebarOpen ? 'mr-2' : 'mx-auto mb-2'">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <span class="text-sm font-medium transition-opacity duration-300 whitespace-nowrap" 
                              :class="sidebarOpen ? 'opacity-100 truncate max-w-[100px]' : 'opacity-0 w-0 overflow-hidden'">{{ $userName }}</span>
                    </div>
                    <div class="flex items-center" :class="sidebarOpen ? '' : 'ml-auto'">
                        <a href="{{ route('profile.edit') }}" class="text-white/70 hover:text-white transition-colors p-1 rounded hover:bg-white/10 mr-1" title="Paramètres">
                            <i class="fas fa-cog"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/70 hover:text-white transition-colors p-1 rounded hover:bg-white/10" title="Déconnexion">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="p-2 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                                    :title="sidebarOpen ? 'Masquer la sidebar' : 'Afficher la sidebar'">
                                <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            @php
                                $currentUser = $authService->getAuthenticatedUser();
                                $nomComplet = $currentUser ? trim(($currentUser->nom ?? '') . ' ' . ($currentUser->prenom ?? '')) : 'Utilisateur';
                                $role = $currentUser ? match($currentUser->role) {
                                    'admin' => 'Administrateur',
                                    'agent' => 'Agent',
                                    'agent_biometrie' => 'Agent Biométrie',
                                    'oneci' => 'Agent ONECI',
                                    default => 'Utilisateur'
                                } : '';
                                $roleColor = $currentUser ? match($currentUser->role) {
                                    'admin' => 'bg-mayelia-100 text-mayelia-800',
                                    'oneci' => 'bg-purple-100 text-purple-800',
                                    'agent_biometrie' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-green-100 text-green-800'
                                } : '';
                            @endphp
                            
                            @if($currentUser)
                            <!-- Notifications -->
                            @if(in_array($currentUser->role, ['admin', 'agent']))
                                @include('components.notifications')
                            @endif
                            
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ $nomComplet }}</div>
                                    <div class="text-xs text-gray-500">{{ $role }}</div>
                                </div>
                                <div class="w-10 h-10 rounded-full {{ $roleColor }} flex items-center justify-center">
                                    <i class="fas {{ match($currentUser->role) {
                                        'admin' => 'fa-user-shield',
                                        'oneci' => 'fa-building',
                                        default => 'fa-user-tie'
                                    } }} text-sm"></i>
                                </div>
                            </div>
                            @endif
                            
                            @yield('header-actions')
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 animate-page-enter">
                <!-- Page Title -->
                @hasSection('title')
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-mayelia-600">@yield('title')</h1>
                        @hasSection('subtitle')
                            <p class="text-gray-600 mt-1">@yield('subtitle')</p>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    @include('components.toast')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccessToast(@json(session('success')));
            @endif

            @if(session('error'))
                showErrorToast(@json(session('error')));
            @endif

            @if(session('warning'))
                showWarningToast(@json(session('warning')));
            @endif

            @if(session('info'))
                showInfoToast(@json(session('info')));
            @endif

            @if(session('status') === 'password-updated')
                showSuccessToast("Mot de passe mis à jour avec succès.");
            @elseif(session('status') === 'profile-updated')
                showSuccessToast("Profil mis à jour avec succès.");
            @elseif(session('status') === 'verification-link-sent')
                showInfoToast("Un nouveau lien de vérification a été envoyé à votre adresse email.");
            @elseif(session('status'))
                showInfoToast(@json(session('status')));
            @endif
        });
    </script>

    <!-- Global QMS Widget -->
    @include('partials.qms-widget')

    @stack('scripts')
</body>
</html>
