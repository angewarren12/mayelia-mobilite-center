# Script pour remplacer les classes blue- par mayelia- dans tous les fichiers Blade
# Exclut certains cas spécifiques où le bleu doit être conservé

$files = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse | Where-Object { 
    $_.FullName -notlike "*\layouts\dashboard.blade.php" 
}

$replacements = @{
    'bg-blue-50' = 'bg-mayelia-50'
    'bg-blue-100' = 'bg-mayelia-100'
    'bg-blue-200' = 'bg-mayelia-200'
    'bg-blue-300' = 'bg-mayelia-300'
    'bg-blue-400' = 'bg-mayelia-400'
    'bg-blue-500' = 'bg-mayelia-500'
    'bg-blue-600' = 'bg-mayelia-600'
    'bg-blue-700' = 'bg-mayelia-700'
    'bg-blue-800' = 'bg-mayelia-800'
    'bg-blue-900' = 'bg-mayelia-900'
    'text-blue-50' = 'text-mayelia-50'
    'text-blue-100' = 'text-mayelia-100'
    'text-blue-200' = 'text-mayelia-200'
    'text-blue-300' = 'text-mayelia-300'
    'text-blue-400' = 'text-mayelia-400'
    'text-blue-500' = 'text-mayelia-500'
    'text-blue-600' = 'text-mayelia-600'
    'text-blue-700' = 'text-mayelia-700'
    'text-blue-800' = 'text-mayelia-800'
    'text-blue-900' = 'text-mayelia-900'
    'border-blue-50' = 'border-mayelia-50'
    'border-blue-100' = 'border-mayelia-100'
    'border-blue-200' = 'border-mayelia-200'
    'border-blue-300' = 'border-mayelia-300'
    'border-blue-400' = 'border-mayelia-400'
    'border-blue-500' = 'border-mayelia-500'
    'border-blue-600' = 'border-mayelia-600'
    'border-blue-700' = 'border-mayelia-700'
    'border-blue-800' = 'border-mayelia-800'
    'border-blue-900' = 'border-mayelia-900'
    'hover:bg-blue-50' = 'hover:bg-mayelia-50'
    'hover:bg-blue-100' = 'hover:bg-mayelia-100'
    'hover:bg-blue-200' = 'hover:bg-mayelia-200'
    'hover:bg-blue-300' = 'hover:bg-mayelia-300'
    'hover:bg-blue-400' = 'hover:bg-mayelia-400'
    'hover:bg-blue-500' = 'hover:bg-mayelia-500'
    'hover:bg-blue-600' = 'hover:bg-mayelia-600'
    'hover:bg-blue-700' = 'hover:bg-mayelia-700'
    'hover:bg-blue-800' = 'hover:bg-mayelia-800'
    'hover:bg-blue-900' = 'hover:bg-mayelia-900'
    'hover:text-blue-50' = 'hover:text-mayelia-50'
    'hover:text-blue-100' = 'hover:text-mayelia-100'
    'hover:text-blue-200' = 'hover:text-mayelia-200'
    'hover:text-blue-300' = 'hover:text-mayelia-300'
    'hover:text-blue-400' = 'hover:text-mayelia-400'
    'hover:text-blue-500' = 'hover:text-mayelia-500'
    'hover:text-blue-600' = 'hover:text-mayelia-600'
    'hover:text-blue-700' = 'hover:text-mayelia-700'
    'hover:text-blue-800' = 'hover:text-mayelia-800'
    'hover:text-blue-900' = 'hover:text-mayelia-900'
    'focus:ring-blue-500' = 'focus:ring-mayelia-500'
    'focus:ring-blue-600' = 'focus:ring-mayelia-600'
    'focus:border-blue-500' = 'focus:border-mayelia-500'
    'focus:border-blue-600' = 'focus:border-mayelia-600'
    'ring-blue-500' = 'ring-mayelia-500'
    'ring-blue-600' = 'ring-mayelia-600'
}

$totalFiles = 0
$totalReplacements = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    $fileReplacements = 0
    
    foreach ($key in $replacements.Keys) {
        if ($content -match [regex]::Escape($key)) {
            $content = $content -replace [regex]::Escape($key), $replacements[$key]
            $fileReplacements++
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $totalFiles++
        $totalReplacements += $fileReplacements
        Write-Host "✓ Mis à jour: $($file.FullName.Replace($PWD.Path + '\', ''))" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Résumé:" -ForegroundColor Cyan
Write-Host "  Fichiers modifiés: $totalFiles" -ForegroundColor Yellow
Write-Host "  Types de remplacements: $totalReplacements" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
