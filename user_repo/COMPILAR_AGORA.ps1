$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Compilando iOS Activation Bypass" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"
$phpExe = Join-Path $clientDir "php.exe"

# Verificar PHP
Write-Host "[1/3] Verificando PHP..." -ForegroundColor Yellow
if (-not (Test-Path $phpExe)) {
    Write-Host "[ERRO] php.exe não encontrado em: $clientDir" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] PHP encontrado!" -ForegroundColor Green

# Compilar
Write-Host "[2/3] Compilando..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
dotnet publish -c Release --self-contained -o "..\dist" 2>&1 | Out-Null
Pop-Location

$clientExe = Join-Path $baseDir "dist" "ClienteWindows.exe"
if (-not (Test-Path $clientExe)) {
    Write-Host "[ERRO] Compilação falhou!" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Cliente compilado!" -ForegroundColor Green

# Copiar arquivos
Write-Host "[3/3] Preparando distribuição..." -ForegroundColor Yellow
$distDir = Join-Path $baseDir "dist"

foreach ($dir in @("public", "config", "assets")) {
    $source = Join-Path $baseDir $dir
    if (Test-Path $source) {
        $dest = Join-Path $distDir $dir
        if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
        Copy-Item $source $dest -Recurse -Force
    }
}

# Copiar PHP
Copy-Item (Join-Path $clientDir "php.exe") $distDir -Force
Get-ChildItem $clientDir -Filter "php*.ini" | Copy-Item -Destination $distDir -Force 2>$null
if (Test-Path (Join-Path $clientDir "ext")) {
    Copy-Item (Join-Path $clientDir "ext") (Join-Path $distDir "ext") -Recurse -Force
}

Write-Host "[OK] Pronto!" -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ✓ Compilação Concluída!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📁 Pasta: dist\" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximo passo - Criar ZIP:" -ForegroundColor Yellow
Write-Host "  Compress-Archive -Path '$distDir' -DestinationPath 'iOS_Bypass.zip' -Force" -ForegroundColor Green
Write-Host ""
