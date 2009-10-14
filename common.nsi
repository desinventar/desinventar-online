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
WindowIcon            On
InstProgressFlags     smooth

; 2009-10-02 (jhcaiced) Choose between install modes
!ifndef INSTALLMODE
	!define INSTALLMODE "devel"
!endif

; Language Selection, First is default
!insertmacro MUI_LANGUAGE "Spanish";
!insertmacro MUI_LANGUAGE "English";


!define      NAME    "DesInventar"
!define      MAJORVER "8"
!define      MINORVER "2.0.57"
!define      PUBLISHER "Corporación OSSO - DesInventar Project http://www.desinventar.org"
!define      VERSION "${MAJORVER}.${MINORVER}"
!define      SHORTNAME "${NAME}${MAJORVER}"
!define      REGBASE "Software\OSSO\${SHORTNAME}"
!define      HTTPDPORT "8081"

Name    "${NAME} ${MAJORVER}"
Caption "${NAME} ${VERSION} ${__DATE__}"
BrandingText "(c) 1998-2009 ${PUBLISHER}"
OutFile Setup/desinventar-${INSTALLMODE}-${VERSION}.exe
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
!if ${INSTALLMODE} == 'install'
Page custom apachePortPage apachePortPageLeave ": Apache Port"
!endif
!insertmacro MUI_PAGE_INSTFILES
;!insertmacro MUI_PAGE_FINISH

; UnInstall Pages
;!insertmacro MUI_UNPAGE_WELCOME
!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES
;!insertmacro MUI_UNPAGE_FINISH

Var INSTDIR_forward
Var Return
Var Port
Var Dialog
Var Label
Var Text

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
!if ${INSTALLMODE} == 'install'
Section "Core Files"
	SectionIn RO
	SetShellVarContext all

	; Try to stop the current apache service in order to update files...
	SetOutPath '$INSTDIR\ms4w'
	IfFileExists "$INSTDIR\ms4w\apache-uninstall.bat" continue1 skip1
	continue1:
           ExecWait 'apache-uninstall.bat' $0
        skip1:

	; Install ms4w core file into install directory
	!define distFile "ms4w_3.0_beta7.zip"
	IfFileExists "$EXEDIR\${distFile}" continue2 skip2
	continue2:
           ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR'
        skip2:
	!undef distFile
	
	; Install extJS file into install directory
	!define distFile "ext-2.2.1.zip"
	IfFileExists "$EXEDIR\${distFile}" continue3 skip3
	continue3:
           ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps\ExtJS'
        skip3:
	!undef distFile

	; Install jQuery file into install directory
	!define distFile "jquery-1.3.2.min.zip"
	IfFileExists "$EXEDIR\${distFile}" continue4 skip4
	continue4:
	    ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps\jquery'
	skip4:
	!undef distFile

	; Install Smarty file into install directory
	!define distFile "Smarty-2.6.26.zip"
	IfFileExists "$EXEDIR\${distFile}" continue5 skip5
	continue5:
	    ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
		Rename '$INSTDIR\ms4w\apps\smarty-2.6.26' '$INSTDIR\ms4w\apps\smarty'
	skip5:
	!undef distFile

	; Install JPGraph file into install directory
	!define distFile "jpgraph-3.0.6.zip"
	IfFileExists "$EXEDIR\${distFile}" continue6 skip6
	continue6:
	    ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
		Rename '$INSTDIR\ms4w\apps\jpgraph-3.0.6' '$INSTDIR\ms4w\apps\jpgraph'
	skip6:
	!undef distFile
	!define distFile "jpgraph-3.0.3.zip"
	IfFileExists "$EXEDIR\${distFile}" continue6A skip6A
	continue6A:
	    ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
		Rename '$INSTDIR\ms4w\apps\jpgraph-3.0.3' '$INSTDIR\ms4w\apps\jpgraph'
	skip6A:
	!undef distFile

	; Extract OpenLayers into install directory
	!define distFile "OpenLayers-2.8.zip"
	IfFileExists "$EXEDIR\${distFile}" continue7 skip7
	continue7:
		ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\ms4w\apps'
		Rename '$INSTDIR\ms4w\apps\openlayers-2.8' '$INSTDIR\ms4w\apps\openlayers'
	skip7:
	!undef distFile
SectionEnd
!endif

