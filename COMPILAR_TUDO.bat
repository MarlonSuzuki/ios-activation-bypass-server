@echo off
REM Script para compilar tudo no Windows (clique duplo)
setlocal enabledelayedexpansion

cd /d "%~dp0"

REM Executar PowerShell script
powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "& '.\COMPILAR_TUDO.ps1'"

pause
