# Workflow des Modes QMS - Application Kiosk

Ce document décrit les workflows complets pour les deux modes de gestion de file d'attente jusqu'à l'impression du ticket.

---

## 📋 MODE FIFO (Premier Arrivé, Premier Servi)

### Étape 1 : Initialisation
1. ✅ L'application démarre
2. ✅ Chargement des informations du centre depuis l'API (`/api/qms/centre/{id}`)
3. ✅ Récupération du `qms_mode = 'fifo'`
4. ✅ Chargement des services actifs du centre

### Étape 2 : Page d'Accueil
**Interface affichée :**
- 🎯 **1 seule carte** : "PRENDRE UN TICKET"
- 📝 Texte : "Ticket pour service standard"
- 📐 Layout : 1 colonne (pleine largeur)

### Étape 3 : Sélection du Service
**Deux scénarios possibles :**

#### Scénario A : Un seul service disponible
1. ✅ Clic sur "PRENDRE UN TICKET"
2. ✅ **Pas de sélection** : Création directe du ticket
3. ⏭️ **Passe directement à l'impression**

#### Scénario B : Plusieurs services disponibles
1. ✅ Clic sur "PRENDRE UN TICKET"
2. ✅ **Affichage de la sélection de services** (grille avec tous les services)
3. ✅ Clic sur un service
4. ✅ Création du ticket avec le service sélectionné

### Étape 4 : Création du Ticket
1. ✅ Appel API `POST /api/qms/tickets` avec :
   ```json
   {
     "centre_id": 1,
     "type": "sans_rdv",
     "service_id": 2
   }
   ```
2. ✅ Réception du ticket créé avec :
   - `numero` : Numéro du ticket (ex: "D001")
   - `id` : ID du ticket
   - `service_id` : ID du service
   - `priorite` : Priorité calculée
   - `statut` : "en_attente"

### Étape 5 : Confirmation & Impression
1. ✅ Affichage de l'écran de confirmation avec :
   - Numéro du ticket (ex: "D001")
   - Nom du service
   - Nom du centre
   - QR Code du ticket
2. ✅ **Impression automatique** du ticket via Bluetooth
3. ✅ Retour à l'accueil après 3 secondes

---

## 📋 MODE FENÊTRE DE TOLÉRANCE

### Étape 1 : Initialisation
1. ✅ L'application démarre
2. ✅ Chargement des informations du centre depuis l'API (`/api/qms/centre/{id}`)
3. ✅ Récupération du `qms_mode = 'fenetre_tolerance'`
4. ✅ Chargement des services actifs du centre

