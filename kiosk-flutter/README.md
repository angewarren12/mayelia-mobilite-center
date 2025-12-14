# 📱 Application Kiosk Flutter - Mayelia

Application Flutter pour borne libre-service de gestion de tickets avec impression Bluetooth directe.

## 🎯 Fonctionnalités

- ✅ Interface identique au kiosk web
- ✅ Connexion Bluetooth directe à l'imprimante thermique
- ✅ Impression ESC/POS automatique
- ✅ Responsive (Portrait et Paysage)
- ✅ Gestion des tickets (Sans RDV / Avec RDV)
- ✅ QR Code sur les tickets
- ✅ Thème Mayelia (#02913F)

## 🚀 Installation

### Prérequis

- Flutter SDK (3.5.0+)
- Dart SDK
- Android Studio (pour Android)
- Android SDK avec API 21+

### Installation des dépendances

```bash
cd kiosk-flutter
flutter pub get
```

## ⚙️ Configuration

### 1. Configuration API

Modifier `lib/config/api_config.dart` :

```dart
static const String baseUrl = 'http://votre-serveur-laravel.com';
```

### 2. Configuration Imprimante

Modifier `lib/config/printer_config.dart` :

```dart
static const String printerName = 'Nom Exact De Votre Imprimante Bluetooth';
```

### 3. Configuration Centre

Modifier `lib/main.dart` :

```dart
static const int centreId = 1; // ID du centre
static const String centreNom = 'Nom du Centre';
```

## 📱 Lancer l'application

```bash
# Sur Android
flutter run

# Build APK
flutter build apk --release
```

L'APK se trouve dans : `build/app/outputs/flutter-apk/app-release.apk`

## 🔧 Structure du projet

```
lib/
├── config/          # Configuration (API, Imprimante)
├── models/          # Modèles de données (Ticket, Service, Centre)
├── providers/       # State management (KioskProvider)
├── screens/         # Écrans de l'application
├── services/        # Services (API, Bluetooth, Impression)
├── theme/           # Thème et couleurs
├── widgets/         # Widgets réutilisables
└── main.dart        # Point d'entrée
```

## 📋 Écrans

1. **HomeScreen** - Écran d'accueil avec choix "Sans RDV" / "Avec RDV"
2. **ServiceSelectionScreen** - Sélection du service (si plusieurs services)
3. **RdvInputScreen** - Saisie du numéro RDV avec clavier virtuel
4. **ConfirmationScreen** - Confirmation et impression

## 🎨 Design

L'application reproduit fidèlement le design du kiosk web :
- Couleurs Mayelia (#02913F)
- Cartes blanches avec ombres
- Bordures arrondies (rounded-3xl)
- Animations et transitions

## 🔌 Bluetooth

L'application détecte automatiquement l'imprimante Bluetooth appairée. Si plusieurs imprimantes sont disponibles, elle sélectionne celle correspondant au nom configuré ou la première disponible.

### Permissions Android

Les permissions Bluetooth sont déjà configurées dans `android/app/src/main/AndroidManifest.xml`.

## 📦 Build de production

```bash
# APK Release
flutter build apk --release

# App Bundle (pour Play Store)
flutter build appbundle --release
```

## 🐛 Dépannage

### L'imprimante n'est pas détectée

1. Vérifier que l'imprimante est appairée avec la tablette
2. Vérifier le nom exact dans `printer_config.dart`
3. Autoriser les permissions Bluetooth au premier lancement

### Erreur de connexion API

1. Vérifier l'URL dans `api_config.dart`
2. Vérifier que le serveur Laravel est accessible
3. Vérifier les routes API publiques du kiosk

### Build Android échoue

```bash
flutter clean
flutter pub get
flutter build apk --release
```

## 📝 Notes

- L'application est optimisée pour tablettes Android
- Mode kiosk : désactive la barre de navigation système
- L'orientation peut être verrouillée dans `main.dart` si nécessaire
