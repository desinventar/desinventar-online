;
; NSIS - Install Script
; Project : DesInventar
; - Install MS4W (Apache + PHP etc.)
; - Install DesInventar
; - Create Apache Service
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
WindowIcon  On
InstProgressFlags smooth

!insertmacro MUI_LANGUAGE "English";
!define      NAME    "DesInventar"
!define      MAJORVER "8"
!define      MINORVER "2.0"
!define      PUBLISHER "DesInventar Project - Corporación OSSO"
!define      VERSION "${MAJORVER}.${MINORVER}"
!define      SHORTNAME "DesInventar${MAJORVER}"
!define      REGBASE "Software\OSSO\${SHORTNAME}"
!define      HTTPDPORT "8081"

Name    "${NAME} ${MAJORVER}"
Caption "${NAME} ${VERSION} ${__DATE__}"
BrandingText "(c) 1998-2009 ${PUBLISHER}"
OutFile Setup/desinventar-${VERSION}.exe
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
Page custom apachePortPage apachePortPageLeave ": Apache Port"
!insertmacro MUI_PAGE_INSTFILES
;!insertmacro MUI_PAGE_FINISH

; UnInstall Pages
!insertmacro MUI_UNPAGE_WELCOME
!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES
!insertmacro MUI_UNPAGE_FINISH

Var INSTDIR_forward
Var Return
Var Port
Var Dialog
Var Label
Var Text

; Installer's Pages
;Page components
;Page directory
;Page custom apachePortPage apachePortPageLeave ": Apache Port"
;Page instfiles

;UninstPage uninstConfirm
;UninstPage instfiles

; Custom Pages
Function apachePortPage
	nsDialogs::Create /NOUNLOAD 1018
	Pop $Dialog
	${If} $Dialog == error
		Abort
	${EndIf}
	Push "${HTTPDPORT}"
	Pop $Port
	${NSD_CreateLabel} 0 0 100% 20u "Optionally specify a different \
	   Apache port.  If you think that the shown port is in use, then try \
	   a different port number (above 1024)."
	Pop $Label
	${NSD_CreateLabel} 20u 30u 20% 12u "Apache port: "
	Pop $Label
	${NSD_CreateText} 80u 30u 10% 12u "$Port"
	Pop $Text
	nsDialogs::Show
FunctionEnd

;put the apache port value into a variable
Function apachePortPageLeave
	${NSD_GetText} $Text $Port
FunctionEnd

; Callback Functions
Function .onInit
    !insertmacro MUI_LANGDLL_DISPLAY
FunctionEnd

; Installer Sections
Section "MS4W - MapServer Installation Core"
	SectionIn RO
	SetShellVarContext all

	; Try to stop the current apache service in order to update files...
	SetOutPath '$INSTDIR\ms4w'
    ExecWait 'apache-uninstall.bat' $0

	; Install ms4w core file into install directory
	;!define distFile "ms4w_2.3.1.zip"
	!define distFile "ms4w_3.0_beta7.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR'
	!undef distFile
	
	; Install extJS file into install directory
	!define distFile "ext-2.2.1.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps\ExtJS'
	!undef distFile

	; Install jQuery file into install directory
	!define distFile "jquery-1.3.2.min.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps\jquery'
	!undef distFile

	; Install Smarty file into install directory
	!define distFile "Smarty-2.6.26.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
	!undef distFile
	Rename '$INSTDIR\ms4w\apps\smarty-2.6.26' '$INSTDIR\ms4w\apps\smarty'


	; Install JPGraph file into install directory
	!define distFile "jpgraph-3.0.3.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
	!undef distFile
	Rename '$INSTDIR\ms4w\apps\jpgraph-3.0.3' '$INSTDIR\ms4w\apps\jpgraph'

	; Extract OpenLayers into install directory
	!define distFile "OpenLayers-2.8.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
	!undef distFile
	Rename '$INSTDIR\ms4w\apps\openlayers-2.8' '$INSTDIR\ms4w\apps\openlayers'

	; Sample for Handling a tar.gz file...
	;untgz::extract -d '$INSTDIR' "$EXEDIR\${ms4wfile}"
SectionEnd

Section "Application Install"
	SectionIn RO
	SetShellVarContext all

	; Delete Original htdocs files
	SetOutPath $INSTDIR\ms4w\Apache\htdocs
	Delete *.*

	File /r /x '.svn' Web\*.*

	SetOutPath $INSTDIR
	File Files\license\license.txt

	CreateDirectory $INSTDIR\tmp
	CreateDirectory $INSTDIR\www
	CreateDirectory $INSTDIR\data\main
	CreateDirectory $INSTDIR\data\database
	
	SetOutPath $INSTDIR\data\main
	File Files\database\core.db
	File Files\database\base.db
	File Files\database\desinventar.db

	SetOutPath $INSTDIR\ms4w\httpd.d
	File Files\conf\httpd_extJS.conf
	File Files\conf\httpd_jquery.conf
	File Files\conf\httpd_openlayers.conf
	File Files\conf\httpd_desinventar-8.2-data.conf
	
	;MessageBox MB_OK $INSTDIR"\n"$INSTDIR_forward
	WriteUninstaller "uninstall.exe"
	
	;Store installation folder in registry
	WriteRegStr HKLM ${REGBASE} "Install_Dir" "$INSTDIR"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "DisplayName" "${NAME} ${MAJORVER}"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "UninstallString" "$INSTDIR\uninstall.exe"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "Publisher" "${PUBLISHER}"
	WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "NoModify" 1
	WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "NoRepair" 1
SectionEnd

Section "Application Local Configuration"
	SectionIn RO
	SetShellVarContext all

	; Personalize Configuration Files
	${WordReplace} $INSTDIR "\" "/" "+*" $INSTDIR_forward

	SetOutPath $INSTDIR
	!define FILE "$INSTDIR\ms4w\Apache\conf\httpd.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "Listen 80" "Listen $Port" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\Apache\conf\extra\httpd-manual.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_owtchart.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_php_ogr.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_extJS.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_jquery.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_openlayers.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_desinventar-8.2-data.conf"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\setenv.bat"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "\ms4w" "$INSTDIR\ms4w" "" $Return
	!undef FILE

	!define FILE "$INSTDIR\ms4w\Apache\cgi-bin\php.ini"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "\ms4w" "$INSTDIR\ms4w" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	!undef FILE
SectionEnd

Section "Install Sample Database Data"
	SetShellVarContext all

	; Install Sample Data files
	!define distFile "di82SampleDatabases.zip"
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\data'
	!undef distFile
SectionEnd

Section 'Install Apache Service'
	SectionIn RO
	SetShellVarContext all
	; Modify Scripts to Change Service Name
	!define FILE "$INSTDIR\ms4w\apache-install.bat"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	!undef FILE
	!define FILE "$INSTDIR\ms4w\apache-uninstall.bat"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	!undef FILE
	!define FILE "$INSTDIR\ms4w\apache-restart.bat"
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	!undef FILE
	; Create Apache Service
    SectionIn RO
    SetOutPath '$INSTDIR\ms4w'
    ExecWait 'apache-install.bat' $0
SectionEnd

Section "Uninstall"
	; Remove Apache Service
	SetOutPath '$INSTDIR\ms4w'
	ExecWait 'apache-uninstall.bat' $0

	;Remove All Files
	RMDir /r $INSTDIR
	;Remove StartMenu Links
	;Remove Desktop Shortcuts
	;Delete Registry Items
	DeleteRegKey HKLM "${REGBASE}"
	DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}"
SectionEnd
