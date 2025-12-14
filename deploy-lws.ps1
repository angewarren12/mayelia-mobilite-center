# Script de préparation pour déploiement LWS
# Version optimisée - Exclut node_modules et vendor

Write-Host "🚀 Préparation du déploiement LWS..." -ForegroundColor Green

# 1. Vérifier que le build existe
if (-not (Test-Path "public/build/manifest.json")) {
    Write-Host "❌ Erreur : Les assets ne sont pas compilés." -ForegroundColor Red
    Write-Host "Exécutez d'abord : npm run build" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Assets compilés détectés" -ForegroundColor Green

# 2. Créer le dossier temporaire
$tempDir = "mayelia_deploy_temp"
$zipName = "mayelia-lws-deploy.zip"

if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }
if (Test-Path $zipName) { Remove-Item $zipName -Force }

New-Item -ItemType Directory -Path $tempDir | Out-Null

Write-Host "📦 Copie des fichiers..." -ForegroundColor Yellow

# 3. Liste des dossiers à copier
$includeDirs = @(
    "app",
    "bootstrap",
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "storage"
)

foreach ($dir in $includeDirs) {
    if (Test-Path $dir) {
        Write-Host "  → $dir" -ForegroundColor Cyan
        Copy-Item -Path $dir -Destination "$tempDir/$dir" -Recurse -Force
    }
}

# 4. Copier les fichiers racine importants
$rootFiles = @(
    "artisan",
    "composer.json",
    "composer.lock",
    "package.json"
)

foreach ($file in $rootFiles) {
    if (Test-Path $file) {
        Copy-Item -Path $file -Destination "$tempDir/$file" -Force
    }
}

# 5. Nettoyer storage (supprimer logs et cache)
Write-Host "🧹 Nettoyage du dossier storage..." -ForegroundColor Yellow
Remove-Item "$tempDir/storage/logs/*.log" -Force -ErrorAction SilentlyContinue
Remove-Item "$tempDir/storage/framework/cache/data/*" -Force -ErrorAction SilentlyContinue
Remove-Item "$tempDir/storage/framework/sessions/*" -Force -ErrorAction SilentlyContinue
Remove-Item "$tempDir/storage/framework/views/*" -Force -ErrorAction SilentlyContinue

# 6. Créer les dossiers vides nécessaires
$emptyDirs = @(
    "$tempDir/storage/app/public",
    "$tempDir/storage/framework/cache/data",
    "$tempDir/storage/framework/sessions",
    "$tempDir/storage/framework/views",
    "$tempDir/storage/logs"
)

foreach ($dir in $emptyDirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

# 7. Créer un fichier .env.example pour LWS
$envExample = @"
APP_NAME="Mayelia Mobilité"
APP_ENV=production
APP_KEY=
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
"@

Set-Content -Path "$tempDir/.env.example" -Value $envExample

# 8. Créer un README de déploiement
$readme = @"
# DÉPLOIEMENT LWS - INSTRUCTIONS

## 1. Télécharger l'archive sur LWS
- Via FTP ou File Manager
- Extraire dans /home/votre_user/

## 2. Créer le fichier .env
- Copier .env.example vers .env
- Remplir les informations de base de données LWS

## 3. Installer les dépendances (via SSH)
``````bash
cd /home/votre_user/public_html
composer install --no-dev --optimize-autoloader
``````

## 4. Configurer les permissions
``````bash
chmod -R 755 storage bootstrap/cache
``````

## 5. Migrer la base de données
``````bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=GuichetSeeder
``````

## 6. Optimiser pour production
``````bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
``````

## 7. Tester
- Accéder à votre-domaine.com
- Login : admin@mayelia.com / password (à changer)

Voir DEPLOIEMENT_LWS.md pour plus de détails.
"@

Set-Content -Path "$tempDir/README_DEPLOY.md" -Value $readme

# 9. Copier le guide de déploiement
if (Test-Path "DEPLOIEMENT_LWS.md") {
    Copy-Item -Path "DEPLOIEMENT_LWS.md" -Destination "$tempDir/DEPLOIEMENT_LWS.md" -Force
}

# 10. Créer l'archive ZIP
Write-Host "🗜️ Création de l'archive..." -ForegroundColor Yellow
Compress-Archive -Path "$tempDir/*" -DestinationPath $zipName -Force

# 11. Nettoyer
Remove-Item $tempDir -Recurse -Force

# 12. Résumé
$zipSize = (Get-Item $zipName).Length / 1MB
Write-Host ""
Write-Host "✅ Archive créée avec succès !" -ForegroundColor Green
Write-Host "📦 Fichier : $zipName" -ForegroundColor Cyan
Write-Host "📏 Taille : $([math]::Round($zipSize, 2)) MB" -ForegroundColor Cyan
Write-Host ""
Write-Host "📤 Prochaines étapes :" -ForegroundColor Yellow
Write-Host "  1. Télécharger $zipName sur LWS" -ForegroundColor White
Write-Host "  2. Extraire dans /home/votre_user/" -ForegroundColor White
Write-Host "  3. Suivre les instructions dans README_DEPLOY.md" -ForegroundColor White
Write-Host ""
Write-Host "📖 Documentation complète : DEPLOIEMENT_LWS.md" -ForegroundColor Cyan
