@extends('layouts.dashboard')

@section('title', 'Statistiques')
@section('subtitle', 'Suivi de performance des agents')

@section('content')
<div class="space-y-6">
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('statistics.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
            </div>
            
            @if(Auth::user()->role === 'admin')
            <div>
                <label for="centre_id" class="block text-sm font-medium text-gray-700 mb-2">Centre</label>
                <select name="centre_id" id="centre_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-mayelia-500 focus:ring-mayelia-500">
                    <option value="">Tous les centres</option>
                    @foreach($centres as $centre)
                        <option value="{{ $centre->id }}" {{ request('centre_id') == $centre->id ? 'selected' : '' }}>
                            {{ $centre->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('statistics.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" title="Réinitialiser">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques Globales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dossiers Ouverts</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">
                        {{ collect($stats)->sum('dossiers_ouverts') }}
                    </h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                    <i class="fas fa-folder-plus text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dossiers Finalisés</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">
                        {{ collect($stats)->sum('dossiers_finalises') }}
                    </h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Temps Moyen / Dossier</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">
                        @php
                            $totalDuration = collect($stats)->sum(function($s) { return $s->avg_duration_minutes * $s->dossiers_finalises; });
                            $totalFinalized = collect($stats)->sum('dossiers_finalises');
                            $globalAvg = $totalFinalized > 0 ? round($totalDuration / $totalFinalized) : 0;
                            
                            $formatted = '-';
                            if ($globalAvg > 0) {
                                $h = floor($globalAvg / 60);
                                $m = $globalAvg % 60;
                                $formatted = ($h > 0 ? "{$h}h " : "") . "{$m}m";
                            }
                        @endphp
                        {{ $formatted }}
                    </h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau par Agent -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Détail par Agent</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centre</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dossiers Ouverts</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dossiers Finalisés</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Temps Moyen (Ouverture -> Fin)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats as $stat)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-mayelia-100 flex items-center justify-center text-mayelia-700 font-bold mr-3">
                                        {{ substr($stat->agent_nom, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $stat->agent_nom }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $stat->centre_nom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">
                                {{ $stat->dossiers_ouverts }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ $stat->dossiers_finalises }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-mono">
                                {{ $stat->avg_duration_formatted }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">
                                Aucun agent trouvé pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
