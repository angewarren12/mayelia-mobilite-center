# 🚀 Guide de Déploiement Simplifié - LWS (avec CDN)

## ✅ Avantages de cette configuration

- ✅ **Pas besoin de `npm run build`**
- ✅ **Déploiement ultra-simple**
- ✅ **Fonctionne immédiatement**
- ✅ **Pas de gestion d'assets**

---

## 📦 Étape 1 : Préparer l'archive

### Créer un ZIP contenant TOUS les fichiers SAUF :
- `node_modules/`
- `vendor/`
- `.env`
- `storage/logs/*.log`
- `public/build/` (plus nécessaire avec les CDN)

---

## 🌐 Étape 2 : Upload sur LWS

### Structure sur le serveur :
```
/home/votre-compte/
├── laravel-app/              # Application Laravel
│   ├── app/
│   ├── config/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── ...
└── public_html/              # Dossier public
    ├── index.php
    ├── .htaccess
    ├── images/
    └── ...
```

### Actions :
1. Uploadez le ZIP dans `/home/votre-compte/`
2. Décompressez
3. Déplacez le contenu de `/public/` vers `/public_html/`
4. Le reste va dans `/home/votre-compte/laravel-app/`

---

## ⚙️ Étape 3 : Configuration

### 3.1 Créer le fichier `.env`

Dans `/home/votre-compte/laravel-app/.env` :

```env
APP_NAME="Mayelia Mobilite Center"
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=https://rendez-vous.mayeliamobilite.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_base
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 3.2 Modifier `public_html/index.php`

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Modifier ces chemins
require __DIR__.'/../laravel-app/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## 🔧 Étape 4 : Installation (via SSH)

```bash
# Se connecter
ssh votre-compte@votre-serveur.lws.fr

# Aller dans le dossier
cd /home/votre-compte/laravel-app

# Installer les dépendances PHP
composer install --no-dev --optimize-autoloader

# Générer la clé
php artisan key:generate

# Exécuter les migrations
php artisan migrate --force

# Créer le lien symbolique
php artisan storage:link

# Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## ✅ Étape 5 : Vérification

### Checklist :
- [ ] Le site s'affiche : https://rendez-vous.mayeliamobilite.com
- [ ] Les styles Tailwind fonctionnent
- [ ] Les icônes Font Awesome s'affichent
- [ ] Pas d'erreurs dans la console (F12)

---

## 🔄 Mise à jour du site

Quand vous modifiez le code :

1. **Uploadez uniquement les fichiers modifiés**
2. **Sur le serveur, exécutez :**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

**C'est tout ! Pas besoin de `npm run build` !** 🎉

---

## 🆚 Comparaison : CDN vs Build

| Aspect | Avec CDN (actuel) | Avec Build |
|--------|------------------|------------|
| Déploiement | ✅ Simple | ❌ Complexe |
| Taille fichiers | ⚠️ Plus lourd | ✅ Optimisé |
| Vitesse | ⚠️ Dépend du CDN | ✅ Rapide |
| Maintenance | ✅ Facile | ⚠️ Nécessite build |
| Offline | ❌ Non | ✅ Oui |

**Pour votre cas, les CDN sont parfaits !** 👍

---

## 📞 Support

En cas de problème :
1. Vérifiez les logs : `storage/logs/laravel.log`
2. Vérifiez la console du navigateur (F12)
3. Testez l'accès aux CDN :
   - https://cdn.tailwindcss.com
   - https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css
