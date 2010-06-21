{-config_load file=`$lg`.conf section="di8_DBBackup"-}
<div class="contentBlock" id="divDatabaseBackup">
	<h2>{-#msgDBBackupTitle#-}</h2>
	<b><span id="txtDBBackupRegionLabel"></span></b><br />
	<div id="divDBBackupResults" style="display:none;" class="DBBackup">
		<p>{-#msgDBBackupComplete#-}</p>
		<a id="linkDBBackupDownload" href="#"><img src="images/save-as-icon.png"></a><br />
		<br />
		<hr size="2" noshade />
	</div>
	<div id="divDBBackupParameters" class="DBBackup">
		<p>{-#msgDBBackupParameters#-}</p>
		<input id="btnDBBackupDoBackup" type="button" class="line" value="{-#msgDBBackupButton#-}" />
	</div>
	<br />
	<div id="divDBBackupProgress" style="display:none;" class="DBBackup">
		{-#msgDBBackupWaiting#-} &nbsp;&nbsp; <img src="loading.gif" />
	</div>
	<br />
	<div id="divDBBackupErrors" style="display:none;" class="DBBackup">
		{-#msgDBBackupError#-}<br />
	</div>
</div>
