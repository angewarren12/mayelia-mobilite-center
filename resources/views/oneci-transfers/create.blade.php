@extends('layouts.dashboard')

@section('title', 'Créer un Transfert ONECI')
@section('subtitle', 'Sélectionner les dossiers finalisés à envoyer')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('oneci-transfers.create') }}" class="mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="date_finalisation" class="block text-sm font-medium text-gray-700 mb-1">Date de finalisation</label>
                    <input type="date" id="date_finalisation" name="date_finalisation" value="{{ request('date_finalisation', today()->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700">
                        <i class="fas fa-search mr-2"></i>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('oneci-transfers.store') }}" id="transferForm">
            @csrf
            <input type="hidden" name="date_envoi" value="{{ request('date_finalisation', today()->format('Y-m-d')) }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner les dossiers</label>
                @if($dossiers->count() > 0)
                    <div class="border border-gray-300 rounded-md max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-mayelia-600 focus:ring-mayelia-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Finalisation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dossiers as $dossier)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="dossiers[]" value="{{ $dossier->id }}" class="dossier-checkbox rounded border-gray-300 text-mayelia-600 focus:ring-mayelia-500">
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $dossier->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span id="selectedCount">0</span> dossier(s) sélectionné(s)
                    </p>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-4"></i>
                        <p>Aucun dossier finalisé trouvé pour cette date.</p>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optionnel)</label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('oneci-transfers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-mayelia-600 text-white rounded-md hover:bg-mayelia-700" id="submitBtn" disabled>
                    <i class="fas fa-paper-plane mr-2"></i>
                    Créer le Transfert
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.dossier-checkbox');
    const submitBtn = document.getElementById('submitBtn');
    const selectedCount = document.getElementById('selectedCount');

    function updateCount() {
        const count = document.querySelectorAll('.dossier-checkbox:checked').length;
        selectedCount.textContent = count;
        submitBtn.disabled = count === 0;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateCount();
            selectAll.checked = checkboxes.length === document.querySelectorAll('.dossier-checkbox:checked').length;
        });
    });

    updateCount();
});
</script>
@endsection


