$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  iOS Activation Bypass - Setup Completo" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"

# Verificar se PHP já existe
$phpExe = Join-Path $clientDir "php.exe"
if (Test-Path $phpExe) {
    Write-Host "[OK] PHP já está instalado!" -ForegroundColor Green
} else {
    Write-Host "[!] PHP não encontrado!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Escolha uma opção:" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Opção 1: Download automático (requer conexão)" -ForegroundColor White
    Write-Host "  - Executar: .\BAIXAR_PHP.ps1" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Opção 2: Download manual (mais rápido)" -ForegroundColor White
    Write-Host "  1. Acesse: https://windows.php.net/downloads/releases/" -ForegroundColor Gray
    Write-Host "  2. Baixe: php-8.3-nts-Win32-vs17-x64.zip (últimaversão)" -ForegroundColor Gray
    Write-Host "  3. Extraia em: $clientDir" -ForegroundColor Gray
    Write-Host "  4. Depois execute: .\COMPILAR_SEM_PHP.ps1" -ForegroundColor Gray
    Write-Host ""
    exit 1
}

Write-Host "[1/4] PHP validado!" -ForegroundColor Green

# Compilar Cliente
Write-Host "[2/4] Compilando Cliente Windows..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
$compileOutput = dotnet publish -c Release --self-contained -o "..\dist" 2>&1
Pop-Location

$clientExe = Join-Path $baseDir "dist" "ClienteWindows.exe"
if (-not (Test-Path $clientExe)) {
    Write-Host "[ERRO] Compilação falhou!" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Cliente compilado!" -ForegroundColor Green

# Copiar arquivos do servidor
Write-Host "[3/4] Preparando servidor..." -ForegroundColor Yellow
$distDir = Join-Path $baseDir "dist"

$dirs = @("public", "config", "assets")
foreach ($dir in $dirs) {
    $source = Join-Path $baseDir $dir
    if (Test-Path $source) {
        $dest = Join-Path $distDir $dir
        if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
        Copy-Item $source $dest -Recurse -Force
    }
}

# Copiar PHP
Copy-Item (Join-Path $clientDir "php.exe") $distDir -Force
Get-ChildItem $clientDir -Filter "php*.ini" | Copy-Item -Destination $distDir -Force
if (Test-Path (Join-Path $clientDir "ext")) {
    Copy-Item (Join-Path $clientDir "ext") (Join-Path $distDir "ext") -Recurse -Force
}
Write-Host "[OK] Servidor preparado!" -ForegroundColor Green

# README
Write-Host "[4/4] Finalizando..." -ForegroundColor Yellow
$readmeContent = @"
# iOS Activation Bypass - Standalone

## Como Usar

1. Execute: ClienteWindows.exe
   - Servidor inicia automaticamente
   - Cliente abre

2. Escolha servidor e clique "Detect iPhone"

## Tudo incluído:
✅ Cliente compilado
✅ Servidor PHP
✅ Nenhuma instalação necessária

---
Compilado: $(Get-Date -Format 'yyyy-MM-dd HH:mm')
"@
$readmeContent | Out-File (Join-Path $distDir "LEIA-ME.txt") -Encoding UTF8

Write-Host "[OK] Finalizado!" -ForegroundColor Green

# Resumo
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ✓ Tudo Pronto para Distribuição!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📁 Pasta: $distDir" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Yellow
Write-Host "  1. Teste: ClienteWindows.exe (duplo clique)" -ForegroundColor White
Write-Host "  2. Compacte para ZIP:" -ForegroundColor White
Write-Host "     Compress-Archive -Path '$distDir' -DestinationPath 'iOS_Bypass.zip' -Force" -ForegroundColor Cyan
Write-Host "  3. Distribua o ZIP!" -ForegroundColor White
Write-Host ""
