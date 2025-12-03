$ErrorActionPreference = "Stop"

Write-Host "Baixando PHP..." -ForegroundColor Yellow

$baseDir = Get-Location
$clientDir = Join-Path $baseDir "Cliente Windows"
$phpZip = Join-Path $clientDir "php.zip"

# Tentar múltiplas URLs
$urls = @(
    "https://windows.php.net/downloads/releases/php-8.3.13-nts-Win32-vs17-x64.zip",
    "https://windows.php.net/downloads/releases/php-8.2.13-nts-Win32-vs17-x64.zip"
)

$downloaded = $false
foreach ($url in $urls) {
    try {
        Write-Host "Tentando: $url" -ForegroundColor Gray
        $ProgressPreference = 'SilentlyContinue'
        Invoke-WebRequest -Uri $url -OutFile $phpZip -UseBasicParsing -TimeoutSec 60
        $downloaded = $true
        Write-Host "[OK] PHP baixado!" -ForegroundColor Green
        break
    } catch {
        Write-Host "  Falhou, tentando próxima..." -ForegroundColor Gray
    }
}

if (-not $downloaded) {
    Write-Host "[ERRO] Não consegui baixar de nenhuma URL!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Download manual:" -ForegroundColor Yellow
    Write-Host "  1. https://windows.php.net/downloads/releases/" -ForegroundColor Cyan
    Write-Host "  2. Procure: php-8.3-nts-Win32-vs17-x64.zip" -ForegroundColor Cyan
    Write-Host "  3. Salve em: $clientDir\php.zip" -ForegroundColor Cyan
    Write-Host "  4. Execute: .\COMPILAR_SEM_PHP.ps1" -ForegroundColor Cyan
    exit 1
}

Write-Host "Extraindo PHP..." -ForegroundColor Yellow
Expand-Archive -Path $phpZip -DestinationPath $clientDir -Force
Remove-Item $phpZip -Force

if (Test-Path (Join-Path $clientDir "php.exe")) {
    Write-Host "[OK] PHP pronto! Agora execute:" -ForegroundColor Green
    Write-Host "  .\BAIXAR_E_COMPILAR_V2.ps1" -ForegroundColor Cyan
} else {
    Write-Host "[ERRO] PHP.exe não encontrado após extração!" -ForegroundColor Red
    exit 1
}
