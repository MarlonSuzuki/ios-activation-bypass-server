@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

cd /d "%~dp0Cliente Windows"

echo ═══════════════════════════════════════════════════════
echo [*] Compilando Cliente Windows com .NET 8.0 (Dual-Server)
echo ═══════════════════════════════════════════════════════

rmdir /s /q bin 2>nul
rmdir /s /q obj 2>nul

echo [*] Executando: dotnet publish -c Release --self-contained -o bin
dotnet publish -c Release --self-contained -o bin

if %errorlevel% equ 0 (
    echo.
    echo ═══════════════════════════════════════════════════════
    echo [✓] COMPILADO COM SUCESSO!
    echo ═══════════════════════════════════════════════════════
    echo.
    echo Executavel: ClienteWindows.exe (com fallback Replit+Railway)
    echo.
    pause
) else (
    echo [ERRO] Falha na compilação!
    echo [DICA] Verifique se tem .NET 8.0 SDK instalado: dotnet --version
    pause
)
