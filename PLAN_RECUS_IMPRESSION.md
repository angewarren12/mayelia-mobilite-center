# Plan : Types de Reçus et Impressions

## 📋 Liste des Types de Reçus/Impressions

### 1. **Reçu de Réservation (Booking)**
- **Fichier** : `resources/views/booking/receipt.blade.php`
- **Contrôleur** : `BookingController::downloadReceipt()`
- **Usage** : Reçu PDF après réservation d'un rendez-vous en ligne
- **Données** : RendezVous, Client, Service, Formule, QR Code
- **Format** : PDF A4 portrait

### 2. **Reçu de Dossier Finalisé**
- **Fichier** : `resources/views/agent/dossier/recu-pdf.blade.php`
- **Contrôleur** : `DossierWorkflowController::imprimerRecu()`
- **Usage** : Reçu PDF après finalisation d'un dossier
- **Données** : DossierOuvert, RendezVous, Client, Service, Formule, Paiement
- **Format** : PDF A4 portrait
- **Condition** : Dossier doit être finalisé

### 3. **Étiquette de Dossier**
- **Fichier** : `resources/views/dossiers/etiquette.blade.php`
- **Contrôleur** : `DossierController::imprimerEtiquette()`
- **Usage** : Étiquette avec code-barres pour un dossier
- **Données** : DossierOuvert, Code-barres
- **Format** : Vue HTML (peut être imprimée)
- **Caractéristiques** : Code-barres, Informations dossier

### 4. **Ticket QMS (File d'attente)**
- **Fichier** : `resources/views/qms/ticket-print.blade.php`
- **Contrôleur** : `QmsController::printTicket()`
- **Usage** : Ticket thermique pour la file d'attente
- **Données** : Ticket, Centre, Service
- **Format** : 58mm (ticket thermique)
- **Caractéristiques** : Numéro ticket, QR Code, Informations centre

### 5. **Étiquettes ONECI Transfert**
- **Fichier** : `resources/views/oneci-transfers/etiquettes.blade.php`
- **Contrôleur** : `OneciTransferController::imprimerEtiquettes()`
- **Usage** : Étiquettes avec codes-barres pour transfert ONECI
- **Données** : DossierOneciTransfer, Items avec codes-barres
- **Format** : Vue HTML (peut être imprimée)
- **Caractéristiques** : Codes-barres multiples, Informations transfert

---

## 🎨 Améliorations à Apporter

### Design Unifié
- Créer un style cohérent pour tous les reçus
- Utiliser les couleurs de la marque Mayelia
- Standardiser les polices et tailles

### Structure de Données
- Créer des classes/services pour générer des données de test
- Uniformiser les formats de dates, montants, etc.

### Responsive Design
- S'assurer que les reçus s'adaptent à différents formats d'impression
- Optimiser pour impression PDF et thermique

### Codes-barres et QR Codes
- Standardiser la génération des codes-barres
- Améliorer la lisibilité des QR codes

### Informations Obligatoires
- Vérifier que tous les reçus contiennent les informations légales
- Ajouter les coordonnées du centre, numéros de téléphone, etc.

---

## 🧪 Page de Test HTML

Créer une page HTML avec :
- Un bouton pour chaque type de reçu
- Génération de données aléatoires mais réalistes
- Prévisualisation dans une nouvelle fenêtre
- Même design que les vrais reçus


