@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Background avec gradient animé -->
    <div class="absolute inset-0 bg-gradient-to-br from-mayelia-600 via-mayelia-700 to-mayelia-900">
        <!-- Formes décoratives animées -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-mayelia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Contenu principal -->
    <div class="relative z-10 w-full max-w-lg px-4 sm:px-6 lg:px-8">
        <!-- Carte de connexion avec glassmorphism -->
        <div class="backdrop-blur-xl bg-white/10 rounded-3xl shadow-2xl border border-white/20 p-8 sm:p-12">
            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="mx-auto h-28 w-28 bg-white rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform duration-300 p-4">
                    <img src="{{ asset('img/logo-oneci.jpg') }}" 
                         alt="Mayelia Mobilité & ONECI" 
                         class="w-full h-full object-contain">
                </div>
                <h2 class="mt-6 text-3xl sm:text-4xl font-extrabold text-white">
                    Bienvenue
                </h2>
                <p class="mt-2 text-sm text-mayelia-100">
                    Connectez-vous à votre espace Mayelia
                </p>
            </div>
            
            <!-- Formulaire -->
            <form class="space-y-6" method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-white">
                        <i class="fas fa-envelope mr-2"></i>Adresse email
                    </label>
                    <div class="relative">
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="{{ old('email') }}"
                               class="appearance-none block w-full px-4 py-3 pl-12 border border-white/30 rounded-xl text-white placeholder-white/50 bg-white/10 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-300 @error('email') border-red-400 @enderror" 
                               placeholder="votre@email.com">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-white/50"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-300 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Mot de passe -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-white">
                        <i class="fas fa-lock mr-2"></i>Mot de passe
                    </label>
                    <div class="relative">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required 
                               class="appearance-none block w-full px-4 py-3 pl-12 pr-12 border border-white/30 rounded-xl text-white placeholder-white/50 bg-white/10 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-300 @error('password') border-red-400 @enderror" 
                               placeholder="••••••••">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-white/50"></i>
                        </div>
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/50 hover:text-white transition-colors">
                            <i id="toggleIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-300 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Se souvenir de moi -->
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-mayelia-600 focus:ring-white border-white/30 rounded bg-white/10">
                    <label for="remember" class="ml-2 block text-sm text-white">
                        Se souvenir de moi
                    </label>
                </div>

                <!-- Bouton de connexion -->
                <div>
                    <button type="submit" 
                            id="submitBtn"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-base font-medium rounded-xl text-mayelia-600 bg-white hover:bg-mayelia-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <i id="submitIcon" class="fas fa-sign-in-alt text-mayelia-600 group-hover:text-mayelia-700 transition-colors"></i>
                        </span>
                        <span id="submitText">Se connecter</span>
                    </button>
                </div>
                
                <!-- Messages d'erreur globaux -->
                @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="bg-red-500/20 backdrop-blur-sm border border-red-400/50 text-white px-4 py-3 rounded-xl">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mt-0.5 mr-3"></i>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </form>
            
            <!-- Comptes de test (environnement de développement) -->
            @if(config('app.env') !== 'production')
            <div class="mt-8 pt-6 border-t border-white/20">
                <p class="text-xs text-center text-white/70 mb-3">
                    <i class="fas fa-flask mr-1"></i>Comptes de test disponibles
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <button type="button" 
                            onclick="fillCredentials('admin@mayelia.com', 'password')"
                            class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white px-3 py-2 rounded-lg transition-all duration-200 border border-white/20">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('agent@mayelia.com', 'password')"
                            class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white px-3 py-2 rounded-lg transition-all duration-200 border border-white/20">
                        <i class="fas fa-user mr-1"></i>Agent
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-white/70">
                © {{ date('Y') }} Mayelia Mobilité Center. Tous droits réservés.
            </p>
        </div>
    </div>
</div>

<style>
    /* Animation pour les blobs de fond */
    @keyframes blob {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        25% {
            transform: translate(20px, -50px) scale(1.1);
        }
        50% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        75% {
            transform: translate(50px, 50px) scale(1.05);
        }
    }

    .animate-blob {
        animation: blob 20s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    /* Effet de focus amélioré */
    input:focus {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }

    /* Animation de chargement du bouton */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Remplir les credentials (pour les tests)
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        
        // Animation de feedback
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        emailInput.classList.add('ring-2', 'ring-green-400');
        passwordInput.classList.add('ring-2', 'ring-green-400');
        
        setTimeout(() => {
            emailInput.classList.remove('ring-2', 'ring-green-400');
            passwordInput.classList.remove('ring-2', 'ring-green-400');
        }, 1000);
    }

    // Animation du bouton lors de la soumission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');
        
        // Désactiver le bouton
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Changer l'icône en spinner
        submitIcon.classList.remove('fa-sign-in-alt');
        submitIcon.classList.add('fa-spinner', 'animate-spin');
        
        // Changer le texte
        submitText.textContent = 'Connexion en cours...';
    });

    // Animation d'entrée
    window.addEventListener('load', function() {
        const card = document.querySelector('.backdrop-blur-xl');
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
@endsection