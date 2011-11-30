{-config_load file="$lg.conf" section="grpDatabaseCreate"-}
<div style="margin:10px;">
	<h4>{-#msgDatabaseCreateMsg1#-}</h4>
	<br />
	{-include file="database_edit.tpl"-}
	<div align="center">
		<input type="button" class="line" id="btnDatabaseCreateSend" value="Create" />
		<input type="button" class="line" id="btnDatabaseCreateCancel" value="Cancel" />
	</div>
</div>
