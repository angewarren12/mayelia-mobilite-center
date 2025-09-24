@extends('layouts.app')

@section('title', 'Mayelia Mobilité - Votre partenaire mobilité en Côte d\'Ivoire')
@section('description', 'Mayelia Mobilité vous accompagne dans vos démarches de mobilité : visa, transport VIP, assistance aéroport et formalités administratives. Service professionnel et fiable en Côte d\'Ivoire.')

@section('content')
<!-- Hero Section with Slider -->
<section class="relative py-20 lg:py-32 bg-gradient-to-br from-turquoise-50 via-sky-blue-50 to-white overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%2311B49A" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <!-- Hero Slider -->
        <div class="hero-slider-container">
            <!-- Slide 1: Visa -->
            <div class="hero-slide active">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Content -->
                    <div class="text-center lg:text-left">
                        <!-- Logo -->
                        <div class="mb-8 animate-fade-in-up">
                            <img src="{{ asset('img/logo.png') }}" 
                                 alt="Mayelia Mobilité" 
                                 class="h-16 lg:h-20 mx-auto lg:mx-0 mb-6 animate-float">
                        </div>
                        
                        <!-- Main Title -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 animate-fade-in-up animation-delay-200">
                            <span class="block text-gray-900">Votre partenaire</span>
                            <span class="block bg-gradient-to-r from-turquoise-600 to-sky-blue-600 bg-clip-text text-transparent">
                                visa
                            </span>
                            <span class="block text-gray-900">de confiance</span>
                        </h1>
                        
                        <!-- Subtitle -->
                        <p class="text-lg sm:text-xl lg:text-2xl mb-8 text-gray-600 animate-fade-in-up animation-delay-400 leading-relaxed">
                            Obtenez votre visa rapidement et en toute sécurité avec notre expertise et notre réseau de partenaires internationaux
                        </p>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center animate-fade-in-up animation-delay-600">
                            <a href="{{ route('booking.wizard') }}" 
                               class="cta-button text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-calendar-alt mr-3 text-xl"></i>
                                Demander un visa
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#services" 
                               class="cta-button-secondary text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-info-circle mr-3"></i>
                                En savoir plus
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="relative animate-fade-in-up animation-delay-400">
                        <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Visa et passeport" 
                             class="w-full h-80 lg:h-96 object-cover rounded-2xl shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-t from-turquoise-900/20 to-transparent rounded-2xl"></div>
                        
                        <!-- Floating Card -->
                        <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 animate-float">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-passport text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Visa Express</div>
                                    <div class="text-sm text-gray-600">48h de traitement</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Transport -->
            <div class="hero-slide">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Content -->
                    <div class="text-center lg:text-left">
                        <!-- Logo -->
                        <div class="mb-8">
                            <img src="{{ asset('img/logo.png') }}" 
                                 alt="Mayelia Mobilité" 
                                 class="h-16 lg:h-20 mx-auto lg:mx-0 mb-6">
                        </div>
                        
                        <!-- Main Title -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6">
                            <span class="block text-gray-900">Transport</span>
                            <span class="block bg-gradient-to-r from-sky-blue-600 to-turquoise-600 bg-clip-text text-transparent">
                                VIP
                            </span>
                            <span class="block text-gray-900">de luxe</span>
                        </h1>
                        
                        <!-- Subtitle -->
                        <p class="text-lg sm:text-xl lg:text-2xl mb-8 text-gray-600 leading-relaxed">
                            Déplacements sécurisés et confortables avec nos véhicules haut de gamme et chauffeurs professionnels
                        </p>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center">
                            <a href="{{ route('booking.wizard') }}" 
                               class="cta-button text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-car mr-3 text-xl"></i>
                                Réserver un transport
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#services" 
                               class="cta-button-secondary text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-info-circle mr-3"></i>
                                Nos véhicules
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Transport VIP" 
                             class="w-full h-80 lg:h-96 object-cover rounded-2xl shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-t from-sky-blue-900/20 to-transparent rounded-2xl"></div>
                        
                        <!-- Floating Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-4 animate-float">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-sky-blue-500 to-turquoise-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-car text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Luxe & Confort</div>
                                    <div class="text-sm text-gray-600">24h/7j disponible</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Assistance Aéroport -->
            <div class="hero-slide">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Content -->
                    <div class="text-center lg:text-left">
                        <!-- Logo -->
                        <div class="mb-8">
                            <img src="{{ asset('img/logo.png') }}" 
                                 alt="Mayelia Mobilité" 
                                 class="h-16 lg:h-20 mx-auto lg:mx-0 mb-6">
                        </div>
                        
                        <!-- Main Title -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6">
                            <span class="block text-gray-900">Assistance</span>
                            <span class="block bg-gradient-to-r from-turquoise-600 to-sky-blue-600 bg-clip-text text-transparent">
                                Aéroport
                            </span>
                            <span class="block text-gray-900">complète</span>
                        </h1>
                        
                        <!-- Subtitle -->
                        <p class="text-lg sm:text-xl lg:text-2xl mb-8 text-gray-600 leading-relaxed">
                            Accompagnement VIP pour vos formalités aéroportuaires avec accès fast track et assistance bagages
                        </p>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center">
                            <a href="{{ route('booking.wizard') }}" 
                               class="cta-button text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-plane mr-3 text-xl"></i>
                                Assistance aéroport
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#services" 
                               class="cta-button-secondary text-lg px-8 py-4 inline-flex items-center group">
                                <i class="fas fa-info-circle mr-3"></i>
                                Nos services
                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Aéroport international" 
                             class="w-full h-80 lg:h-96 object-cover rounded-2xl shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-t from-turquoise-900/20 to-transparent rounded-2xl"></div>
                        
                        <!-- Floating Card -->
                        <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 animate-float">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-turquoise-500 to-sky-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-plane text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Fast Track</div>
                                    <div class="text-sm text-gray-600">Passage prioritaire</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slider Navigation -->
        <div class="flex justify-center mt-12 space-x-3">
            <button class="slider-dot active w-3 h-3 rounded-full bg-turquoise-600 transition-all duration-300"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-gray-300 hover:bg-turquoise-400 transition-all duration-300"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-gray-300 hover:bg-turquoise-400 transition-all duration-300"></button>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="flex flex-col items-center text-turquoise-600">
            <span class="text-sm mb-2 font-medium">Découvrir</span>
            <i class="fas fa-chevron-down text-xl"></i>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-24 bg-gradient-to-br from-gray-50 to-blue-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23000000" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-20">
            <div class="inline-block px-6 py-2 bg-gradient-to-r from-turquoise-100 to-sky-blue-100 text-turquoise-600 rounded-full text-sm font-semibold mb-4 animate-fade-in-up">
                Nos Services
            </div>
            <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6 animate-fade-in-up animation-delay-200">
                Solutions <span class="bg-gradient-to-r from-turquoise-600 to-sky-blue-600 bg-clip-text text-transparent">complètes</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-400">
                Des services professionnels pour tous vos besoins de mobilité, 
                de la demande de visa à l'assistance aéroportuaire
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['services'] as $index => $service)
            <div class="service-card group cursor-pointer animate-fade-in-up animation-delay-{{ ($index + 1) * 200 }}">
                <!-- Service Image -->
                <div class="relative h-48 mb-6 overflow-hidden rounded-xl">
                    @if($index == 0)
                        <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Service Visa" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @elseif($index == 1)
                        <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Service Transport" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @elseif($index == 2)
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Service Aéroport" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Service Administrative" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    
                    <!-- Colored Icon Overlay -->
                    <div class="absolute top-4 right-4 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg
                        @if($service['color'] == 'turquoise') bg-gradient-to-r from-turquoise-500 to-turquoise-600
                        @else bg-gradient-to-r from-sky-blue-500 to-sky-blue-600 @endif">
                        <i class="{{ $service['icon'] }} text-white text-xl"></i>
                    </div>
                </div>

                <div class="p-6 text-center relative">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-{{ $service['color'] }}-600 transition-colors">
                        {{ $service['title'] }}
                    </h3>
                    
                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $service['description'] }}</p>
                    
                    <!-- Features with animated checkmarks -->
                    <div class="space-y-3 mb-8">
                        @foreach($service['features'] as $feature)
                        <div class="flex items-center justify-center text-sm text-gray-600">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center mr-3 group-hover:bg-green-500 transition-colors">
                                <i class="fas fa-check text-green-600 text-xs group-hover:text-white transition-colors"></i>
                            </div>
                            {{ $feature }}
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- CTA Button -->
                    <a href="{{ $service['link'] }}" 
                       class="inline-flex items-center text-{{ $service['color'] }}-600 font-semibold hover:text-{{ $service['color'] }}-700 transition-all duration-300 group-hover:translate-x-1">
                        En savoir plus
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Pourquoi choisir Mayelia Mobilité ?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Plus qu'un simple prestataire, nous sommes votre partenaire de confiance 
                pour tous vos projets de mobilité
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['advantages'] as $index => $advantage)
            <div class="text-center group animate-fade-in-up animation-delay-{{ ($index + 1) * 200 }}">
                <!-- Advantage Image -->
                <div class="relative h-48 mb-6 overflow-hidden rounded-xl">
                    @if($index == 0)
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Rapidité" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @elseif($index == 1)
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Sécurité" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @elseif($index == 2)
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Expertise" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <img src="https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Couverture" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    
                    <!-- Icon Overlay -->
                    <div class="absolute top-4 right-4 w-12 h-12 rounded-xl bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-lg group-hover:bg-white transition-all duration-300">
                        <i class="{{ $advantage['icon'] }} text-turquoise-600 text-xl group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-turquoise-600 transition-colors duration-300">{{ $advantage['title'] }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $advantage['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-gradient-to-r from-turquoise-600 to-sky-blue-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.3"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['stats'] as $index => $stat)
            <div class="text-center text-white animate-fade-in-up animation-delay-{{ ($index + 1) * 200 }}">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg group-hover:bg-white/30 transition-all duration-300">
                    <i class="{{ $stat['icon'] }} text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="text-4xl sm:text-5xl font-bold mb-2 counter stats-counter" data-target="{{ $stat['number'] }}">
                    0
                </div>
                <div class="text-lg text-white/90">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Ce que disent nos clients
            </h2>
            <p class="text-xl text-gray-600">
                Des milliers de clients nous font confiance pour leurs projets de mobilité
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($data['testimonials'] as $testimonial)
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-yellow-400 {{ $i <= $testimonial['rating'] ? '' : 'text-gray-300' }}"></i>
                    @endfor
                </div>
                <p class="text-gray-600 mb-6 italic">"{{ $testimonial['comment'] }}"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                        <div class="text-sm text-gray-500">{{ $testimonial['service'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Alerts Section -->
@if(!empty($data['alerts']))
<section class="py-16 bg-orange-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Actualités & Alertes
            </h2>
            <p class="text-xl text-gray-600">
                Restez informé des dernières actualités et changements réglementaires
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($data['alerts'] as $alert)
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-{{ $alert['type'] === 'warning' ? 'orange' : 'blue' }}-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-{{ $alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle' }} text-{{ $alert['type'] === 'warning' ? 'orange' : 'blue' }}-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $alert['title'] }}</h3>
                        <p class="text-gray-600 mb-2">{{ $alert['message'] }}</p>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($alert['date'])->locale('fr')->isoFormat('DD MMMM YYYY') }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-turquoise-600 via-sky-blue-600 to-turquoise-700 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.3"><circle cx="30" cy="30" r="4"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6 animate-fade-in-up">
            Prêt à commencer votre projet de mobilité ?
        </h2>
        <p class="text-xl text-white/90 mb-8 animate-fade-in-up animation-delay-200 max-w-2xl mx-auto">
            Contactez-nous dès aujourd'hui pour un accompagnement personnalisé et professionnel
        </p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center animate-fade-in-up animation-delay-400">
            <a href="{{ route('booking.wizard') }}" 
               class="cta-button text-lg px-10 py-4 inline-flex items-center justify-center group">
                <i class="fas fa-calendar-alt mr-3 text-xl"></i>
                Prendre un rendez-vous
                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="/contact" 
               class="cta-button-secondary text-lg px-10 py-4 inline-flex items-center justify-center group">
                <i class="fas fa-envelope mr-3"></i>
                Nous contacter
                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.8s ease-out forwards;
        opacity: 0;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
    }

    .counter {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Hero Slider
    class HeroSlider {
        constructor() {
            this.slides = document.querySelectorAll('.hero-slide');
            this.dots = document.querySelectorAll('.slider-dot');
            this.currentSlide = 0;
            this.slideInterval = null;
            this.slideDuration = 5000; // 5 seconds per slide
            
            this.init();
        }
        
        init() {
            if (this.slides.length === 0) return;
            
            // Add click events to dots
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    this.goToSlide(index);
                });
            });
            
            // Start auto-slide
            this.startAutoSlide();
            
            // Pause on hover
            const sliderContainer = document.querySelector('.hero-slider-container');
            if (sliderContainer) {
                sliderContainer.addEventListener('mouseenter', () => this.stopAutoSlide());
                sliderContainer.addEventListener('mouseleave', () => this.startAutoSlide());
            }
        }
        
        goToSlide(index) {
            // Remove active class from current slide and dot
            this.slides[this.currentSlide].classList.remove('active');
            this.dots[this.currentSlide].classList.remove('active');
            
            // Update current slide
            this.currentSlide = index;
            
            // Add active class to new slide and dot
            this.slides[this.currentSlide].classList.add('active');
            this.dots[this.currentSlide].classList.add('active');
            
            // Reset auto-slide timer
            this.startAutoSlide();
        }
        
        nextSlide() {
            const nextIndex = (this.currentSlide + 1) % this.slides.length;
            this.goToSlide(nextIndex);
        }
        
        startAutoSlide() {
            this.stopAutoSlide();
            this.slideInterval = setInterval(() => {
                this.nextSlide();
            }, this.slideDuration);
        }
        
        stopAutoSlide() {
            if (this.slideInterval) {
                clearInterval(this.slideInterval);
                this.slideInterval = null;
            }
        }
    }

    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        });
    }

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                if (entry.target.classList.contains('counter')) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            }
        });
    }, observerOptions);

    // Smooth scrolling for anchor links
    function initSmoothScrolling() {
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
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize hero slider
        new HeroSlider();
        
        // Initialize animations
        const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-slide-in-left, .animate-slide-in-right, .animate-scale-in, .counter');
        animatedElements.forEach(element => {
            observer.observe(element);
        });
        
        // Initialize smooth scrolling
        initSmoothScrolling();
        
        // Add loading animation
        document.body.classList.add('loaded');
    });

    // Add CSS for animate-in class and slider
    const style = document.createElement('style');
    style.textContent = `
        .hero-slide {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        .hero-slide.active {
            opacity: 1;
            transform: translateX(0);
            position: relative;
        }
        
        .hero-slider-container {
            position: relative;
            overflow: hidden;
        }
        
        .slider-dot.active {
            background-color: #11B49A !important;
            transform: scale(1.2);
        }
        
        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        .loaded {
            overflow-x: hidden;
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
