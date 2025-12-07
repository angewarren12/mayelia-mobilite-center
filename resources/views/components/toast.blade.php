<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
    <!-- Toast notifications will be inserted here -->
</div>

<script>
// Fonction pour afficher une notification toast
function showToast(message, type = 'success', duration = 5000) {
    const container = document.getElementById('toast-container');
    
    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `max-w-md w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out translate-x-full`;
    
    // Définir les couleurs selon le type
    let bgColor = 'bg-green-50';
    let textColor = 'text-green-800';
    let iconColor = 'text-green-400';
    let icon = 'fas fa-check-circle';
    
    if (type === 'error') {
        bgColor = 'bg-red-50';
        textColor = 'text-red-800';
        iconColor = 'text-red-400';
        icon = 'fas fa-exclamation-circle';
    } else if (type === 'warning') {
        bgColor = 'bg-yellow-50';
        textColor = 'text-yellow-800';
        iconColor = 'text-yellow-400';
        icon = 'fas fa-exclamation-triangle';
    } else if (type === 'info') {
        bgColor = 'bg-mayelia-50';
        textColor = 'text-mayelia-800';
        iconColor = 'text-mayelia-400';
        icon = 'fas fa-info-circle';
    }
    
    toast.innerHTML = `
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="${icon} ${iconColor} h-6 w-6"></i>
                </div>
                <div class="ml-4 w-0 flex-1 pt-0.5">
                    <p class="text-base font-medium ${textColor}">
                        ${message}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="closeToast(this)" class="bg-white rounded-md inline-flex ${textColor} hover:${textColor.replace('800', '600')} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Fermer</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter le toast au container
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-suppression après la durée spécifiée
    setTimeout(() => {
        closeToast(toast.querySelector('button'));
    }, duration);
}

// Fonction pour fermer un toast
function closeToast(button) {
    const toast = button.closest('div');
    toast.classList.add('translate-x-full');
    setTimeout(() => {
        toast.remove();
    }, 300);
}

// Fonctions utilitaires pour différents types de notifications
function showSuccessToast(message, duration = 5000) {
    showToast(message, 'success', duration);
}

function showErrorToast(message, duration = 7000) {
    showToast(message, 'error', duration);
}

function showWarningToast(message, duration = 6000) {
    showToast(message, 'warning', duration);
}

function showInfoToast(message, duration = 5000) {
    showToast(message, 'info', duration);
}
</script>
