@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo ============================================
echo Cliente iOS Activation Bypass
echo ============================================
echo.

if exist "ClienteWindows.exe" (
    echo [*] Iniciando aplicação...
    start ClienteWindows.exe
    exit /b 0
)

if exist "ClienteWindows.dll" (
    echo [*] Iniciando com dotnet...
    dotnet ClienteWindows.dll
    exit /b 0
)

echo [ERRO] Executável não encontrado!
echo Certifique-se que ClienteWindows.exe ou ClienteWindows.dll estão nesta pasta
pause
exit /b 1
