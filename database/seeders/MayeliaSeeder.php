<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ville;
use App\Models\Centre;
use App\Models\User;
use App\Models\Service;
use App\Models\Formule;
use App\Models\JourTravail;
use App\Models\TemplateCreneau;
use Illuminate\Support\Facades\Hash;

class MayeliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les villes
        $villeSanPedro = Ville::create([
            'nom' => 'San Pedro',
            'code_postal' => 'SP001'
        ]);

        $villeDaloa = Ville::create([
            'nom' => 'Daloa',
            'code_postal' => 'DL001'
        ]);

        // Créer les centres
        $centreSanPedro = Centre::create([
            'ville_id' => $villeSanPedro->id,
            'nom' => 'Centre Mayelia San Pedro',
            'adresse' => 'Quartier Nitoro, San Pedro, Côte d\'Ivoire',
            'email' => 'sanpedro@mayelia.com',
            'telephone' => '+225 34 45 67 89',
            'statut' => 'actif'
        ]);

        $centreDaloa = Centre::create([
            'ville_id' => $villeDaloa->id,
            'nom' => 'Centre Mayelia Daloa',
            'adresse' => 'Non loin de la Source Hotel, Daloa, Côte d\'Ivoire',
            'email' => 'daloa@mayelia.com',
            'telephone' => '+225 30 45 67 89',
            'statut' => 'actif'
        ]);

        // Créer les services
        $serviceResidentCedeao = Service::create([
            'nom' => 'Demande de carte de résident CEDEAO',
            'description' => 'Service pour la demande de carte de résident de la CEDEAO',
            'duree_rdv' => 60, // 1 heure
            'statut' => 'actif'
        ]);

        $serviceResidentHorsCedeao = Service::create([
            'nom' => 'Demande de carte de résident hors CEDEAO',
            'description' => 'Service pour la demande de carte de résident hors CEDEAO',
            'duree_rdv' => 90, // 1h30
            'statut' => 'actif'
        ]);

        $serviceResidentReligieux = Service::create([
            'nom' => 'Demande de carte de résident religieux',
            'description' => 'Service pour la demande de carte de résident religieux',
            'duree_rdv' => 45, // 45 minutes
            'statut' => 'actif'
        ]);

        $serviceCNI = Service::create([
            'nom' => 'Demande de CNI',
            'description' => 'Service pour la demande de carte nationale d\'identité',
            'duree_rdv' => 30, // 30 minutes
            'statut' => 'actif'
        ]);

        $serviceVisa = Service::create([
            'nom' => 'Demande de visa',
            'description' => 'Service pour la demande de visa',
            'duree_rdv' => 120, // 2 heures
            'statut' => 'actif'
        ]);

        // Créer les formules
        $formuleStandard = Formule::create([
            'service_id' => $serviceResidentCedeao->id,
            'nom' => 'Standard',
            'prix' => 100000.00, // 100 000 FCFA
            'couleur' => '#28a745', // Vert
            'statut' => 'actif'
        ]);

        $formuleStandardHorsCedeao = Formule::create([
            'service_id' => $serviceResidentHorsCedeao->id,
            'nom' => 'Standard',
            'prix' => 300000.00, // 300 000 FCFA
            'couleur' => '#28a745', // Vert
            'statut' => 'actif'
        ]);

        $formuleStandardReligieux = Formule::create([
            'service_id' => $serviceResidentReligieux->id,
            'nom' => 'Standard',
            'prix' => 35000.00, // 35 000 FCFA
            'couleur' => '#28a745', // Vert
            'statut' => 'actif'
        ]);

        $formuleVIP = Formule::create([
            'service_id' => $serviceCNI->id,
            'nom' => 'VIP',
            'prix' => 15000.00, // 15 000 FCFA
            'couleur' => '#ffc107', // Jaune
            'statut' => 'actif'
        ]);

        // Pour le service visa, on crée une formule standard
        $formuleVisaStandard = Formule::create([
            'service_id' => $serviceVisa->id,
            'nom' => 'Standard',
            'prix' => 50000.00, // 50 000 FCFA (prix à définir)
            'couleur' => '#28a745', // Vert
            'statut' => 'actif'
        ]);

        // Créer les utilisateurs pour San Pedro
        $adminSanPedro = User::create([
            'centre_id' => $centreSanPedro->id,
            'nom' => 'Admin San Pedro',
            'email' => 'admin.sanpedro@mayelia.com',
            'password' => Hash::make('password'),
            'telephone' => '+225 34 45 67 90',
            'role' => 'admin',
            'statut' => 'actif'
        ]);

        $agentSanPedro = User::create([
            'centre_id' => $centreSanPedro->id,
            'nom' => 'Agent San Pedro',
            'email' => 'agent.sanpedro@mayelia.com',
            'password' => Hash::make('password'),
            'telephone' => '+225 34 45 67 91',
            'role' => 'agent',
            'statut' => 'actif'
        ]);

        // Activer tous les services pour le centre San Pedro
        $centreSanPedro->services()->attach([
            $serviceResidentCedeao->id => ['actif' => true],
            $serviceResidentHorsCedeao->id => ['actif' => true],
            $serviceResidentReligieux->id => ['actif' => true],
            $serviceCNI->id => ['actif' => true],
            $serviceVisa->id => ['actif' => true]
        ]);

        // Activer toutes les formules pour le centre San Pedro
        $centreSanPedro->formules()->attach([
            $formuleStandard->id => ['actif' => true],
            $formuleStandardHorsCedeao->id => ['actif' => true],
            $formuleStandardReligieux->id => ['actif' => true],
            $formuleVIP->id => ['actif' => true],
            $formuleVisaStandard->id => ['actif' => true]
        ]);

        // Activer tous les services pour le centre Daloa aussi
        $centreDaloa->services()->attach([
            $serviceResidentCedeao->id => ['actif' => true],
            $serviceResidentHorsCedeao->id => ['actif' => true],
            $serviceResidentReligieux->id => ['actif' => true],
            $serviceCNI->id => ['actif' => true],
            $serviceVisa->id => ['actif' => true]
        ]);

        // Activer toutes les formules pour le centre Daloa
        $centreDaloa->formules()->attach([
            $formuleStandard->id => ['actif' => true],
            $formuleStandardHorsCedeao->id => ['actif' => true],
            $formuleStandardReligieux->id => ['actif' => true],
            $formuleVIP->id => ['actif' => true],
            $formuleVisaStandard->id => ['actif' => true]
        ]);

        // Créer les jours de travail pour San Pedro (Lundi à Vendredi)
        for ($jour = 1; $jour <= 5; $jour++) {
            JourTravail::create([
                'centre_id' => $centreSanPedro->id,
                'jour_semaine' => $jour,
                'actif' => true,
                'heure_debut' => '08:00',
                'heure_fin' => '15:00',
                'pause_debut' => '12:00',
                'pause_fin' => '13:00',
                'intervalle_minutes' => 60 // 1 heure par défaut
            ]);
        }

        // Créer les jours de travail pour Daloa (Lundi à Vendredi)
        for ($jour = 1; $jour <= 5; $jour++) {
            JourTravail::create([
                'centre_id' => $centreDaloa->id,
                'jour_semaine' => $jour,
                'actif' => true,
                'heure_debut' => '08:00',
                'heure_fin' => '15:00',
                'pause_debut' => '12:00',
                'pause_fin' => '13:00',
                'intervalle_minutes' => 60 // 1 heure par défaut
            ]);
        }

        $this->command->info('✅ Base de données initialisée avec succès !');
        $this->command->info('🏢 Centres créés : San Pedro et Daloa');
        $this->command->info('👥 Utilisateurs San Pedro : admin.sanpedro@mayelia.com / agent.sanpedro@mayelia.com');
        $this->command->info('🔑 Mot de passe : password');
        $this->command->info('📋 Services : 5 services avec leurs formules');
    }
}