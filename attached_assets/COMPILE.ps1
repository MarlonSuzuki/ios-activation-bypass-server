Write-Host "iOS Activation Bypass - Compilador Windows" -ForegroundColor Cyan
Write-Host "============================================`n" -ForegroundColor Cyan

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$clientDir = Split-Path -Parent $scriptDir

Write-Host "[*] Diretório: $clientDir" -ForegroundColor Yellow
Write-Host "[*] Limpando arquivos antigos..." -ForegroundColor Yellow

if (Test-Path "$clientDir\Cliente Windows\Cliente Windows.csproj") {
    Remove-Item "$clientDir\Cliente Windows\Cliente Windows.csproj" -Force
}
if (Test-Path "$clientDir\Cliente Windows\Program.cs") {
    Remove-Item "$clientDir\Cliente Windows\Program.cs" -Force
}

Write-Host "[✓] Arquivos antigos removidos" -ForegroundColor Green

Write-Host "`n[*] Criando novo projeto..." -ForegroundColor Yellow

$csproj = @'
<Project Sdk="Microsoft.NET.Sdk">
  <PropertyGroup>
    <OutputType>Exe</OutputType>
    <TargetFramework>net10.0</TargetFramework>
    <LangVersion>latest</LangVersion>
  </PropertyGroup>
</Project>
'@

$csproj | Out-File "$clientDir\Cliente Windows\Cliente Windows.csproj" -Encoding UTF8 -Force

Write-Host "[✓] Arquivo de projeto criado" -ForegroundColor Green

Write-Host "`n[*] Copiando código-fonte..." -ForegroundColor Yellow
Copy-Item "$clientDir\attached_assets\client_windows_console_v2.cs" "$clientDir\Cliente Windows\Program.cs" -Force

Write-Host "[✓] Código-fonte pronto" -ForegroundColor Green

Write-Host "`n[*] Compilando (isso pode levar 30 segundos)..." -ForegroundColor Yellow
Write-Host "================================================" -ForegroundColor Yellow

cd "$clientDir\Cliente Windows"
dotnet build -c Release

Write-Host "`n================================================" -ForegroundColor Yellow

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n[✓✓✓] COMPILAÇÃO CONCLUÍDA COM SUCESSO! [✓✓✓]" -ForegroundColor Green
    Write-Host "`nExecutável em:" -ForegroundColor Cyan
    Write-Host "  $clientDir\Cliente Windows\bin\Release\net10.0\Cliente Windows.exe" -ForegroundColor Green
    Write-Host "`nPara executar:" -ForegroundColor Cyan
    Write-Host "  cd `"$clientDir\Cliente Windows\bin\Release\net10.0`"" -ForegroundColor Yellow
    Write-Host "  .\\`"Cliente Windows.exe`"" -ForegroundColor Yellow
} else {
    Write-Host "`n[-] ERRO NA COMPILAÇÃO" -ForegroundColor Red
    Write-Host "Verifique os erros acima" -ForegroundColor Red
}

Write-Host "`nPressione qualquer tecla para sair..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