!if ${INSTALLMODE} == 'install'
Section "Application Local Configuration"
	SectionIn RO
	SetShellVarContext all

	; Personalize Configuration Files
	${WordReplace} $INSTDIR "\" "/" "+*" $INSTDIR_forward

	SetOutPath $INSTDIR
	!define FILE "$INSTDIR\ms4w\Apache\conf\httpd.conf"
	IfFileExists ${FILE} continue1 skip1
	continue1:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "Listen 80" "Listen $Port" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip1:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\Apache\conf\extra\httpd-manual.conf"
	IfFileExists ${FILE} continue2 skip2
	continue2:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip2:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_owtchart.conf"
	IfFileExists ${FILE} continue3 skip3
	continue3:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip3:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_php_ogr.conf"
	IfFileExists ${FILE} continue4 skip4
	continue4:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip4:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_extJS.conf"
	IfFileExists ${FILE} continue5 skip5
	continue5:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip5:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_jquery.conf"
	IfFileExists ${FILE} continue6 skip6
	continue6:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip6:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_openlayers.conf"
	IfFileExists ${FILE} continue7 skip7
	continue7:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip7:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\httpd.d\httpd_desinventar-8.2-data.conf"
	IfFileExists ${FILE} continue8 skip8
	continue8:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	skip8:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\setenv.bat"
	IfFileExists ${FILE} continue9 skip9
	continue9:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "\ms4w" "$INSTDIR\ms4w" "" $Return
	skip9:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\Apache\cgi-bin\php.ini"
	IfFileExists ${FILE} continue10 skip10
	continue10:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "\ms4w" "$INSTDIR\ms4w" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "/ms4w" "$INSTDIR_forward/ms4w" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "post_max_size = 8M" "post_max_size = 48M" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "upload_max_filesize = 2M" "upload_max_filesize = 48M" "" $Return
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "max_execution_time = 30" "max_execution_time = 180" "" $Return
	skip10:
	!undef FILE
	
	; Fix proj/nad/epsg file to add Spherical Mercator Projection
	!define FILE "$INSTDIR\ms4w\proj\nad\epsg"
	IfFileExists ${FILE} continue11 skip11
	continue11:
	FileOpen $4 "${FILE}" a
	FileSeek $4 0 END
	FileWrite $4 "$\r$\n" ; we write a new line
	FileWrite $4 "<900913> +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +no_defs"
	FileWrite $4 "$\r$\n" ; we write an extra line
	FileClose $4 ; and close the file
	skip11:
	!undef FILE
SectionEnd
!endif

Section "Application Install"
	SectionIn RO
	SetShellVarContext all

	; Delete Original htdocs files
	SetOutPath $INSTDIR\ms4w\Apache\htdocs
	Delete *.*

	File /r /x '.svn' Web\*.*

	SetOutPath $INSTDIR
	File Files\license\license.txt
	File Files\icon\${NAME}.ico

	CreateDirectory $INSTDIR\tmp
	CreateDirectory $INSTDIR\www
	CreateDirectory $INSTDIR\www\graphs
	CreateDirectory $INSTDIR\data\main
	CreateDirectory $INSTDIR\data\database

	SetOutPath $INSTDIR\data\main
	File Files\database\core.db
	File Files\database\base.db
	File Files\database\desinventar.db
	File Files\fonts\fontswin.txt

    ; Install worldmap shape file
    CreateDirectory $INSTDIR\data\main\worldmap
	!define distFile "world_adm0.zip"
	IfFileExists "$EXEDIR\${distFile}" installmap skipmap
	installmap:
		ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\data\main\worldmap'
	skipmap:
	!undef distFile

	SetOutPath $INSTDIR\ms4w
	File Files\conf\apache-start.bat
	File Files\conf\apache-stop.bat

	SetOutPath $INSTDIR\ms4w\httpd.d
	File Files\conf\httpd_extJS.conf
	File Files\conf\httpd_jquery.conf
	File Files\conf\httpd_openlayers.conf
	File Files\conf\httpd_desinventar-8.2-data.conf

	;Store installation folder in registry
	WriteRegStr HKLM ${REGBASE} "Install_Dir" "$INSTDIR"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "DisplayName" "${NAME} ${MAJORVER}"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "UninstallString" "$INSTDIR\uninstall.exe"
	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "Publisher" "${PUBLISHER}"
	WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "NoModify" 1
	WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}" "NoRepair" 1
SectionEnd

/*
!if ${INSTALLMODE} == 'install'
Section "Install Sample Database Data"
	SetShellVarContext all

	; Install Sample Data files
	!define distFile "di82SampleDatabases.zip"
	IfFileExists "$EXEDIR\${distFile}" continue1 skip1
	continue1:
	ZipDLL::extractall "$EXEDIR\${distFile}" '$INSTDIR\data'
	skip1:
	!undef distFile
SectionEnd
!endif
*/

