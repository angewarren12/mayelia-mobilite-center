@props(['slides' => []])

<div class="hero-slider-container relative h-screen overflow-hidden">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1: Pôle Mobilité -->
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center h-full">
                            <!-- Left Content -->
                            <div class="text-left relative z-20 flex flex-col justify-center h-full py-20 lg:py-20 py-10">
                                <div class="inline-block px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold uppercase tracking-wider mb-8">
                                    Pôle Mobilité
                                </div>
                                
                                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black text-white mb-6 lg:mb-8 leading-tight">
                                    <span class="block">MOBILITÉ</span>
                                    <span class="block text-white/90">DURABLE</span>
                                </h1>
                                
                                <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white/90 mb-8 lg:mb-12 leading-relaxed">
                                    Solutions de transport modernes et écologiques
                                </p>
                                
                                <div class="space-y-6 mb-12">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-motorcycle text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Motos électriques</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-bus text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Transport urbain</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-truck text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Livraison & logistique</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('booking.wizard') }}" class="inline-flex items-center px-8 py-4 lg:px-12 lg:py-6 bg-white text-turquoise-600 font-bold text-lg lg:text-xl rounded-2xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                                    <i class="fas fa-calendar-alt mr-4 text-xl"></i>
                                    Découvrir nos solutions
                                    <i class="fas fa-arrow-right ml-4 text-xl"></i>
                                </a>
                            </div>
                            
                            <!-- Right Content -->
                            <div class="relative flex items-center justify-center h-full hidden lg:flex">
                                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-8 shadow-2xl">
                                    <div class="text-center mb-8">
                                        <div class="w-20 h-20 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-motorcycle text-white text-3xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Mobilité Durable</h3>
                                        <p class="text-gray-600">Solutions écologiques</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-turquoise-600 mb-2" data-countup="100">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">% Écologique</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-sky-blue-600 mb-2" data-countup="24">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">h/24 Disponible</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Assistance Visa -->
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center h-full">
                            <!-- Left Content -->
                            <div class="text-left relative z-20 flex flex-col justify-center h-full py-20 lg:py-20 py-10">
                                <div class="inline-block px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold uppercase tracking-wider mb-8">
                                    Pôle Identification
                                </div>
                                
                                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black text-white mb-6 lg:mb-8 leading-tight">
                                    <span class="block">DOCUMENTS</span>
                                    <span class="block text-white/90">SÉCURISÉS</span>
                                </h1>
                                
                                <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white/90 mb-8 lg:mb-12 leading-relaxed">
                                    Gestion administrative et production de documents officiels
                                </p>
                                
                                <div class="space-y-6 mb-12">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">CNI & Cartes de résident</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-passport text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Visas & Passeports</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-fingerprint text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Biométrie & Identification</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('booking.wizard') }}" class="inline-flex items-center px-8 py-4 lg:px-12 lg:py-6 bg-white text-turquoise-600 font-bold text-lg lg:text-xl rounded-2xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                                    <i class="fas fa-calendar-alt mr-4 text-xl"></i>
                                    Demander un document
                                    <i class="fas fa-arrow-right ml-4 text-xl"></i>
                                </a>
                            </div>
                            
                            <!-- Right Content -->
                            <div class="relative" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="300">
                                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-8 shadow-2xl animate-float">
                                    <div class="text-center mb-8">
                                        <div class="w-20 h-20 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-passport text-white text-3xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Documents Sécurisés</h3>
                                        <p class="text-gray-600">Service officiel</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-turquoise-600 mb-2" data-countup="98">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Taux de réussite</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-sky-blue-600 mb-2" data-countup="15">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Jours ouvrables</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Livraison Moto Électrique -->
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center h-full">
                            <!-- Left Content -->
                            <div class="text-left relative z-20 flex flex-col justify-center h-full py-20 lg:py-20 py-10">
                                <div class="inline-block px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold uppercase tracking-wider mb-8">
                                    Pôle Aménagement
                                </div>
                                
                                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black text-white mb-6 lg:mb-8 leading-tight">
                                    <span class="block">ESPACES</span>
                                    <span class="block text-white/90">MODERNES</span>
                                </h1>
                                
                                <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white/90 mb-8 lg:mb-12 leading-relaxed">
                                    Aménagement et gestion d'espaces publics et commerciaux
                                </p>
                                
                                <div class="space-y-6 mb-12">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-motorcycle text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Marchés modernes</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-parking text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Gestion de parkings</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-city text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Aménagement urbain</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('booking.wizard') }}" class="inline-flex items-center px-8 py-4 lg:px-12 lg:py-6 bg-white text-sky-blue-600 font-bold text-lg lg:text-xl rounded-2xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                                    <i class="fas fa-calendar-alt mr-4 text-xl"></i>
                                    Découvrir nos solutions
                                    <i class="fas fa-arrow-right ml-4 text-xl"></i>
                                </a>
                            </div>
                            
                            <!-- Right Content -->
                            <div class="relative" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="300">
                                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-8 shadow-2xl animate-float">
                                    <div class="text-center mb-8">
                                        <div class="w-20 h-20 bg-gradient-to-r from-sky-blue-500 to-turquoise-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-motorcycle text-white text-3xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Espaces Modernes</h3>
                                        <p class="text-gray-600">Gestion urbaine</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-sky-blue-600 mb-2" data-countup="50">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Espaces gérés</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-turquoise-600 mb-2" data-countup="1000">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Commerçants</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Transport VIP -->
            <div class="swiper-slide hero-slide" style="background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center h-full">
                            <!-- Left Content -->
                            <div class="text-left relative z-20 flex flex-col justify-center h-full py-20 lg:py-20 py-10">
                                <div class="inline-block px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold uppercase tracking-wider mb-8">
                                    Transport Premium
                                </div>
                                
                                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black text-white mb-6 lg:mb-8 leading-tight">
                                    <span class="block">TRANSPORT</span>
                                    <span class="block text-white/90">VIP</span>
                                </h1>
                                
                                <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white/90 mb-8 lg:mb-12 leading-relaxed">
                                    confort et sécurité garantis
                                </p>
                                
                                <div class="space-y-6 mb-12">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-car text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Véhicules de luxe</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-tie text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Chauffeur professionnel</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-white text-xl"></i>
                                        </div>
                                        <span class="text-lg sm:text-xl lg:text-2xl font-bold text-white">Ponctualité garantie</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('booking.wizard') }}" class="inline-flex items-center px-8 py-4 lg:px-12 lg:py-6 bg-white text-turquoise-700 font-bold text-lg lg:text-xl rounded-2xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                                    <i class="fas fa-calendar-alt mr-4 text-xl"></i>
                                    Réserver un transport
                                    <i class="fas fa-arrow-right ml-4 text-xl"></i>
                                </a>
                            </div>
                            
                            <!-- Right Content -->
                            <div class="relative" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="300">
                                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-8 shadow-2xl animate-float">
                                    <div class="text-center mb-8">
                                        <div class="w-20 h-20 bg-gradient-to-r from-turquoise-600 to-sky-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-car text-white text-3xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900">Transport VIP</h3>
                                        <p class="text-gray-600">Service premium</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-turquoise-700 mb-2" data-countup="100">0</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Satisfaction</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-4xl font-black text-sky-blue-600 mb-2">5★</div>
                                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Qualité</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="swiper-button-next text-white hover:text-turquoise-300 transition-colors"></div>
        <div class="swiper-button-prev text-white hover:text-turquoise-300 transition-colors"></div>
        <div class="swiper-pagination"></div>
        
        <!-- Progress Bar -->
        <div class="swiper-progress">
            <div class="swiper-progress-bar"></div>
        </div>
    </div>
