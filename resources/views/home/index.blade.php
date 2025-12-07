@extends('layouts.app')

@section('title', 'Mayelia Mobilité - Votre partenaire mobilité en Côte d\'Ivoire')
@section('description', 'Mayelia Mobilité vous accompagne dans vos démarches de mobilité : visa, transport VIP, assistance aéroport et formalités administratives. Service professionnel et fiable en Côte d\'Ivoire.')

@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
    /* Custom animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(17, 180, 154, 0.3); }
        50% { box-shadow: 0 0 40px rgba(17, 180, 154, 0.6); }
    }
    
    @keyframes slideInLeft {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeInUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    .animate-slide-in-left { animation: slideInLeft 0.8s ease-out; }
    .animate-slide-in-right { animation: slideInRight 0.8s ease-out; }
    .animate-fade-in-up { animation: fadeInUp 0.8s ease-out; }
    
    /* Hero section styles */
    .hero-slide {
        position: relative;
        height: 100vh;
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(17, 180, 154, 0.9), rgba(29, 160, 219, 0.9));
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    /* Service cards */
    .service-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(17, 180, 154, 0.1);
    }
    
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(17, 180, 154, 0.2);
        border-color: rgba(17, 180, 154, 0.3);
    }
    
    /* Statistics */
    .stat-item {
        position: relative;
        overflow: hidden;
    }
    
    .stat-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .stat-item:hover::before {
        left: 100%;
    }
    
    /* Testimonial cards */
    .testimonial-card {
        position: relative;
        overflow: hidden;
    }
    
    .testimonial-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(17, 180, 154, 0.05), rgba(29, 160, 219, 0.05));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .testimonial-card:hover::after {
        opacity: 1;
    }
    
    /* Process steps */
    .process-step {
        position: relative;
    }
    
    .process-step::before {
        content: '';
        position: absolute;
        top: 50%;
        right: -50px;
        width: 100px;
        height: 2px;
        background: linear-gradient(90deg, #11B49A, #1DA0DB);
        transform: translateY(-50%);
    }
    
    .process-step:last-child::before {
        display: none;
    }
    
    /* CTA section */
    .cta-section {
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');
        animation: float 20s linear infinite;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .hero-slide {
            background-attachment: scroll;
        }
        
        .process-step::before {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section with Enhanced Slider -->
<section class="hero-section">
    <x-hero-slider />
</section>

<!-- Services Section -->
<section id="services" class="py-24 bg-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%2311B49A" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20" data-aos="fade-up">
            <div class="inline-block px-8 py-3 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 text-white rounded-full text-sm font-bold uppercase tracking-wider mb-6 shadow-lg" style="color: white !important;">
                NOS SERVICES
            </div>
            <h2 class="text-5xl sm:text-6xl font-black text-gray-900 mb-8" style="color: #1f2937 !important;">
                Solutions de <span class="text-turquoise-600" style="color: #11B49A !important;">mobilité</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed" style="color: #4b5563 !important;">
                Découvrez notre gamme complète de services pour faciliter vos déplacements et démarches administratives
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['services'] as $index => $service)
            <div class="group relative" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <!-- Card -->
                <div class="service-card bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 relative overflow-hidden">
                    <!-- Background Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-turquoise-50/50 to-sky-blue-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Icon -->
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-white border-2 {{ $service['color'] == 'turquoise' ? 'border-turquoise-500' : 'border-sky-blue-500' }} rounded-3xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="{{ $service['icon'] }} text-3xl {{ $service['color'] == 'turquoise' ? 'text-turquoise-500' : 'text-sky-blue-500' }}" style="color: {{ $service['color'] == 'turquoise' ? '#11B49A' : '#1DA0DB' }} !important;"></i>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-black text-gray-900 mb-4 group-hover:text-turquoise-600 transition-colors duration-300" style="color: #1f2937 !important;">{{ $service['title'] }}</h3>
                        
                        <!-- Description -->
                        <p class="text-gray-600 mb-8 leading-relaxed text-lg" style="color: #4b5563 !important;">{{ $service['description'] }}</p>
                        
                        <!-- Features -->
                        <ul class="space-y-3 mb-10">
                            @foreach($service['features'] as $feature)
                            <li class="flex items-center text-gray-600" style="color: #4b5563 !important;">
                                <div class="w-6 h-6 bg-white border-2 border-turquoise-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-check text-xs" style="color: #11B49A !important;"></i>
                                </div>
                                <span class="font-medium" style="color: #4b5563 !important;">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        
                        <!-- CTA Button -->
                        <a href="{{ $service['link'] }}" class="w-full bg-white border-2 {{ $service['color'] == 'turquoise' ? 'border-turquoise-500 hover:bg-turquoise-50' : 'border-sky-blue-500 hover:bg-sky-blue-50' }} font-bold py-4 px-6 rounded-2xl transition-all duration-300 text-center block transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center {{ $service['color'] == 'turquoise' ? 'text-turquoise-500' : 'text-sky-blue-500' }}" style="color: {{ $service['color'] == 'turquoise' ? '#11B49A' : '#1DA0DB' }} !important;">
                                Découvrir
                                <i class="fas fa-arrow-right ml-2" style="color: {{ $service['color'] == 'turquoise' ? '#11B49A' : '#1DA0DB' }} !important;"></i>
                            </span>
                        </a>
                    </div>
                    
                    <!-- Decorative Elements -->
                    <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-r from-turquoise-100 to-sky-blue-100 rounded-full opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 w-8 h-8 bg-gradient-to-r from-sky-blue-100 to-turquoise-100 rounded-full opacity-30 group-hover:opacity-60 transition-opacity duration-300"></div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Call to Action -->
        <div class="text-center mt-16" data-aos="fade-up" data-aos-delay="400">
            <div class="inline-flex items-center space-x-4 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 text-white px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-phone text-2xl"></i>
                <div class="text-left">
                    <div class="text-sm font-medium opacity-90">Besoin d'aide ?</div>
                    <div class="text-lg font-bold">+225 XX XX XX XX XX</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <div class="inline-block px-6 py-2 bg-gradient-to-r from-turquoise-100 to-sky-blue-100 text-turquoise-600 rounded-full text-sm font-semibold mb-4">
                    À PROPOS
                </div>
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-6">
                    Votre partenaire <span class="bg-gradient-to-r from-turquoise-600 to-sky-blue-600 bg-clip-text text-transparent">mobilité</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Mayelia Mobilité est votre partenaire de confiance pour tous vos besoins de mobilité. Avec plus de 5 ans d'expérience, nous vous accompagnons dans vos démarches administratives et vos déplacements.
                </p>
                
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-award text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Expertise reconnue</h3>
                            <p class="text-gray-600">Plus de 5 ans d'expérience</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-sky-blue-500 to-turquoise-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Équipe qualifiée</h3>
                            <p class="text-gray-600">Professionnels certifiés</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div data-aos="fade-left">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Équipe Mayelia Mobilité" 
                         class="rounded-3xl shadow-2xl">
                    <div class="absolute -bottom-6 -right-6 bg-white rounded-2xl p-6 shadow-xl">
                        <div class="text-center">
                            <div class="text-3xl font-black text-turquoise-600 mb-2" data-countup="10000">0</div>
                            <div class="text-sm font-bold text-gray-600 uppercase tracking-wider">Clients satisfaits</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-24 bg-gradient-to-br from-sky-blue-600 via-sky-blue-500 to-turquoise-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20" data-aos="fade-up">
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-6" style="color: white !important;">
                Nos <span class="text-white" style="color: white !important;">chiffres</span>
            </h2>
            <p class="text-xl text-white/90 max-w-3xl mx-auto" style="color: rgba(255,255,255,0.9) !important;">
                Des résultats qui témoignent de notre expertise et de notre engagement
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['stats'] as $index => $stat)
            <div class="stat-item text-center bg-white/20 backdrop-blur-sm rounded-3xl p-8 border border-white/30" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="w-20 h-20 bg-white/30 backdrop-blur-sm rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white/40">
                    <i class="{{ $stat['icon'] }} text-white text-3xl" style="color: white !important;"></i>
                </div>
                
                <div class="text-6xl font-black text-white mb-4" style="color: white !important;">
                    <span data-countup="{{ $stat['number'] }}" class="counter-number">0</span>{{ $stat['suffix'] }}
                </div>
                
                <div class="text-xl font-bold text-white/90" style="color: rgba(255,255,255,0.9) !important;">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-24 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20" data-aos="fade-up">
            <div class="inline-block px-6 py-2 bg-gradient-to-r from-turquoise-100 to-sky-blue-100 text-turquoise-600 rounded-full text-sm font-semibold mb-4">
                TÉMOIGNAGES
            </div>
            <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-6" style="color: #1f2937 !important;">
                Ce que disent nos <span class="text-turquoise-600" style="color: #11B49A !important;">clients</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto" style="color: #4b5563 !important;">
                Découvrez les expériences de nos clients satisfaits
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($data['testimonials'] as $index => $testimonial)
            <div class="testimonial-card bg-white rounded-2xl p-8 shadow-lg" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="flex items-center mb-6">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-yellow-400 text-lg"></i>
                    @endfor
                </div>
                
                <blockquote class="text-gray-700 mb-6 leading-relaxed">
                    "{{ $testimonial['comment'] }}"
                </blockquote>
                
                <div class="flex items-center">
                    <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                        <div class="text-sm text-turquoise-600">{{ $testimonial['service'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20" data-aos="fade-up">
            <div class="inline-block px-6 py-2 bg-gradient-to-r from-turquoise-100 to-sky-blue-100 text-turquoise-600 rounded-full text-sm font-semibold mb-4">
                NOTRE PROCESSUS
            </div>
            <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-6" style="color: #1f2937 !important;">
                Comment ça <span class="text-turquoise-600" style="color: #11B49A !important;">fonctionne</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto" style="color: #4b5563 !important;">
                Un processus simple et efficace en 4 étapes
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="process-step text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="w-20 h-20 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">1</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Demande</h3>
                <p class="text-gray-600">Remplissez notre formulaire en ligne</p>
            </div>
            
            <div class="process-step text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-gradient-to-r from-sky-blue-500 to-turquoise-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">2</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Validation</h3>
                <p class="text-gray-600">Vérification de vos documents</p>
            </div>
            
            <div class="process-step text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">3</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Traitement</h3>
                <p class="text-gray-600">Prise en charge de votre dossier</p>
            </div>
            
            <div class="process-step text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 bg-gradient-to-r from-sky-blue-500 to-turquoise-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">4</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Livraison</h3>
                <p class="text-gray-600">Récupération de vos documents</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-24 bg-gradient-to-r from-turquoise-600 to-sky-blue-600 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div data-aos="fade-up">
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-6" style="color: white !important;">
                Prêt à commencer votre <span class="text-white" style="color: white !important;">voyage</span> ?
            </h2>
            <p class="text-xl text-white/90 mb-12 max-w-3xl mx-auto" style="color: rgba(255,255,255,0.9) !important;">
                Contactez-nous dès maintenant pour obtenir un devis personnalisé et commencer vos démarches
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ route('booking.wizard') }}" class="bg-white text-turquoise-600 font-bold px-10 py-5 rounded-2xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl">
                    <i class="fas fa-calendar-alt mr-4"></i>
                    Prendre rendez-vous
                </a>
                <a href="/contact" class="border-2 border-white text-white font-bold px-10 py-5 rounded-2xl hover:bg-white hover:text-turquoise-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-phone mr-4"></i>
                    Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Initialize Enhanced Swiper
    const swiper = new Swiper('.hero-swiper', {
        loop: true,
        autoplay: {
            delay: 8000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 1500,
        on: {
            slideChange: function() {
                // Reset progress bar
                const progressBar = document.querySelector('.swiper-progress-bar');
                if (progressBar) {
                    progressBar.style.width = '0%';
                    setTimeout(() => {
                        progressBar.style.width = '100%';
                    }, 100);
                }
                
                // Ensure content is visible on slide change
                const activeSlide = document.querySelector('.swiper-slide-active .hero-content');
                if (activeSlide) {
                    activeSlide.style.opacity = '1';
                    activeSlide.style.visibility = 'visible';
                }
            },
            init: function() {
                // Start progress bar
                const progressBar = document.querySelector('.swiper-progress-bar');
                if (progressBar) {
                    progressBar.style.width = '100%';
                }
                
                // Ensure first slide content is visible
                const firstSlide = document.querySelector('.swiper-slide-active .hero-content');
                if (firstSlide) {
                    firstSlide.style.opacity = '1';
                    firstSlide.style.visibility = 'visible';
                }
            }
        }
    });

    // Progress bar animation
    let progressInterval;
    function startProgress() {
        const progressBar = document.querySelector('.swiper-progress-bar');
        if (progressBar) {
            progressBar.style.width = '0%';
            let width = 0;
            progressInterval = setInterval(() => {
                width += 100 / (8000 / 50); // 8000ms / 50ms intervals
                progressBar.style.width = width + '%';
                if (width >= 100) {
                    clearInterval(progressInterval);
                }
            }, 50);
        }
    }

    // Start progress on slide change
    swiper.on('slideChange', () => {
        clearInterval(progressInterval);
        startProgress();
    });

    // Start initial progress
    startProgress();

    // Initialize CountUp with enhanced animation
    const countUpElements = document.querySelectorAll('[data-countup]');
    const countUpObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const target = parseInt(element.getAttribute('data-countup'));
                
                // Add animation class
                element.classList.add('animate-pulse');
                
                const countUp = new CountUp(element, target, {
                    duration: 3,
                    useEasing: true,
                    useGrouping: true,
                    separator: ' ',
                    decimal: ',',
                    prefix: '',
                    suffix: '',
                    onComplete: () => {
                        element.classList.remove('animate-pulse');
                        element.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            element.style.transform = 'scale(1)';
                        }, 200);
                    }
                });
                
                if (!countUp.error) {
                    countUp.start();
                } else {
                    console.error(countUp.error);
                }
                
                countUpObserver.unobserve(element);
            }
        });
    }, {
        threshold: 0.3
    });

    countUpElements.forEach(element => {
        countUpObserver.observe(element);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Parallax effect for floating elements
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.animate-float');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
});
</script>
@endpush
@endsection