<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequis; 
use App\Models\Service;

class DocumentRequisSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les services CNI et CEDEAO
        $cniService = Service::where('nom', 'LIKE', '%CNI%')->first();
        $cedeaService = Service::where('nom', 'LIKE', '%CEDEAO%')->first();

        if (!$cniService || !$cedeaService) {
            $this->command->warn('Services CNI ou CEDEAO non trouvés. Veuillez d\'abord créer ces services.');
            return;
        }

        // Documents requis pour la CNI - Première demande
        $cniPremiereDemande = [
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de naissance (datant de moins de 2 ans)',
                'description' => 'Copie intégrale/Extrait d\'acte de naissance ou Acte de notoriété portant la mention « en vue de l\'établissement de la CNI »',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de nationalité (datant de moins de 2 ans)',
                'description' => 'Certificat de nationalité ou Copie lisible de la CNI de l\'un des parents',
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de mariage (femme mariée)',
                'description' => 'Copie intégrale/Extrait d\'acte de naissance portant la mention du mariage ou Copie intégrale/Extrait d\'acte de mariage',
                'obligatoire' => false,
                'ordre' => 3
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de profession',
                'description' => 'Obligatoire pour les professions classifiées spécifiques',
                'obligatoire' => false,
                'ordre' => 4
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu de paiement',
                'description' => 'Reçu de paiement exigé pour tous les types de demande',
                'obligatoire' => true,
                'ordre' => 5
            ]
        ];

        // Documents requis pour la CNI - Renouvellement
        $cniRenouvellement = [
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Carte Nationale d\'Identité à renouveler',
                'description' => 'La CNI à renouveler ou la photocopie de la CNI à renouveler ou une fiche d\'identité délivrée par l\'ONECI',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Justificatif de la profession',
                'description' => 'Un justificatif de la profession (profession spécifique)',
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Copie intégrale de l\'acte de naissance',
                'description' => 'Une copie intégrale de l\'acte de naissance ou un extrait d\'acte de naissance',
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Reçu de paiement',
                'description' => 'Reçu de paiement exigé pour tous les types de demande',
                'obligatoire' => true,
                'ordre' => 4
            ]
        ];

        // Documents requis pour la CNI - Duplicata
        $cniDuplicata = [
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Attestation de déclaration de perte',
                'description' => 'Une attestation de déclaration de perte de sa carte délivrée par les autorités compétentes',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Copie recto-verso de la CNI biométrique égarée',
                'description' => 'Une copie recto-verso de la CNI biométrique égarée ou une fiche d\'identité délivrée par l\'ONECI',
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Copie intégrale de l\'acte de naissance',
                'description' => 'Une copie intégrale de l\'acte de naissance ou un extrait d\'acte de naissance',
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $cniService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Reçu de paiement',
                'description' => 'Reçu de paiement exigé pour tous les types de demande',
                'obligatoire' => true,
                'ordre' => 4
            ]
        ];

        // Documents requis pour la CEDEAO - Première demande
        $cedeaPremiereDemande = [
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Carte nationale d\'identité du pays d\'origine',
                'description' => 'Ou Carte consulaire ou Extrait d\'acte de naissance ou Passeport du pays d\'origine ou Formulaire de déclaration d\'appartenance à la CEDEAO',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de profession réglementée',
                'description' => 'Obligatoire pour les professions réglementées (voir liste des professions spécifiques)',
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu d\'enrôlement',
                'description' => 'Reçu d\'enrôlement',
                'obligatoire' => true,
                'ordre' => 3
            ]
        ];

        // Documents requis pour la CEDEAO - Renouvellement
        $cedeaRenouvellement = [
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Carte de Résident (CR) à renouveler',
                'description' => 'La CR à renouveler ou la photocopie de la CR à renouveler ou une fiche d\'identité délivrée par l\'ONECI',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Justificatif de profession réglementée',
                'description' => 'Obligatoire pour les professions réglementées (voir liste des professions spécifiques)',
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Reçu d\'enrôlement',
                'description' => 'Reçu d\'enrôlement',
                'obligatoire' => true,
                'ordre' => 3
            ]
        ];

        // Documents requis pour la CEDEAO - Duplicata
        $cedeaDuplicata = [
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Attestation de déclaration de perte',
                'description' => 'Une attestation de déclaration de perte de sa carte délivrée par les autorités compétentes',
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Copie recto-verso du CR biométrique égaré',
                'description' => 'Une copie recto-verso du CR biométrique égaré ou une fiche d\'identité délivrée par l\'ONECI',
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $cedeaService->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Reçu d\'enrôlement',
                'description' => 'Reçu d\'enrôlement',
                'obligatoire' => true,
                'ordre' => 3
            ]
        ];

        // Insérer tous les documents requis
        $allDocuments = array_merge(
            $cniPremiereDemande,
            $cniRenouvellement,
            $cniDuplicata,
            $cedeaPremiereDemande,
            $cedeaRenouvellement,
            $cedeaDuplicata
        );

        foreach ($allDocuments as $document) {
            DocumentRequis::create($document);
        }

        $this->command->info('Documents requis créés avec succès !');
    }
}