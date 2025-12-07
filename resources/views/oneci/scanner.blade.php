@extends('layouts.oneci')

@section('title', 'Scanner Code-barres')
@section('subtitle', 'Scanner un code-barres pour marquer une carte comme prête')

@section('content')
<div class="space-y-6">
    <!-- Zone de scan principale -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-lg p-8 border-2 border-mayelia-200">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-mayelia-600 rounded-full mb-4">
                <i class="fas fa-barcode text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Scanner le code-barres</h2>
            <p class="text-gray-600">Pointez votre scanner vers le code-barres ou entrez-le manuellement</p>
        </div>
        
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" 
                       id="code_barre" 
                       name="code_barre" 
                       autofocus
                       placeholder="Le code-barres s'affichera automatiquement ici..."
                       class="w-full px-6 py-4 text-2xl font-mono text-center border-4 border-mayelia-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-mayelia-500 focus:border-mayelia-500 transition-all"
                       style="letter-spacing: 2px;">
                <div id="scanIndicator" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                    <i class="fas fa-spinner fa-spin text-mayelia-600 text-xl"></i>
                </div>
            </div>
            <p class="mt-4 text-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Détection automatique - Le scan se déclenche automatiquement
            </p>
        </div>
    </div>

    <!-- Résultat du scan -->
    <div id="scanResult" class="hidden">
        <div class="bg-white rounded-lg shadow-xl border-2 border-green-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Dossier trouvé
                </h3>
                <span id="statutBadge" class="px-4 py-2 rounded-full text-sm font-medium"></span>
            </div>
            
            <div id="dossierInfo" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Les informations seront injectées ici -->
            </div>
            
            <div class="flex justify-center space-x-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        id="marquerPreteBtn" 
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle mr-2"></i>
                    Marquer la carte comme prête
                </button>
                <button type="button" 
                        id="resetBtn" 
                        onclick="resetScan()"
                        class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all font-semibold">
                    <i class="fas fa-redo mr-2"></i>
                    Nouveau scan
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
    const scanResult = document.getElementById('scanResult');
    const dossierInfo = document.getElementById('dossierInfo');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const marquerPreteBtn = document.getElementById('marquerPreteBtn');
    const scanIndicator = document.getElementById('scanIndicator');
    const statutBadge = document.getElementById('statutBadge');

    let currentItemId = null;
    let scanTimeout = null;
    let isScanning = false;

    // Détection automatique du scan (entrée rapide = scanner USB)
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

    // Empêcher la soumission du formulaire avec Enter
    codeBarreInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value && !isScanning) {
                scannerCode(this.value);
            }
        }
    });

    marquerPreteBtn.addEventListener('click', function() {
        if (currentItemId) {
            marquerCartePrete(currentItemId);
        }
    });

    function scannerCode(code) {
        if (isScanning) return;
        
        isScanning = true;
        scanIndicator.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        scanResult.classList.add('hidden');
        
        fetch('{{ route("oneci.scanner.code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code_barre: code.trim() })
        })
        .then(response => response.json())
        .then(data => {
            isScanning = false;
            scanIndicator.classList.add('hidden');
            
            if (data.success) {
                currentItemId = data.item.id;
                
                // Badge statut
                const statutColors = {
                    'en_attente': 'bg-yellow-100 text-yellow-800',
                    'recu': 'bg-mayelia-100 text-mayelia-800',
                    'traite': 'bg-indigo-100 text-indigo-800'
                };
                const color = statutColors[data.item.statut] || 'bg-gray-100 text-gray-800';
                statutBadge.className = `px-4 py-2 rounded-full text-sm font-medium ${color}`;
                statutBadge.textContent = data.item.statut_formate;
                
                // Informations du dossier
                dossierInfo.innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Code-barres</p>
                        <p class="font-mono font-bold text-lg text-gray-900">${data.item.code_barre}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Client</p>
                        <p class="font-semibold text-gray-900">${data.item.client}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Service</p>
                        <p class="font-semibold text-gray-900">${data.item.service}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Centre Mayelia</p>
                        <p class="font-semibold text-gray-900">${data.item.centre}</p>
                    </div>
                    ${data.item.date_reception ? `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Date réception</p>
                        <p class="font-semibold text-gray-900">${data.item.date_reception}</p>
                    </div>
                    ` : ''}
                `;
                
                scanResult.classList.remove('hidden');
                marquerPreteBtn.disabled = !['en_attente', 'recu', 'traite'].includes(data.item.statut);
                
                // Son de confirmation (optionnel)
                // const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWi77+efTRAMUKfj8LZjHAY4kdfyzHksBSR3x/DdkEAKFF606OuoVRQKRp/g8r5sIQUrgc7y2Yk2CBtou+/nn00QDFCn4/C2YxwGOJHX8sx5LAUkd8fw3ZBAC');
                // audio.play();
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

    function marquerCartePrete(itemId) {
        if (!confirm('Confirmer que la carte est prête pour ce dossier ?')) {
            return;
        }
        
        marquerPreteBtn.disabled = true;
        marquerPreteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
        
        fetch(`/oneci/dossiers/${itemId}/carte-prete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
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
                            <h4 class="font-semibold text-green-900 mb-1">Carte marquée comme prête !</h4>
                            <p class="text-green-800">${data.message}</p>
                        </div>
                    </div>
                `;
                scanResult.insertBefore(successDiv, scanResult.firstChild);
                
                // Réinitialiser après 2 secondes
                setTimeout(() => {
                    resetScan();
                }, 2000);
            } else {
                alert('Erreur: ' + data.message);
                marquerPreteBtn.disabled = false;
                marquerPreteBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Marquer la carte comme prête';
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
            marquerPreteBtn.disabled = false;
            marquerPreteBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Marquer la carte comme prête';
        });
    }

    function resetScan() {
        codeBarreInput.value = '';
        scanResult.classList.add('hidden');
        errorMessage.classList.add('hidden');
        currentItemId = null;
        codeBarreInput.focus();
    }

    // Focus automatique au chargement
    codeBarreInput.focus();
});
</script>
@endsection
