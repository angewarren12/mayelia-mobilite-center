# 🖨️ Guide : Impression Automatique sur Tablette

## 🎯 Objectif
Configurer votre tablette pour que **quand le client clique sur "Imprimer ticket", le ticket sorte automatiquement de l'imprimante SANS popup**.

---

## 📋 Étapes de Configuration

### Étape 1 : Installer et Configurer l'Imprimante Bluetooth

1. **Allumer l'imprimante thermique**
   - Appuyer sur le bouton d'alimentation
   - Attendre que l'indicateur Bluetooth clignote

2. **Appairer l'imprimante avec la tablette** :
   - Sur la tablette : **Paramètres** → **Bluetooth**
   - Activer le Bluetooth si ce n'est pas fait
   - Chercher l'imprimante dans la liste (nom généralement "BT Printer" ou similaire)
   - Cliquer sur l'imprimante pour l'appairer
   - Code PIN si demandé : généralement `0000` ou `1234`

3. **Installer le pilote de l'imprimante** :
   - Télécharger le pilote depuis : http://www.weinprinter.com/products_detail.aspx?ProductsID=126&ProductsCateId=74&CurrCateID=74&CateID=74
   - Installer le pilote sur la tablette
   - L'imprimante devrait apparaître dans **Paramètres** → **Imprimantes**

4. **Définir l'imprimante comme imprimante par défaut** :
   - Aller dans **Paramètres** → **Imprimantes**
   - Clic droit sur votre imprimante thermique
   - Sélectionner **"Définir comme imprimante par défaut"**
   - ⚠️ **C'EST TRÈS IMPORTANT** : Sans ça, l'impression automatique ne fonctionnera pas !

5. **Configurer les propriétés de l'imprimante** :
   - Clic droit sur l'imprimante → **Propriétés de l'imprimante**
   - Taille du papier : **58mm** (ou format personnalisé 58mm x auto)
   - Orientation : **Portrait**
   - Marges : **0mm** (ou minimales)

---

### Étape 2 : Installer Google Chrome

1. Si Chrome n'est pas installé, le télécharger depuis : https://www.google.com/chrome/
2. Installer Chrome sur la tablette

---

### Étape 3 : Configurer Chrome pour l'Impression Automatique

1. **Ouvrir Chrome**

2. **Aller dans les paramètres d'impression** :
   - Dans la barre d'adresse, taper : `chrome://settings/printing`
   - Appuyer sur Entrée

3. **Configurer l'impression système** :
   - Activer **"Utiliser l'impression système"** (si disponible)
   - Sélectionner votre imprimante thermique comme **imprimante par défaut**

4. **Fermer Chrome complètement**

---

### Étape 4 : Créer le Fichier de Lancement Kiosk

1. **Créer un fichier texte** sur le bureau de la tablette

2. **Le renommer** : `launch_kiosk.bat` (⚠️ Important : extension .bat, pas .txt)

3. **Ouvrir avec le Bloc-notes** et copier-coller ce contenu :

```batch
@echo off
REM Lancer Chrome en mode Kiosk avec impression automatique
REM Pour la borne de prise de tickets Mayelia

echo Demarrage de la borne Kiosk...
echo.

REM Fermer toutes les instances de Chrome existantes
taskkill /F /IM chrome.exe 2>nul

REM Attendre 2 secondes
timeout /t 2 /nobreak >nul

REM Lancer Chrome en mode Kiosk
"C:\Program Files\Google\Chrome\Application\chrome.exe" ^
  --kiosk ^
  --kiosk-printing ^
  --disable-pinch ^
  --overscroll-history-navigation=0 ^
  --disable-features=TranslateUI ^
  --no-first-run ^
  --disable-infobars ^
  --disable-session-crashed-bubble ^
  --disable-translate ^
  --disable-sync ^
  --disable-background-networking ^
  "http://127.0.0.1:8000/qms/kiosk/1"
```

