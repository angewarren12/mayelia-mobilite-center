# 🚀 Guide de Déploiement LWS - Mayelia Mobilité Center

## 📋 Pré-requis
- Accès FTP ou File Manager LWS
- Base de données MySQL créée sur LWS
- PHP 8.1+ activé sur l'hébergement

## 📦 Étape 1 : Préparation de l'archive

### Fichiers à exclure (déjà fait automatiquement)
- `node_modules/`
- `vendor/` (sera réinstallé sur le serveur)
- `.git/`
- `.env` (à créer manuellement sur le serveur)
- `storage/logs/*.log`
- Fichiers de cache

### Fichiers critiques à inclure
✅ `public/build/` (assets compilés - DÉJÀ FAIT)
✅ `public/manifest.json` (PWA pour tablette)
✅ `public/img/` (logos et images)
✅ Tous les fichiers `.blade.php`
✅ Contrôleurs et modèles

## 🔧 Étape 2 : Configuration sur LWS

### 2.1 Créer le fichier `.env`
Connectez-vous via FTP et créez `.env` à la racine avec :

```env
APP_NAME="Mayelia Mobilité"
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_base_lws
DB_USERNAME=votre_user_lws
DB_PASSWORD=votre_password_lws

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 2.2 Générer APP_KEY
Via SSH LWS ou localement :
```bash
php artisan key:generate --show
```
Copiez la clé générée dans `.env`

### 2.3 Installer les dépendances PHP
Via SSH sur LWS :
```bash
cd /home/votre_user/public_html
composer install --no-dev --optimize-autoloader
```

### 2.4 Configurer les permissions
```bash
chmod -R 755 storage bootstrap/cache
```

### 2.5 Migrer la base de données
```bash
php artisan migrate --force
php artisan db:seed --class=GuichetSeeder
```

### 2.6 Optimiser pour la production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Étape 3 : Configuration Apache (.htaccess)

Vérifiez que `public/.htaccess` contient :
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

## 📱 Étape 4 : Configuration des URLs

### Structure recommandée LWS
```
/home/votre_user/
├── public_html/          ← Racine web (pointe vers Laravel/public)
│   ├── index.php
│   ├── build/
│   ├── img/
│   └── manifest.json
├── app/
├── resources/
├── routes/
└── .env
```

### Redirection racine vers /public
Si LWS ne permet pas de changer la racine, créez un `.htaccess` à la racine :
```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

## 🔐 Étape 5 : Sécurité

1. **Désactiver le mode debug** : `APP_DEBUG=false`
2. **HTTPS obligatoire** : Activer le certificat SSL LWS
3. **Protéger .env** : Vérifier qu'il n'est pas accessible via web

## ✅ Étape 6 : Tests Post-Déploiement

1. **Page d'accueil** : `https://votre-domaine.com`
2. **Login** : `/login`
3. **Dashboard** : `/dashboard`
4. **QMS Kiosk** : `/qms/kiosk`
5. **QMS Agent** : `/qms/agent`
6. **QMS Display** : `/qms/display`

## 🆘 Dépannage

### Erreur 500
- Vérifier les logs : `storage/logs/laravel.log`
- Vérifier les permissions : `chmod -R 755 storage`

### Assets non chargés
- Vérifier que `public/build/` existe
- Vérifier `APP_URL` dans `.env`

### Base de données
- Tester la connexion : `php artisan tinker` puis `DB::connection()->getPdo();`

## 📞 Support
- Documentation Laravel : https://laravel.com/docs
- Support LWS : https://aide.lws.fr

---
**Date de déploiement** : 2025-12-09
**Version** : QMS v1.0 (Turbo Mode + PWA)
