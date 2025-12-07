$files = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse | Where-Object { 
    $_.FullName -notlike "*\layouts\dashboard.blade.php" 
}

$totalFiles = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    
    $content = $content -replace 'bg-blue-', 'bg-mayelia-'
    $content = $content -replace 'text-blue-', 'text-mayelia-'
    $content = $content -replace 'border-blue-', 'border-mayelia-'
    $content = $content -replace 'hover:bg-blue-', 'hover:bg-mayelia-'
    $content = $content -replace 'hover:text-blue-', 'hover:text-mayelia-'
    $content = $content -replace 'focus:ring-blue-', 'focus:ring-mayelia-'
    $content = $content -replace 'focus:border-blue-', 'focus:border-mayelia-'
    $content = $content -replace 'ring-blue-', 'ring-mayelia-'
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $totalFiles++
        Write-Host "Updated: $($file.Name)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Total files updated: $totalFiles" -ForegroundColor Yellow
