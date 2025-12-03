@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║         COMPILADOR - Cliente Windows iOS Activation         ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.

cd /d "%~dp0" || exit /b 1

if not exist "Cliente Windows" (
    echo [ERRO] Pasta "Cliente Windows" não encontrada!
    pause
    exit /b 1
)

echo [1/4] Limpando builds anteriores...
rmdir /s /q "Cliente Windows\bin" 2>nul
rmdir /s /q "Cliente Windows\obj" 2>nul
rmdir /s /q "Distribuicao\bin" 2>nul
echo [OK] Limpeza completa

echo.
echo [2/4] Compilando projeto...
cd "Cliente Windows"
dotnet publish -c Release --self-contained -o bin
if errorlevel 1 (
    echo.
    echo [ERRO] Compilação falhou!
    cd ..
    pause
    exit /b 1
)
echo [OK] Compilação sucesso!

echo.
echo [3/4] Copiando para Distribuicao...
cd ..
if not exist "Distribuicao" mkdir "Distribuicao"
xcopy "Cliente Windows\bin\*" "Distribuicao\bin\" /E /I /Y >nul
if errorlevel 1 (
    echo [ERRO] Falha ao copiar arquivos!
    pause
    exit /b 1
)
echo [OK] Arquivos copiados!

echo.
echo [4/4] Verificando arquivos...
if exist "Distribuicao\bin\ClienteWindows.exe" (
    echo [OK] ✓ ClienteWindows.exe gerado com sucesso!
) else (
    echo [AVISO] ClienteWindows.exe não encontrado!
)

echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                  COMPILAÇÃO CONCLUÍDA!                        ║
echo ║                                                               ║
echo ║  Estrutura gerada:                                            ║
echo ║    Distribuicao/                                              ║
echo ║    ├── ClienteWindows.exe                                     ║
echo ║    ├── iOS.exe (copie da pasta anterior)                     ║
echo ║    ├── LEIA-ME.txt                                            ║
echo ║    └── bin/ (todas as DLLs .NET aqui)                        ║
echo ║                                                               ║
echo ║  Próximo passo: Copiar iOS.exe para Distribuicao/           ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
pause