### Étape 2 : Page d'Accueil
**Interface affichée :**
- 🎯 **2 cartes** :
  1. **"SANS RENDEZ-VOUS"** (File d'attente standard)
  2. **"J'AI UN RENDEZ-VOUS"** (Scanner ou saisir numéro)
- 📐 Layout : 2 colonnes (grid)

---

### 🔵 WORKFLOW A : Sans Rendez-Vous

#### Étape A.1 : Clic sur "SANS RENDEZ-VOUS"
1. ✅ Clic sur la carte "SANS RENDEZ-VOUS"

#### Étape A.2 : Sélection du Service
**Deux scénarios possibles :**

##### Scénario A.2.1 : Un seul service disponible
1. ✅ **Pas de sélection** : Création directe du ticket
2. ⏭️ **Passe directement à la création**

##### Scénario A.2.2 : Plusieurs services disponibles
1. ✅ **Affichage de la sélection de services** (grille avec tous les services)
2. ✅ Clic sur un service
3. ✅ Création du ticket avec le service sélectionné

#### Étape A.3 : Création du Ticket
1. ✅ Appel API `POST /api/qms/tickets` avec :
   ```json
   {
     "centre_id": 1,
     "type": "sans_rdv",
     "service_id": 2
   }
   ```
2. ✅ Réception du ticket créé

#### Étape A.4 : Confirmation & Impression
1. ✅ Affichage de l'écran de confirmation
2. ✅ **Impression automatique** du ticket
3. ✅ Retour à l'accueil après 3 secondes

---

### 🟣 WORKFLOW B : Avec Rendez-Vous

#### Étape B.1 : Clic sur "J'AI UN RENDEZ-VOUS"
1. ✅ Clic sur la carte "J'AI UN RENDEZ-VOUS"

#### Étape B.2 : Saisie du Numéro d je veu e RDV
**Interface affichée :**
- 📱 Clavier virtuel numérique (0-9)
- 🔤 Préfixe affiché : `MAYELIA-2025-` (fixe)
- ⌨️ Saisie : Les 6 chiffres du numéro de RDV
  - Exemple : Si le RDV est `MAYELIA-2025-123456`
  - L'utilisateur saisit : `123456`

#### Étape B.3 : Vérification du RDV
1. ✅ Clic sur "Valider" ou touche ✓
2. ✅ Construction automatique : `MAYELIA-2025-[chiffres saisis]`
3. ✅ Appel API `POST /api/qms/check-rdv` avec :
   ```json
   {
     "numero": "MAYELIA-2025-123456",
     "centre_id": 1
   }
   ```
4. ✅ Vérifications côté serveur :
   - Le RDV existe avec ce numéro
   - Le RDV est pour le centre 1
   - Le RDV est pour aujourd'hui
5. ✅ Réponse API :
   ```json
   {
     "success": true,
     "rdv": {
       "id": 123,
       "client_nom": "DUPONT Jean",
       "heure": "14:30",
       "service_id": 2
     }
   }
   ```

#### Étape B.4 : Création du Ticket avec RDV
1. ✅ Si le RDV est valide :
   - Appel API `POST /api/qms/tickets` avec :
     ```json
     {
       "centre_id": 1,
       "type": "rdv",
       "service_id": 2,
       "numero_rdv": "MAYELIA-2025-123456"
     }
     ```
2. ✅ Si le RDV est invalide :
   - Affichage d'un message d'erreur
   - Retour à la saisie du numéro

#### Étape B.5 : Confirmation & Impression
1. ✅ Affichage de l'écran de confirmation avec :
   - Numéro du ticket (ex: "D001")
   - Nom du service
   - Nom du centre
   - Heure du RDV (si applicable)
   - QR Code du ticket
2. ✅ **Impression automatique** du ticket
3. ✅ Retour à l'accueil après 3 secondes

---

## 🖨️ Impression du Ticket

### Format du Ticket Imprimé
```
═══════════════════════════════════════════
         MAYELIA MOBILITE CENTER
═══════════════════════════════════════════

TICKET: D001
DATE: 10/09/2025 14:25

CENTRE: Centre Mayelia San-Pedro
SERVICE: Demande de CNI

[TYPE: SANS RDV / AVEC RDV]
[HEURE RDV: 14:30] (si applicable)

[QR CODE]

Merci de votre visite !
═══════════════════════════════════════════
```

### Processus d'Impression
1. ✅ Génération des commandes ESC/POS
2. ✅ Connexion Bluetooth à l'imprimante configurée
3. ✅ Envoi des données au format ESC/POS
4. ✅ Impression du ticket
5. ✅ Gestion des erreurs (imprimante non disponible, etc.)

---

## 🔄 Retour à l'Accueil

- ⏱️ **Après impression** : Retour automatique après 3 secondes
- 🔄 **Réinitialisation** : Tous les états sont réinitialisés
- 🏠 **Affichage** : Retour à la page d'accueil avec les boutons selon le mode

---

## ⚠️ Gestion des Erreurs

### Erreurs possibles :
1. ❌ **Erreur de connexion API**
   - Message : "Erreur de connexion"
   - Action : Possibilité de réessayer

2. ❌ **Aucun service disponible**
   - Message : "Aucun service disponible pour ce centre"
   - Action : Contacter l'administrateur

3. ❌ **RDV introuvable**
   - Message : "Rendez-vous introuvable pour aujourd'hui"
   - Action : Vérifier le numéro ou contacter le centre

4. ❌ **Erreur d'impression**
   - Message : Erreur silencieuse (log)
   - Action : L'utilisateur peut continuer (le ticket est créé en base)

---

## 📊 Résumé des Différences

| Aspect | Mode FIFO | Mode Fenêtre |
|--------|-----------|--------------|
| **Boutons accueil** | 1 bouton | 2 boutons |
| **Texte bouton 1** | "PRENDRE UN TICKET" | "SANS RENDEZ-VOUS" |
| **Bouton RDV** | ❌ Non affiché | ✅ "J'AI UN RENDEZ-VOUS" |
| **Layout** | 1 colonne | 2 colonnes |
| **Workflow RDV** | ❌ Non disponible | ✅ Saisie numéro → Vérification → Ticket |
| **Priorité tickets** | Ordre d'arrivée | RDV dans fenêtre > Sans RDV |

