{-config_load file="$lg.conf" section="grpAdminDatabaseExport"-}
<h4>
	<span id="txtAdminDatabaseExportRegionLabel"></span>
</h4>
<br />
<div id="divAdminDatabaseExportProgress" style="display:none;" class="clsAdminDatabaseExport">
	{-#msgAdminDatabaseExportWait#-} &nbsp;&nbsp; <img src="{-$desinventarURL-}/images/loading.gif" />
</div>
<div id="divAdminDatabaseExportResults" style="display:none;" class="clsAdminDatabaseExport">
	<p>{-#msgAdminDatabaseExportComplete#-}</p>
	<div style="text-align:center">
	</div>
	<hr size="2" noshade />
</div>
<div id="divAdminDatabaseExportError" style="display:none;" class="clsAdminDatabaseExport">
	{-#msgAdminDatabaseExportError#-}<br />
</div>
