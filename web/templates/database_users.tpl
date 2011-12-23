{-config_load file="$lg.conf" section="grpDatabaseUsers"-}
<div class="clsDatabaseUsers">
	<b>{-#msgDatabaseUsers_TitleOptions#-}</b>
	<br />
	<div class="line" style="padding:5px;">
		<table>
			<tr>
				<td>
					<span title="Region is Active">
						<input type="checkbox" id="fldDatabaseUsers_RegionActive" />
						<span id="txtDatabaseUsers_RegionActive">&nbsp;&nbsp;{-#msgDatabaseUsers_RegionActive#-}</span>
						</span>
						<br />
				</td>
			</tr>
			<tr>
				<td>
					<span title="Region is Public">
						<input type="checkbox" id="fldDatabaseUsers_RegionPublic" />
						<span id="txtDatabaseUsers_RegionPublic">&nbsp;&nbsp;{-#msgDatabaseUsers_RegionPublic#-}</span>
					</span>
					<br />
				</td>
			</tr>
		</table>
		<div class="fixw center">
			<a input type="hidden" id="fldDatabaseUsers_RegionStatus" value="0" />
			<a class="button" id="btnDatabaseUsers_OptionsSave"><span>{-#msgDatabaseUsers_Save#-}</span></a>
			<a class="button" id="btnDatabaseUsers_OptionsCancel"><span>{-#msgDatabaseUsers_Cancel#-}</span></a>
		</div>
		<div class="center">
			<br />
			<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_OptionsStatusOk">{-#msgDatabaseUsers_OptionsStatusOk#-}</span>
			<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_OptionsStatusError">{-#msgDatabaseUsers_OptionsStatusError#-}</span>
		</div>
	</div>
	<br />
	<b>{-#msgDatabaseUsers_TitleRoles#-}</b>
	<br />
	<div class="line">
		<table width="100%" class="grid dwin" id="tblDatabaseUsers_List">
			<thead>
				<tr>
					<td class="header UserId">
						<b>{-#msgDatabaseUsers_UserId#-}</b>
					</td>
					<td class="header UserName">
						<b>{-#msgDatabaseUsers_UserId#-}</b>
					</td>
					<td class="header UserRole">
						<b>{-#msgDatabaseUsers_UserRole#-}</b>
					</td>
					<td class="header UserRoleLabel">
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
	<br />
	<div id="divDatabaseUsers_Edit" style="display:none;" class="line">
		<br />
		{-#msgDatabaseUsers_UserId#-}<br />
		<select id="fldDatabaseUsers_UserId" class="line fixw">
			<option></option>
		</select>
		<br />
		{-#msgDatabaseUsers_UserRole#-}<br />
		<select id="fldDatabaseUsers_UserRole" class="line fixw">
			<option value="NONE">{-#msgDatabaseUsers_RoleNone#-}</option>
			<option value="OBSERVER">{-#msgDatabaseUsers_RoleObserver#-}</option>
			<option value="USER">{-#msgDatabaseUsers_RoleFeeder#-}</option>
			<option value="SUPERVISOR">{-#msgDatabaseUsers_RoleSupervisor#-}</option>
			<option value="ADMINREGION">{-#msgDatabaseUsers_RoleAdminRegion#-}</option>
		</select>
		<br />
		<br />
		<div class="fixw center">
			<a class="button" id="btnDatabaseUsers_Save"><span>{-#msgDatabaseUsers_Save#-}</span></a>
			<a class="button" id="btnDatabaseUsers_Cancel"><span>{-#msgDatabaseUsers_Cancel#-}</span></a>
		</div>
	</div>
	<br />
	<div class="center">
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListStatusOk">{-#msgDatabaseUsers_RoleListStatusOk#-}</span>
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListStatusError">{-#msgDatabaseUsers_RoleListStatusError#-}</span>
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListEmptyFields">{-#msgDatabaseUsers_RoleListEmptyFields#-}</span>
	</div>
	<span id="txtDatabaseUsers_RoleListReloadNeeded" style="display:none;">{-#msgDatabaseUsers_RoleListReloadNeeded#-}</span>
</div>
