@extends('layouts.dashboard')

@section('title', 'Scanner Récupération - Mode Individuel')
@section('subtitle', 'Scannez un code-barres à la fois pour confirmer la récupération')

@section('content')
<div class="space-y-6">
    <!-- Alerte informative -->
    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-green-900 mb-2">Confirmation de récupération physique</h3>
                <div class="text-sm text-green-800 space-y-1">
                    <p>Scannez le code-barres sur l'enveloppe du dossier que vous avez récupéré à l'ONECI.</p>
                    <p class="font-semibold">Après confirmation, un SMS sera automatiquement envoyé au client pour l'informer que sa carte est prête.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone de scan principale -->
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-lg p-8 border-2 border-green-200">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-600 rounded-full mb-4">
                <i class="fas fa-barcode text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Scanner le code-barres</h2>
            <p class="text-gray-600">Pointez votre scanner vers le code-barres sur l'enveloppe</p>
        </div>
        
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" 
                       id="code_barre" 
                       name="code_barre" 
                       autofocus
                       placeholder="Le code-barres s'affichera automatiquement ici..."
                       class="w-full px-6 py-4 text-2xl font-mono text-center border-4 border-green-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-green-500 focus:border-green-500 transition-all"
                       style="letter-spacing: 2px;">
                <div id="scanIndicator" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                    <i class="fas fa-spinner fa-spin text-green-600 text-xl"></i>
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
                    Dossier trouvé - Carte prête
                </h3>
            </div>
            
            <div id="dossierInfo" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Les informations seront injectées ici -->
            </div>
            
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Attention :</strong> En confirmant, vous indiquez que vous avez physiquement récupéré cette carte à l'ONECI. Un SMS sera automatiquement envoyé au client.
                </p>
            </div>
            
            <div class="flex justify-center space-x-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        id="confirmerBtn" 
                        onclick="confirmerRecuperation()"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg font-semibold">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirmer la récupération
                </button>
                <button type="button" 
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
    const scanIndicator = document.getElementById('scanIndicator');
    const confirmerBtn = document.getElementById('confirmerBtn');

    let currentItemId = null;
    let scanTimeout = null;
    let isScanning = false;

    // Si un code est passé en paramètre URL, le pré-remplir et scanner
    const urlParams = new URLSearchParams(window.location.search);
    const codeParam = urlParams.get('code');
    if (codeParam) {
        codeBarreInput.value = codeParam;
        setTimeout(() => scannerCode(codeParam), 300);
    }

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

    function scannerCode(code) {
        if (isScanning) return;
        
        isScanning = true;
        scanIndicator.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        scanResult.classList.add('hidden');
        
        fetch('{{ route("oneci-recuperation.scanner.code") }}', {
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
                        <p class="text-xs text-gray-500 mb-1">Centre</p>
                        <p class="font-semibold text-gray-900">${data.item.centre}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Date envoi à ONECI</p>
                        <p class="font-semibold text-gray-900">${data.item.date_envoi || 'N/A'}</p>
                    </div>
                `;
                
                scanResult.classList.remove('hidden');
            } else {
                errorText.textContent = data.message || 'Code-barres introuvable ou carte non prête';
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

    window.confirmerRecuperation = function() {
        if (!currentItemId) return;
        
        if (!confirm('Confirmer la récupération physique de ce dossier ?\n\nUn SMS sera envoyé au client.')) {
            return;
        }
        
        confirmerBtn.disabled = true;
        confirmerBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
        
        fetch(`/oneci-recuperation/${currentItemId}/confirmer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (response.ok || response.status === 302) {
                // Message de succès
                const successDiv = document.createElement('div');
                successDiv.className = 'bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-4';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-green-900 mb-1">Récupération confirmée !</h4>
                            <p class="text-green-800">✓ Statut du dossier mis à jour<br>✓ SMS envoyé au client<br>✓ Le client peut venir récupérer sa carte au centre le jour suivant</p>
                        </div>
                    </div>
                `;
                scanResult.insertBefore(successDiv, scanResult.firstChild);
                
                // Réinitialiser après 3 secondes
                setTimeout(() => {
                    resetScan();
                }, 3000);
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'Erreur inconnue');
                });
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
            confirmerBtn.disabled = false;
            confirmerBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Confirmer la récupération';
        });
    };

    window.resetScan = function() {
        codeBarreInput.value = '';
        scanResult.classList.add('hidden');
        errorMessage.classList.add('hidden');
        currentItemId = null;
        codeBarreInput.focus();
    };

    // Focus automatique au chargement
    codeBarreInput.focus();
});
</script>
@endsection
