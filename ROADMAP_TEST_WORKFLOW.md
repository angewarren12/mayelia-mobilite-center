# 🧪 ROADMAP DE TEST - Workflow Dossier Client

## 📋 Prérequis

### 1. Configuration de l'environnement
```bash
# Vérifier que le serveur est lancé
php artisan serve

# Vérifier que le stockage est lié
php artisan storage:link

# Vérifier les permissions du dossier storage
# Windows PowerShell:
icacls storage /grant Everyone:F /T
```

### 2. Données de test nécessaires
- ✅ Un compte agent actif
- ✅ Un rendez-vous avec statut "confirmé"
- ✅ Des documents requis configurés pour le service
- ✅ Un client avec informations complètes

---

## 🎯 PHASE 1 : Tests de Base (30 min)

### Test 1.1 : Accès au Workflow
**Objectif** : Vérifier que la page du workflow s'affiche correctement

**Étapes** :
1. Se connecter en tant qu'agent
2. Aller dans "Rendez-vous" → Sélectionner un rendez-vous "confirmé"
3. Cliquer sur "Ouvrir le dossier" ou accéder via `/dossier/{id}/workflow`

**Résultat attendu** :
- ✅ La page s'affiche sans erreur
- ✅ Les 4 étapes sont visibles (Fiche, Documents, Infos Client, Paiement)
- ✅ La barre de progression affiche 0%
- ✅ Toutes les étapes ont le statut "En attente"
- ✅ Les informations du client et du rendez-vous sont affichées

**Capture d'écran** : `test_1.1_page_workflow.png`

---

### Test 1.2 : Validation Étape 1 - Fiche de Pré-enrôlement
**Objectif** : Valider la première étape

**Étapes** :
1. Cliquer sur "Vérifier la fiche"
2. Ajouter un commentaire (optionnel) : "Fiche conforme"
3. Cliquer sur "Valider la fiche"

**Résultat attendu** :
- ✅ Toast de succès : "Fiche pré-enrôlement validée avec succès"
- ✅ Le statut de l'étape 1 passe à "Vérifiée" (badge vert)
- ✅ La barre de progression passe à 25%
- ✅ Le bouton devient gris et désactivé
- ✅ Message de confirmation affiché

**Vérifications backend** :
```sql
-- Vérifier dans la base de données
SELECT * FROM dossier_ouvert WHERE id = [ID_DOSSIER];
-- fiche_pre_enrolement_verifiee doit être = 1

SELECT * FROM dossier_actions_log WHERE dossier_ouvert_id = [ID_DOSSIER] ORDER BY created_at DESC LIMIT 1;
-- action doit être = 'fiche_verifiee'
```

**Capture d'écran** : `test_1.2_etape1_validee.png`

---

### Test 1.3 : Validation Étape 2 - Documents (SANS upload)
**Objectif** : Valider les documents sans uploader de fichiers

