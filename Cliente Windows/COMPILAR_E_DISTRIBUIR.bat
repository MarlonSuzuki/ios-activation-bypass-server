@echo off
cd /d "%~dp0"
echo Diretorio: %cd%
echo.
echo Compilando Cliente Windows...
dotnet publish -c Release

echo.
echo Copiando arquivos para Distribuicao...
xcopy "bin\Release\net10.0-windows\win-x64\publish\ClienteWindows.exe" "..\Distribuicao\" /Y /Q
xcopy "bin\Release\net10.0-windows\win-x64\publish\*.dll" "..\Distribuicao\" /Y /Q
xcopy "bin\Release\net10.0-windows\win-x64\publish\*.json" "..\Distribuicao\" /Y /Q

echo.
echo Pronto! Verifique se iOS.exe esta em ..\Distribuicao\
echo.
pause
