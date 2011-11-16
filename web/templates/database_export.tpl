{-config_load file="$lg.conf" section="grpAdminDatabaseExport"-}
<div id="divDatabaseExportWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgAdminDatabaseExportTitle#-}
	</div>
	<div id="divDatabaseExportContent">
		<h4>
			<span id="txtAdminDatabaseExportRegionLabel"></span>
		</h4>
		<br />
		<span id="divAdminDatabaseExportProgress" class="clsAdminDatabaseExport" style="display:none;">
			{-#msgAdminDatabaseExportWait#-} &nbsp;&nbsp;<br />
			<img src="{-$desinventarURL-}/images/loading.gif" alt="" />
		</span>
		<span id="divAdminDatabaseExportResults" class="clsAdminDatabaseExport" style="display:none;">
			{-#msgAdminDatabaseExportComplete#-}
		</span>
		<span id="divAdminDatabaseExportError" class="clsAdminDatabaseExport" style="display:none;">
			{-#msgAdminDatabaseExportError#-}
		</span>
		<br />
	</div>
</div>
<div style="display:none;">
	<span id="msgAdminDatabaseExportButtonSend">{-#msgAdminDatabaseExportButtonSend#-}</span>
	<span id="msgAdminDatabaseExportButtonClose">{-#msgAdminDatabaseExportButtonClose#-}</span>
	<input type="hidden" id="fldAdminDatabaseExportSave" value="1" />
</div>