**Étapes** :
1. Cliquer sur "Vérifier les documents"
2. Sélectionner le type de demande : "Première demande"
3. Cocher les documents présents (ex: Passeport, Photo d'identité)
4. **NE PAS** cliquer sur "Ajouter fichier"
5. Cliquer sur "Valider les documents"

**Résultat attendu** :
- ✅ Toast de succès
- ✅ Le statut de l'étape 2 passe à "Vérifiés" ou "Manquants" selon les documents
- ✅ La barre de progression passe à 50%
- ✅ Les résultats s'affichent dans la carte :
  - Documents présents (liste verte)
  - Documents manquants (liste rouge)
- ✅ Le type de demande est affiché

**Vérifications backend** :
```sql
SELECT * FROM document_verification WHERE dossier_ouvert_id = [ID_DOSSIER];
-- Vérifier que present = 1 pour les docs cochés
-- nom_fichier doit être NULL (pas d'upload)
```

**Capture d'écran** : `test_1.3_etape2_sans_upload.png`

---

### Test 1.4 : Validation Étape 2 - Documents (AVEC upload)
**Objectif** : Valider les documents avec upload de fichiers

**Étapes** :
1. Réinitialiser l'étape 2 (ou créer un nouveau dossier)
2. Cliquer sur "Vérifier les documents"
3. Sélectionner "Première demande"
4. Cocher "Passeport"
5. **Cliquer sur le bouton "Ajouter fichier"** à côté de Passeport
6. Sélectionner un fichier PDF de test (< 10MB)
7. Vérifier que le bouton devient vert "Fichier ajouté"
8. Répéter pour un autre document avec une image JPG
9. Cliquer sur "Valider les documents"

**Résultat attendu** :
- ✅ Les zones d'upload apparaissent/disparaissent correctement
- ✅ Le bouton change de couleur (bleu → vert)
- ✅ Toast de succès
- ✅ Les fichiers sont uploadés
- ✅ Les résultats affichent "fichier_uploade: true"

**Vérifications backend** :
```sql
SELECT nom_fichier, chemin_fichier, taille_fichier, type_mime 
FROM document_verification 
WHERE dossier_ouvert_id = [ID_DOSSIER] AND present = 1;
-- Vérifier que les fichiers sont enregistrés
```

**Vérifications fichiers** :
```bash
# Vérifier que les fichiers existent
ls storage/app/public/dossiers/[ID_DOSSIER]/documents/
```

**Capture d'écran** : `test_1.4_etape2_avec_upload.png`

---

### Test 1.5 : Suppression de Fichier
**Objectif** : Tester la suppression d'un fichier avant validation

**Étapes** :
1. Dans le modal documents, cliquer sur "Ajouter fichier"
2. Sélectionner un fichier
3. Cliquer sur le bouton ❌ (croix rouge)

**Résultat attendu** :
- ✅ La zone d'upload se cache
- ✅ Le bouton redevient bleu "Ajouter fichier"
- ✅ Le champ file est réinitialisé

**Capture d'écran** : `test_1.5_suppression_fichier.png`

---

### Test 1.6 : Validation Étape 3 - Informations Client (R.A.S)
**Objectif** : Valider sans modification

**Étapes** :
1. Cliquer sur "Modifier les informations"
2. Cliquer sur "R.A.S" (sans modifier les champs)

**Résultat attendu** :
- ✅ Toast : "Informations client validées (R.A.S)"
- ✅ Le statut passe à "Complétées"
- ✅ La barre de progression passe à 75%
- ✅ Les informations client s'affichent dans la carte

**Vérifications backend** :
```sql
SELECT * FROM dossier_actions_log 
WHERE dossier_ouvert_id = [ID_DOSSIER] AND action = 'infos_client_verifiees';
-- description doit contenir "R.A.S"
```

**Capture d'écran** : `test_1.6_etape3_ras.png`

---

### Test 1.7 : Validation Étape 3 - Avec Modifications
**Objectif** : Modifier les informations client

**Étapes** :
1. Réinitialiser l'étape 3 (ou nouveau dossier)
2. Cliquer sur "Modifier les informations"
3. Modifier le téléphone : "+225 07 XX XX XX XX"
4. Modifier la CNI : "CI123456789"
5. Cliquer sur "Valider les modifications"

**Résultat attendu** :
- ✅ Toast : "Informations client mises à jour avec succès"
- ✅ Les nouvelles informations s'affichent dans la carte
- ✅ La progression passe à 75%

**Vérifications backend** :
```sql
SELECT telephone, numero_piece_identite FROM clients WHERE id = [ID_CLIENT];
-- Vérifier que les modifications sont enregistrées

SELECT * FROM dossier_actions_log 
WHERE dossier_ouvert_id = [ID_DOSSIER] AND action = 'infos_client_maj';
```

**Capture d'écran** : `test_1.7_etape3_modif.png`

---

### Test 1.8 : Validation Étape 4 - Paiement
**Objectif** : Valider le paiement

**Étapes** :
1. Cliquer sur "Vérifier le paiement"
2. Remplir les champs :
   - Référence : "REF-2024-001234"
   - Montant : "50000"
   - Mode : "Mobile Money"
3. (Optionnel) Uploader un reçu
4. Cliquer sur "Valider le paiement"

**Résultat attendu** :
- ✅ Toast : "Paiement validé avec succès"
- ✅ Le statut passe à "Vérifié"
- ✅ La barre de progression passe à 100%
- ✅ Les détails du paiement s'affichent

**Vérifications backend** :
```sql
SELECT * FROM paiement_verification WHERE dossier_ouvert_id = [ID_DOSSIER];
-- Vérifier montant_paye, mode_paiement, reference_paiement
```

**Capture d'écran** : `test_1.8_etape4_paiement.png`

---

### Test 1.9 : Finalisation du Dossier
**Objectif** : Finaliser le dossier complet

**Étapes** :
1. Vérifier que les 4 étapes sont validées (100%)
2. Cliquer sur "Finaliser le dossier"
3. Attendre le traitement

**Résultat attendu** :
- ✅ Modal de chargement s'affiche
- ✅ Modal de succès : "Dossier finalisé avec succès !"
- ✅ Le statut du dossier devient "Finalisé" (badge vert)
- ✅ Le bouton "Imprimer le reçu" apparaît

**Vérifications backend** :
```sql
SELECT statut FROM dossier_ouvert WHERE id = [ID_DOSSIER];
-- statut doit être = 'finalise'

SELECT statut FROM rendez_vous WHERE id = [ID_RDV];
-- statut doit être = 'finalise'
```

**Capture d'écran** : `test_1.9_finalisation.png`

---

### Test 1.10 : Impression du Reçu
**Objectif** : Générer le PDF du reçu

**Étapes** :
1. Cliquer sur "Imprimer le reçu"

**Résultat attendu** :
- ✅ Un PDF se télécharge
- ✅ Le PDF contient toutes les informations du dossier
- ✅ Le nom du fichier : `recu-mayelia-dossier-[ID]-[DATE].pdf`

**Capture d'écran** : `test_1.10_recu_pdf.png`

---

## 🔍 PHASE 2 : Tests de la Timeline (15 min)

### Test 2.1 : Vérification de la Timeline
**Objectif** : Vérifier que toutes les actions sont loggées

**Étapes** :
1. Aller sur la page du dossier finalisé
2. Scroller jusqu'à la section "Historique du dossier"

**Résultat attendu** :
- ✅ Toutes les actions sont affichées dans l'ordre chronologique :
  1. Dossier ouvert
  2. Fiche vérifiée
  3. Documents vérifiés (ou incomplets)
  4. Informations client validées (ou mises à jour)
  5. Paiement vérifié
  6. Dossier finalisé
- ✅ Chaque action a :
  - Une icône appropriée
  - Une couleur correspondante
  - Un timestamp
  - Le nom de l'agent
  - Une description
  - Les données additionnelles (si présentes)

**Capture d'écran** : `test_2.1_timeline_complete.png`

---

### Test 2.2 : Vérification des Données de Log
**Objectif** : Vérifier que les métadonnées sont correctes

**Étapes** :
1. Dans la timeline, vérifier l'action "Documents vérifiés"
2. Cliquer pour déplier les données additionnelles

**Résultat attendu** :
- ✅ Les données JSON affichent :
  - `type_demande`: "Première demande"
  - `documents_manquants`: [liste]
  - `documents_uploades`: nombre

**Capture d'écran** : `test_2.2_log_data.png`

---

## ⚠️ PHASE 3 : Tests d'Erreurs (20 min)

### Test 3.1 : Validation Sans Type de Demande
**Étapes** :
1. Ouvrir le modal documents
2. Cliquer sur "Valider" sans sélectionner de type

**Résultat attendu** :
- ✅ Toast d'erreur : "Veuillez sélectionner un type de demande"
- ✅ Le modal reste ouvert

---

### Test 3.2 : Upload de Fichier Trop Gros
**Étapes** :
1. Essayer d'uploader un fichier > 10MB

**Résultat attendu** :
- ✅ Erreur de validation
- ✅ Message d'erreur clair

---

### Test 3.3 : Upload de Mauvais Format
**Étapes** :
1. Essayer d'uploader un fichier .docx ou .exe

**Résultat attendu** :
- ✅ Le fichier n'est pas accepté par le champ file
- ✅ Ou erreur de validation si accepté

---

### Test 3.4 : Finalisation Incomplète
**Étapes** :
1. Créer un nouveau dossier
2. Valider seulement l'étape 1
3. Essayer de finaliser

**Résultat attendu** :
- ✅ Toast d'erreur : "Veuillez valider toutes les étapes..."
- ✅ Liste des étapes manquantes affichée

---

### Test 3.5 : Accès Non Autorisé
**Étapes** :
1. Se connecter avec un autre agent
2. Essayer d'accéder au dossier d'un autre agent

**Résultat attendu** :
- ✅ Erreur 403 : "Vous ne pouvez pas gérer ce dossier"

---

## 📊 PHASE 4 : Tests de Performance (10 min)

### Test 4.1 : Upload Multiple
**Étapes** :
1. Uploader 5 documents différents en même temps

**Résultat attendu** :
- ✅ Tous les fichiers sont uploadés correctement
- ✅ Pas de timeout
- ✅ Temps de réponse < 5 secondes

---

### Test 4.2 : Gros Fichier PDF
**Étapes** :
1. Uploader un PDF de 9MB

**Résultat attendu** :
- ✅ L'upload fonctionne
- ✅ Le fichier est bien stocké
- ✅ Temps raisonnable (< 10 secondes)

---

## 🎨 PHASE 5 : Tests UI/UX (15 min)

### Test 5.1 : Responsive Design
**Étapes** :
1. Tester sur mobile (F12 → mode responsive)
2. Tester sur tablette
3. Tester sur desktop

**Résultat attendu** :
- ✅ Toutes les cartes s'adaptent
- ✅ Les modals sont utilisables
- ✅ Pas de débordement horizontal

---

### Test 5.2 : Animations et Transitions
**Étapes** :
1. Observer les transitions lors des validations
2. Observer les toasts
3. Observer les changements de couleur

**Résultat attendu** :
- ✅ Animations fluides
- ✅ Pas de clignotement
- ✅ Feedback visuel clair

---

## 📝 CHECKLIST FINALE

### Fonctionnalités Core
- [ ] Étape 1 : Validation fiche
- [ ] Étape 2 : Validation documents (sans upload)
- [ ] Étape 2 : Validation documents (avec upload)
- [ ] Étape 2 : Upload facultatif fonctionne
- [ ] Étape 2 : Suppression de fichier fonctionne
- [ ] Étape 3 : Validation R.A.S
- [ ] Étape 3 : Modification informations
- [ ] Étape 4 : Validation paiement
- [ ] Finalisation du dossier
- [ ] Impression du reçu

### Timeline
- [ ] Toutes les actions sont loggées
- [ ] Icônes correctes
- [ ] Couleurs appropriées
- [ ] Timestamps affichés
- [ ] Données additionnelles présentes

### Gestion des Erreurs
- [ ] Validation sans type de demande
- [ ] Fichier trop gros
- [ ] Mauvais format
- [ ] Finalisation incomplète
- [ ] Accès non autorisé

### Performance
- [ ] Upload multiple
- [ ] Gros fichiers
- [ ] Temps de réponse acceptable

### UI/UX
- [ ] Responsive
- [ ] Animations fluides
- [ ] Feedback visuel clair

---

## 🐛 RAPPORT DE BUGS

### Format de rapport
Pour chaque bug trouvé, noter :
```
BUG #[numéro]
Titre: [Description courte]
Étape: [Quelle étape du test]
Reproduction:
1. [Étape 1]
2. [Étape 2]
3. [Étape 3]

Résultat attendu: [Ce qui devrait se passer]
Résultat obtenu: [Ce qui s'est passé]
Capture d'écran: [Nom du fichier]
Priorité: [Haute/Moyenne/Basse]
```

---

## ⏱️ TEMPS ESTIMÉ TOTAL : ~90 minutes

- Phase 1 (Tests de base) : 30 min
- Phase 2 (Timeline) : 15 min
- Phase 3 (Erreurs) : 20 min
- Phase 4 (Performance) : 10 min
- Phase 5 (UI/UX) : 15 min

---

## 📞 SUPPORT

En cas de problème :
1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier la console JavaScript (F12)
3. Vérifier la base de données
4. Vérifier les permissions des fichiers

**Bon test ! 🚀**
