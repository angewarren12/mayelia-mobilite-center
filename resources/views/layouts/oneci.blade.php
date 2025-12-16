<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mayelia Centers') }} - ONECI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS Local -->
    <script src="{{ asset('js/tailwind.js') }}"></script>
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    
    <!-- Alpine.js for dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpenOneci') !== 'false' }" 
      x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpenOneci', value))">
    <div class="min-h-screen flex">
        <!-- Sidebar ONECI -->
        <div class="bg-purple-700 shadow-lg transition-all duration-300 ease-in-out" :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="p-6 flex justify-center items-center relative">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <span class="text-purple-700 font-bold text-xl">O</span>
                </div>
                <span class="text-xl font-bold text-white transition-opacity duration-300 whitespace-nowrap ml-3" 
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">ONECI</span>
            </div>
            
            <nav class="mt-8">
                <a href="{{ route('oneci.dashboard') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.dashboard') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}"
                   :title="!sidebarOpen ? 'Tableau de bord' : ''">
                    <i class="fas fa-tachometer-alt w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Tableau de bord</span>
                </a>
                
                <a href="{{ route('oneci.dossiers') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.dossiers') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}"
                   :title="!sidebarOpen ? 'Dossiers' : ''">
                    <i class="fas fa-folder w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dossiers</span>
                </a>
                
                <a href="{{ route('oneci.scanner') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.scanner') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}"
                   :title="!sidebarOpen ? 'Scanner' : ''">
                    <i class="fas fa-barcode w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Scanner</span>
                </a>
                
                <a href="{{ route('oneci.cartes-prete') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.cartes-prete') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}"
                   :title="!sidebarOpen ? 'Cartes prêtes' : ''">
                    <i class="fas fa-check-circle w-5 h-5" :class="sidebarOpen ? 'mr-3' : 'mx-auto'"></i>
                    <span class="transition-opacity duration-300 whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Cartes prêtes</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 p-6 w-full" :class="sidebarOpen ? 'w-64' : 'w-20'">
                <div class="flex items-center justify-between" :class="sidebarOpen ? 'flex-row' : 'flex-col'">
                    @php
                        $currentUser = auth()->user();
                        $nomComplet = $currentUser ? trim(($currentUser->nom ?? '') . ' ' . ($currentUser->prenom ?? '')) : 'Utilisateur';
                    @endphp
                    <span class="text-sm text-purple-100 transition-opacity duration-300 whitespace-nowrap" 
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">{{ $nomComplet }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline" :class="sidebarOpen ? '' : 'ml-auto'">
                        @csrf
                        <button type="submit" class="text-purple-100 hover:text-white" title="Déconnexion">
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
                        <div class="flex items-center space-x-4">
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="p-2 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                                    :title="sidebarOpen ? 'Masquer la sidebar' : 'Afficher la sidebar'">
                                <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            @php
                                $currentUser = auth()->user();
                                $nomComplet = $currentUser ? trim(($currentUser->nom ?? '') . ' ' . ($currentUser->prenom ?? '')) : 'Utilisateur';
                            @endphp
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ $nomComplet }}</div>
                                    <div class="text-xs text-purple-600 font-medium">Agent ONECI</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            </div>
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

                <!-- Page Title -->
                @hasSection('title')
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-purple-600">@yield('title')</h1>
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
</body>
</html>

