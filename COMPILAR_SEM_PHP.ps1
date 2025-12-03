$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Compilando (assumindo PHP já instalado)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"
$phpExe = Join-Path $clientDir "php.exe"

if (-not (Test-Path $phpExe)) {
    Write-Host "[ERRO] PHP não encontrado em: $phpExe" -ForegroundColor Red
    Write-Host ""
    Write-Host "Instruções:" -ForegroundColor Yellow
    Write-Host "  1. Baixe PHP: https://windows.php.net/downloads/releases/" -ForegroundColor White
    Write-Host "  2. Extraia em: $clientDir" -ForegroundColor White
    Write-Host "  3. Execute: .\COMPILAR_SEM_PHP.ps1" -ForegroundColor White
    exit 1
}

Write-Host "[1/3] PHP validado!" -ForegroundColor Green

# Compilar
Write-Host "[2/3] Compilando Client..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
dotnet publish -c Release --self-contained -o "..\dist" 2>&1 | Out-Null
Pop-Location

if (-not (Test-Path (Join-Path $baseDir "dist" "ClienteWindows.exe"))) {
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

Copy-Item (Join-Path $clientDir "php.exe") $distDir -Force
Get-ChildItem $clientDir -Filter "php*.ini" | Copy-Item -Destination $distDir -Force
if (Test-Path (Join-Path $clientDir "ext")) {
    Copy-Item (Join-Path $clientDir "ext") (Join-Path $distDir "ext") -Recurse -Force
}

Write-Host "[OK] Pronto!" -ForegroundColor Green
Write-Host ""
Write-Host "Distribuição em: $distDir" -ForegroundColor Cyan
Write-Host ""
Write-Host "Compactar para ZIP:" -ForegroundColor Yellow
Write-Host "  Compress-Archive -Path '$distDir' -DestinationPath 'iOS_Bypass.zip' -Force" -ForegroundColor Cyan
