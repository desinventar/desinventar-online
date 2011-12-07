{-config_load file="$lg.conf" section="grpDatabaseCreate"-}
<div style="margin:10px;">
	<h4>{-#msgDatabaseCreateMsg1#-}</h4>
	<br />
	{-include file="database_edit.tpl"-}
	<div class="center">
		<a class="line" href="#" id="btnDatabaseCreateSend"><span>{-#msgDatabaseCreateSend#-}</span></a>
		<a class="line" href="#" id="btnDatabaseCreateCancel"><span>{-#msgDatabaseCreateCancel#-}</span></a>
	</div>
	<div id="divDatabaseCreateStatus" align="center">
		<br />
		<h4>
			<span class="clsDatabaseCreateStatus" id="txtDatabaseCreateOk">{-#msgDatabaseCreateOk#-}</span>
			<span class="clsDatabaseCreateStatus" id="txtDatabaseCreateError">{-#msgDatabaseCreateError#-}</span>
		</h4>
	</div>
</div>
