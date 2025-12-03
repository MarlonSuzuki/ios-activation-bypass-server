@echo off
chcp 65001 > nul

cd /d "%~dp0Distribuicao"

echo [*] Criando atalho do ClienteWindows.exe...

powershell -NoProfile -Command "
$WshShell = New-Object -ComObject WScript.Shell
$atalho = $WshShell.CreateShortcut('ClienteWindows.lnk')
$atalho.TargetPath = (Get-Location).Path + '\bin\ClienteWindows.exe'
$atalho.WorkingDirectory = (Get-Location).Path + '\bin'
$atalho.IconLocation = (Get-Location).Path + '\bin\ClienteWindows.exe'
$atalho.Save()
Write-Host '[OK] Atalho criado com sucesso!'
"

echo.
echo Estrutura final:
echo   Distribuicao/
echo   ├── ClienteWindows.lnk  ^(atalho - clique aqui^)
echo   ├── iOS.exe
echo   ├── LEIA-ME.txt
echo   └── bin/
echo       └── ClienteWindows.exe ^(programa real aqui^)
echo.
pause
