<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OneciCniVerificationService
{
    protected $baseUrl = 'https://pre-enrolement-cni.oneci.ci/api';
    protected $clientId = 'e749fb0d-1944-436e-b628-2cc647640297';
    protected $clientSecret = 'k0bXnjwrB1taiVUAkm7Zen5aMOvbIF4OAtC1GLsS';

    /**
     * Récupère le token d'accès (Bearer Token)
     */
    public function getAccessToken()
    {
        // Essayer de récupérer le token depuis le cache pour éviter de spammer l'API d'auth
        return Cache::remember('oneci_cni_token', 3000, function () {
            try {
                $response = Http::asForm()->post($this->baseUrl . '/token', [
                    'oneciprecni_client' => $this->clientId,
                    'oneciprecni_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['access_token'] ?? null;
                }

                Log::error('Erreur Auth ONECI CNI', ['response' => $response->body()]);
                return null;
            } catch (\Exception $e) {
                Log::error('Exception Auth ONECI CNI: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Vérifie le numéro de dossier CNI
     */
    public function verifyNumeroDossier($numeroDossier)
    {
        try {
            $token = $this->getAccessToken();

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Service d\'authentification indisponible'
                ];
            }

            Log::info('Vérification ONECI CNI', ['dossier' => $numeroDossier]);

            $response = Http::withToken($token)
                ->asForm()
                ->post($this->baseUrl . '/info', [
                    'numero_dossier' => $numeroDossier
                ]);

            Log::info('Réponse API CNI', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();

                // La réponse peut contenir "error": false
                if (isset($data['error']) && $data['error'] === false) {
                    $dossierData = $data['data'];
                    $statut = $dossierData['statut'];

                    // Vérifier si le statut est FPD (Fiche de pré-enrôlement disponible)
                    $isEligible = ($statut['code_statut'] === 'FPD');

                    $message = $isEligible 
                        ? 'Dossier vérifié avec succès. Fiche disponible.'
                        : 'Statut du dossier non éligible pour la prise de rendez-vous (' . $statut['libelle_statut'] . ').';

                    return [
                        'success' => $isEligible,
                        'message' => $message,
                        'statut_code' => $statut['code_statut'],
                        'statut_label' => $statut['libelle_statut'],
                        'data' => [
                            'nom' => $dossierData['nom'] ?? '',
                            'prenoms' => $dossierData['prenom'] ?? '',
                            'date_naissance' => $dossierData['date_naissance'] ?? '',
                            'lieu_naissance' => $dossierData['lieu_naissance'] ?? '',
                            'telephone' => $dossierData['numero_telephone'] ?? '',
                            'email' => $dossierData['email'] ?? '',
                            'genre' => $dossierData['genre'] ?? '',
                            'statut' => $statut['libelle_statut'],
                            'numero_pre_enrolement' => $dossierData['numero_dossier']
                        ]
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'Dossier introuvable ou erreur API'
                    ];
                }
            }

            // Gestion des erreurs 404 (numéro introuvable)
            if ($response->status() === 404) {
                 return [
                    'success' => false,
                    'message' => 'Numéro de dossier introuvable.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            Log::error('Exception Vérification CNI: ' . $e->getMessage());
            


            return [
                'success' => false,
                'message' => 'Une erreur interne est survenue (API injoignable).'
            ];
        }
    }
}
