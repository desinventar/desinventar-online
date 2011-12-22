{-config_load file="$lg.conf" section="grpDatabaseUsers"-}
<fieldset style="padding:5px 5px 5px 5px;">
	<legend>
		<b title="{-$dic.DBPermissions[2]-}">{-#msgDatabaseUsers_Title#-}</b>
	</legend>
	<div class="dwin clsDatabaseUsers_List">
		<table width="100%" class="grid" id="tblDatabaseUsers_List">
			<thead>
				<tr>
					<td class="header UserId" title="{-$dic.DBUserId[2]-}">
						<b>{-#msgDatabaseUsers_UserId#-}</b>
					</td>
					<td class="header UserName" title="{-$dic.DBUserId[2]-}">
						<b>{-#msgDatabaseUsers_UserId#-}</b>
					</td>
					<td class="header UserRole" title="{-$dic.DBRole[2]-}">
						<b>{-#msgDatabaseUsers_UserRole#-}</b>
					</td>
					<td class="header UserRoleLabel" title="{-$dic.DBRole[2]-}">
						<b>{-#msgDatabaseUsers_UserRole#-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseUsers_List">
				<tr style="display:none;">
					<td class="UserId">
					</td>
					<td class="UserName">
					</td>
					<td class="UserRole">
					</td>
					<td class="UserRoleLabel">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<a class="button" id="btnDatabaseUsers_Add"><span>{-#msgDatabaseUsers_Add#-}</span></a>
	<br />
	
	<div id="divDatabaseUsers_Edit" style="display:none;max-width:400px;">
		<br />
		{-#msgDatabaseUsers_UserId#-}<br />
		<select id="fldDatabaseUsers_UserId" class="line fixw">
			<option></option>
		</select>
		<br />
		{-#msgDatabaseUsers_UserRole#-}<br />
		<select id="fldDatabaseUsers_UserRole" class="line fixw">
			<option value="NONE">None</option>
			<option value="USER">User</option>
			<option value="OBSERVER">Observer</option>
			<option value="FEEDER">Feeder</option>
			<option value="SUPERVISOR">Supervisor</option>
			<option value="ADMINREGION">Administrador Región</option>
		</select>
		<br />
		<br />
		<div class="fixw center">
			<a class="button" id="btnDatabaseUsers_Save"><span>{-#msgDatabaseUsers_Save#-}</span></a>
			<a class="button" id="btnDatabaseUsers_Cancel"><span>{-#msgDatabaseUsers_Cancel#-}</span></a>
		</div>
	</div>
</fieldset>
<br />
<br />
