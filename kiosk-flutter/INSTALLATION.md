# 📲 Guide d'Installation - Application Kiosk Flutter

## 🎯 Vue d'ensemble

Ce guide vous explique comment installer et configurer l'application kiosk Flutter sur une tablette Android dédiée.

## 📋 Prérequis

### Matériel
- ✅ Tablette Android (Android 5.0+ / API 21+)
- ✅ Imprimante thermique Bluetooth (58mm)
- ✅ Connexion Internet (WiFi ou 4G)

### Logiciel
- ✅ APK de l'application (`app-release.apk`)
- ✅ Accès aux paramètres développeur de la tablette

## 🔧 Installation

### Étape 1 : Préparer la Tablette

1. **Activer le Mode Développeur**
   - Aller dans `Paramètres` → `À propos de la tablette`
   - Appuyer 7 fois sur `Numéro de build`
   - Message "Vous êtes maintenant développeur" affiché

2. **Activer le Débogage USB** (pour installation via USB)
   - Aller dans `Paramètres` → `Options pour les développeurs`
   - Activer `Débogage USB`

3. **Autoriser les Sources Inconnues** (pour installation via fichier)
   - Aller dans `Paramètres` → `Sécurité`
   - Activer `Sources inconnues` ou `Installer des applications inconnues`

### Étape 2 : Appairer l'Imprimante Bluetooth

1. Aller dans `Paramètres` → `Bluetooth`
2. Activer le Bluetooth
3. Mettre l'imprimante en mode appairage
4. Sélectionner l'imprimante dans la liste
5. Confirmer l'appairage
6. **Noter le nom exact de l'imprimante** (ex: `MTP-II_EAF`)

### Étape 3 : Installer l'Application

#### Méthode A : Installation via USB (Recommandée)

1. Connecter la tablette à l'ordinateur via USB
2. Autoriser le débogage USB sur la tablette
3. Sur l'ordinateur, exécuter :
   ```bash
   adb install app-release.apk
   ```
   ou
   ```bash
   flutter install
   ```

#### Méthode B : Installation via Fichier

1. Copier `app-release.apk` sur la tablette (via USB, email, cloud, etc.)
2. Ouvrir le gestionnaire de fichiers sur la tablette
3. Naviguer vers le fichier APK
4. Appuyer sur le fichier pour lancer l'installation
5. Confirmer l'installation

### Étape 4 : Configurer l'Application

1. **Lancer l'application** pour la première fois
2. **Autoriser les permissions** :
   - Bluetooth
   - Localisation (requis pour Bluetooth sur Android 12+)
   - Caméra (pour scanner QR code)

3. **Configurer l'ID du Centre** (si nécessaire)
   - Modifier `lib/main.dart` avant le build
   - Ou créer un fichier de configuration (à implémenter)

4. **Vérifier la connexion Bluetooth**
   - L'application tentera de se connecter automatiquement à l'imprimante
   - Vérifier que le nom de l'imprimante correspond dans `lib/config/printer_config.dart`

## ⚙️ Configuration Avancée

### Mode Kiosk (Tablette Dédiée)

Pour transformer la tablette en kiosk dédié :

1. **Installer une application de verrouillage kiosk** (optionnel)
   - Exemples : Kiosk Browser, Fully Kiosk Browser
   - Configurer pour lancer automatiquement l'application kiosk

2. **Désactiver la barre de navigation système**
   - Déjà implémenté dans le code (`SystemUiMode.immersive`)
   - L'application masque automatiquement la barre système

3. **Verrouiller l'orientation** (optionnel)
   - Décommenter dans `lib/main.dart` :
   ```dart
   SystemChrome.setPreferredOrientations([
     DeviceOrientation.landscapeLeft,
     DeviceOrientation.landscapeRight,
   ]);
   ```

4. **Désactiver les notifications système** (via paramètres Android)

