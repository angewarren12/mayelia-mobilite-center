<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CarteResidentVerificationService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = 'https://pre-enregistrement-carte-resident.oneci.ci/api/oneci-info';
        // Token officiel récupéré de la documentation
        $this->token = env('CARTE_RESIDENT_API_TOKEN', 'bf437c2f-6ec5-4949-9233-9293ad77b34c');
    }

    /**
     * Vérifier un numéro de dossier carte de résident
     */
    public function verifyNumeroDossier($numeroDossier)
    {
        try {
            Log::info('Tentative de connexion API Carte Résident', [
                'url' => $this->apiUrl,
                'token_utilise' => substr($this->token, 0, 5) . '...',
                'numero_dossier' => $numeroDossier
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'numero_dossier' => $numeroDossier
            ]);

            Log::info('Réponse API brute', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] === true) {
                    Log::info('Vérification réussie', [
                        'numero_dossier' => $numeroDossier,
                        'nom' => $data['data']['nom'] ?? null
                    ]);

                    // Gestion de la clé date_inscription qui peut avoir un espace à la fin
                    $dateInscription = $data['data']['date_inscription'] 
                        ?? $data['data']['date_inscription '] 
                        ?? '';

                    return [
                        'success' => true,
                        'message' => 'Numéro de dossier vérifié avec succès',
                        'statut' => $data['data']['statut'] ?? 'Inconnu',
                        'data' => [
                            'nom' => $data['data']['nom'] ?? '',
                            'prenoms' => $data['data']['prenoms'] ?? '',
                            'date_naissance' => $data['data']['date_naissance'] ?? '',
                            'lieu_naissance' => $data['data']['lieu_naissance'] ?? '',
                            'telephone' => $data['data']['telephone'] ?? '',
                            'statut' => $data['data']['statut'] ?? '',
                            'date_inscription' => $dateInscription,
                        ]
                    ];
                }
            }

            Log::warning('Numéro de dossier non trouvé', [
                'numero_dossier' => $numeroDossier,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Numéro de dossier non trouvé ou invalide'
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du numéro de dossier', [
                'numero_dossier' => $numeroDossier,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification. Veuillez réessayer.'
            ];
        }
    }

    /**
     * Valider le statut du dossier
     */
    public function isStatutValide($statut)
    {
        // Statuts acceptés pour la prise de rendez-vous
        $statutsValides = ['Traité', 'Validé', 'En cours'];
        
        return in_array($statut, $statutsValides);
    }
}
