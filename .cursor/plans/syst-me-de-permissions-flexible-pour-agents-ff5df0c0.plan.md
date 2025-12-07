<!-- ff5df0c0-d5f7-436d-a6c1-8f5e2b0b240c bbdb3feb-5007-49ee-8461-f481cc1cb197 -->
# Plan : Workflow ONECI et Synchronisation Mayelia-ONECI

## 1. Masquer les RDV finalisés du calendrier

**Fichier**: `app/Http/Controllers/DashboardController.php`

- Modifier `getRendezVousByMonth()` pour exclure les rendez-vous avec `statut = 'finalise'`
- Filtrer également les rendez-vous liés à des dossiers finalisés

**Fichier**: `resources/views/dashboard.blade.php`

- Aucun changement nécessaire (le filtrage se fait côté serveur)

## 2. Système de regroupement et envoi à l'ONECI

### 2.1 Migration - Table `dossier_oneci_transfers`

**Fichier**: `database/migrations/YYYY_MM_DD_create_dossier_oneci_transfers_table.php`

- `id`, `centre_id`, `date_envoi`, `statut` (en_attente, envoye, recu_oneci, traite, carte_prete, recupere)
- `code_transfert` (unique), `nombre_dossiers`, `agent_mayelia_id`, `agent_oneci_id`
- `date_reception_oneci`, `date_traitement`, `date_carte_prete`, `date_recuperation`
- `notes`, `timestamps`

### 2.2 Migration - Table `dossier_oneci_items`

**Fichier**: `database/migrations/YYYY_MM_DD_create_dossier_oneci_items_table.php`

- `id`, `transfer_id`, `dossier_ouvert_id`, `code_barre` (unique)
- `statut` (en_attente, recu, traite, carte_prete, recupere)
- `date_reception`, `date_traitement`, `date_carte_prete`, `date_recuperation`
- `agent_oneci_id`, `agent_mayelia_id`, `timestamps`

### 2.3 Migration - Ajout colonnes à `dossier_ouvert`

**Fichier**: `database/migrations/YYYY_MM_DD_add_oneci_fields_to_dossier_ouvert.php`

- `code_barre` (string, unique, nullable)
- `statut_oneci` (enum: null, envoye, recu, traite, carte_prete, recupere)
- `transfer_id` (foreign key vers dossier_oneci_transfers)
- `date_envoi_oneci`, `date_reception_oneci`, `date_carte_prete`, `date_recuperation`

### 2.4 Modèle `DossierOneciTransfer`

**Fichier**: `app/Models/DossierOneciTransfer.php`

- Relations: `centre()`, `agentMayelia()`, `agentOneci()`, `items()`
- Méthodes: `generateCodeTransfert()`, `getStatutFormateAttribute()`

### 2.5 Modèle `DossierOneciItem`

**Fichier**: `app/Models/DossierOneciItem.php`

- Relations: `transfer()`, `dossierOuvert()`, `agentOneci()`, `agentMayelia()`
- Méthodes: `generateCodeBarre()`, `getStatutFormateAttribute()`

### 2.6 Service de génération de code-barres

**Fichier**: `app/Services/BarcodeService.php`

- Utiliser la bibliothèque `picqer/php-barcode-generator` (Code 128)
- Méthode `generateCode128(string $code): string` (retourne le code-barres en SVG/PNG)
- Méthode `generateCodeBarreForDossier(DossierOuvert $dossier): string`

### 2.7 Contrôleur `OneciTransferController`

**Fichier**: `app/Http/Controllers/OneciTransferController.php`

- `index()`: Liste des transferts avec filtres
- `create()`: Afficher les dossiers finalisés disponibles pour envoi
- `store()`: Créer un transfert, générer codes-barres, regrouper dossiers
- `show()`: Détails d'un transfert avec liste des dossiers
- `envoyer()`: Marquer le transfert comme envoyé
- `imprimerEtiquettes()`: Générer PDF avec codes-barres pour impression

### 2.8 Vue de regroupement

**Fichier**: `resources/views/oneci-transfers/create.blade.php`

- Liste des dossiers finalisés du jour (ou période sélectionnée)
- Sélection multiple de dossiers
- Bouton "Créer le transfert et générer les codes-barres"
- Aperçu des codes-barres avant impression

### 2.9 Vue liste des transferts

**Fichier**: `resources/views/oneci-transfers/index.blade.php`

- Tableau avec filtres (date, statut, centre)
- Colonnes: Code transfert, Date, Nombre de dossiers, Statut, Actions
- Bouton "Imprimer étiquettes" pour chaque transfert

