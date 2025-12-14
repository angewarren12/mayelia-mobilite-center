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
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar ONECI -->
        <div class="w-64 bg-purple-700 shadow-lg">
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="text-purple-700 font-bold text-xl">O</span>
                    </div>
                    <span class="text-xl font-bold text-white">ONECI</span>
                </div>
            </div>
            
            <nav class="mt-8">
                <a href="{{ route('oneci.dashboard') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.dashboard') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                    Tableau de bord
                </a>
                
                <a href="{{ route('oneci.dossiers') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.dossiers') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}">
                    <i class="fas fa-folder w-5 h-5 mr-3"></i>
                    Dossiers
                </a>
                
                <a href="{{ route('oneci.scanner') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.scanner') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}">
                    <i class="fas fa-barcode w-5 h-5 mr-3"></i>
                    Scanner
                </a>
                
                <a href="{{ route('oneci.cartes-prete') }}" class="flex items-center px-6 py-3 text-purple-100 hover:bg-purple-600 hover:text-white {{ request()->routeIs('oneci.cartes-prete') ? 'bg-purple-600 text-white border-r-2 border-white' : '' }}">
                    <i class="fas fa-check-circle w-5 h-5 mr-3"></i>
                    Cartes prêtes
                </a>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-6">
                <div class="flex items-center justify-between">
                    @php
                        $currentUser = auth()->user();
                        $nomComplet = $currentUser ? trim(($currentUser->nom ?? '') . ' ' . ($currentUser->prenom ?? '')) : 'Utilisateur';
                    @endphp
                    <span class="text-sm text-purple-100">{{ $nomComplet }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-purple-100 hover:text-white">
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
                            <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Dashboard ONECI')</h1>
                            @hasSection('subtitle')
                                <p class="text-gray-600 mt-1">@yield('subtitle')</p>
                            @endif
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

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

