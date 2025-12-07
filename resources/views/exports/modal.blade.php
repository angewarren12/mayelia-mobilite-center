<!-- Modal d'export des rendez-vous -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-download text-mayelia-600 mr-2"></i>
                        Exporter les rendez-vous
                    </h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="exportForm" method="POST" action="{{ route('export.rendez-vous') }}">
                    @csrf
                    
                    <!-- Type d'export -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Période d'export
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="aujourdhui" class="mr-3" checked>
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-day text-mayelia-500 mr-2"></i>
                                    Aujourd'hui
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="date" class="mr-3">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar text-green-500 mr-2"></i>
                                    Date spécifique
                                </span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="radio" name="type_export" value="plage" class="mr-3">
                                <span class="text-sm text-gray-700">
                                    <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                    Plage de dates
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date spécifique -->
                    <div id="dateField" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date
                        </label>
                        <input type="date" name="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                    </div>
                    
                    <!-- Plage de dates -->
                    <div id="plageFields" class="mb-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date début
                                </label>
                                <input type="date" name="date_debut" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date fin
                                </label>
                                <input type="date" name="date_fin" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtres supplémentaires -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtres supplémentaires (optionnels)</h4>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut
                                </label>
                                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                    <option value="">Tous les statuts</option>
                                    <option value="confirme">Confirmé</option>
                                    <option value="dossier_ouvert">Dossier ouvert</option>
                                    <option value="documents_verifies">Documents vérifiés</option>
                                    <option value="paiement_effectue">Paiement effectué</option>
                                    <option value="dossier_oneci">Dossier ONECI</option>
                                    <option value="carte_mayelia">Carte Mayelia</option>
                                    <option value="carte_prete">Carte prête</option>
                                    <option value="termine">Terminé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Centre
                                </label>
                                <select name="centre_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-mayelia-500">
                                    <option value="">Tous les centres</option>
                                    @foreach(\App\Models\Centre::all() as $centre)
                                        <option value="{{ $centre->id }}">{{ $centre->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeExportModal()" 
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-mayelia-600 text-white rounded-lg hover:bg-mayelia-700">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

// Gestion des champs conditionnels
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type_export"]');
    const dateField = document.getElementById('dateField');
    const plageFields = document.getElementById('plageFields');
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Masquer tous les champs
            dateField.classList.add('hidden');
            plageFields.classList.add('hidden');
            
            // Afficher le champ approprié
            if (this.value === 'date') {
                dateField.classList.remove('hidden');
            } else if (this.value === 'plage') {
                plageFields.classList.remove('hidden');
            }
        });
    });
});
</script>
