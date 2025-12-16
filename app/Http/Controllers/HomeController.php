<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\DocumentRequis;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil institutionnelle
     */
    public function index()
    {
        // Récupérer les services depuis la base de données
        $services = Service::actif()
            ->withRelations()
            ->orderBy('id', 'asc')
            ->take(4)
            ->get()
            ->map(function ($service) {
                // Mapper les services avec leurs couleurs et icônes
                $serviceConfig = $this->getServiceConfig($service->nom);
                
                return [
                    'id' => $service->id,
                    'icon' => $serviceConfig['icon'],
                    'title' => $service->nom,
                    'description' => $service->description,
                    'features' => $service->formules->pluck('nom')->take(4)->toArray(),
                    'link' => "/services/{$service->id}",
                    'color' => $serviceConfig['color'],
                    'documents_requis' => $service->documentsRequis->count()
                ];
            });

        // Debug: Afficher les services récupérés
        \Log::info('Services récupérés depuis la BD:', ['count' => $services->count(), 'services' => $services->toArray()]);

        // Si pas assez de services en base, utiliser les services par défaut
        if ($services->count() < 4) {
            $defaultServices = $this->getDefaultServices();
            $services = $services->merge($defaultServices->take(4 - $services->count()));
            \Log::info('Services par défaut ajoutés:', ['default_count' => $defaultServices->count()]);
        }

        // Données pour la page d'accueil
        $data = [
            'services' => $services,
            'stats' => [
                [
                    'number' => '10000',
                    'label' => 'Clients accompagnés',
                    'suffix' => '+',
                    'icon' => 'fas fa-users'
                ],
                [
                    'number' => '98',
                    'label' => 'Taux de satisfaction',
                    'suffix' => '%',
                    'icon' => 'fas fa-star'
                ],
                [
                    'number' => '5',
                    'label' => 'Années d\'expérience',
                    'suffix' => '+',
                    'icon' => 'fas fa-calendar-alt'
                ],
                [
                    'number' => '24',
                    'label' => 'Support disponible',
                    'suffix' => '/7',
                    'icon' => 'fas fa-headset'
                ]
            ],
            'advantages' => [
                [
                    'icon' => 'fas fa-bolt',
                    'title' => 'Rapidité',
                    'description' => 'Délais optimisés grâce à notre expertise et notre réseau de partenaires'
                ],
                [
                    'icon' => 'fas fa-shield-alt',
                    'title' => 'Sécurité',
                    'description' => 'Vos données et documents sont protégés avec les plus hauts standards de sécurité'
                ],
                [
                    'icon' => 'fas fa-user-graduate',
                    'title' => 'Expertise',
                    'description' => 'Une équipe qualifiée et formée aux dernières réglementations'
                ],
                [
                    'icon' => 'fas fa-globe',
                    'title' => 'Couverture',
                    'description' => 'Réseau international avec des partenaires dans les principales destinations'
                ]
            ],
            'testimonials' => [
                [
                    'name' => 'Marie Kouassi',
                    'service' => 'Visa touristique',
                    'rating' => 5,
                    'comment' => 'Service exceptionnel ! Mon visa a été traité en 48h. Je recommande vivement Mayelia Mobilité.',
                    'avatar' => '/images/avatars/marie-kouassi.jpg'
                ],
                [
                    'name' => 'Jean-Baptiste Traoré',
                    'service' => 'Transport VIP',
                    'rating' => 5,
                    'comment' => 'Chauffeur professionnel et véhicule de qualité. Parfait pour mes déplacements d\'affaires.',
                    'avatar' => '/images/avatars/jean-baptiste.jpg'
                ],
                [
                    'name' => 'Fatou Diallo',
                    'service' => 'Assistance Aéroport',
                    'rating' => 5,
                    'comment' => 'Accueil chaleureux et assistance complète. Plus de stress pour mes voyages !',
                    'avatar' => '/images/avatars/fatou-diallo.jpg'
                ]
            ],
            'alerts' => [
                [
                    'type' => 'info',
                    'title' => 'Nouveaux délais de visa',
                    'message' => 'Les délais pour les visas Schengen ont été réduits à 15 jours ouvrés.',
                    'date' => '2024-01-15'
                ],
                [
                    'type' => 'warning',
                    'title' => 'Fermeture temporaire',
                    'message' => 'L\'ambassade du Canada sera fermée du 20 au 25 janvier pour travaux.',
                    'date' => '2024-01-10'
                ]
            ]
        ];

        return view('home.index', compact('data'));
    }

    /**
     * Configuration des services avec icônes et couleurs
     */
    private function getServiceConfig($serviceName)
    {
        $configs = [
            // Services de visa et documents
            'visa' => [
                'icon' => 'fas fa-passport',
                'color' => 'turquoise'
            ],
            'carte de résident' => [
                'icon' => 'fas fa-id-card',
                'color' => 'sky-blue'
            ],
            'CNI' => [
                'icon' => 'fas fa-id-badge',
                'color' => 'turquoise'
            ],
            'religieux' => [
                'icon' => 'fas fa-mosque',
                'color' => 'sky-blue'
            ],
            'CEDEAO' => [
                'icon' => 'fas fa-globe-africa',
                'color' => 'turquoise'
            ],
            
            // Services de transport
            'transport' => [
                'icon' => 'fas fa-car',
                'color' => 'sky-blue'
            ],
            'aéroport' => [
                'icon' => 'fas fa-plane',
                'color' => 'turquoise'
            ],
            
            // Services administratifs
            'formalities' => [
                'icon' => 'fas fa-file-alt',
                'color' => 'sky-blue'
            ],
            'administratif' => [
                'icon' => 'fas fa-building',
                'color' => 'turquoise'
            ]
        ];

        $serviceNameLower = strtolower($serviceName);

        // Recherche par correspondance partielle
        foreach ($configs as $key => $config) {
            if (strpos($serviceNameLower, $key) !== false) {
                return $config;
            }
        }

        // Configuration par défaut
        return [
            'icon' => 'fas fa-cog',
            'color' => 'turquoise'
        ];
    }

    /**
     * Services par défaut si pas assez en base
     */
    private function getDefaultServices()
    {
        return collect([
            [
                'icon' => 'fas fa-passport',
                'title' => 'Assistance Visa',
                'description' => 'Obtenez votre visa rapidement et en toute sécurité avec notre expertise',
                'features' => ['Visa touristique', 'Visa affaires', 'Visa étudiant', 'Renouvellement'],
                'link' => '/services/visa',
                'color' => 'turquoise'
            ],
            [
                'icon' => 'fas fa-car',
                'title' => 'Transport VIP',
                'description' => 'Déplacements sécurisés et confortables avec chauffeur professionnel',
                'features' => ['Aéroport', 'Hôtel', 'Ville', 'Inter-villes'],
                'link' => '/services/transport',
                'color' => 'sky-blue'
            ],
            [
                'icon' => 'fas fa-plane',
                'title' => 'Assistance Aéroport',
                'description' => 'Accompagnement complet pour vos formalités aéroportuaires',
                'features' => ['Accueil VIP', 'Fast track', 'Assistance bagages', 'Lounge'],
                'link' => '/services/aeroport',
                'color' => 'turquoise'
            ],
            [
                'icon' => 'fas fa-file-alt',
                'title' => 'Formalités Administratives',
                'description' => 'Gestion complète de vos documents officiels et démarches',
                'features' => ['CNI/Passeport', 'Actes de naissance', 'Certificats', 'Légalisation'],
                'link' => '/services/formalites',
                'color' => 'sky-blue'
            ]
        ]);
    }
}
