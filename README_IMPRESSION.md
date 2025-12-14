# 🖨️ Guide Rapide : Impression Automatique des Tickets

## 📍 Choisissez votre plateforme :

### 🖥️ **Ordinateur Windows / PC**
👉 Voir : [`IMPRESSION_AUTOMATIQUE.md`](IMPRESSION_AUTOMATIQUE.md)

### 📱 **Tablette Android**
👉 Voir : [`GUIDE_ANDROID_IMPRESSION.md`](GUIDE_ANDROID_IMPRESSION.md)

---

## 🎯 Objectif

Configurer votre dispositif (PC ou tablette Android) pour que **quand le client clique sur "Imprimer ticket", le ticket sorte automatiquement de l'imprimante Bluetooth thermique 58mm**.

---

## ⚡ Configuration Rapide

### Pour Windows :
1. Appairer l'imprimante Bluetooth
2. Définir comme imprimante par défaut
3. Utiliser le fichier `launch_kiosk.bat` fourni
4. Lancer Chrome avec `--kiosk-printing`

### Pour Android :
1. Appairer l'imprimante Bluetooth
2. Installer l'application de l'imprimante
3. Installer Chrome et créer une PWA (ajouter à l'écran d'accueil)
4. Activer l'épinglage d'écran pour le mode kiosk

---

## 📋 Caractéristiques de l'Imprimante

- **Type** : Thermique sans fil Bluetooth
- **Largeur papier** : 58mm (2 pouces)
- **Largeur impression** : 48mm
- **Résolution** : 203 dpi
- **Vitesse** : 50-80mm/s
- **Interface** : Bluetooth / USB
- **Format supporté** : ESC/POS

---

## ✅ Résultat Attendu

Après configuration :
- ✅ Client clique sur "Imprimer ticket"
- ✅ Le ticket sort automatiquement de l'imprimante
- ✅ Format correct (58mm, QR code visible)
- ✅ Pas de popup (ou popup minimale sur Android)
- ✅ Écran revient à l'accueil après impression

---

## ❓ Besoin d'Aide ?

1. Consultez le guide spécifique à votre plateforme ci-dessus
2. Vérifiez la checklist de configuration
3. Consultez la section "Problèmes courants" dans chaque guide

---

**Dernière mise à jour** : 2025-12-11


