# 🖨️ Guide : Impression Automatique sur Tablette Android

## 🎯 Objectif
Configurer votre tablette Android pour que **quand le client clique sur "Imprimer ticket", le ticket sorte automatiquement de l'imprimante Bluetooth SANS popup**.

---

## 📋 Prérequis

- **Tablette Android** (version 8.0 ou supérieure recommandée)
- **Imprimante thermique Bluetooth 58mm** (compatible dlabel)
- **Application dlabel** (téléchargeable via QR code du manuel ou Play Store)
- **Connexion Internet** (WiFi ou données mobiles)
- **Google Chrome** installé sur la tablette

> 💡 **Astuce** : Le manuel de l'imprimante contient un QR code pour télécharger directement l'application dlabel. C'est l'application officielle recommandée pour cette imprimante.

---

## 📱 Étapes de Configuration

### Étape 1 : Appairer l'Imprimante Bluetooth

1. **Allumer l'imprimante thermique**
   - Appuyer sur le bouton d'alimentation
   - L'indicateur Bluetooth doit clignoter

2. **Sur la tablette Android** :
   - Ouvrir **Paramètres** → **Bluetooth**
   - Activer le Bluetooth si nécessaire
   - Chercher l'imprimante dans la liste (nom généralement "BT Printer", "Mobile Printer" ou similaire)
   - Appuyer sur l'imprimante pour l'appairer
   - Code PIN si demandé : généralement `0000`, `1234`, ou `8888`

3. **Vérifier la connexion** :
   - L'indicateur Bluetooth de l'imprimante doit être fixe (pas de clignotement)
   - L'imprimante doit apparaître comme "Connecté" dans les paramètres Bluetooth

---

### Étape 2 : Installer l'Application dlabel

1. **Scanner le QR code du manuel** :
   - Utiliser votre téléphone/tablette pour scanner le QR code dans le manuel de l'imprimante
   - Ou aller directement sur le Play Store et chercher **"dlabel"**
   - L'application dlabel est l'application officielle pour cette imprimante thermique

2. **Installer dlabel** :
   - Télécharger et installer l'application depuis le Play Store
   - Ouvrir l'application après installation

3. **Configurer dlabel avec votre imprimante** :
   - Ouvrir l'application dlabel
   - Aller dans les paramètres ou la section "Imprimantes"
   - Activer le Bluetooth si nécessaire
   - Sélectionner votre imprimante thermique dans la liste des appareils Bluetooth
   - Appairer l'imprimante si demandé
   - Tester une impression de test depuis l'app

⚠️ **Important** : L'application dlabel est nécessaire pour que l'impression fonctionne depuis le navigateur. Sans cette app, Android ne pourra pas trouver l'imprimante lors de l'impression.

---

### Étape 3 : Installer Chrome et Configurer en Mode Kiosk

#### Option A : Utiliser Chrome en Mode Application (Recommandé)

1. **Ouvrir Chrome sur la tablette**

2. **Aller sur votre page de kiosk** :
   - Entrer l'URL : `http://votre-serveur.com/qms/kiosk/1`
   - Remplacer par l'URL de votre serveur

3. **Ajouter à l'écran d'accueil** :
   - Appuyer sur le menu (3 points en haut à droite)
   - Sélectionner **"Ajouter à l'écran d'accueil"** ou **"Installer l'application"**
   - Donner un nom : "Borne Tickets"
   - Appuyer sur **"Ajouter"** ou **"Installer"**

4. **Configurer le mode plein écran** :
   - Ouvrir l'application installée
   - L'application devrait s'ouvrir en plein écran automatiquement
   - Si non, Chrome devrait ouvrir en mode application

#### Option B : Utiliser une Application Kiosk (Pour environnement contrôlé)