4. **⚠️ IMPORTANT** : Modifier l'URL à la fin :
   - Remplacer `http://127.0.0.1:8000/qms/kiosk/1` par l'URL de VOTRE serveur
   - Par exemple : `http://192.168.1.100:8000/qms/kiosk/1` (remplacer par l'IP de votre serveur)
   - Ou : `https://votre-domaine.com/qms/kiosk/1`

5. **Sauvegarder le fichier**

---

### Étape 5 : Tester l'Impression Automatique

1. **Double-cliquer sur `launch_kiosk.bat`**
   - Chrome devrait s'ouvrir en plein écran (mode kiosk)
   - L'interface de prise de ticket devrait s'afficher

2. **Tester l'impression** :
   - Cliquer sur "Sans rendez-vous" (ou "Avec rendez-vous")
   - Sélectionner un service si demandé
   - Cliquer sur "Imprimer ticket"

3. **Vérifier que** :
   - ✅ **AUCUNE popup d'impression n'apparaît**
   - ✅ **Le ticket sort automatiquement de l'imprimante**
   - ✅ **L'écran revient à l'accueil après 3 secondes**

---

### Étape 6 : Configurer le Démarrage Automatique (Optionnel)

Pour que la borne démarre automatiquement au démarrage de la tablette :

1. Appuyer sur **Win + R**
2. Taper : `shell:startup`
3. Appuyer sur Entrée
4. **Copier** le fichier `launch_kiosk.bat` dans ce dossier
5. Maintenant, à chaque démarrage de la tablette, la borne se lancera automatiquement

---

## ❌ Problèmes Courants et Solutions

### Problème 1 : Une popup d'impression apparaît encore

**Solutions** :
1. Vérifier que Chrome est bien lancé avec `--kiosk-printing` (dans le fichier .bat)
2. Vérifier dans `chrome://settings/printing` que "Utiliser l'impression système" est activé
3. Redémarrer Chrome complètement (fermer toutes les fenêtres)
4. Vérifier que l'imprimante est bien définie comme imprimante par défaut dans Windows

### Problème 2 : Le ticket ne sort pas

**Solutions** :
1. Vérifier que l'imprimante est allumée
2. Vérifier que l'imprimante est connectée en Bluetooth (indicateur allumé)
3. Tester l'impression manuellement : Paramètres → Imprimantes → Clic droit → Imprimer une page de test
4. Vérifier que le pilote de l'imprimante est bien installé

### Problème 3 : Le format du ticket est incorrect

**Solutions** :
1. Vérifier les propriétés de l'imprimante : Taille du papier = 58mm
2. Vérifier l'orientation : Portrait
3. Redémarrer l'imprimante
4. Réinstaller le pilote si nécessaire

### Problème 4 : Chrome ne se lance pas en mode kiosk

**Solutions** :
1. Vérifier le chemin de Chrome dans le fichier .bat :
   - Par défaut : `C:\Program Files\Google\Chrome\Application\chrome.exe`
   - Si Chrome est installé ailleurs, modifier le chemin
2. Vérifier que Chrome est bien installé
3. Essayer de lancer Chrome manuellement d'abord pour vérifier qu'il fonctionne

---

## ✅ Checklist de Configuration

Avant de mettre la borne en production, vérifier :

- [ ] Imprimante allumée et appairée en Bluetooth
- [ ] Pilote de l'imprimante installé
- [ ] Imprimante définie comme imprimante par défaut dans Windows
- [ ] Chrome installé et configuré (`chrome://settings/printing`)
- [ ] Fichier `launch_kiosk.bat` créé avec la bonne URL
- [ ] Test d'impression réussi (ticket sort automatiquement sans popup)
- [ ] Format du ticket correct (58mm, bien centré)
- [ ] QR code visible et lisible sur le ticket

---

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifier cette checklist
2. Consulter le fichier `IMPRESSION_AUTOMATIQUE.md` pour plus de détails techniques
3. Vérifier les logs de Chrome (si accessible)

---

**Dernière mise à jour** : 2025-12-11