5. **Configurer l'application comme launcher** (optionnel)
   - Utiliser une application de launcher kiosk
   - Empêcher l'accès aux autres applications

### Configuration Réseau

1. **Vérifier la connexion Internet**
   - L'application nécessite une connexion pour communiquer avec l'API Laravel
   - Tester la connexion : `ping rendez-vous.mayeliamobilite.com`

2. **Configurer le WiFi** (si nécessaire)
   - Aller dans `Paramètres` → `WiFi`
   - Se connecter au réseau WiFi du centre

3. **Vérifier l'URL de l'API**
   - Par défaut : `https://rendez-vous.mayeliamobilite.com`
   - Modifier dans `lib/config/api_config.dart` si nécessaire

## 🧪 Tests de Vérification

### Test 1 : Connexion API
1. Lancer l'application
2. Vérifier que l'écran de chargement s'affiche
3. Vérifier que les informations du centre se chargent
4. Vérifier qu'aucune erreur n'apparaît

### Test 2 : Création de Ticket (Sans RDV)
1. Appuyer sur "SANS RENDEZ-VOUS" ou "PRENDRE UN TICKET"
2. Sélectionner un service (si plusieurs disponibles)
3. Vérifier que le ticket est créé
4. Vérifier que l'écran de confirmation s'affiche

### Test 3 : Impression Bluetooth
1. Créer un ticket
2. Vérifier que l'impression démarre automatiquement
3. Vérifier que le ticket est imprimé correctement
4. Vérifier le format du ticket (centre, numéro, service, type, date)

### Test 4 : Scanner QR Code
1. Appuyer sur "J'AI UN RENDEZ-VOUS"
2. Appuyer sur "Scanner le QR Code"
3. Scanner un QR code de reçu
4. Vérifier que le numéro est détecté
5. Vérifier que le ticket est créé

### Test 5 : Saisie Manuelle RDV
1. Appuyer sur "J'AI UN RENDEZ-VOUS"
2. Saisir un numéro RDV valide avec le clavier virtuel
3. Appuyer sur "Valider"
4. Vérifier que le ticket est créé

## 🐛 Dépannage

### L'application ne se lance pas
- Vérifier que l'APK est installé correctement
- Vérifier les permissions dans `Paramètres` → `Applications`
- Réinstaller l'application

### Erreur de connexion API
- Vérifier la connexion Internet
- Vérifier l'URL dans `api_config.dart`
- Vérifier que le serveur Laravel est accessible
- Vérifier les logs : `adb logcat | grep flutter`

### L'imprimante n'est pas détectée
- Vérifier que l'imprimante est appairée
- Vérifier le nom exact dans `printer_config.dart`
- Réappairer l'imprimante
- Vérifier les permissions Bluetooth

### L'impression ne fonctionne pas
- Vérifier que l'imprimante est allumée
- Vérifier que l'imprimante a du papier
- Vérifier la connexion Bluetooth
- Tester l'impression depuis une autre application

### Le scanner QR code ne fonctionne pas
- Vérifier les permissions caméra
- Vérifier que la caméra fonctionne
- Nettoyer l'objectif de la caméra
- Vérifier l'éclairage

## 📞 Support

En cas de problème :
1. Vérifier les logs : `adb logcat`
2. Vérifier les erreurs dans l'application
3. Vérifier les logs du serveur Laravel
4. Contacter l'équipe de développement

## 🔄 Mise à Jour

Pour mettre à jour l'application :
1. Désinstaller l'ancienne version (optionnel)
2. Installer la nouvelle version APK
3. Vérifier que la configuration est correcte
4. Tester toutes les fonctionnalités

## 📝 Notes Importantes

- ⚠️ **Sauvegarder la configuration** avant de réinstaller
- ⚠️ **Tester sur tablette réelle** avant déploiement
- ⚠️ **Vérifier la batterie** de la tablette et de l'imprimante
- ⚠️ **Formation du personnel** sur l'utilisation du kiosk

