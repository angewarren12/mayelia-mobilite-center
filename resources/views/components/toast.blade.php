<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50" style="max-width: 400px; pointer-events: none;">
    <!-- Toast notifications will be inserted here -->
</div>

<script>
// Fonction pour obtenir ou créer le container toast
function getToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        // Créer le container s'il n'existe pas
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50';
        container.style.maxWidth = '400px';
        container.style.pointerEvents = 'none';
        document.body.appendChild(container);
    }
    return container;
}

// Fonction pour afficher une notification toast
function showToast(message, type = 'success', duration = 5000) {
    const container = getToastContainer();
    
    // Créer l'élément toast avec ID unique
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = 'bg-white rounded-lg shadow-xl border border-gray-200 pointer-events-auto mb-3 transform transition-all duration-300 ease-in-out';
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    
    // Définir les couleurs selon le type
    let iconBgColor = 'bg-green-500';
    let iconColor = 'text-white';
    let icon = 'fas fa-check';
    let textColor = 'text-green-600';
    let closeButtonColor = 'text-green-600 hover:text-green-800';
    
    if (type === 'error') {
        iconBgColor = 'bg-red-500';
        iconColor = 'text-white';
        icon = 'fas fa-times-circle';
        textColor = 'text-red-600';
        closeButtonColor = 'text-red-600 hover:text-red-800';
    } else if (type === 'warning') {
        iconBgColor = 'bg-yellow-500';
        iconColor = 'text-white';
        icon = 'fas fa-exclamation-triangle';
        textColor = 'text-yellow-600';
        closeButtonColor = 'text-yellow-600 hover:text-yellow-800';
    } else if (type === 'info') {
        iconBgColor = 'bg-blue-500';
        iconColor = 'text-white';
        icon = 'fas fa-info-circle';
        textColor = 'text-blue-600';
        closeButtonColor = 'text-blue-600 hover:text-blue-800';
    }
    
    // Échapper le message pour éviter les problèmes XSS
    const escapedMessage = message.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    
    toast.innerHTML = `
        <div class="p-4 flex items-center">
            <!-- Cercle avec icône à gauche -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 ${iconBgColor} rounded-full flex items-center justify-center">
                    <i class="${icon} ${iconColor} text-sm"></i>
                </div>
            </div>
            
            <!-- Message au centre -->
            <div class="ml-4 flex-1">
                <div class="text-sm font-medium ${textColor} leading-relaxed">
                    ${escapedMessage.split(' ').map((word, index, words) => {
                        // Permettre les retours à la ligne naturels et ajuster si nécessaire
                        if (index > 0 && index % 8 === 0 && word.length > 0) {
                            return '<br>' + word;
                        }
                        return word;
                    }).join(' ')}
                </div>
            </div>
            
            <!-- Bouton fermer à droite -->
            <div class="ml-4 flex-shrink-0">
                <button onclick="closeToastById('${toastId}')" class="${closeButtonColor} focus:outline-none transition-colors" type="button">
                    <span class="sr-only">Fermer</span>
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
    `;
    
    // Ajouter le toast au container
    container.appendChild(toast);
    
    // Forcer le reflow pour que l'animation fonctionne
    toast.offsetHeight;
    
    // Animation d'entrée
    requestAnimationFrame(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    });
    
    // Auto-suppression après la durée spécifiée
    const autoCloseTimeout = setTimeout(() => {
        closeToastById(toastId);
    }, duration);
    
    // Stocker le timeout pour pouvoir l'annuler si l'utilisateur ferme manuellement
    toast.dataset.timeoutId = autoCloseTimeout;
}

// Fonction pour fermer un toast par ID
function closeToastById(toastId) {
    const toast = document.getElementById(toastId);
    if (!toast) return;
    
    // Annuler le timeout d'auto-fermeture s'il existe
    if (toast.dataset.timeoutId) {
        clearTimeout(parseInt(toast.dataset.timeoutId));
        delete toast.dataset.timeoutId;
    }
    
    // Animation de sortie
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    
    // Supprimer après l'animation
    setTimeout(() => {
        if (toast && toast.parentNode) {
            toast.remove();
        }
    }, 300);
}

// Fonction pour fermer un toast via le bouton (rétrocompatibilité)
function closeToast(button) {
    const toast = button.closest('div[id^="toast-"]');
    if (toast && toast.id) {
        closeToastById(toast.id);
    } else {
        // Fallback : fermer directement
        const toastElement = button.closest('.bg-white.rounded-lg');
        if (toastElement) {
            toastElement.style.transform = 'translateX(100%)';
            toastElement.style.opacity = '0';
            setTimeout(() => {
                if (toastElement && toastElement.parentNode) {
                    toastElement.remove();
                }
            }, 300);
        }
    }
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

// Initialisation : s'assurer que le container existe au chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', getToastContainer);
} else {
    getToastContainer();
}
</script>
