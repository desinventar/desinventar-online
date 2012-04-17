{-config_load file="$lg.conf" section="grpUserPermAdmin"-}
<div style="margin:10px;">
	<h4>{-#msgUserPermAdminMsg1#-}</h4>
	<br />
	<table class="width100">
		<tr>
			<td class="top">
				<b>{-#msgUserPermAdminCurrentAdmin#-} : </b>
			</td>
			<td class="top">
				<p id="txtUserPermAdminCurrent"></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<br />
			</td>
		</tr>
		<tr>
			<td class="top">
				<b>{-#msgUserPermAdminNewAdmin#-} : </b>
			</td>
			<td class="top">
				<select id="fldUserPermAdmin_UserId">
					<option></option>
				</select>
			</td>
		</tr>
	</table>
	<br />
	<div class="center">
		<a class="button" href="#" id="btnUserPermAdminSend"><span>{-#msgUserPermAdminSend#-}</span></a>
		<a class="button" href="#" id="btnUserPermAdminCancel"><span>{-#msgUserPermAdminCancel#-}</span></a>
	</div>
	<div id="divUserPermAdminStatus" class="center">
		<br />
		<h4>
			<span class="clsUserPermAdminStatus" id="txtUserPermAdminFormError">{-#msgUserPermAdminFormError#-}</span>
			<span class="clsUserPermAdminStatus" id="txtUserPermAdminOk">{-#msgUserPermAdminOk#-}</span>
			<span class="clsUserPermAdminStatus" id="txtUserPermAdminError">{-#msgUserPermAdminError#-}</span>
		</h4>
	</div>
</div>
