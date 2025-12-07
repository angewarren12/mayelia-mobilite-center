<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service de vérification ONECI
 * 
 * Ce service gère la communication avec l'API ONECI pour vérifier
 * les numéros de pré-enrôlement et générer des tokens de vérification.
 * 
 * Pour le développement, il utilise un mock de l'API ONECI.
 */
class OneciVerificationService
{
    /**
     * Mock de l'API ONECI - Données de test
     * 
     * En production, ces données viendront de l'API réelle ONECI
     */
    private array $mockDatabase = [
        'ONECI2025001' => [
            'numero_pre_enrolement' => 'ONECI2025001',
            'statut' => 'valide',
            'nom' => 'KOUASSI',
            'prenoms' => 'Jean Marc',
            'date_naissance' => '1990-05-15',
            'lieu_naissance' => 'Abidjan',
            'telephone' => '+225 07 12 34 56 78',
            'email' => 'jean.kouassi@example.com',
            'date_validation' => '2025-12-01 10:30:00'
        ],
        'ONECI2025002' => [
            'numero_pre_enrolement' => 'ONECI2025002',
            'statut' => 'valide',
            'nom' => 'YAO',
            'prenoms' => 'Marie Claire',
            'date_naissance' => '1985-08-22',
            'lieu_naissance' => 'Bouaké',
            'telephone' => '+225 05 98 76 54 32',
            'email' => 'marie.yao@example.com',
            'date_validation' => '2025-12-02 14:15:00'
        ],
        'ONECI2025003' => [
            'numero_pre_enrolement' => 'ONECI2025003',
            'statut' => 'en_attente',
            'nom' => 'TRAORE',
            'prenoms' => 'Amadou',
            'date_naissance' => '1992-03-10',
            'lieu_naissance' => 'Yamoussoukro',
            'telephone' => '+225 07 11 22 33 44',
            'email' => 'amadou.traore@example.com',
            'date_soumission' => '2025-12-03 09:00:00'
        ],
        'ONECI2025004' => [
            'numero_pre_enrolement' => 'ONECI2025004',
            'statut' => 'rejete',
            'nom' => 'KONE',
            'prenoms' => 'Ibrahim',
            'date_naissance' => '1988-11-05',
            'lieu_naissance' => 'Korhogo',
            'telephone' => '+225 01 23 45 67 89',
            'email' => 'ibrahim.kone@example.com',
            'date_rejet' => '2025-12-02 16:45:00',
            'motif_rejet' => 'Documents incomplets'
        ]
    ];

    /**
     * Vérifie un numéro de pré-enrôlement auprès de l'API ONECI
     * 
     * @param string $numeroPreEnrolement
     * @return array
     */
    public function verifyPreEnrollmentNumber(string $numeroPreEnrolement): array
    {
        Log::info('Vérification du numéro de pré-enrôlement ONECI', [
            'numero' => $numeroPreEnrolement
        ]);

        // En production, remplacer par un appel HTTP à l'API ONECI
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . config('services.oneci.api_key'),
        //     'Accept' => 'application/json',
        // ])->get(config('services.oneci.api_url') . '/verify/' . $numeroPreEnrolement);
        
        // Mock de la réponse API
        $data = $this->mockApiCall($numeroPreEnrolement);

        if ($data === null) {
            return [
                'success' => false,
                'message' => 'Numéro de pré-enrôlement introuvable',
                'statut' => null,
                'data' => null
            ];
        }

        return [
            'success' => true,
            'message' => $this->getStatusMessage($data['statut']),
            'statut' => $data['statut'],
            'data' => $data
        ];
    }

    /**
     * Mock de l'appel API ONECI
     * 
     * @param string $numeroPreEnrolement
     * @return array|null
     */
    private function mockApiCall(string $numeroPreEnrolement): ?array
    {
        // Simuler un délai réseau
        usleep(500000); // 0.5 secondes

        return $this->mockDatabase[$numeroPreEnrolement] ?? null;
    }

    /**
     * Génère un token de vérification unique
     * 
     * Ce token sera utilisé dans le lien envoyé par ONECI
     * pour permettre un accès sécurisé à la prise de RDV
     * 
     * @return string
     */
    public function generateVerificationToken(): string
    {
        return Str::random(64);
    }

    /**
     * Valide un token de vérification
     * 
     * @param string $token
     * @return array|null Retourne les données du rendez-vous si le token est valide
     */
    public function validateToken(string $token): ?array
    {
        // En production, vérifier dans la base de données
        $rendezVous = \App\Models\RendezVous::where('token_verification', $token)
            ->whereNotNull('verified_at')
            ->where('statut_oneci', 'valide')
            ->first();

        if (!$rendezVous) {
            return null;
        }

        return [
            'numero_pre_enrolement' => $rendezVous->numero_pre_enrolement,
            'donnees_oneci' => $rendezVous->donnees_oneci,
            'verified_at' => $rendezVous->verified_at
        ];
    }

    /**
     * Obtient le message correspondant au statut
     * 
     * @param string $statut
     * @return string
     */
    private function getStatusMessage(string $statut): string
    {
        return match($statut) {
            'valide' => 'Votre pré-enrôlement a été validé. Vous pouvez prendre rendez-vous.',
            'en_attente' => 'Votre pré-enrôlement est en cours de traitement. Veuillez patienter.',
            'rejete' => 'Votre pré-enrôlement a été rejeté. Veuillez contacter le service ONECI.',
            default => 'Statut inconnu'
        };
    }

    /**
     * Ajoute un numéro de test au mock (pour les tests)
     * 
     * @param array $data
     * @return void
     */
    public function addMockData(array $data): void
    {
        $this->mockDatabase[$data['numero_pre_enrolement']] = $data;
    }

    /**
     * Récupère toutes les données mock (pour les tests)
     * 
     * @return array
     */
    public function getMockDatabase(): array
    {
        return $this->mockDatabase;
    }
}
