# Script PowerShell pour vider le cache Laravel après modification des routes/contrôleurs
# Usage: .\clear-cache-qms.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Clear Laravel Cache - QMS Fix" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier que nous sommes dans le bon répertoire
if (-not (Test-Path "artisan")) {
    Write-Host "ERREUR: Le fichier artisan n'a pas été trouvé." -ForegroundColor Red
    Write-Host "Assurez-vous d'exécuter ce script depuis la racine du projet Laravel." -ForegroundColor Yellow
    exit 1
}

Write-Host "Vidage du cache Laravel..." -ForegroundColor Yellow
Write-Host ""

# Vider le cache de configuration
Write-Host "1. Cache de configuration..." -ForegroundColor Gray
php artisan config:clear
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ⚠️  Erreur lors du vidage du cache de configuration" -ForegroundColor Yellow
} else {
    Write-Host "   ✓ Cache de configuration vidé" -ForegroundColor Green
}

# Vider le cache des routes
Write-Host "2. Cache des routes..." -ForegroundColor Gray
php artisan route:clear
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ⚠️  Erreur lors du vidage du cache des routes" -ForegroundColor Yellow
} else {
    Write-Host "   ✓ Cache des routes vidé" -ForegroundColor Green
}

# Vider le cache de l'application
Write-Host "3. Cache de l'application..." -ForegroundColor Gray
php artisan cache:clear
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ⚠️  Erreur lors du vidage du cache de l'application" -ForegroundColor Yellow
} else {
    Write-Host "   ✓ Cache de l'application vidé" -ForegroundColor Green
}

# Vider le cache des vues
Write-Host "4. Cache des vues..." -ForegroundColor Gray
php artisan view:clear
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ⚠️  Erreur lors du vidage du cache des vues" -ForegroundColor Yellow
} else {
    Write-Host "   ✓ Cache des vues vidé" -ForegroundColor Green
}

# Optimiser l'application (optionnel)
Write-Host ""
Write-Host "5. Optimisation de l'application..." -ForegroundColor Gray
php artisan optimize:clear
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ⚠️  Erreur lors de l'optimisation" -ForegroundColor Yellow
} else {
    Write-Host "   ✓ Application optimisée" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  CACHE VIDÉ AVEC SUCCÈS !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Les modifications du contrôleur QmsController sont maintenant actives." -ForegroundColor Yellow
Write-Host "Testez l'API /qms/api/queue/{centre} pour vérifier que l'erreur 403 est résolue." -ForegroundColor Yellow
Write-Host ""

