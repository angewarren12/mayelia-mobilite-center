# Script PowerShell pour build automatique de l'APK
# Usage: .\build-apk.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Build APK - Mayelia Kiosk Flutter" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier que Flutter est installé
Write-Host "Vérification de Flutter..." -ForegroundColor Yellow
$flutterVersion = flutter --version 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERREUR: Flutter n'est pas installé ou pas dans le PATH" -ForegroundColor Red
    exit 1
}
Write-Host "Flutter détecté ✓" -ForegroundColor Green
Write-Host ""

# Nettoyer le projet
Write-Host "Nettoyage du projet..." -ForegroundColor Yellow
flutter clean
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERREUR lors du nettoyage" -ForegroundColor Red
    exit 1
}
Write-Host "Nettoyage terminé ✓" -ForegroundColor Green
Write-Host ""

# Installer les dépendances
Write-Host "Installation des dépendances..." -ForegroundColor Yellow
flutter pub get
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERREUR lors de l'installation des dépendances" -ForegroundColor Red
    exit 1
}
Write-Host "Dépendances installées ✓" -ForegroundColor Green
Write-Host ""

# Build APK Release
Write-Host "Build APK Release en cours..." -ForegroundColor Yellow
Write-Host "Cela peut prendre plusieurs minutes..." -ForegroundColor Gray
flutter build apk --release
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERREUR lors du build" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Afficher le chemin du fichier généré
$apkPath = "build\app\outputs\flutter-apk\app-release.apk"
if (Test-Path $apkPath) {
    $fileInfo = Get-Item $apkPath
    $fileSize = [math]::Round($fileInfo.Length / 1MB, 2)
    
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  BUILD RÉUSSI !" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "APK généré :" -ForegroundColor Cyan
    Write-Host "  $apkPath" -ForegroundColor White
    Write-Host ""
    Write-Host "Taille : $fileSize MB" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Vous pouvez maintenant installer l'APK sur votre tablette." -ForegroundColor Yellow
} else {
    Write-Host "ERREUR: L'APK n'a pas été généré" -ForegroundColor Red
    exit 1
}

