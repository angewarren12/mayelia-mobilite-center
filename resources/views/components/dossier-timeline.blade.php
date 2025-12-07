@props(['dossier'])

<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
        <i class="fas fa-history text-mayelia-500 mr-2"></i>
        Historique du dossier
    </h3>

    <div class="relative pl-4 border-l-2 border-gray-200 space-y-8">
        @forelse($dossier->actionsLog as $log)
            <div class="relative">
                <!-- Icone -->
                <div class="absolute -left-[25px] bg-white p-1">
                    <div class="h-8 w-8 rounded-full bg-{{ $log->color }}-100 flex items-center justify-center border-2 border-white shadow-sm">
                        <i class="fas {{ $log->icon }} text-{{ $log->color }}-600 text-xs"></i>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="ml-4">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $log->action_formatted }}</h4>
                        <span class="text-xs text-gray-500" title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        @if($log->description)
                            <p class="mb-1">{{ $log->description }}</p>
                        @endif
                        
                        <p class="text-xs text-gray-400">
                            Par {{ $log->user->nom_complet ?? 'Système' }}
                        </p>
                    </div>

                    <!-- Données additionnelles (si présentes) -->
                    @if($log->data)
                        <div class="mt-2 bg-gray-50 rounded p-2 text-xs text-gray-500 font-mono">
                            <pre>{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4">
                Aucune action enregistrée pour le moment.
            </div>
        @endforelse
    </div>
</div>
