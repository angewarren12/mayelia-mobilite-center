@extends('layouts.dashboard')

@section('title', 'Scanner Récupération - Mode Lot')
@section('subtitle', 'Scanner plusieurs codes-barres en une fois')

@section('content')
<div class="space-y-6">
    <!-- Alerte informative -->
    <div class="bg-mayelia-50 border-2 border-mayelia-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-list-check text-mayelia-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-mayelia-900 mb-2">Mode scan en lot</h3>
                <div class="text-sm text-mayelia-800 space-y-1">
                    <p>Scannez plusieurs codes-barres successivement. Chaque scan ajoute le dossier à la liste de confirmation.</p>
                    <p class="font-semibold">Cliquez sur "Confirmer tout" pour marquer tous les dossiers scannés comme récupérés et envoyer les SMS aux clients.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone de scan -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-lg p-8 border-2 border-mayelia-200">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-mayelia-600 rounded-full mb-4">
                <i class="fas fa-barcode text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Scanner les codes-barres</h2>
            <p class="text-gray-600">Scannez successivement tous les dossiers récupérés</p>
        </div>
        
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" 
                       id="code_barre" 
                       name="code_barre" 
                       autofocus
                       placeholder="Le code-barres s'ajoutera automatiquement à la liste..."
                       class="w-full px-6 py-4 text-2xl font-mono text-center border-4 border-mayelia-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-mayelia-500 focus:border-mayelia-500 transition-all"
                       style="letter-spacing: 2px;">
                <div id="scanIndicator" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                    <i class="fas fa-spinner fa-spin text-mayelia-600 text-xl"></i>
                </div>
            </div>
            <p class="mt-4 text-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Détection automatique - Continuez à scanner jusqu'à avoir scanné tous les dossiers
            </p>
        </div>
    </div>

    <!-- Liste des dossiers scannés -->
    <div id="scannedListContainer" class="hidden">
        <div class="bg-white rounded-lg shadow-xl border-2 border-green-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-list mr-2"></i>
                    Dossiers scannés (<span id="scannedCount">0</span>)
                </h3>
                <button type="button" 
                        onclick="clearScannedList()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">
                    <i class="fas fa-trash mr-2"></i>
                    Effacer la liste
                </button>
            </div>
            
            <div id="scannedList" class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                <!-- Les dossiers scannés seront ajoutés ici -->
            </div>

            <div class="flex justify-center space-x-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        id="confirmerToutBtn" 
                        onclick="confirmerTout()"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg font-semibold">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirmer tous les dossiers scannés
                </button>
            </div>
        </div>
    </div>

    <!-- Message d'erreur -->
    <div id="errorMessage" class="hidden bg-red-50 border-2 border-red-200 rounded-lg p-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-4"></i>
            <div>
                <h4 class="font-semibold text-red-900 mb-1">Erreur de scan</h4>
                <p class="text-red-800" id="errorText"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeBarreInput = document.getElementById('code_barre');
    const scanIndicator = document.getElementById('scanIndicator');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const scannedListContainer = document.getElementById('scannedListContainer');
    const scannedList = document.getElementById('scannedList');
    const scannedCount = document.getElementById('scannedCount');
    const confirmerToutBtn = document.getElementById('confirmerToutBtn');

    let scannedItems = []; // {id, code_barre, client, service, centre}
    let scanTimeout = null;
    let isScanning = false;

    // Détection automatique du scan
    codeBarreInput.addEventListener('input', function() {
        clearTimeout(scanTimeout);
        
        // Si l'utilisateur tape rapidement (scanner USB), déclencher immédiatement
        if (this.value.length >= 10 && !isScanning) {
            scannerCode(this.value);
        } else {
            // Sinon, attendre 800ms pour la saisie manuelle
            scanTimeout = setTimeout(() => {
                if (this.value.length > 0 && !isScanning) {
                    scannerCode(this.value);
                }
            }, 800);
        }
    });

    codeBarreInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value && !isScanning) {
                scannerCode(this.value);
            }
        }
    });

    function scannerCode(code) {
        if (isScanning) return;
        
        // Vérifier si déjà scanné
        const codeTrimmed = code.trim();
        if (scannedItems.some(item => item.code_barre === codeTrimmed)) {
            errorText.textContent = 'Ce code-barres a déjà été scanné';
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 2000);
            codeBarreInput.value = '';
            codeBarreInput.focus();
            return;
        }
        
        isScanning = true;
        scanIndicator.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        
        fetch('{{ route("oneci-recuperation.scanner.code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code_barre: codeTrimmed })
        })
        .then(response => response.json())
        .then(data => {
            isScanning = false;
            scanIndicator.classList.add('hidden');
            
            if (data.success) {
                // Ajouter à la liste des scannés
                scannedItems.push(data.item);
                
                // Afficher dans la liste
                const itemDiv = document.createElement('div');
                itemDiv.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
                itemDiv.id = 'item-' + data.item.id;
                itemDiv.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <div>
                                    <div class="font-mono font-bold text-gray-900">${data.item.code_barre}</div>
                                    <div class="text-sm text-gray-600">${data.item.client} - ${data.item.service}</div>
                                    <div class="text-xs text-gray-500">${data.item.centre}</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="removeItem(${data.item.id})"
                                class="text-red-600 hover:text-red-800 ml-4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                scannedList.appendChild(itemDiv);
                
                // Afficher le container si pas encore visible
                scannedListContainer.classList.remove('hidden');
                
                // Mettre à jour le compteur
                scannedCount.textContent = scannedItems.length;
                
                // Réinitialiser l'input et focus pour le prochain scan
                codeBarreInput.value = '';
                codeBarreInput.focus();
                
            } else {
                errorText.textContent = data.message || 'Code-barres introuvable';
                errorMessage.classList.remove('hidden');
                codeBarreInput.select();
            }
        })
        .catch(error => {
            isScanning = false;
            scanIndicator.classList.add('hidden');
            errorText.textContent = 'Erreur lors du scan: ' + error.message;
            errorMessage.classList.remove('hidden');
        });
    }

    window.removeItem = function(itemId) {
        scannedItems = scannedItems.filter(item => item.id !== itemId);
        const itemDiv = document.getElementById('item-' + itemId);
        if (itemDiv) {
            itemDiv.remove();
        }
        scannedCount.textContent = scannedItems.length;
        
        if (scannedItems.length === 0) {
            scannedListContainer.classList.add('hidden');
        }
    };

    window.clearScannedList = function() {
        if (confirm('Effacer tous les dossiers scannés ?')) {
            scannedItems = [];
            scannedList.innerHTML = '';
            scannedCount.textContent = '0';
            scannedListContainer.classList.add('hidden');
            codeBarreInput.focus();
        }
    };

    window.confirmerTout = function() {
        if (scannedItems.length === 0) {
            alert('Aucun dossier à confirmer');
            return;
        }
        
        if (!confirm(`Confirmer la récupération de ${scannedItems.length} dossier(s) ?\n\nUn SMS sera envoyé à chaque client.`)) {
            return;
        }
        
        confirmerToutBtn.disabled = true;
        confirmerToutBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement en cours...';
        
        const itemIds = scannedItems.map(item => item.id);
        
        fetch('{{ route("oneci-recuperation.confirmer-lot") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items: itemIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Message de succès
                const successDiv = document.createElement('div');
                successDiv.className = 'bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-4';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-green-900 mb-1">Récupération confirmée !</h4>
                            <p class="text-green-800">${data.message}</p>
                        </div>
                    </div>
                `;
                scannedListContainer.insertBefore(successDiv, scannedListContainer.firstChild);
                
                // Réinitialiser après 3 secondes
                setTimeout(() => {
                    window.location.href = '{{ route("oneci-recuperation.cartes-prete") }}';
                }, 3000);
            } else {
                alert('Erreur: ' + data.message);
                confirmerToutBtn.disabled = false;
                confirmerToutBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Confirmer tous les dossiers scannés';
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
            confirmerToutBtn.disabled = false;
            confirmerToutBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Confirmer tous les dossiers scannés';
        });
    };

    // Focus automatique
    codeBarreInput.focus();
});
</script>
@endsection


