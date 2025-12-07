# prepare_deploy.ps1

Write-Host "🚀 Préparation du déploiement..." -ForegroundColor Green

# 1. Build assets
Write-Host "📦 Compilation des assets..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors de la compilation des assets." -ForegroundColor Red
    exit 1
}

# 2. Check build folder
if (-not (Test-Path "public/build/manifest.json")) {
    Write-Host "❌ Le dossier public/build n'a pas été généré correctement." -ForegroundColor Red
    exit 1
}

# 3. Create ZIP
$zipName = "mayelia-deploy.zip"
Write-Host "🗜️ Création de l'archive $zipName..." -ForegroundColor Yellow

if (Test-Path $zipName) {
    Remove-Item $zipName
}

# Define exclusion list
$exclude = @(
    "node_modules",
    "vendor",
    ".git",
    ".github",
    ".idea",
    ".vscode",
    "tests",
    "storage/logs/*.log",
    "storage/framework/cache/data/*",
    "storage/framework/sessions/*",
    "storage/framework/views/*",
    ".env",
    "deploy-production.sh",
    "prepare_deploy.ps1",
    "*.zip",
    "*.tar.gz"
)

# Get all files excluding the list
$files = Get-ChildItem -Path . -Recurse -File | Where-Object {
    $path = $_.FullName
    $relPath = $path.Substring((Get-Location).Path.Length + 1)
    
    # Simple check for exclusions (can be improved but sufficient for basic usage)
    $shouldExclude = $false
    foreach ($ex in $exclude) {
        if ($relPath -like "*$ex*" -or $relPath -match "^$ex") {
            $shouldExclude = $true
            break
        }
    }
    -not $shouldExclude
}

# Use Compress-Archive (might be slow for many files, but native)
# Using a temp folder approach is often safer for preserving structure in the zip
$tempDir = "temp_deploy"
if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }
New-Item -ItemType Directory -Path $tempDir | Out-Null

Write-Host "📂 Copie des fichiers vers un dossier temporaire..." -ForegroundColor Yellow
# robocopy is faster but more complex to use with excludes in this context, 
# lets use a simple copy loop or just Compress-Archive with strict selection if possible.
# Actually, Compress-Archive with exclusions is a bit tricky. 
# Better strategy: Zip everything then delete? No.
# Creating a list of files to include is safer.

# Alternative: use 7z if available, or just copy to temp and zip temp.
# Let's copy to temp with exclusions.
# Note: This is a simple script, might take a moment.

$excludePatterns = @(
    "node_modules",
    "vendor", 
    ".git", 
    ".vscode", 
    "tests", 
    "storage\logs", 
    ".env", 
    "*.zip",
    "temp_deploy"
)

# Manual exclusion logic is painful in PS. 
# Let's verify if we can use 'git archive' if git is available?
# "git archive -o mayelia-deploy.zip HEAD" only zips committed files. 
# BUT we need public/build which is likely ignored.
# So git archive + add public/build is a good strategy if git is here.

if (Get-Command git -ErrorAction SilentlyContinue) {
    Write-Host "Create base archive from git..."
    git archive --format=zip --output=$zipName HEAD
    
    # Add public/build to the zip. 
    # PowerShell's Compress-Archive with -Update acts weird sometimes.
    # Let's fall back to a full copy method which is more robust for "everything generated".
}

# Let's go with the Copy to Temp dir verify method.
$skip = @("node_modules", "vendor", ".git", ".vscode", ".idea", "tests", "storage", ".env", $zipName, "temp_deploy")

Get-ChildItem -Path . -Exclude $skip | Copy-Item -Destination $tempDir -Recurse -Force

# Re-create storage structure empty
New-Item -ItemType Directory -Path "$tempDir/storage/app/public" -Force | Out-Null
New-Item -ItemType Directory -Path "$tempDir/storage/framework/cache/data" -Force | Out-Null
New-Item -ItemType Directory -Path "$tempDir/storage/framework/sessions" -Force | Out-Null
New-Item -ItemType Directory -Path "$tempDir/storage/framework/views" -Force | Out-Null
New-Item -ItemType Directory -Path "$tempDir/storage/logs" -Force | Out-Null

# Copy specific generated files we need (like public/build)
# The above copy should have taken public/build if it wasn't excluded.
# "public" is not in skip list, so it should be there.

Write-Host "🗜️ Compression..." -ForegroundColor Yellow
Compress-Archive -Path "$tempDir/*" -DestinationPath $zipName -Force

# Cleanup
Remove-Item $tempDir -Recurse -Force

Write-Host "✅ Terminé ! Archive : $zipName" -ForegroundColor Green
Write-Host "👉 Taille : $(("{0:N2} MB" -f ((Get-Item $zipName).Length / 1MB)))"
