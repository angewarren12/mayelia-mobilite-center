@extends('layouts.dashboard')

@section('title', 'Dashboard Super Admin')
@section('subtitle', 'Vue d\'ensemble multi-centres - Mayelia Mobilité')

@section('header-actions')
<div class="flex items-center space-x-4">
    <form method="GET" action="{{ route('super-admin.dashboard') }}" class="flex items-center space-x-2">
        <select name="centre_id" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold hover:border-mayelia-500 focus:ring-2 focus:ring-mayelia-500 focus:border-transparent transition-all">
            <option value="">🌍 Tous les centres</option>
            @foreach(\App\Models\Centre::orderBy('nom')->get() as $c)
                <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->nom }}
                </option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('super-admin.export-rapport') }}" class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-md">
        <i class="fas fa-file-excel mr-2"></i>
        Exporter Rapport
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- STATS GLOBALES --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-globe text-mayelia-600 mr-3"></i>
            Statistiques Globales
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-building text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">CENTRES</span>
                </div>
                <div class="text-4xl font-black">{{ $statsGlobales['total_centres'] }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">{{ \Illuminate\Support\Str::plural('Centre', $statsGlobales['total_centres']) }} actif{{ $statsGlobales['total_centres'] > 1 ? 's' : '' }}</div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-users text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">USERS</span>
                </div>
                <div class="text-4xl font-black">{{ $statsGlobales['total_utilisateurs'] }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Utilisateurs actifs</div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-address-book text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">CLIENTS</span>
                </div>
                <div class="text-4xl font-black">{{ number_format($statsGlobales['total_clients']) }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Clients enregistrés</div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-folder-open text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">MOIS</span>
                </div>
                <div class="text-4xl font-black">{{ $statsGlobales['total_dossiers_ce_mois'] }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Dossiers ce mois</div>
            </div>

            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-id-card text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">MOIS</span>
                </div>
                <div class="text-4xl font-black">{{ $statsGlobales['total_retraits_ce_mois'] }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Retraits ce mois</div>
            </div>

            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-layer-group text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">STOCK</span>
                </div>
                <div class="text-4xl font-black">{{ number_format($statsGlobales['total_stock_cartes']) }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Cartes en stock</div>
            </div>

            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-ticket-alt text-3xl opacity-80"></i>
                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-full">AUJOURD'HUI</span>
                </div>
                <div class="text-4xl font-black">{{ $statsGlobales['total_tickets_aujourdhui'] }}</div>
                <div class="text-sm font-medium opacity-90 mt-1">Tickets QMS</div>
            </div>
        </div>
    </div>

    {{-- STATS DU CENTRE SÉLECTIONNÉ --}}
    @if($selectedCentre && $statsCentre)
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-map-marker-alt text-mayelia-600 mr-3"></i>
            {{ $selectedCentre->nom }} - Détails
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-orange-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Dossiers (Mois)</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $statsCentre['dossiers_mois'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-pink-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Retraits (Mois)</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $statsCentre['retraits_mois'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-indigo-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Stock Cartes</div>
                <div class="text-3xl font-black text-indigo-600 mt-2">{{ number_format($statsCentre['stock_cartes']) }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-teal-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Tickets (Auj.)</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $statsCentre['tickets_aujourdhui'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Clients</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $statsCentre['clients_centre'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-purple-500">
                <div class="text-sm text-gray-500 font-bold uppercase">Agents</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $statsCentre['utilisateurs_actifs'] }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- TOP 5 CENTRES --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-trophy text-yellow-500 mr-3"></i>
            Top 5 Centres - Activité
        </h2>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-black text-gray-700 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-black text-gray-700 uppercase">Centre</th>
                        <th class="px-6 py-3 text-right text-xs font-black text-gray-700 uppercase">Retraits (Mois)</th>
                        <th class="px-6 py-3 text-right text-xs font-black text-gray-700 uppercase">Stock Cartes</th>
                        <th class="px-6 py-3 text-right text-xs font-black text-gray-700 uppercase">Tickets (Aujourd'hui)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topCentres as $index => $centre)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-100 text-gray-600')) }} font-black text-sm">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $centre->nom }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-100 text-pink-800 font-bold text-sm">
                                {{ $centre->retraits_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full {{ $centre->stock_cartes < 50 ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }} font-bold text-sm">
                                {{ number_format($centre->stock_cartes) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-teal-100 text-teal-800 font-bold text-sm">
                                {{ $centre->tickets_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune donnée disponible</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ACTIVITÉ RÉCENTE --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Derniers Retraits --}}
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Derniers Retraits</h3>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="max-h-96 overflow-y-auto">
                    @forelse($activiteRecente['retraits'] as $retrait)
                    <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-bold text-gray-900">{{ $retrait->client->nom_complet }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $retrait->centre->nom }} • {{ $retrait->created_at->format('d/m à H:i') }}
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $retrait->statut === 'finalise' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $retrait->statut }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">Aucun retrait récent</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Derniers Dossiers --}}
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4">📁 Derniers Dossiers</h3>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="max-h-96 overflow-y-auto">
                    @forelse($activiteRecente['dossiers'] as $dossier)
                    <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-bold text-gray-900">{{ $dossier->client->nom_complet }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $dossier->rendezVous->centre->nom }} • {{ $dossier->created_at->format('d/m à H:i') }}
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $dossier->statut === 'finalise' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $dossier->statut }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">Aucun dossier récent</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
