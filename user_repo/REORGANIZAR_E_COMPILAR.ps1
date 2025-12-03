$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Reorganizando PHP e Compilando" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"
$phpDir = Join-Path $clientDir "php"

# Procurar pasta PHP com qualquer nome
Write-Host "[1/4] Procurando PHP..." -ForegroundColor Yellow
$phpFolders = Get-ChildItem $clientDir -Directory -Filter "*php*" | Where-Object { $_.Name -like "*php*" }

if ($phpFolders.Count -eq 0) {
    Write-Host "[ERRO] Nenhuma pasta PHP encontrada em: $clientDir" -ForegroundColor Red
    exit 1
}

$phpSource = $phpFolders[0].FullName
Write-Host "  Encontrado: $($phpFolders[0].Name)" -ForegroundColor Green

# Se não for chamado "php", renomear
if ($phpSource -ne $phpDir) {
    Write-Host "  Reorganizando..." -ForegroundColor Yellow
    
    # Remover antiga se existir
    if (Test-Path $phpDir) { 
        Remove-Item $phpDir -Recurse -Force 
    }
    
    # Renomear pasta
    Rename-Item -Path $phpSource -NewName "php" -Force
    Write-Host "  ✓ Renomeado para: php" -ForegroundColor Green
}

# Verificar php.exe
$phpExe = Join-Path $phpDir "php.exe"
if (-not (Test-Path $phpExe)) {
    Write-Host "[ERRO] php.exe não encontrado em: $phpDir" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] PHP pronto!" -ForegroundColor Green

# Compilar Cliente
Write-Host "[2/4] Compilando Cliente Windows..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
$compile = dotnet publish -c Release --self-contained -o "..\dist" 2>&1
Pop-Location

$clientExe = Join-Path $baseDir "dist" "ClienteWindows.exe"
if (-not (Test-Path $clientExe)) {
    Write-Host "[ERRO] Compilação falhou!" -ForegroundColor Red
    Write-Host $compile -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Cliente compilado!" -ForegroundColor Green

# Copiar servidor
Write-Host "[3/4] Preparando servidor..." -ForegroundColor Yellow
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
Copy-Item (Join-Path $phpDir "php.exe") $distDir -Force
Get-ChildItem $phpDir -Filter "php*.ini" | Copy-Item -Destination $distDir -Force
if (Test-Path (Join-Path $phpDir "ext")) {
    Copy-Item (Join-Path $phpDir "ext") (Join-Path $distDir "ext") -Recurse -Force
}
Write-Host "[OK] Servidor pronto!" -ForegroundColor Green

# README
Write-Host "[4/4] Finalizando..." -ForegroundColor Yellow
$readme = @"
# iOS Activation Bypass - Standalone

Compilado: $(Get-Date -Format 'yyyy-MM-dd HH:mm')

## Como Usar

1. Execute: ClienteWindows.exe
2. Servidor inicia automaticamente
3. Escolha servidor e clique "Detect iPhone"

Tudo incluído, nenhuma instalação necessária!
"@
$readme | Out-File (Join-Path $distDir "LEIA-ME.txt") -Encoding UTF8

Write-Host "[OK] Completo!" -ForegroundColor Green

# Resumo
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ✓ Tudo Pronto!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📁 Distribuição: $distDir" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Yellow
Write-Host "  1. Teste: duplo clique em dist\ClienteWindows.exe" -ForegroundColor White
Write-Host "  2. Compactar:" -ForegroundColor White
Write-Host "     Compress-Archive -Path '$distDir' -DestinationPath 'iOS_Bypass.zip' -Force" -ForegroundColor Cyan
Write-Host "  3. Distribua o ZIP (~100MB)" -ForegroundColor White
Write-Host ""