## 3. Interface ONECI

### 3.1 Rôle ONECI

**Fichier**: `app/Models/User.php`

- Ajouter `'oneci'` dans les rôles possibles
- Scope `scopeOneci()` pour filtrer les agents ONECI

### 3.2 Migration - Table `notifications`

**Fichier**: `database/migrations/YYYY_MM_DD_create_notifications_table.php`

- `id`, `user_id`, `type` (oneci_carte_prete, mayelia_dossier_envoye, etc.)
- `title`, `message`, `data` (JSON), `read_at`, `timestamps`
- Index sur `user_id` et `read_at`

### 3.3 Modèle `Notification`

**Fichier**: `app/Models/Notification.php`

- Relation `user()`, scope `unread()`, méthode `markAsRead()`

### 3.4 Contrôleur ONECI

**Fichier**: `app/Http/Controllers/OneciController.php`

- `dashboard()`: Vue d'ensemble des dossiers reçus
- `dossiers()`: Liste des dossiers reçus avec filtres
- `scanner()`: Interface de scan de code-barres
- `scannerCode()`: API pour scanner un code-barres (POST)
- `marquerCartePrete()`: Marquer une carte comme prête après scan
- `dossiersCartesPrete()`: Liste des dossiers avec cartes prêtes

### 3.5 Vues ONECI

**Fichier**: `resources/views/oneci/dashboard.blade.php`

- Statistiques: Dossiers reçus, en traitement, cartes prêtes
- Liste récente des dossiers

**Fichier**: `resources/views/oneci/dossiers.blade.php`

- Tableau des dossiers avec filtres
- Colonnes: Code-barres, Client, Service, Date réception, Statut, Actions
- Bouton "Scanner code-barres" pour chaque dossier

**Fichier**: `resources/views/oneci/scanner.blade.php`

- Zone de scan (input pour code-barres ou webcam)
- Affichage des informations du dossier scanné
- Bouton "Marquer carte prête"

## 4. Système de scan de code-barres

### 4.1 API de scan

**Fichier**: `app/Http/Controllers/OneciController.php`

- Méthode `scannerCode(Request $request)`: 
- Valider le code-barres
- Trouver le `DossierOneciItem` correspondant
- Retourner les infos du dossier
- Vérifier que le dossier est bien reçu à l'ONECI

### 4.2 Marquer carte prête

**Fichier**: `app/Http/Controllers/OneciController.php`

- Méthode `marquerCartePrete(DossierOneciItem $item)`:
- Mettre à jour `statut = 'carte_prete'`
- Créer notification pour le centre Mayelia
- Mettre à jour `date_carte_prete`

### 4.3 JavaScript pour scan

**Fichier**: `resources/views/oneci/scanner.blade.php`

- Écouter les entrées clavier (scanner USB)
- Détection automatique du scan (entrée rapide)
- Validation et affichage des infos
- Option webcam pour QR code (si besoin plus tard)

## 5. Notifications

### 5.1 Service de notifications

**Fichier**: `app/Services/NotificationService.php`

- `notifyUser(User $user, string $type, string $title, string $message, array $data = [])`
- `notifyCentre(Centre $centre, string $type, string $title, string $message, array $data = [])`
- `notifyAgentMayelia(DossierOneciItem $item)`: Notifier quand carte prête

### 5.2 Événements et listeners

**Fichier**: `app/Events/CartePreteEvent.php`

- Événement déclenché quand une carte est marquée prête

**Fichier**: `app/Listeners/NotifyCentreCartePrete.php`

- Listener qui crée les notifications pour le centre

### 5.3 Vue notifications

**Fichier**: `resources/views/components/notifications.blade.php`

- Badge avec nombre de notifications non lues
- Dropdown avec liste des notifications
- Marquer comme lu au clic

**Fichier**: `resources/views/layouts/dashboard.blade.php`

- Intégrer le composant notifications dans le header

## 6. Récupération des cartes par Mayelia

### 6.1 Contrôleur récupération

**Fichier**: `app/Http/Controllers/OneciRecuperationController.php`

- `cartesPrete()`: Liste des cartes prêtes à récupérer
- `scannerRecuperation()`: Interface de scan pour récupération
- `scannerCodeRecuperation()`: API pour scanner lors de la récupération
- `confirmerRecuperation()`: Confirmer la récupération d'un dossier

### 6.2 Vue récupération

**Fichier**: `resources/views/oneci-recuperation/cartes-prete.blade.php`

- Liste des dossiers avec cartes prêtes
- Filtres par date, centre
- Bouton "Scanner pour récupération"

