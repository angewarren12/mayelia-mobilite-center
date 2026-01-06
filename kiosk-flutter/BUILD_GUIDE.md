# 📱 Guide de Build - Application Kiosk Flutter

## ✅ État de l'Application

L'application Flutter kiosk est **complète et prête à être compilée**.

### Structure Complète
- ✅ **17 fichiers Dart** dans `lib/`
- ✅ **Modèles de données** : Ticket, Service, Centre
- ✅ **Services** : API, Bluetooth, Impression
- ✅ **Écrans** : Accueil, Sélection service, Saisie RDV, Scanner QR, Confirmation
- ✅ **Provider** : Gestion d'état avec KioskProvider
- ✅ **Thème** : Design Mayelia (#02913F)
- ✅ **Configuration** : API, Imprimante Bluetooth

## 🚀 Compilation de l'Application

### Prérequis

1. **Flutter SDK** (3.5.0+)
   ```bash
   flutter --version
   ```

2. **Android Studio** avec :
   - Android SDK (API 21+)
   - Flutter plugin
   - Dart plugin

3. **Dépendances installées**
   ```bash
   cd kiosk-flutter
   flutter pub get
   ```

### Build APK (Android)

#### 1. Build Debug (pour tests)
```bash
cd kiosk-flutter
flutter build apk --debug
```
**Fichier généré :** `build/app/outputs/flutter-apk/app-debug.apk`

#### 2. Build Release (Production)
```bash
cd kiosk-flutter
flutter build apk --release
```
**Fichier généré :** `build/app/outputs/flutter-apk/app-release.apk`

#### 3. Build App Bundle (pour Play Store)
```bash
cd kiosk-flutter
flutter build appbundle --release
```
**Fichier généré :** `build/app/outputs/bundle/release/app-release.aab`

### Configuration Avant Build

#### 1. URL de l'API
Modifier `lib/config/api_config.dart` :
```dart
static const String baseUrl = 'https://rendez-vous.mayeliamobilite.com';
```

#### 2. ID du Centre
Modifier `lib/main.dart` :
```dart
static const int centreId = 2; // Changer selon le centre
static const String centreNom = 'Centre de Daloa';
```

#### 3. Nom de l'Imprimante Bluetooth
Modifier `lib/config/printer_config.dart` :
```dart
static const String printerName = 'MTP-II_EAF'; // Nom exact de l'imprimante
```

## 📦 Installation sur Tablette Android

### Méthode 1 : Via USB (ADB)
```bash
# Activer le mode développeur sur la tablette
# Activer le débogage USB
# Connecter la tablette via USB

flutter install
# ou
adb install build/app/outputs/flutter-apk/app-release.apk
```

### Méthode 2 : Via Fichier APK
1. Copier `app-release.apk` sur la tablette
2. Activer "Sources inconnues" dans les paramètres
3. Installer l'APK depuis le gestionnaire de fichiers

## 🔧 Mode Kiosk (Tablette Dédiée)

### Configuration Android pour Mode Kiosk

1. **Installer l'application**
2. **Configurer l'application comme launcher par défaut** (optionnel)
3. **Désactiver la barre de navigation système** (déjà fait dans le code)
4. **Verrouiller l'orientation** (optionnel, décommenter dans `main.dart`)

### Script PowerShell pour Build Automatique

Créer `build-apk.ps1` :
```powershell
cd kiosk-flutter
flutter clean
flutter pub get
flutter build apk --release
Write-Host "APK généré dans: build/app/outputs/flutter-apk/app-release.apk"
```

## 🐛 Dépannage

### Erreur : "Gradle build failed"
```bash
cd kiosk-flutter/android
./gradlew clean
cd ../..
flutter clean
flutter pub get
flutter build apk --release
```

### Erreur : "SDK not found"
- Vérifier que Android SDK est installé
- Configurer `ANDROID_HOME` dans les variables d'environnement

### Erreur : "Bluetooth permissions"
- Les permissions sont déjà configurées dans `AndroidManifest.xml`
- Vérifier que l'application a les permissions Bluetooth sur la tablette

### Erreur : "API connection failed"
- Vérifier l'URL dans `api_config.dart`
- Vérifier que le serveur Laravel est accessible
- Vérifier les routes API dans `routes/api.php`

## 📋 Checklist Avant Build Production

- [ ] URL API configurée correctement
- [ ] ID du centre configuré
- [ ] Nom de l'imprimante Bluetooth configuré
- [ ] Logo ONECI présent dans `assets/images/logo-oneci.jpg`
- [ ] Version et build number mis à jour dans `pubspec.yaml`
- [ ] Permissions Android vérifiées
- [ ] Tests effectués sur tablette réelle
- [ ] Impression Bluetooth testée

## 🎯 Fonctionnalités Implémentées

✅ **Mode FIFO** : Un seul bouton "PRENDRE UN TICKET"
✅ **Mode Fenêtre de Tolérance** : Deux boutons "SANS RDV" / "J'AI UN RDV"
✅ **Sélection de service** (si plusieurs services disponibles)
✅ **Saisie manuelle** du numéro RDV avec clavier virtuel
✅ **Scanner QR Code** pour numéro RDV
✅ **Impression automatique** via Bluetooth ESC/POS
✅ **Interface responsive** (portrait et paysage)
✅ **Gestion d'erreurs** avec messages clairs
✅ **Thème Mayelia** (#02913F)

## 📝 Notes Importantes

1. **Bluetooth** : L'imprimante doit être appairée avec la tablette avant utilisation
2. **API** : Les routes API sont publiques (sans authentification) pour les kiosks
3. **Rate Limiting** : 120 requêtes/minute par IP pour les routes QMS
4. **Orientation** : L'application supporte portrait et paysage (orientation verrouillable)

## 🔄 Mise à Jour

Pour mettre à jour l'application :
```bash
cd kiosk-flutter
flutter pub upgrade
flutter clean
flutter pub get
flutter build apk --release
```

## 📞 Support

En cas de problème :
1. Vérifier les logs : `flutter logs`
2. Vérifier la console Android : `adb logcat`
3. Vérifier les erreurs API dans les logs Laravel

