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
		<span id="mnuUserAccountManagement">{-#mnuUserAccountManagement#-}</span>
		<span id="mnuMenuUserLanguage">{-#mnuMenuUserLanguage#-}</span>
			{-foreach name=LanguageList key=key item=item from=$LanguageList-}
			<span id="mnuUserLanguage-{-$key-}">{-$item-}</span>
			{-/foreach-}
		<span id="mnuUserLogout">{-#mnuUserLogout#-}</span>
	<span id="mnuMenuDatabase">{-#mnuMenuDatabase#-}</span>
		<span id="mnuDatabaseRecordView">{-#mnuDatabaseRecordView#-}</span>
		<span id="mnuDatabaseRecordEdit">{-#mnuDatabaseRecordEdit#-}</span>
		<span id="mnuDatabaseRegionInfo">{-#mnuDatabaseRegionInfo#-}</span>
		<span id="mnuDatabaseExport">{-#mnuDatabaseExport#-}</span>
		<span id="mnuDatabaseCopy">{-#mnuDatabaseCopy#-}</span>
		<span id="mnuDatabaseReplace">{-#mnuDatabaseReplace#-}</span>
		<span id="mnuDatabaseCreate">{-#mnuDatabaseCreate#-}</span>
		<span id="mnuDatabaseConfig">{-#mnuDatabaseConfig#-}</span>
		<span id="mnuDatabaseSelect">{-#mnuDatabaseSelect#-}</span>
		<span id="mnuDatabaseSelectAnother">{-#mnuDatabaseSelectAnother#-}</span>
		<span id="mnuDatacardImport">{-#mnuDatacardImport#-}</span>
		<span id="mnuDatabaseSetAdminUser">{-#mnuDatabaseSetAdminUser#-}</span>
		<span id="mnuAdminDatabases">{-#mnuAdminDatabases#-}</span>
	<span id="mnuMenuHelp">{-#mnuMenuHelp#-}</span>
		<span id="mnuHelpWebsite">{-#mnuHelpWebsite#-}</span>
		<span id="mnuHelpMethodology">{-#mnuHelpMethodology#-}</span>
		<span id="mnuHelpDocumentation">{-#mnuHelpDocumentation#-}</span>
		<span id="mnuHelpAbout">{-#mnuHelpAbout#-}</span>
</div>