**Fichier**: `resources/views/oneci-recuperation/scanner.blade.php`

- Interface de scan pour récupération
- Affichage des infos du dossier
- Bouton "Confirmer récupération"

## 7. Notification finale au client

### 7.1 Service SMS (préparation)

**Fichier**: `app/Services/SmsService.php`

- Structure préparée pour intégration SMS future
- Méthode `sendSms(string $phone, string $message)`
- Pour l'instant, logger le message

### 7.2 Événement et listener

**Fichier**: `app/Events/CarteRecupereeEvent.php`

- Événement déclenché quand carte récupérée

**Fichier**: `app/Listeners/NotifyClientCartePrete.php`

- Listener qui envoie SMS au client (pour l'instant log)
- Message: "Votre carte est prête et récupérable à l'agence [nom]"

## 8. Routes

**Fichier**: `routes/web.php`

- Routes ONECI (middleware pour rôle oneci):
- `GET /oneci/dashboard`
- `GET /oneci/dossiers`
- `GET /oneci/scanner`
- `POST /oneci/scanner/code`
- `POST /oneci/dossiers/{item}/carte-prete`
- Routes transferts Mayelia:
- `GET /oneci-transfers`
- `GET /oneci-transfers/create`
- `POST /oneci-transfers`
- `GET /oneci-transfers/{transfer}`
- `POST /oneci-transfers/{transfer}/envoyer`
- `GET /oneci-transfers/{transfer}/imprimer-etiquettes`
- Routes récupération:
- `GET /oneci-recuperation/cartes-prete`
- `GET /oneci-recuperation/scanner`
- `POST /oneci-recuperation/scanner/code`
- `POST /oneci-recuperation/{item}/confirmer`

## 9. Permissions ONECI

**Fichier**: `database/seeders/PermissionSeeder.php`

- Ajouter permissions pour module `oneci`:
- `oneci.view`, `oneci.scan`, `oneci.marquer_carte_prete`
- `oneci-transfers.view`, `oneci-transfers.create`, `oneci-transfers.envoyer`
- `oneci-recuperation.view`, `oneci-recuperation.scan`, `oneci-recuperation.confirmer`

## 10. Fonctionnalités supplémentaires de synchronisation

### 10.1 Tableau de bord ONECI

- Statistiques: Dossiers reçus aujourd'hui, en traitement, cartes prêtes
- Graphique d'évolution
- Alertes pour dossiers en attente depuis X jours

### 10.2 Historique et traçabilité

**Fichier**: `resources/views/oneci-transfers/show.blade.php`

- Timeline complète: Envoi → Réception → Traitement → Carte prête → Récupération
- Dates et agents responsables à chaque étape
- Logs d'actions

### 10.3 Export et rapports

- Export Excel des transferts
- Rapport mensuel des dossiers traités
- Statistiques de délais de traitement

### 10.4 Recherche avancée

- Recherche par code-barres
- Recherche par nom client
- Recherche par numéro de dossier

## 11. Améliorations UI/UX

- Badge de notification dans le header
- Indicateurs visuels de statut (couleurs)
- Filtres rapides par statut
- Pagination pour les listes
- Responsive design pour scanner mobile

### To-dos

- [ ] Créer la migration pour la table permissions
- [ ] Créer la migration pour la table pivot agent_permissions
- [ ] Créer le modèle Permission avec relations
- [ ] Ajouter relations et méthodes de vérification de permissions au modèle Agent
- [ ] Créer le seeder avec toutes les permissions de base
- [ ] Créer AuthService pour gérer l'authentification unifiée Agent/User
- [ ] Créer le middleware CheckPermission
- [ ] Créer le trait ChecksPermissions pour les contrôleurs
- [ ] Adapter CentreController avec vérifications de permissions
- [ ] Adapter CreneauxController avec vérifications de permissions
- [ ] Adapter RendezVousController avec vérifications de permissions
- [ ] Adapter DossierWorkflowController pour bloquer la suppression
- [ ] Adapter ClientController avec vérifications de permissions
- [ ] Ajouter section permissions au formulaire de création agent
- [ ] Créer/modifier le formulaire de modification agent avec permissions
- [ ] Adapter AgentController pour gérer les permissions (store/update)
- [ ] Adapter la sidebar pour masquer/afficher selon les permissions
- [ ] Adapter les vues pour masquer les actions non autorisées
- [ ] Enregistrer le middleware dans bootstrap/app.php
- [ ] Tester le système de permissions avec différents scénarios d'agents