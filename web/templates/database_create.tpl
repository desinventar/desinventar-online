{-config_load file="$lg.conf" section="grpDatabaseCreate"-}
{-config_load file="$lg.conf" section="grpMainStrings"-}
<div style="margin:10px;">
	<br />
	<div id="divDatabaseCreateParams">
		<br />
		{-include file="database_edit.tpl"-}
		<div class="center">
			<a class="button" href="#" id="btnDatabaseCreateSend"><span>{-#msgDatabaseCreateSend#-}</span></a>
			<a class="button" href="#" id="btnDatabaseCreateCancel"><span>{-#msgDatabaseCreateCancel#-}</span></a>
		</div>
		<div id="divDatabaseCreateStatus" align="center">
			<br />
			<h4>
				<span class="clsDatabaseCreateStatus" id="txtDatabaseCreateFormError">{-#msgDatabaseCreateFormError#-}</span>
				<span class="clsDatabaseCreateStatus" id="txtDatabaseCreateOk">{-#msgDatabaseCreateOk#-}</span>
				<span class="clsDatabaseCreateStatus" id="txtDatabaseCreateError">{-#msgDatabaseCreateError#-}</span>
			</h4>
		</div>
	</div>
	<div id="divDatabaseCreateResult">
		{-#msgDatabaseCreateMsg1#-} <a id="lnkDatabaseCreateDBURL" href="#"><span id="txtDatabaseCreate_RegionId"></span></a>
		<br />
		<br />
		{-#msgDatabaseCreateMsg2#-}:<br />
		<ul>
			<li>- {-#msgDatabaseCreateMsg2A#-}</li>
			<li>- {-#msgDatabaseCreateMsg2B#-}</li>
		</ul>
		{-#msgDatabaseCreateMsg3#-}:</br />
		<ul>
			<li>- {-#mreginfo#-}</li>
			<li>- {-#msgDBConfig_Events#-}</li>
			<li>- {-#mcauses#-}</li>
			<li>- {-#meeffects#-}</li>
		</ul>
		<br />
		<br />
		<div class="right">
			<a class="button" id="lnkDatabaseCreateOpenDB" href="#"><span>{-#msgDatabaseCreateMsg4#-}</span></a>
		</div>
	</div>
</div>
