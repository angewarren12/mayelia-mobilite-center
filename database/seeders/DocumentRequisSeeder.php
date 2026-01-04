<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequis;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DocumentRequisSeeder extends Seeder
{
    public function run()
    {
        // Vider toute la table des documents requis
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DocumentRequis::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. SERVICE CNI
        $cniService = Service::where('nom', 'LIKE', '%CNI%')->first();
        if ($cniService) {
            $this->seedCNIDocuments($cniService->id);
            $this->command->info('Documents CNI ajoutés.');
        }

        // 2. SERVICE CARTE DE RÉSIDENT CEDEAO
        $cedeaoService = Service::where('nom', 'LIKE', '%résident CEDEAO%')->first();
        if ($cedeaoService) {
            $this->seedResidentCedeaoDocuments($cedeaoService->id);
            $this->command->info('Documents Résident CEDEAO ajoutés.');
        }

        // 3. SERVICES RÉSIDENT HORS CEDEAO ET RELIGIEUX (Utilisent la liste longue)
        $nonCedeaoServices = Service::where(function($q) {
                $q->where('nom', 'LIKE', '%résident hors CEDEAO%')
                  ->orWhere('nom', 'LIKE', '%résident religieux%');
            })->get();
            
        foreach ($nonCedeaoServices as $service) {
            $this->seedResidentNonCedeaoDocuments($service->id);
            $this->command->info("Documents pour {$service->nom} ajoutés.");
        }
        
        // 4. SERVICE VISA
        $visaService = Service::where('nom', 'LIKE', '%visa%')->first();
        if ($visaService) {
            $this->seedVisaDocuments($visaService->id);
            $this->command->info('Documents Visa ajoutés.');
        }

        $this->command->info('Seeder DocumentRequis terminé avec succès !');
    }

    private function seedCNIDocuments($serviceId)
    {
        $docs = [
            // PREMIERE DEMANDE
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de naissance (moins de 2 ans)',
                'description' => "Né en CI: Copie intégrale/Extrait ou Acte de notoriété. Né hors CI: Copie/Extrait par Ambassade ou MAE. Naturalisé: Acte naissance CI ou pays origine traduit.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de nationalité (moins de 2 ans)',
                'description' => "Né ivoirien: Certificat nationalité + CNI parent. Naturalisé: Certificat + Décret authentifié DACP. Mariage: Certificat + CNI conjoint + Lettre non-opposition DACP.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de mariage (femme mariée)',
                'description' => "Copie intégrale/Extrait acte de naissance avec mention mariage ou Acte de mariage.",
                'obligatoire' => false,
                'ordre' => 3
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de profession',
                'description' => "Obligatoire pour les professions classifiées spécifiques.",
                'obligatoire' => false,
                'ordre' => 4
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu de paiement',
                'description' => "Le reçu de paiement est exigé pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 5
            ],
            // RENOUVELLEMENT
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'CNI à renouveler',
                'description' => "CNI originale, photocopie, fiche d'identité ONECI ou document avec le numéro CNI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Justificatif de profession',
                'description' => "Un justificatif de la profession (profession spécifique).",
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Acte de naissance',
                'description' => "Une copie intégrale de l'acte de naissance ou un extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Reçu de paiement',
                'description' => "Le reçu de paiement est exigé pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 4
            ],
            // RENOUVELLEMENT AVEC MODIFICATION
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'CNI à renouveler',
                'description' => "CNI originale, photocopie, fiche d'identité ONECI ou document avec le numéro CNI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Acte de naissance',
                'description' => "Une copie intégrale de l'acte de naissance ou un extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Justificatif de profession',
                'description' => "Un justificatif de la profession (profession spécifique).",
                'obligatoire' => false,
                'ordre' => 3
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Justificatif de la modification',
                'description' => "Ex: une copie de l'acte de mariage ou un extrait naissance avec mention mariage.",
                'obligatoire' => true,
                'ordre' => 4
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Reçu de paiement',
                'description' => "Le reçu de paiement est exigé pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 5
            ],
            // DUPLICATA
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Attestation de déclaration de perte',
                'description' => "Délivrée par les autorités compétentes.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Copie CNI biométrique égarée',
                'description' => "Recto-verso, fiche ONECI ou document portant le NNI.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Acte de naissance',
                'description' => "Une copie intégrale de l'acte de naissance ou un extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Reçu de paiement',
                'description' => "Le reçu de paiement est exigé pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 4
            ],
        ];

        foreach ($docs as $d) {
            DocumentRequis::create($d);
        }
    }

    private function seedResidentCedeaoDocuments($serviceId)
    {
        $docs = [
            // PREMIÈRE DEMANDE
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Pièce d’identité origine',
                'description' => "Un des documents : CNI pays d'origine, Carte consulaire, Extrait naissance, Passeport, ou Formulaire appartenance CEDEAO.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de profession',
                'description' => "Obligatoire pour les professions réglementées (voir liste professions spécifiques).",
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu d’enrôlement',
                'description' => "Le reçu d’enrôlement requis.",
                'obligatoire' => true,
                'ordre' => 3
            ],

            // RENOUVELLEMENT
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Carte de Résident (CR) à renouveler',
                'description' => "CR originale, photocopie, ou fiche d'identité délivrée par l'ONECI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Justificatif de profession',
                'description' => "Obligatoire pour les professions réglementées.",
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Reçu d’enrôlement',
                'description' => "Le reçu d’enrôlement requis.",
                'obligatoire' => true,
                'ordre' => 3
            ],

            // RENOUVELLEMENT AVEC MODIFICATION
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Fiche de demande de modification',
                'description' => "Remplie sur le site de l’ONECI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Reçu d’enrôlement',
                'description' => "Le reçu d’enrôlement requis.",
                'obligatoire' => true,
                'ordre' => 2
            ],

            // DUPLICATA
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Attestation de perte',
                'description' => "Attestation délivrée par les autorités compétentes.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Identifiant CR égarée',
                'description' => "Copie recto-verso de la CR, fiche ONECI, ou document portant le NNI.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Reçu d’enrôlement',
                'description' => "Le reçu d’enrôlement requis.",
                'obligatoire' => true,
                'ordre' => 3
            ],
        ];

        foreach ($docs as $d) {
            DocumentRequis::create($d);
        }
    }

    private function seedResidentNonCedeaoDocuments($serviceId)
    {
        $docs = [
            // PRÉMIÈRE DEMANDE (LONGUE)
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Copie identité passeport ou carte consulaire', 'description' => 'En cours de validité.', 'obligatoire' => true, 'ordre' => 1],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Copie du visa et du cachet d’entrée', 'description' => 'Copies lisibles du visa et du cachet d’entrée.', 'obligatoire' => true, 'ordre' => 2],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Extrait du casier judiciaire', 'description' => 'Un extrait de casier judiciaire.', 'obligatoire' => true, 'ordre' => 3],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Récépissé de pré-enrôlement en ligne', 'description' => 'Récépissé de pré-enrôlement.', 'obligatoire' => true, 'ordre' => 4],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Reçu d’enrôlement', 'description' => 'Le reçu d’enrôlement.', 'obligatoire' => true, 'ordre' => 5],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Extrait d’acte de naissance', 'description' => 'L’extrait d’acte de naissance ou tout autre document en tenant lieu.', 'obligatoire' => true, 'ordre' => 6],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Certificat de résidence (ONECI)', 'description' => 'Original datant de moins de six (06) mois.', 'obligatoire' => true, 'ordre' => 7],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Reçu de paiement (Versus Bank)', 'description' => 'Reçu de paiement délivré par Versus Bank.', 'obligatoire' => true, 'ordre' => 8],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Justificatif de statut (Travailleur, Religieux, etc.)', 'description' => 'Attestation travail, Vie religieuse, Registre commerce, Bulletin pension, ou Carte étudiant selon le cas.', 'obligatoire' => false, 'ordre' => 9],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Autorisation parentale (pour mineur)', 'description' => 'Autorisation parentale légalisée + Copie carte résident/CNI du tuteur.', 'obligatoire' => false, 'ordre' => 10],
            ['service_id' => $serviceId, 'type_demande' => 'Première demande', 'nom_document' => 'Attestation de prise en charge (conjoint)', 'description' => 'Attestation de prise en charge légalisée + Copie carte/CNI du conjoint.', 'obligatoire' => false, 'ordre' => 11],

            // RENOUVELLEMENT
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Copie identité passeport ou carte consulaire', 'description' => 'En cours de validité.', 'obligatoire' => true, 'ordre' => 1],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Ancienne carte de résident', 'description' => 'Ancienne carte ou fiche d’identification ONECI.', 'obligatoire' => true, 'ordre' => 2],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Récépissé de pré-enrôlement en ligne', 'description' => 'Récépissé de pré-enrôlement.', 'obligatoire' => true, 'ordre' => 3],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Certificat de résidence (ONECI)', 'description' => 'Moins de six (6) mois.', 'obligatoire' => true, 'ordre' => 4],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Casier judiciaire', 'description' => 'Moins de trois (3) mois.', 'obligatoire' => true, 'ordre' => 5],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Reçu d’enrôlement', 'description' => 'Le reçu d’enrôlement.', 'obligatoire' => true, 'ordre' => 6],
            ['service_id' => $serviceId, 'type_demande' => 'Renouvellement', 'nom_document' => 'Justificatif de statut', 'description' => 'Attestation travail, Vie religieuse, Registre commerce, etc.', 'obligatoire' => false, 'ordre' => 7],

            // DUPLICATA
            ['service_id' => $serviceId, 'type_demande' => 'Duplicata', 'nom_document' => 'Attestation de déclaration de perte', 'description' => 'Délivrée par les autorités compétentes.', 'obligatoire' => true, 'ordre' => 1],
            ['service_id' => $serviceId, 'type_demande' => 'Duplicata', 'nom_document' => 'Ancienne carte ou fiche ONECI', 'description' => 'Copie de l’ancienne carte ou fiche d’identification.', 'obligatoire' => true, 'ordre' => 2],
            ['service_id' => $serviceId, 'type_demande' => 'Duplicata', 'nom_document' => 'Reçu de paiement (Versus Bank)', 'description' => 'Le reçu de paiement Versus Bank.', 'obligatoire' => true, 'ordre' => 3],
        ];
        foreach ($docs as $d) {
            DocumentRequis::create($d);
        }
    }

    private function seedVisaDocuments($serviceId)
    {
        $docs = [
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Passeport',
                'description' => "Passeport en cours de validité.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $serviceId,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu de paiement',
                'description' => "Paiement en ligne ou au guichet.",
                'obligatoire' => true,
                'ordre' => 2
            ],
        ];
        foreach ($docs as $d) {
            DocumentRequis::create($d);
        }
    }
}