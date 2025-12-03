param(
    [string]$phpUrl = "https://windows.php.net/downloads/releases/php-8.3.14-nts-Win32-vs17-x64.zip"
)

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  iOS Activation Bypass - Compilação" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"
$distDir = Join-Path $baseDir "dist"
$phpDir = Join-Path $distDir "php"

# Passo 1: Limpar compilações antigas
Write-Host "[1/6] Limpando compilações antigas..." -ForegroundColor Yellow
if (Test-Path $distDir) { Remove-Item $distDir -Recurse -Force }
New-Item -ItemType Directory -Path $distDir -Force | Out-Null

# Passo 2: Compilar Cliente Windows
Write-Host "[2/6] Compilando Cliente Windows..." -ForegroundColor Yellow
Push-Location $clientDir
dotnet clean -c Release 2>&1 | Out-Null
dotnet publish -c Release --self-contained -o (Join-Path $distDir "build") 2>&1 | Out-Null
Pop-Location

if (-not (Test-Path (Join-Path $distDir "build" "ClienteWindows.exe"))) {
    Write-Host "[ERRO] Compilação falhou!" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Cliente compilado com sucesso!" -ForegroundColor Green

# Passo 3: Copiar arquivos do cliente
Write-Host "[3/6] Copiando arquivos compilados..." -ForegroundColor Yellow
Copy-Item (Join-Path $distDir "build" "*") $distDir -Recurse -Force
Remove-Item (Join-Path $distDir "build") -Recurse -Force

# Passo 4: Copiar servidor PHP
Write-Host "[4/6] Copiando servidor PHP..." -ForegroundColor Yellow
$serverDirs = @("public", "config", "assets")
foreach ($dir in $serverDirs) {
    $source = Join-Path $baseDir $dir
    if (Test-Path $source) {
        Copy-Item $source (Join-Path $distDir $dir) -Recurse -Force
    }
}

# Passo 5: Copiar PHP Portable (se existir)
Write-Host "[5/6] Verificando PHP Portable..." -ForegroundColor Yellow
$phpExeLocal = Join-Path $clientDir "php.exe"
if (Test-Path $phpExeLocal) {
    Write-Host "  ✓ PHP encontrado localmente, copiando..." -ForegroundColor Green
    Copy-Item $phpExeLocal $distDir -Force
    Get-ChildItem (Join-Path $clientDir "php*") -Filter "php*.ini" | Copy-Item -Destination $distDir -Force
    if (Test-Path (Join-Path $clientDir "ext")) {
        Copy-Item (Join-Path $clientDir "ext") (Join-Path $distDir "ext") -Recurse -Force
    }
} else {
    Write-Host "  ⚠ PHP não encontrado em: $phpExeLocal" -ForegroundColor Yellow
    Write-Host "  Instruções:" -ForegroundColor Yellow
    Write-Host "    1. Download: https://windows.php.net/download (thread-safe)" -ForegroundColor White
    Write-Host "    2. Extrair em: $phpExeLocal" -ForegroundColor White
    Write-Host "    3. Executar novamente este script" -ForegroundColor White
}

# Passo 6: Criar README de instruções
Write-Host "[6/6] Criando documentação..." -ForegroundColor Yellow
$readmeContent = @"
# iOS Activation Bypass - Servidor Standalone

## Como Usar

1. Execute **ClienteWindows.exe**
   - Servidor PHP inicia automaticamente
   - Cliente Windows abre

2. Escolha servidor:
   - **Remote Server**: Replit hospedado
   - **Localhost**: Servidor local (automático)
   - **Custom URL**: URL customizada

3. Clique em **"Detect iPhone"**

## Características

✅ Compilado completamente (tudo em um pacote)
✅ Servidor PHP em background
✅ Sem instalação necessária
✅ Suporta múltiplas opções de servidor

## Requisitos

- Windows 7+ (64-bit)
- iPhone conectado via USB
- Nenhuma instalação adicional necessária

## Solução de Problemas

### Porta 5000 já está em uso
- Abra Prompt de Comando e execute:
  netstat -ano | findstr :5000
- Encerre o processo: taskkill /PID <número> /F

### PHP não inicia
- Verifique se php.exe está no mesmo diretório que ClienteWindows.exe
- Verifique se a porta 5000 está disponível

### iPhone não é detectado
- Confira se o iPhone está desbloqueado
- Reconecte o cabo USB
- Reinicie o cliente

---
Versão 1.0 - $(Get-Date -Format 'yyyy-MM-dd')
"@

$readmeContent | Out-File (Join-Path $distDir "LEIA-ME.txt") -Encoding UTF8

# Resumo Final
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ✓ Compilação Concluída!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Pasta de distribuição:" -ForegroundColor White
Write-Host "  $distDir" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Yellow
Write-Host "  1. Abra a pasta 'dist'" -ForegroundColor White
Write-Host "  2. Teste executando: ClienteWindows.exe" -ForegroundColor White
Write-Host "  3. Comprima para ZIP:" -ForegroundColor White
Write-Host "     Compress-Archive -Path '$distDir' -DestinationPath 'iOS_Bypass_Standalone.zip'" -ForegroundColor White
Write-Host ""
Write-Host "Distribuição:" -ForegroundColor Yellow
Write-Host "  Compartilhe o ZIP com qualquer pessoa" -ForegroundColor White
Write-Host "  Basta extrair e executar: ClienteWindows.exe" -ForegroundColor White
Write-Host ""
