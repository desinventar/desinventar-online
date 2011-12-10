<div id="divMainMenu" style="display:none;">
	<span id="mnuMenuQuery">{-#msearch#-}</span>
		<span id="mnuQueryToggle">{-#mgotoqd#-}</span>
		<span id="mnuQueryNew">{-#mnewsearch#-}</span>
		<span id="mnuQuerySave">{-#msavequery#-}</span>
		<span id="mnuQueryOpen">{-#mopenquery#-}</span>
	<span id="mnuMenuUser">{-#mnuMenuUser#-}</span>
		<span id="mnuUserLogin">{-#mnuUserLogin#-}</span>
		<span id="mnuUserChangeLogin">{-#mnuUserChangeLogin#-}</span>
		<span id="mnuUserEditAccount">{-#mnuUserEditAccount#-}</span>
		<span id="mnuUserLogout">{-#mnuUserLogout#-}</span>
		<span id="mnuMenuUserLanguage">{-#mnuMenuUserLanguage#-}</span>
			{-foreach name=LanguageList key=key item=item from=$LanguageList-}
			<span id="mnuUserLanguage-{-$key-}">{-$item-}</span>
			{-/foreach-}
		<span id="mnuUserQuit">{-#mnuUserQuit#-}</span>
	<span id="mnuMenuDatabase">{-#mnuMenuDatabase#-}</span>
		<span id="mnuDatabaseRecordView">{-#mnuDatabaseRecordView#-}</span>
		<span id="mnuDatabaseRecordEdit">{-#mnuDatabaseRecordEdit#-}</span>
		<span id="mnuDatabaseExport">{-#mnuDatabaseExport#-}</span>
		<span id="mnuDatabaseUpload">{-#mnuDatabaseUpload#-}</span>
		<span id="mnuDatabaseCreate">{-#mnuDatabaseCreate#-}</span>
		<span id="mnuDatabaseConfig">{-#mnuDatabaseConfig#-}</span>
		<span id="mnuDatabaseSelect">{-#mnuDatabaseSelect#-}</span>
		<span id="mnuDatacardImport">{-#mnuDatacardImport#-}</span>
		<span id="mnuDatabaseSetAdminUser">{-#mnuDatabaseSetAdminUser#-}</span>
		<span id="mnuAdminUsers">{-#mnuAdminUsers#-}</span>
		<span id="mnuAdminDatabases">{-#mnuAdminDatabases#-}</span>
	<span id="mnuMenuHelp">{-#mnuMenuHelp#-}</span>
		<span id="mnuHelpWebsite">{-#mnuHelpWebsite#-}</span>
		<span id="mnuHelpMethodology">{-#mnuHelpMethodology#-}</span>
		<span id="mnuHelpDocumentation">{-#mnuHelpDocumentation#-}</span>
		<span id="mnuHelpRegionInfo">{-#mnuHelpRegionInfo#-}</span>
		<span id="mnuHelpAbout">{-#mnuHelpAbout#-}</span>
</div>