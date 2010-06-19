{-config_load file=`$lg`.conf section="di8_databasebackup"-}
<h2>Realizar Backup Base de Datos</h2>
<b><span id="txtDBBackupRegionLabel"></span></b><br />
<div id="divDBBackupParameters" class="DBBackup">
	<p>Presione el boton para realizar el backup</p>
	<input id="btnDBBackupDoBackup" type="button" class="line" value="Crea Backup" />
</div>
<br />
<div id="divDBBackupProgress" style="display:none;" class="DBBackup">
	La copia de seguridad esta siendo realizada... <img src="loading.gif" />
</div>
<br />
<div id="divDBBackupResults" style="display:none;" class="DBBackup">
	El backup ha sido realizado, puede descargarlo. 
	<a id="linkDBBackupDownload" href="#"><img src="images/save-as-icon.png"></a><br />
</div>
<div id="divDBBackupErrors" style="display:none;" class="DBBackup">
	La copia de seguridad no pudo se creada, ocurrio un error.<br />
</div>
