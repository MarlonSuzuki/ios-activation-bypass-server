@echo off
chcp 65001 > nul
cd /d "%~dp0"

echo ============================================
echo Cliente iOS Activation Bypass
echo ============================================
echo.

REM Verifica se já está compilado
if exist "bin\Release\net10.0-windows\ClienteWindows.exe" (
    echo [OK] Executável encontrado. Iniciando...
    echo.
    bin\Release\net10.0-windows\ClienteWindows.exe
    if "%ERRORLEVEL%" NEQ "0" (
        echo.
        echo [ERRO] Erro ao executar: %ERRORLEVEL%
        pause
    )
    exit /b 0
)

if exist "bin\Release\net10.0-windows\ClienteWindows.dll" (
    echo [OK] DLL encontrada. Iniciando com dotnet...
    echo.
    dotnet bin\Release\net10.0-windows\ClienteWindows.dll
    if "%ERRORLEVEL%" NEQ "0" (
        echo.
        echo [ERRO] Erro ao executar: %ERRORLEVEL%
        pause
    )
    exit /b 0
)

echo [*] Executável não encontrado. Compilando...
echo.

REM Verifica se dotnet está disponível
dotnet --version >nul 2>&1
if "%ERRORLEVEL%" NEQ "0" (
    echo [ERRO] .NET SDK não encontrado!
    echo.
    echo Instale de: https://dotnet.microsoft.com/download
    echo.
    pause
    exit /b 1
)

echo [*] Compilando projeto...
dotnet build -c Release
if "%ERRORLEVEL%" NEQ "0" (
    echo.
    echo [ERRO] Compilação falhou!
    pause
    exit /b 1
)

echo.
echo [OK] Compilação concluída! Iniciando...
echo.

if exist "bin\Release\net10.0-windows\ClienteWindows.exe" (
    bin\Release\net10.0-windows\ClienteWindows.exe
) else if exist "bin\Release\net10.0-windows\ClienteWindows.dll" (
    dotnet bin\Release\net10.0-windows\ClienteWindows.dll
) else (
    echo [ERRO] Nenhum executável encontrado!
    pause
    exit /b 1
)
