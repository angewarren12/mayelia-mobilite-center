<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequis;
use App\Models\Service;

class CNIDocumentRequisSeeder extends Seeder
{
    public function run()
    {
        $service = Service::where('nom', 'LIKE', '%CNI%')->first();

        if (!$service) {
            $this->command->error('Service "Demande de CNI" non trouvé.');
            return;
        }

        $documents = [
            // PREMIERE DEMANDE
            [
                'service_id' => $service->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de naissance (moins de 2 ans)',
                'description' => "a. Né en CI: Copie intégrale/Extrait ou Acte de notoriété.\nb. Né hors CI: Copie/Extrait par Ambassade ou MAE.\nc. Naturalisé: Acte de naissance CI (né en CI) ou pays naissance (traduit).",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de nationalité (moins de 2 ans)',
                'description' => "a. Né ivoirien: Certificat nationalité + CNI parent (recommandé).\nb. Naturalisé: Certificat + Décret authentifié DACP.\nc. Mariage: Certificat + CNI conjoint + Lettre non-opposition/non-déclination authentifiée DACP.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de mariage (femme mariée)',
                'description' => "Copie intégrale/Extrait acte de naissance avec mention mariage ou Acte de mariage.",
                'obligatoire' => false,
                'ordre' => 3
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Justificatif de profession',
                'description' => "Obligatoire pour les professions classifiées spécifiques.",
                'obligatoire' => false,
                'ordre' => 4
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Première demande',
                'nom_document' => 'Reçu de paiement',
                'description' => "Obligatoire pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 5
            ],

            // RENOUVELLEMENT
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'CNI à renouveler',
                'description' => "CNI originale, photocopie, fiche d'identité ONECI ou tout document avec le numéro CNI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Justificatif de profession',
                'description' => "Un justificatif de la profession (profession spécifique).",
                'obligatoire' => false,
                'ordre' => 2
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Acte de naissance',
                'description' => "Copie intégrale ou extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement',
                'nom_document' => 'Reçu de paiement',
                'description' => "Obligatoire pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 4
            ],

            // RENOUVELLEMENT AVEC MODIFICATION
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'CNI à renouveler',
                'description' => "CNI originale, photocopie, fiche d'identité ONECI ou tout document avec le numéro CNI.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Acte de naissance',
                'description' => "Copie intégrale ou extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Justificatif de profession',
                'description' => "Un justificatif de la profession (profession spécifique).",
                'obligatoire' => false,
                'ordre' => 3
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Justificatif de la modification',
                'description' => "Ex: acte de mariage ou extrait naissance avec mention mariage.",
                'obligatoire' => true,
                'ordre' => 4
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Renouvellement avec modification',
                'nom_document' => 'Reçu de paiement',
                'description' => "Obligatoire pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 5
            ],

            // DUPLICATA
            [
                'service_id' => $service->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Attestation de déclaration de perte',
                'description' => "Délivrée par les autorités compétentes.",
                'obligatoire' => true,
                'ordre' => 1
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Copie CNI biométrique égarée',
                'description' => "Photocopie, fiche ONECI ou tout document portant le NNI.",
                'obligatoire' => true,
                'ordre' => 2
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Acte de naissance',
                'description' => "Copie intégrale ou extrait d'acte de naissance.",
                'obligatoire' => true,
                'ordre' => 3
            ],
            [
                'service_id' => $service->id,
                'type_demande' => 'Duplicata',
                'nom_document' => 'Reçu de paiement',
                'description' => "Obligatoire pour tous les types de demande.",
                'obligatoire' => true,
                'ordre' => 4
            ],
        ];

        // Nettoyer les anciens documents pour ce service
        DocumentRequis::where('service_id', $service->id)->delete();

        foreach ($documents as $doc) {
            DocumentRequis::create($doc);
        }

        $this->command->info('Documents pour la CNI mis à jour avec succès !');
    }
}
