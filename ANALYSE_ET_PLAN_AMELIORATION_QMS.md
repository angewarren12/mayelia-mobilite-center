# Plan d'Amélioration du Système QMS - Mayelia

Ce document synthétise les corrections apportées et le plan stratégique pour stabiliser et moderniser le système de gestion de file d'attente (QMS).

---

## 🛠 1. Corrections Immédiates (Déjà Appliquées)

### A. Kiosk Flutter (Impression & Connectivité)
*   **Wakelock (Anti-sommeil)** : La tablette est désormais forcée de rester allumée à 100%. Cela empêche Android de couper le Bluetooth pour économiser l'énergie.
*   **Transaction d'Impression "Safe"** : Le flux est inversé. Le kiosk vérifie maintenant que l'imprimante est connectée **AVANT** de demander un numéro au serveur.
*   **Fin des "Trous" de Numérotation** : Si l'imprimante est débranchée, la borne affiche un message d'erreur et refuse de générer un ticket, évitant ainsi de perdre des numéros en base de données.
*   **Bouton de Ré-impression** : En cas d'incident mineur (papier coincé, etc.), un bouton **RÉ-IMPRIMER** apparaît sur l'écran de confirmation pour permettre de sortir le ticket sans doubler le numéro.
*   **Gestion Heartbeat** : Un système de "battement de cœur" vérifie la connexion Bluetooth toutes les 10 secondes et tente une reconnexion automatique si nécessaire.

### B. Display TV (Visibilité & Stabilité)
*   **Indicateur de Santé** : Un voyant discret (Vert/Rouge) a été ajouté en haut à droite pour indiquer si la TV est bien connectée au serveur central.
*   **Gestion des Erreurs de Réseau** : La TV détecte maintenant les baisses de Wi-Fi et tente de se reconnecter silencieusement sans figer l'écran.

---

## 📈 2. Plan d'Amélioration Moyen Terme

### A. Passage au Temps Réel (WebSockets)
Actuellement, la TV "demande" au serveur s'il y a des nouveaux tickets toutes les 2 secondes (Polling). 
*   **Objectif** : Installer **Laravel Reverb**. 
*   **Bénéfice** : Dès que l'agent clique sur "Appel", la TV réagit instantanément (zéro latence) et la charge sur le serveur est divisée par 10.

### B. Fiabilisation de l'Audio (Sons Naturels)
La synthèse vocale peut varier selon la marque de la TV (Samsung vs TCL vs PC).
*   **Objectif** : Utiliser un pack de fichiers audio pré-enregistrés (F001.mp3, G002.mp3) stockés localement sur le serveur.
*   **Bénéfice** : Une voix parfaite et identique sur tous les centres, sans dépendre de la connexion internet ou du moteur TTS de la TV.

### C. Supervision Centralisée
*   **Dashboard de Santé** : Créer une page dans le Backoffice permettant de voir l'état de chaque borne (Niveau de batterie de la tablette, état de l'imprimante, dernière synchro).

---

## 🚀 3. Recommandations Matérielles
Pour un fonctionnement optimal à 100% :
1.  **Tablettes** : Garder les tablettes branchées sur secteur en permanence (le Wakelock consomme plus de batterie).
2.  **Bluetooth** : S'assurer que l'imprimante est à moins de 3 mètres de la tablette pour éviter les micro-coupures.
3.  **Wi-Fi** : Utiliser un réseau Wi-Fi dédié au QMS pour éviter les interférences avec les clients.

---
*Plan rédigé par Antigravity - Janvier 2026*