!if ${INSTALLMODE} == 'install'
Section 'Install Apache Service'
	SectionIn RO
	SetShellVarContext all

	; Modify Scripts to Change Service Name
	!define FILE "$INSTDIR\ms4w\apache-install.bat"
	IfFileExists ${FILE} continue1 skip1
	continue1:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	skip1:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\apache-uninstall.bat"
	IfFileExists ${FILE} continue2 skip2
	continue2:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	skip2:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\apache-restart.bat"
	IfFileExists ${FILE} continue3 skip3
	continue3:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	skip3:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\apache-start.bat"
	IfFileExists ${FILE} continue4 skip4
	continue4:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	skip4:
	!undef FILE

	!define FILE "$INSTDIR\ms4w\apache-stop.bat"
	IfFileExists ${FILE} continue5 skip5
	continue5:
	${textreplace::ReplaceInFile} "${FILE}" "${FILE}" "MS4W" "${SHORTNAME}" "" $Return
	skip5:
	!undef FILE

	; Create Apache Service
	!define FILE "$INSTDIR\ms4w\apache-install.bat"
	IfFileExists ${FILE} continue6 skip6
	continue6:
		SetOutPath '$INSTDIR\ms4w'
		ExecWait 'apache-install.bat' $0
	skip6:
	!undef FILE
SectionEnd
!endif

!Macro "CreateURL" "URLFile" "URLSite" "URLDesc"
  WriteINIStr "$EXEDIR\${URLFile}.URL" "InternetShortcut" "URL" "${URLSite}"
  SetShellVarContext "all"
  CreateShortCut "$DESKTOP\${URLFile}.lnk" "$EXEDIR\${URLFile}.url" "" \
                 "$EXEDIR\makeURL.exe" 0 "SW_SHOWNORMAL" "" "${URLDesc}"
!macroend

Section "Create Shortcuts"
    SectionIn RO
	SetShellVarContext all
	;MessageBox MB_OK $INSTDIR"\n"$INSTDIR_forward
	SetOutPath '$INSTDIR'
	WriteUninstaller "uninstall.exe"
	CreateDirectory "$SMPROGRAMS\${SHORTNAME}"
	WriteINIStr "$INSTDIR\${SHORTNAME}.url" "InternetShortcut" "URL" "http://127.0.0.1:${HTTPDPORT}"
	WriteINIStr "$INSTDIR\${NAME} Website.url" "InternetShortcut" "URL" "http://www.desinventar.org"
	; "$INSTDIR\${NAME}.ico"
	; "$EXEDIR\makeURL.exe"
	SetShellVarContext "all"
	CreateShortCut "$DESKTOP\${SHORTNAME}.lnk" "$INSTDIR\${SHORTNAME}.url" "" \
                   "$INSTDIR\${NAME}.ico" 0 "SW_SHOWNORMAL" "" "${SHORTNAME} Local"
	;CreateShortCut "$DESKTOP\${SHORTNAME}.lnk" "$INSTDIR\${SHORTNAME}.url" "" \
    ;               "$EXEDIR\makeURL.exe" 0 "SW_SHOWNORMAL" "" "${SHORTNAME} Local"
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME}.lnk" "$INSTDIR\${SHORTNAME}.url" "" \
                   "$INSTDIR\${NAME}.ico" 0 "SW_SHOWNORMAL" "" "${SHORTNAME} Local"
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\${NAME} Project Website.lnk" "$INSTDIR\${NAME} Website.url" "" \
                   "$INSTDIR\${NAME}.ico" 0 "SW_SHOWNORMAL" "" "${SHORTNAME} Local"
	SetOutPath '$INSTDIR\ms4w'
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Restart.lnk" "$INSTDIR\ms4w\apache-restart.bat"
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Start.lnk" "$INSTDIR\ms4w\apache-start.bat"
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Stop.lnk" "$INSTDIR\ms4w\apache-stop.bat"
	CreateShortCut "$SMPROGRAMS\${SHORTNAME}\UnInstall.lnk" "$INSTDIR\uninstall.exe"
SectionEnd

Section "Uninstall"
	SectionIn RO
	SetShellVarContext all
	
	; Always remove uninstaller.exe first
	Delete $INSTDIR\uninstall.exe

	; Remove Apache Service
	SetOutPath '$INSTDIR\ms4w'
	ExecWait 'apache-uninstall.bat' $0

	;Delete Registry Items
	DeleteRegKey HKLM "${REGBASE}"
	DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${SHORTNAME}"
	
	;Remove Desktop Shortcuts
	Delete "$DESKTOP\${SHORTNAME}.lnk"
	
	;Remove StartMenu Links
	Delete "$SMPROGRAMS\${SHORTNAME}\UnInstall.lnk"
	Delete "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME}.lnk"
	Delete "$SMPROGRAMS\${SHORTNAME}\${NAME} Project Website.lnk"
	Delete "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Restart.lnk"
	Delete "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Start.lnk"
	Delete "$SMPROGRAMS\${SHORTNAME}\${SHORTNAME} Apache Stop.lnk"
	RMDir  "$SMPROGRAMS\${SHORTNAME}"

	;Remove All Files
	RMDir /r $INSTDIR
	RMDir $INSTDIR\ms4w
	RMDir $INSTDIR\ms4w
	RMDir $INSTDIR
SectionEnd
