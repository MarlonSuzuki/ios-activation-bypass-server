; iOS Activation Bypass Installer (NSIS)
; Compile with: makensis CRIAR_INSTALADOR_NSIS.nsi

!include "MUI2.nsh"
!include "WinMessages.nsh"

; App Details
Name "iOS Activation Bypass"
OutFile "iOS_Activation_Bypass_Setup.exe"
InstallDir "$PROGRAMFILES\iOS Activation Bypass"
InstallDirRegKey HKCU "Software\iOS Activation Bypass" "Install_Dir"

; Pages
!insertmacro MUI_PAGE_WELCOME
!insertmacro MUI_PAGE_DIRECTORY
!insertmacro MUI_PAGE_INSTFILES
!insertmacro MUI_PAGE_FINISH

!insertmacro MUI_LANGUAGE "Portuguese"
!insertmacro MUI_LANGUAGE "English"

; Installer sections
Section "Install"
  SetOutPath "$INSTDIR"
  
  ; Copy files from dist folder
  File /r "dist\*.*"
  
  ; Create Start Menu shortcuts
  CreateDirectory "$SMPROGRAMS\iOS Activation Bypass"
  CreateShortCut "$SMPROGRAMS\iOS Activation Bypass\iOS Activation Bypass.lnk" "$INSTDIR\ClienteWindows.exe" "" "$INSTDIR\app.ico"
  CreateShortCut "$SMPROGRAMS\iOS Activation Bypass\Uninstall.lnk" "$INSTDIR\uninstall.exe"
  
  ; Create Desktop shortcut
  CreateShortCut "$DESKTOP\iOS Activation Bypass.lnk" "$INSTDIR\ClienteWindows.exe" "" "$INSTDIR\app.ico"
  
  ; Write uninstaller
  WriteUninstaller "$INSTDIR\uninstall.exe"
  
  ; Registry
  WriteRegStr HKCU "Software\iOS Activation Bypass" "Install_Dir" "$INSTDIR"
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\iOS Activation Bypass" "DisplayName" "iOS Activation Bypass"
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\iOS Activation Bypass" "UninstallString" "$INSTDIR\uninstall.exe"
SectionEnd

; Uninstaller
Section "Uninstall"
  ; Remove shortcuts
  Delete "$SMPROGRAMS\iOS Activation Bypass\iOS Activation Bypass.lnk"
  Delete "$SMPROGRAMS\iOS Activation Bypass\Uninstall.lnk"
  RMDir "$SMPROGRAMS\iOS Activation Bypass"
  Delete "$DESKTOP\iOS Activation Bypass.lnk"
  
  ; Remove files
  RMDir /r "$INSTDIR"
  
  ; Remove registry
  DeleteRegKey HKCU "Software\iOS Activation Bypass"
  DeleteRegKey HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\iOS Activation Bypass"
SectionEnd
