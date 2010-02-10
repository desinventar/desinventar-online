;
; NSIS - Install Script
; Project : DesInventar - Sample Databases
; - Unpack sample databases in MS4W/Data dir.
;
; Modern UI - Include and Configuration Commands
!include "WinMessages.nsh"
!include "MUI2.nsh"
!define mui_abortwarning

SetCompressor         lzma
CRCCheck              On
XPStyle               On
AutoCloseWindow       false
ShowInstDetails       show
SetOverwrite          On
SetDatablockOptimize  On
SetCompress           auto
RequestExecutionLevel admin
WindowIcon            On
InstProgressFlags     smooth

; Language Selection, First is default
!insertmacro MUI_LANGUAGE "Spanish";
!insertmacro MUI_LANGUAGE "English";
!define      NAME    "DesInventar"
!define      MAJORVER "8"
!define      MINORVER "2.0.72"
!define      PUBLISHER "Corporación OSSO - DesInventar Project http://www.desinventar.org"
!define      VERSION "${MAJORVER}.${MINORVER}"
!define      SHORTNAME "${NAME}${MAJORVER}"
!define      REGBASE "Software\OSSO\${SHORTNAME}"
!define      HTTPDPORT "8081"

Name    "${NAME} ${MAJORVER}"
Caption "${NAME} ${VERSION} ${__DATE__}"
BrandingText "(c) 1998-2009 ${PUBLISHER}"
OutFile Setup/desinventar-sample-databases-${VERSION}.exe
InstallDir "$PROGRAMFILES\${Name}${MAJORVER}"
InstallDirRegKey HKLM ${REGBASE} "Install_Dir"

; TextReplace Plugin
!include "TextReplace.nsh"
!include "nsDialogs.nsh" ;part of nsis installation
!include "LogicLib.nsh" ;part of nsis installation
!include "WordFunc.nsh"
!insertmacro WordReplace

; Installer Pages
;!insertmacro MUI_PAGE_WELCOME
!insertmacro MUI_PAGE_LICENSE "Files\license\license.txt"
!insertmacro MUI_PAGE_COMPONENTS
!insertmacro MUI_PAGE_DIRECTORY
!insertmacro MUI_PAGE_INSTFILES
;!insertmacro MUI_PAGE_FINISH

; UnInstall Pages
;!insertmacro MUI_UNPAGE_WELCOME
;!insertmacro MUI_UNPAGE_CONFIRM
;!insertmacro MUI_UNPAGE_INSTFILES
;!insertmacro MUI_UNPAGE_FINISH

Var INSTDIR_forward
Var Return
Var Port
Var Dialog
Var Label
Var Text
Var bContinue
Var hasDesInventar

Function checkDesInventarInstall
	Push $R0
	ClearErrors
	; DesInventar 8 Registry Key
	ReadRegStr $R0 HKLM "Software\OSSO\DesInventar8" "Install_Dir"
	IfErrors 0 DesInventarInstalled
	; No key found, return -1 for error code
	StrCpy $R0 "-1"
DesInventarInstalled:
	Exch $R0
FunctionEnd

; Callback Functions
Function .onInit
    !insertmacro MUI_LANGDLL_DISPLAY

	call checkDesInventarInstall
	pop $hasDesInventar
	push $hasDesInventar
	pop $bContinue
	${if} $bContinue < 0
	    MessageBox MB_OK|MB_ICONSTOP "Cannot locate DesInventar8 Installation. Please install this package first." IDYES NoAbort
	    Abort ;
	NoAbort:
	${endif}
FunctionEnd

Section "DesInventar Sample Databases"
	SectionIn RO
	SetShellVarContext all
	
	; Extract sample database files into install directory
	!define distFile "di8_ElSalvador_DisasterDatabase.zip"
	IfFileExists "$EXEDIR\${distFile}" continue1 skip1
	continue1:
       ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\data'
	skip1:
	!undef distFile
SectionEnd
/*
!if ${INSTALLMODE} == 'install'
Section "Install Sample Database Data"
	SetShellVarContext all
	
	${if} $bContinue > 0
		; Install Sample Data files
		!define distFile "di82SampleDatabases.zip"
		IfFileExists "$EXEDIR\${distFile}" continue1 skip1
		continue1:
		ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\data'
		skip1:
		!undef distFile
	${endif}
SectionEnd
!endif
*/
