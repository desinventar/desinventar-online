{-config_load file=`$lg`.conf section="di8_databasebackup"-}
<h2>Realizar Backup Base de Datos</h2>
<b><span id="txtDBBackupRegionLabel"></span></b>
<p>Presione el boton para realizar el backup</p>
<input id="btnDBBackupDoBackup" type="button" class="line" value="Crea Backup" />
<br />
<div id="divDBBackupProgress" style="display:none;">
	La copia de seguridad esta siendo realizada... <img src="loading.gif" />
</div>
<div id="divDBBackupResults" style="display:none;">
	El backup ha sido realizado, puede descargarlo. 
	<a id="linkDBBackupDownload" href="#"><img src="images/save-as-icon.png"></a><br />
</div>