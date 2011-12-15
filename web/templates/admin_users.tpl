{-config_load file="$lg.conf" section="grpAdminUsers"-}
{-config_load file="$lg.conf" section="grpUserEdit"-}
<div style="margin:10px;">
	<div id="divAdminUsersList" style="height:180px;width=100%;">
		<table id="tblAdminUsersList" class="col dwin">
			<thead>
				<tr>
					<th class="header"><b>{-#msgUserEdit_UserId#-}</b></th>
					<th class="header"><b>{-#msgUserEdit_UserName#-}</b></th>
					<th class="header"><b>{-#msgUserEdit_UserEmail#-}</b></th>
					<th class="header"><b>{-#msgUserEdit_UserActive#-}</b></th>
				</tr>
			</thead>
			<tbody id="tbodyAdminUsersList">
				<tr>
					<td class="UserId">
						<br />
					</td>
					<td class="UserName" style="width:100%;">
						<br />
					</td>
					<td class="UserEMail">
						<br />
					</td>
					<td class="UserActive">
						<div class="center">
							<input type="checkbox" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="divAdminUsersStatus">
		<br />
		<a class="button" id="btnAdminUsers_AddUser"><span>{-#msgAdminUsers_AddUser#-}</span></a>
		<br />
		<span id="lblUserStatusMsg" class="dlgmsg"></span>
		<br />
	</div>
	<div id="divAdminUsersEdit">
		{-include file="user_editform.tpl" -}
	</div>
</div>
