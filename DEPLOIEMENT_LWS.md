# 🚀 Guide de Déploiement Automatisé - LWS

> [!NOTE]
> Ce guide explique comment configurer le déploiement automatique via GitHub.
> À chaque fois que vous pousserez (`push`) sur la branche `main`, le site sera mis à jour.

## 🛠 Prérequis

1. Avoir son code sur GitHub.
2. Avoir ses accès FTP LWS (reçus par email ou dans le panel LWS).

---

## 🔐 Étape 1 : Configurer les Secrets GitHub

Pour que GitHub puisse se connecter à votre serveur, vous devez enregistrer vos mots de passe de manière sécurisée.

1. Allez sur votre dépôt **GitHub**.
2. Cliquez sur **Settings** (Paramètres) > **Secrets and variables** > **Actions**.
3. Cliquez sur **New repository secret** pour ajouter chaque variable ci-dessous :

| Nom du Secret | Valeur (Exemple) |
|---------------|------------------|
| `FTP_SERVER`  | `ftp.monsite.com` (ou l'IP : `123.45.67.89`) |
| `FTP_USERNAME`| `moncompte` |
| `FTP_PASSWORD`| `MonMotDePasse123` |

> [!WARNING]
> Assurez-vous que le `FTP_USERNAME` a accès à la racine de votre hébergement (là où se trouvent `public_html` et `laravel-app`). Si votre compte FTP pointe directement dans `public_html`, le déploiement échouera.
> **Idéalement**, créez un compte FTP qui pointe vers la racine `/`.

---

## 🌲 Étape 2 : Structure des dossiers

Le script de déploiement automatique s'attend à cette structure sur votre serveur LWS :

```
/ (Racine de votre compte FTP)
├── laravel-app/       # Dossier contenant le coeur de Laravel
└── public_html/       # Dossier public accessible via le web
```

Le script va :
1. Envoyer le contenu du dossier `public` de votre projet dans `public_html`.
2. Envoyer tout le reste du projet dans `laravel-app`.

---

## 🚀 Étape 3 : Déployer

Il vous suffit maintenant de faire un "push" sur la branche `main` :

```bash
git add .
git commit -m "Mise à jour du site"
git push origin main
```

Allez dans l'onglet **Actions** de votre dépôt GitHub pour suivre le progrès du déploiement.

---

## 🔄 Étape 4 : Après le déploiement (Migrations & Cache)

Comme nous n'avons pas accès SSH direct dans le script (sauf configuration avancée), après un déploiement important (changement de base de données), vous pouvez :

1. **Option A (Recommandée)** : Créer une route sécurisée pour vider le cache.
   - J'ai ajouté un fichier `public/deploy.php` (à venir) qui permet de lancer les commandes de maintenance via le navigateur.
   
2. **Option B** : Se connecter en SSH manuellement et lancer :
   ```bash
   cd laravel-app
   php artisan migrate --force
   php artisan cache:clear
   php artisan config:cache
   php artisan view:clear
   ```
