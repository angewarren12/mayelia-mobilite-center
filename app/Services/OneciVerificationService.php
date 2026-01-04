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
     * Vérifie un numéro de pré-enrôlement auprès de l'API ONECI
     * 
     * @param string $numeroPreEnrolement
     * @return array
     */
    public function verifyPreEnrollmentNumber(string $numeroPreEnrolement): array
    {
        Log::info('Vérification du numéro de pré-enrôlement ONECI réelle', [
            'numero' => $numeroPreEnrolement
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.oneci.api_key'),
                'Accept' => 'application/json',
            ])->timeout(15)->get(config('services.oneci.api_url') . '/verify/' . $numeroPreEnrolement);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => $this->getStatusMessage($data['statut'] ?? 'valide'),
                    'statut' => $data['statut'] ?? 'valide',
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Numéro de pré-enrôlement introuvable ou erreur API',
                'statut' => null,
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('Erreur API ONECI: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion à l\'API ONECI',
                'statut' => null,
                'data' => null
            ];
        }
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
