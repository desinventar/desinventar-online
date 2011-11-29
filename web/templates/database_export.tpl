{-config_load file="$lg.conf" section="grpAdminDatabaseExport"-}
<div id="divDatabaseExportWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgAdminDatabaseExportTitle#-}
	</div>
	<div id="divDatabaseExportContent" style="margin:10px;">
		<h4>
			<span id="txtAdminDatabaseExportRegionLabel"></span>
		</h4>
		<br />
		<span id="divAdminDatabaseExportProgress" class="clsAdminDatabaseExport" style="display:none;">
			{-#msgAdminDatabaseExportWait#-} &nbsp;&nbsp;<br />
		</span>
		<img id="imgAdminDatabaseExportWait" alt="" src="" style="display:none;" />
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
	<input type="hidden" id="fldAdminDatabaseExportSave"  value="1" />
	<input type="hidden" id="fldAdminDatabaseExportImage" value="{-$desinventarURL-}/images/loading.gif" />
</div>
