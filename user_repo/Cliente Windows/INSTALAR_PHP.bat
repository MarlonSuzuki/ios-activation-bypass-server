@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo ============================================
echo Instalador Automático de PHP
echo ============================================
echo.

REM Verifica se já tem PHP instalado
php -v >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] PHP já está instalado!
    php -v
    pause
    exit /b 0
)

echo [1] Verificando Chocolatey...
choco --version >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Chocolatey encontrado!
    echo [*] Instalando PHP via Chocolatey...
    choco install php -y
    echo [OK] PHP instalado!
    pause
    exit /b 0
)

echo [ERRO] Chocolatey não encontrado.
echo.
echo Opções:
echo 1. Instale Chocolatey (recomendado):
echo    - Abra PowerShell como ADMIN
echo    - Execute: Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
echo    - Execute: [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
echo.
echo 2. Ou baixe PHP manualmente:
echo    - Acesse: https://windows.php.net/download/
echo    - Baixe a versão "Non Thread Safe (NTS) x64" mais recente
echo    - Descompacte em: C:\php
echo    - Execute INICIAR_SERVIDOR.bat
echo.
pause