</div>

<style>
.hero-slider-container {
    position: relative;
    height: 100vh;
    overflow: hidden;
}

.hero-slide {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(17, 180, 154, 0.5), rgba(29, 160, 219, 0.5));
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 10;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.5s ease-in-out;
}

/* Swiper Customization */
.hero-swiper .swiper-button-next,
.hero-swiper .swiper-button-prev {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.hero-swiper .swiper-button-next:hover,
.hero-swiper .swiper-button-prev:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.hero-swiper .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.5);
    width: 12px;
    height: 12px;
    transition: all 0.3s ease;
}

.hero-swiper .swiper-pagination-bullet-active {
    background: white;
    transform: scale(1.2);
}

/* Progress Bar */
.swiper-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    z-index: 10;
}

.swiper-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #11B49A, #1DA0DB);
    width: 0%;
    transition: width 0.3s ease;
}

/* Enhanced Animations */
@keyframes slideInLeft {
    from { 
        transform: translateX(-100px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

@keyframes slideInRight {
    from { 
        transform: translateX(100px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

@keyframes fadeInUp {
    from { 
        transform: translateY(50px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
    }
    50% { 
        transform: translateY(-20px) rotate(2deg); 
    }
}

.animate-slide-in-left { 
    animation: slideInLeft 1s ease-out; 
}

.animate-slide-in-right { 
    animation: slideInRight 1s ease-out; 
}

.animate-fade-in-up { 
    animation: fadeInUp 0.8s ease-out; 
}

.animate-float { 
    animation: float 6s ease-in-out infinite; 
}

.animation-delay-200 { 
    animation-delay: 0.2s; 
}

.animation-delay-300 { 
    animation-delay: 0.3s; 
}

.animation-delay-400 { 
    animation-delay: 0.4s; 
}

.animation-delay-500 { 
    animation-delay: 0.5s; 
}

.animation-delay-600 { 
    animation-delay: 0.6s; 
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-slide {
        background-attachment: scroll;
    }
    
    .hero-swiper .swiper-button-next,
    .hero-swiper .swiper-button-prev {
        width: 50px;
        height: 50px;
    }
}

/* Force background image display */
.hero-slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 0;
}

.hero-slide[style*="background-image"]::before {
    background-image: inherit;
}

/* Ensure content stays visible */
.hero-slide .hero-content {
    opacity: 1 !important;
    visibility: visible !important;
}

.hero-slide .hero-content * {
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