Si vous voulez un mode kiosk verrouillé (pour empêcher les clients de quitter l'application) :

1. **Installer une app Kiosk** :
   - Play Store : Chercher **"Kiosk Browser Lockdown"** ou **"Screen Pinning"**
   - Ou utiliser la fonctionnalité native Android : **Épinglage d'écran**

2. **Activer l'épinglage d'écran Android** :
   - **Paramètres** → **Sécurité** → **Épinglage d'écran**
   - Activer l'option
   - Ouvrir Chrome avec votre page de kiosk
   - Appuyer sur le bouton récent (carré)
   - Appuyer sur l'icône d'épingle sur la fenêtre Chrome
   - Maintenant l'écran est épinglé et les utilisateurs ne peuvent pas quitter

---

### Étape 4 : Configurer dlabel comme Service d'Impression

1. **Vérifier que dlabel est bien configurée** :
   - Ouvrir dlabel
   - Vérifier que votre imprimante est bien connectée et visible
   - L'imprimante doit apparaître comme "Connecté" ou "Ready" dans l'app

2. **Configurer dlabel pour recevoir les impressions** :
   - Dans dlabel, aller dans les paramètres
   - Activer l'option "Recevoir les impressions" ou "Service d'impression" (si disponible)
   - dlabel doit rester ouverte en arrière-plan pour recevoir les impressions

3. **Configurer Chrome pour utiliser dlabel** :
   - Ouvrir Chrome
   - Aller dans `chrome://settings/printing` (si disponible sur Android)
   - Ou simplement utiliser le système de partage Android
   - Lors de `window.print()`, Android proposera dlabel dans la liste de partage

⚠️ **Important** : Sur Android, quand vous utilisez `window.print()`, le système affiche le menu de partage Android. dlabel doit apparaître dans cette liste comme option d'impression.

---

### Étape 5 : Tester l'Impression Automatique

1. **Ouvrir votre application Kiosk** sur la tablette

2. **Tester l'impression** :
   - Cliquer sur "Sans rendez-vous" (ou "Avec rendez-vous")
   - Sélectionner un service
   - Cliquer sur "Imprimer ticket"

3. **Résultat attendu** :
   - Une popup de partage Android apparaît
   - **dlabel** doit apparaître dans la liste des options
   - Sélectionner **dlabel** dans la liste
   - Appuyer sur "Partager" ou "Imprimer"
   - Le ticket doit sortir de l'imprimante

4. **Pour les impressions suivantes** :
   - Android mémorise généralement votre choix
   - Après la première sélection de dlabel, il peut être pré-sélectionné
   - Si dlabel est définie comme défaut, l'impression peut être plus rapide

5. **Si dlabel n'apparaît pas dans la liste** :
   - Vérifier que dlabel est bien installée et ouverte
   - Vérifier que l'imprimante est connectée dans dlabel
   - Redémarrer dlabel
   - Redémarrer la tablette si nécessaire

---

## 🔧 Solutions Alternatives pour Android

### Option 1 : Application Android Native (Solution Professionnelle)

Pour une solution complètement automatique, vous pouvez créer une application Android native qui :
- Se connecte directement à l'imprimante via Bluetooth
- Envoie les commandes ESC/POS directement
- Ne nécessite aucune popup

**Avantages** :
- ✅ Impression 100% automatique
- ✅ Pas de popup
- ✅ Contrôle total

**Inconvénients** :
- ❌ Nécessite le développement d'une app Android
- ❌ Plus complexe à maintenir

### Option 2 : PWA avec Service Worker

Créer une Progressive Web App (PWA) qui peut utiliser les APIs Android :

**Avantages** :
- ✅ Fonctionne comme une app native
- ✅ Plus facile à développer qu'une app native
- ✅ Mise à jour automatique

### Option 3 : Utiliser une Application Kiosk Dédiée

Installer une application de kiosk qui gère l'impression :

- **Kiosk Browser Lockdown** (sur Play Store)
- **SureLock Kiosk** (solution payante professionnelle)
- **Android Enterprise** (pour entreprises)

---

## ❌ Problèmes Courants et Solutions

### Problème 1 : dlabel n'apparaît pas dans la liste de partage

**Solutions** :
1. Vérifier que dlabel est bien installée depuis le Play Store (ou via le QR code du manuel)
2. Ouvrir dlabel et vérifier qu'elle est bien configurée avec l'imprimante
3. Vérifier que dlabel est ouverte (pas fermée en arrière-plan)
4. Redémarrer dlabel si nécessaire
5. Vérifier dans les paramètres de dlabel qu'elle accepte les impressions externes
6. Redémarrer la tablette si le problème persiste

### Problème 2 : La popup de partage apparaît toujours

**Solutions** :
1. **C'est normal sur Android** - une popup de partage apparaît toujours
2. Sélectionner dlabel dans la liste une première fois
3. Android peut mémoriser votre choix pour les prochaines fois
4. Si Android propose de "toujours utiliser cette application", accepter pour automatiser

### Problème 3 : L'impression ne fonctionne pas après avoir sélectionné dlabel

**Solutions** :
1. Vérifier dans dlabel que l'imprimante est bien connectée (statut "Connecté" ou "Ready")
2. Tester une impression directement depuis dlabel pour vérifier la connexion
3. Vérifier que l'imprimante est allumée et à portée Bluetooth (< 10 mètres)
4. Vérifier que la batterie de l'imprimante est chargée
5. Redémarrer la connexion Bluetooth si nécessaire

### Problème 3 : Le format du ticket est incorrect

**Solutions** :
1. Dans l'application de l'imprimante, configurer le format à 58mm
2. Vérifier les paramètres d'impression dans Chrome
3. Le CSS de la page devrait gérer le format automatiquement

### Problème 4 : L'impression est très lente

**Solutions** :
1. Vérifier la distance entre la tablette et l'imprimante (moins de 10 mètres)
2. Vérifier qu'il n'y a pas d'interférence Bluetooth
3. Vérifier que la batterie de l'imprimante est chargée

---

## ✅ Checklist de Configuration Android

Avant de mettre la borne en production :

- [ ] Imprimante allumée et appairée en Bluetooth
- [ ] Application **dlabel** installée (via QR code du manuel ou Play Store)
- [ ] dlabel configurée avec l'imprimante Bluetooth connectée
- [ ] Chrome installé et à jour
- [ ] Application Kiosk installée (PWA ajoutée à l'écran d'accueil)
- [ ] Épinglage d'écran activé pour le mode kiosk
- [ ] Test d'impression réussi (dlabel apparaît dans le menu de partage)
- [ ] Format du ticket correct (58mm)
- [ ] QR code visible sur le ticket
- [ ] Tablette configurée pour ne pas se mettre en veille
- [ ] Alimentation de la tablette branchée (si possible)
- [ ] dlabel laissée ouverte en arrière-plan pour recevoir les impressions

---

## 📱 Configuration de la Tablette pour Mode Kiosk

Pour empêcher les clients de quitter l'application :

1. **Désactiver les boutons système** :
   - **Paramètres** → **Sécurité** → **Épinglage d'écran**
   - Activer l'épinglage

2. **Empêcher la mise en veille** :
   - **Paramètres** → **Affichage** → **Mise en veille**
   - Sélectionner **"Jamais"** ou **"30 minutes"**

3. **Désactiver les notifications** :
   - **Paramètres** → **Applications** → **Chrome** → **Notifications**
   - Désactiver les notifications

4. **Mode Ne Pas Déranger** :
   - Activer le mode "Ne pas déranger" pour éviter les interruptions

---

## 🔄 Mise à Jour du Code pour Android

Le code actuel devrait fonctionner sur Android, mais vous pouvez optimiser :

1. **Détecter Android** et utiliser `navigator.share()` en fallback
2. **Améliorer l'UI** pour les tablettes tactiles
3. **Ajouter un bouton "Réessayer l'impression"** si la première tentative échoue

---

## 📞 Support

Pour plus d'informations :
- Documentation technique : `IMPRESSION_AUTOMATIQUE.md`
- Documentation de l'imprimante : Voir le manuel fourni
- Support Android Printing : https://developer.android.com/training/printing

---

**Dernière mise à jour** : 2025-12-11  
**Plateforme** : Android 8.0+

