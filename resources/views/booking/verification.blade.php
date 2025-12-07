@extends('booking.layout')

@section('title', 'Vérification du pré-enrôlement')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-12">
            <div class="mb-6">
                <i class="fas fa-shield-alt text-6xl text-mayelia-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Vérification du pré-enrôlement ONECI
            </h2>
            <p class="text-lg text-gray-600">
                Veuillez entrer votre numéro de pré-enrôlement pour continuer
            </p>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Formulaire de vérification -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form id="verification-form">
                @csrf
                <div class="mb-6">
                    <label for="numero_pre_enrolement" class="block text-sm font-medium text-gray-700 mb-2">
                        Numéro de pré-enrôlement <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="numero_pre_enrolement" 
                        name="numero_pre_enrolement" 
                        required
                        placeholder="Ex: ONECI2025001"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-mayelia-500 focus:border-mayelia-500 text-lg"
                    >
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ce numéro vous a été fourni par l'ONECI après votre pré-enrôlement
                    </p>
                </div>

                <!-- Zone de message -->
                <div id="message-container" class="mb-6 hidden"></div>

                <!-- Bouton de soumission -->
                <button 
                    type="submit" 
                    id="verify-btn"
                    class="w-full px-6 py-3 bg-mayelia-600 hover:bg-mayelia-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center"
                >
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Vérifier mon numéro</span>
                </button>
            </form>

            <!-- Informations supplémentaires -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-question-circle mr-2 text-mayelia-600"></i>
                    Vous n'avez pas encore de numéro de pré-enrôlement ?
                </h3>
                <p class="text-gray-600 mb-4">
                    Pour obtenir votre numéro de pré-enrôlement, vous devez d'abord effectuer votre pré-enrôlement auprès de l'ONECI.
                </p>
                <a 
                    href="{{ route('booking.oneci-redirect') }}" 
                    target="_blank"
                    class="inline-flex items-center text-mayelia-600 hover:text-mayelia-700 font-medium"
                >
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Effectuer mon pré-enrôlement ONECI
                </a>
            </div>

            <!-- Numéros de test (à retirer en production) -->
            <div class="mt-8 pt-8 border-t border-gray-200 bg-yellow-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-yellow-900 mb-2">
                    <i class="fas fa-flask mr-2"></i>
                    Numéros de test disponibles
                </h4>
                <div class="text-xs text-yellow-800 space-y-1">
                    <p><strong>ONECI2025001</strong> - Statut: Validé ✓</p>
                    <p><strong>ONECI2025002</strong> - Statut: Validé ✓</p>
                    <p><strong>ONECI2025003</strong> - Statut: En attente ⏳</p>
                    <p><strong>ONECI2025004</strong> - Statut: Rejeté ✗</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('verification-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('verify-btn');
        const messageContainer = document.getElementById('message-container');
        const numeroInput = document.getElementById('numero_pre_enrolement');
        const numero = numeroInput.value.trim();
        
        // Désactiver le bouton et afficher le chargement
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Vérification en cours...</span>';
        messageContainer.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route('booking.verify-enrollment') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    numero_pre_enrolement: numero
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Succès - rediriger vers le wizard
                showMessage('success', data.message);
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                // Erreur - afficher le message
                let messageClass = 'bg-red-50 border-red-200 text-red-800';
                let icon = 'fa-exclamation-circle';
                
                if (data.statut === 'en_attente') {
                    messageClass = 'bg-yellow-50 border-yellow-200 text-yellow-800';
                    icon = 'fa-clock';
                } else if (data.statut === 'rejete') {
                    messageClass = 'bg-red-50 border-red-200 text-red-800';
                    icon = 'fa-times-circle';
                }
                
                showMessage('error', data.message, messageClass, icon);
                
                // Réactiver le bouton
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span>Vérifier mon numéro</span>';
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            showMessage('error', 'Une erreur est survenue lors de la vérification. Veuillez réessayer.');
            
            // Réactiver le bouton
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span>Vérifier mon numéro</span>';
        }
    });
    
    function showMessage(type, message, customClass = null, customIcon = null) {
        const messageContainer = document.getElementById('message-container');
        
        let className = customClass || (type === 'success' 
            ? 'bg-green-50 border-green-200 text-green-800' 
            : 'bg-red-50 border-red-200 text-red-800');
        
        let icon = customIcon || (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle');
        
        messageContainer.className = `mb-6 border px-4 py-3 rounded-lg flex items-center ${className}`;
        messageContainer.innerHTML = `
            <i class="fas ${icon} mr-3"></i>
            <span>${message}</span>
        `;
        messageContainer.classList.remove('hidden');
    }
</script>
@endsection
