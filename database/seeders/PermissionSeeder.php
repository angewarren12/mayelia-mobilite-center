<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Permissions pour les Centres
            [
                'module' => 'centres',
                'action' => 'view',
                'name' => 'Voir les centres',
                'description' => 'Permet de consulter les informations du centre en lecture seule'
            ],

            // Permissions pour les Créneaux
            [
                'module' => 'creneaux',
                'action' => 'view',
                'name' => 'Voir la gestion des créneaux',
                'description' => 'Accès à la page principale de gestion des créneaux'
            ],
            [
                'module' => 'creneaux',
                'action' => 'templates.view',
                'name' => 'Voir les templates de créneaux',
                'description' => 'Permet de consulter les templates de créneaux en lecture seule'
            ],
            [
                'module' => 'creneaux',
                'action' => 'templates.create',
                'name' => 'Créer des templates de créneaux',
                'description' => 'Permet de créer de nouveaux templates de créneaux'
            ],
            [
                'module' => 'creneaux',
                'action' => 'templates.update',
                'name' => 'Modifier les templates de créneaux',
                'description' => 'Permet de modifier les templates de créneaux existants'
            ],
            [
                'module' => 'creneaux',
                'action' => 'templates.delete',
                'name' => 'Supprimer les templates de créneaux',
                'description' => 'Permet de supprimer les templates de créneaux'
            ],
            [
                'module' => 'creneaux',
                'action' => 'exceptions.view',
                'name' => 'Voir les exceptions',
                'description' => 'Permet de consulter les exceptions en lecture seule'
            ],
            [
                'module' => 'creneaux',
                'action' => 'exceptions.create',
                'name' => 'Créer des exceptions',
                'description' => 'Permet de créer de nouvelles exceptions'
            ],
            [
                'module' => 'creneaux',
                'action' => 'exceptions.update',
                'name' => 'Modifier les exceptions',
                'description' => 'Permet de modifier les exceptions existantes'
            ],
            [
                'module' => 'creneaux',
                'action' => 'exceptions.delete',
                'name' => 'Supprimer les exceptions',
                'description' => 'Permet de supprimer les exceptions'
            ],
            [
                'module' => 'creneaux',
                'action' => 'calendrier.view',
                'name' => 'Voir le calendrier',
                'description' => 'Permet de consulter le calendrier des créneaux en lecture seule'
            ],
            [
                'module' => 'creneaux',
                'action' => 'jours-travail.view',
                'name' => 'Voir les jours de travail',
                'description' => 'Permet de consulter la configuration des jours de travail en lecture seule'
            ],
            [
                'module' => 'creneaux',
                'action' => 'jours-travail.update',
                'name' => 'Modifier les jours de travail',
                'description' => 'Permet de modifier la configuration des jours de travail (activer/désactiver, horaires)'
            ],

            // Permissions pour les Rendez-vous
            [
                'module' => 'rendez-vous',
                'action' => 'view',
                'name' => 'Voir les rendez-vous',
                'description' => 'Permet de consulter la liste des rendez-vous'
            ],
            [
                'module' => 'rendez-vous',
                'action' => 'create',
                'name' => 'Créer des rendez-vous',
                'description' => 'Permet de créer de nouveaux rendez-vous'
            ],
            [
                'module' => 'rendez-vous',
                'action' => 'update',
                'name' => 'Modifier les rendez-vous',
                'description' => 'Permet de modifier les rendez-vous existants'
            ],
            [
                'module' => 'rendez-vous',
                'action' => 'delete',
                'name' => 'Supprimer les rendez-vous',
                'description' => 'Permet de supprimer les rendez-vous'
            ],

            // Permissions pour les Clients
            [
                'module' => 'clients',
                'action' => 'view',
                'name' => 'Voir les clients',
                'description' => 'Permet de consulter la liste des clients'
            ],
            [
                'module' => 'clients',
                'action' => 'create',
                'name' => 'Créer des clients',
                'description' => 'Permet de créer de nouveaux clients'
            ],
            [
                'module' => 'clients',
                'action' => 'update',
                'name' => 'Modifier les clients',
                'description' => 'Permet de modifier les clients existants'
            ],
            [
                'module' => 'clients',
                'action' => 'delete',
                'name' => 'Supprimer les clients',
                'description' => 'Permet de supprimer les clients'
            ],

            // Permissions pour les Dossiers
            [
                'module' => 'dossiers',
                'action' => 'view',
                'name' => 'Voir les dossiers',
                'description' => 'Permet de consulter la liste des dossiers'
            ],
            [
                'module' => 'dossiers',
                'action' => 'create',
                'name' => 'Créer des dossiers',
                'description' => 'Permet de créer de nouveaux dossiers'
            ],
            [
                'module' => 'dossiers',
                'action' => 'update',
                'name' => 'Modifier les dossiers',
                'description' => 'Permet de modifier les dossiers existants (sauf suppression)'
            ],
            [
                'module' => 'dossiers',
                'action' => 'delete',
                'name' => 'Supprimer les dossiers',
                'description' => 'Permet de supprimer les dossiers (réservé aux admins)'
            ],

            // Permissions pour ONECI
            [
                'module' => 'oneci',
                'action' => 'view',
                'name' => 'Voir l\'interface ONECI',
                'description' => 'Permet d\'accéder à l\'interface ONECI'
            ],
            [
                'module' => 'oneci',
                'action' => 'scan',
                'name' => 'Scanner les codes-barres',
                'description' => 'Permet de scanner les codes-barres des dossiers'
            ],
            [
                'module' => 'oneci',
                'action' => 'marquer_carte_prete',
                'name' => 'Marquer les cartes comme prêtes',
                'description' => 'Permet de marquer les cartes comme prêtes après traitement'
            ],

            // Permissions pour les transferts ONECI
            [
                'module' => 'oneci-transfers',
                'action' => 'view',
                'name' => 'Voir les transferts ONECI',
                'description' => 'Permet de consulter la liste des transferts vers l\'ONECI'
            ],
            [
                'module' => 'oneci-transfers',
                'action' => 'create',
                'name' => 'Créer des transferts ONECI',
                'description' => 'Permet de créer de nouveaux transferts vers l\'ONECI'
            ],
            [
                'module' => 'oneci-transfers',
                'action' => 'envoyer',
                'name' => 'Envoyer les transferts ONECI',
                'description' => 'Permet de marquer un transfert comme envoyé'
            ],

            // Permissions pour la récupération
            [
                'module' => 'oneci-recuperation',
                'action' => 'view',
                'name' => 'Voir les cartes prêtes',
                'description' => 'Permet de consulter la liste des cartes prêtes à récupérer'
            ],
            [
                'module' => 'oneci-recuperation',
                'action' => 'scan',
                'name' => 'Scanner pour récupération',
                'description' => 'Permet de scanner les codes-barres lors de la récupération'
            ],
            [
                'module' => 'oneci-recuperation',
                'action' => 'confirmer',
                'name' => 'Confirmer la récupération',
                'description' => 'Permet de confirmer la récupération d\'une carte'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'module' => $permission['module'],
                    'action' => $permission['action']
                ],
                $permission
            );
        }
    }
}
