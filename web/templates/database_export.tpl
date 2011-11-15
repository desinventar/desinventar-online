{-config_load file="$lg.conf" section="grpDatabaseExport"-}
<h4>
	<span id="txtDatabaseExportRegionLabel"></span>
	Language : {-$lg-}
</h4>
<br />
<div id="divAdminDatabaseExportProgress" style="display:none;" class="clsAdminDatabaseExport">
	{-#msgDatabaseExportWai#-} &nbsp;&nbsp; <img src="{-$desinventarURL-}/images/loading.gif" />
</div>
<div id="divAdminDatabaseExportResults" style="display:none;" class="clsAdminDatabaseExport">
	<p>{-#msgDatabaseExportComplete#-}</p>
	<div style="text-align:center">
	</div>
	<hr size="2" noshade />
</div>
<div id="divAdminDatabaseExportError" style="display:none;" class="clsAdminDatabaseExport">
	{-#msgDatabaseExportError#-}<br />
</div>
