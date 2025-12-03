@echo off
chcp 65001 > nul
cls
echo.
echo ========================================
echo Servidor PHP Local - iOS Bypass
echo ========================================
echo.

REM Detectar pasta raiz
cd /d "%~dp0.."
set PASTA_RAIZ=%cd%

REM Verificar PHP
echo [*] Verificando PHP...
php --version >nul 2>&1
if "%ERRORLEVEL%" NEQ "0" (
    echo.
    echo [ERRO] PHP nao encontrado no PATH
    echo.
    echo Siga o tutorial em: PHP_SETUP.txt
    echo.
    pause
    exit /b 1
)

echo [OK] PHP encontrado
echo.

REM Verificar pasta public
if not exist "%PASTA_RAIZ%\public\index.php" (
    echo [ERRO] Pasta public nao encontrada
    echo Pasta atual: %PASTA_RAIZ%
    echo.
    pause
    exit /b 1
)

echo [OK] Servidor encontrado
echo.

REM Iniciar servidor
echo ========================================
echo Servidor iniciando...
echo ========================================
echo.
echo [*] URL: http://localhost:8000
echo [*] Pressione Ctrl+C para parar o servidor
echo.

cd /d "%PASTA_RAIZ%"
php -S 0.0.0.0:8000 -t public
