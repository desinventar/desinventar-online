{-config_load file="$lg.conf" section="grpAdminDatabaseUpload"-}
<div id="divDatabaseUploadWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgAdminDatabaseUploadTitle#-}
	</div>
	<div id="divDatabaseUploadContent">
		{-include file="database_upload.tpl"-}
	</div>
</div>
<div style="display:none;">
	<span id="msgAdminDatabaseUploadButtonClose">{-#msgAdminDatabaseUploadButtonClose#-}</span>
	<input type="hidden" id="fldAdminDatabaseUploadSave"  value="1" />
	<input type="hidden" id="fldAdminDatabaseUploadImage" value="{-$desinventarURL-}images/loading.gif" />
</div>
