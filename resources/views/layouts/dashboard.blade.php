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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">M</span>
                    </div>
                    <span class="text-xl font-bold text-gray-800">Mayelia</span>
                </div>
            </div>
            
            <nav class="mt-8">
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-home w-5 h-5 mr-3"></i>
                    Tableau de bord
                </a>
                
                <a href="{{ route('centres.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('centres.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-building w-5 h-5 mr-3"></i>
                    Gestion du centre
                </a>
                
                <a href="{{ route('creneaux.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('creneaux.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                    Gestion des créneaux
                </a>
                
                
                <a href="{{ route('clients.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                    Clients
                </a>
                
                <a href="{{ route('rendez-vous.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('rendez-vous.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                    Rendez-vous
                </a>
                
                <a href="{{ route('agents.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('agents.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-user-tie w-5 h-5 mr-3"></i>
                    Agents
                </a>
                
                <a href="{{ route('dossiers.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('dossiers.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-folder-open w-5 h-5 mr-3"></i>
                    Dossiers
                </a>
                
                <a href="{{ route('document-requis.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('document-requis.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-file-alt w-5 h-5 mr-3"></i>
                    Documents requis
                </a>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ Auth::user()->nom }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600">
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
