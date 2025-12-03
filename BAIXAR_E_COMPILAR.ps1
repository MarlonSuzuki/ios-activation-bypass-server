param(
    [string]$phpUrl = "https://windows.php.net/downloads/releases/php-8.3.14-nts-Win32-vs17-x64.zip"
)

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  iOS Activation Bypass - Setup Completo" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"

# Passo 1: Baixar PHP
Write-Host "[1/5] Baixando PHP Portable..." -ForegroundColor Yellow
$phpZip = Join-Path $clientDir "php.zip"

try {
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $phpUrl -OutFile $phpZip -UseBasicParsing
    Write-Host "[OK] PHP baixado com sucesso!" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] Falha ao baixar PHP!" -ForegroundColor Red
    Write-Host "URL: $phpUrl" -ForegroundColor Red
    exit 1
}

# Passo 2: Extrair PHP
Write-Host "[2/5] Extraindo PHP..." -ForegroundColor Yellow
Expand-Archive -Path $phpZip -DestinationPath $clientDir -Force
Remove-Item $phpZip -Force
Write-Host "[OK] PHP extraído com sucesso!" -ForegroundColor Green

# Passo 3: Verificar estrutura PHP
Write-Host "[3/5] Verificando PHP..." -ForegroundColor Yellow
$phpExe = Join-Path $clientDir "php.exe"
if (-not (Test-Path $phpExe)) {
    Write-Host "[ERRO] PHP.exe não encontrado após extração!" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] PHP válido!" -ForegroundColor Green

# Passo 4: Compilar Cliente
Write-Host "[4/5] Compilando Cliente Windows..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
$compileOutput = dotnet publish -c Release --self-contained -o "..\dist" 2>&1
Pop-Location

$clientExe = Join-Path $baseDir "dist" "ClienteWindows.exe"
if (-not (Test-Path $clientExe)) {
    Write-Host "[ERRO] Compilação falhou!" -ForegroundColor Red
    Write-Host $compileOutput -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Cliente compilado com sucesso!" -ForegroundColor Green

# Passo 5: Copiar arquivos do servidor
Write-Host "[5/5] Preparando servidor..." -ForegroundColor Yellow
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

# Copiar PHP para dist
Copy-Item (Join-Path $clientDir "php.exe") $distDir -Force
Get-ChildItem $clientDir -Filter "php*.ini" | Copy-Item -Destination $distDir -Force
if (Test-Path (Join-Path $clientDir "ext")) {
    Copy-Item (Join-Path $clientDir "ext") (Join-Path $distDir "ext") -Recurse -Force
}

Write-Host "[OK] Servidor preparado!" -ForegroundColor Green

# README final
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
Write-Host "  3. Distribua o ZIP (~100MB)" -ForegroundColor White
Write-Host ""
Write-Host "Qualquer pessoa que baixar o ZIP pode executar sem instalar nada!" -ForegroundColor Green
Write-Host ""
