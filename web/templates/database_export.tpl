{-config_load file="$lg.conf" section="di8_DBBackup"-}
<table>
	<tr>
		<td rowspan="5" valign="top">
		</td>
		<td>
			<b><span id="txtDBBackupRegionLabel"></span></b><br />
			<span id="txtAdminDatabaseExport_RegionId"></span>
		</td>
	</tr>
	<tr>
		<td>
			<div id="divDBBackupResults" style="display:none;" class="DBBackup">
				<p>{-#msgDBBackupComplete#-}</p>
				<div style="text-align:center">
				<input id="btnDBBackupDownload" type="button" class="line" value="Download" href="" />
				<br />
				</div>
				<hr size="2" noshade />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="divDBBackupParameters" class="DBBackup">
				<p>{-#msgDBBackupParameters#-}</p>
				<input id="btnDBBackupDoBackup" type="button" class="line" value="{-#msgDBBackupButton#-}" />
			</div>
			<br />
		</td>
	</tr>
	<tr>
		<td>
			<div id="divDBBackupProgress" style="display:none;" class="DBBackup">
				{-#msgDBBackupWaiting#-} &nbsp;&nbsp; <img src="{-$desinventarURL-}/images/loading.gif" />
			</div>
			<br />
		</td>
	</tr>
	<tr>
		<td>
			<div id="divDBBackupErrors" style="display:none;" class="DBBackup">
				{-#msgDBBackupError#-}<br />
			</div>
		</td>
	</tr>
</table>
