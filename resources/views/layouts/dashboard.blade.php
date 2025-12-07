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
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    
    <!-- Alpine.js for dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-mayelia-600 to-mayelia-800 shadow-lg flex flex-col">
            <div class="p-6 flex justify-center items-center border-b border-gray-200 bg-white">
                <img src="{{ asset('img/LogoMobilité.svg') }}" alt="Mayelia Mobilité" class="h-16 w-auto">
            </div>
            
            <nav class="mt-6 flex-1 overflow-y-auto">
                @php
                    $authService = app(\App\Services\AuthService::class);
                @endphp
                
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dashboard') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-home w-5 h-5 mr-3"></i>
                    Tableau de bord
                </a>
                
                @if($authService->isAdmin() || $authService->hasPermission('centres', 'view'))
                <a href="{{ route('centres.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('centres.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-building w-5 h-5 mr-3"></i>
                    Gestion du centre
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('creneaux', 'view'))
                <a href="{{ route('creneaux.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('creneaux.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                    Gestion des créneaux
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('clients', 'view'))
                <a href="{{ route('clients.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('clients.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                    Clients
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('rendez-vous', 'view'))
                <a href="{{ route('rendez-vous.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('rendez-vous.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                    Rendez-vous
                </a>
                @endif
                
                @if($authService->isAdmin())
                <a href="{{ route('agents.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('agents.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-user-tie w-5 h-5 mr-3"></i>
                    Agents
                </a>
                @endif
                
                @if($authService->isAdmin() || $authService->hasPermission('dossiers', 'view'))
                <a href="{{ route('dossiers.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dossiers.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-folder-open w-5 h-5 mr-3"></i>
                    Dossiers
                </a>
                @endif
                
                @if($authService->isAdmin())
                <a href="{{ route('document-requis.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('document-requis.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-file-alt w-5 h-5 mr-3"></i>
                    Documents requis
                </a>
                @endif

                @if($authService->isAdmin() || $authService->hasPermission('oneci-transfers', 'view'))
                <a href="{{ route('oneci-transfers.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('oneci-transfers.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-paper-plane w-5 h-5 mr-3"></i>
                    Transferts ONECI
                </a>
                @endif

                @if($authService->isAdmin() || $authService->hasPermission('oneci-recuperation', 'view'))
                <a href="{{ route('oneci-recuperation.cartes-prete') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('oneci-recuperation.*') ? 'bg-white text-mayelia-700 border-r-4 border-mayelia-900 font-semibold' : 'text-white/90 hover:bg-white/10 hover:text-white transition-colors' }}">
                    <i class="fas fa-archive w-5 h-5 mr-3"></i>
                    Récupération cartes
                </a>
                @endif
            </nav>
            
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center justify-between text-white/90">
                    @php
                        $currentUser = $authService->getAuthenticatedUser();
                        $userName = $currentUser ? ($currentUser instanceof \App\Models\Agent ? $currentUser->nom_complet : $currentUser->nom) : 'Utilisateur';
                    @endphp
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-2">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <span class="text-sm font-medium truncate max-w-[100px]">{{ $userName }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/70 hover:text-white transition-colors p-1 rounded hover:bg-white/10" title="Déconnexion">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                            @hasSection('subtitle')
                                <p class="text-gray-600 mt-1">@yield('subtitle')</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            @php
                                $currentUser = $authService->getAuthenticatedUser();
                                $nomComplet = $currentUser ? trim(($currentUser->nom ?? '') . ' ' . ($currentUser->prenom ?? '')) : 'Utilisateur';
                                $role = $currentUser ? match($currentUser->role) {
                                    'admin' => 'Administrateur',
                                    'agent' => 'Agent',
                                    'oneci' => 'Agent ONECI',
                                    default => 'Utilisateur'
                                } : '';
                                $roleColor = $currentUser ? match($currentUser->role) {
                                    'admin' => 'bg-mayelia-100 text-mayelia-800',
                                    'oneci' => 'bg-purple-100 text-purple-800',
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
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Toast Notifications -->
    @include('components.toast')
</body>
</html>
