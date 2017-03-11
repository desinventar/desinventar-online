{-config_load file="$lg.conf" section="grpDatabaseUsers"-}
<div class="clsDatabaseUsers">
	<b>{-#msgDatabaseUsers_TitleOptions#-}</b>
	<br />
	<div class="line" style="padding:5px;">
		<form id="frmDiffusion">
			<table>
				<tr>
					<td>
						<span>
							<input class="RegionActive" type="checkbox" />
							<span class="RegionActiveText">&nbsp;{-#msgDatabaseUsers_RegionActiveTooltip#-}</span>
							</span>
							<br />
					</td>
				</tr>
				<tr>
					<td>
						<span>
							<input class="RegionPublic" type="checkbox" />
							<span class="RegionPublicText">&nbsp;{-#msgDatabaseUsers_RegionPublicTooltip#-}</span>
						</span>
						<br />
					</td>
				</tr>
			</table>
			<div class="fixw center">
				<input class="RegionStatus" type="hidden" value="0" />
				<a class="button btnSave"><span>{-#msgDatabaseUsers_Save#-}</span></a>
				<a class="button btnCancel"><span>{-#msgDatabaseUsers_Cancel#-}</span></a>
			</div>
		</form>
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
		<table class="width100 grid dwin" id="tblDatabaseUsers_List">
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
					<td class="header">
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
					<td>
						<a class="delete" href=""> &#x2718;</a>
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
		<form id="frmUsers">
			<br />
			{-#msgDatabaseUsers_UserId#-}<br />
			<select class="UserId line fixw">
				<option></option>
			</select>
			<br />
			{-#msgDatabaseUsers_UserRole#-}<br />
			<input class="UserRolePrev" type="hidden" value="" />
			<select class="UserRole line fixw">
				<option value="NONE">{-#msgDatabaseUsers_RoleNone#-}</option>
				<option value="OBSERVER">{-#msgDatabaseUsers_RoleObserver#-}</option>
				<option value="USER">{-#msgDatabaseUsers_RoleFeeder#-}</option>
				<option value="SUPERVISOR">{-#msgDatabaseUsers_RoleSupervisor#-}</option>
				<option value="ADMINREGION">{-#msgDatabaseUsers_RoleAdminRegion#-}</option>
			</select>
			<br />
			<br />
			<div class="fixw center">
				<a class="button btnSave"><span>{-#msgDatabaseUsers_Save#-}</span></a>
				<a class="button btnCancel"><span>{-#msgDatabaseUsers_Cancel#-}</span></a>
			</div>
		</form>
	</div>
	<br />
	<div class="center">
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListStatusOk">{-#msgDatabaseUsers_RoleListStatusOk#-}</span>
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListStatusError">{-#msgDatabaseUsers_RoleListStatusError#-}</span>
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListEmptyFields">{-#msgDatabaseUsers_RoleListEmptyFields#-}</span>
		<span class="clsDatabaseUsersStatus" id="txtDatabaseUsers_RoleListCannotRemoveAdminRole">{-#msgDatabaseUsers_RoleListCannotRemoveAdminRole#-}</span>
	</div>
	<div class="hidden">
		<span id="msgDatabaseUsers_ConfirmManagerTitle">{-#msgDatabaseUsers_ConfirmManagerTitle#-}</span>
		<span id="msgDatabaseUsers_ConfirmManagerPrompt1">{-#msgDatabaseUsers_ConfirmManagerPrompt1#-}</span>
		<span id="msgDatabaseUsers_ConfirmManagerPrompt2">{-#msgDatabaseUsers_ConfirmManagerPrompt2#-}</span>
		<span id="msgDatabaseUsers_ConfirmManagerPrompt3">{-#msgDatabaseUsers_ConfirmManagerPrompt3#-}</span>
		<span id="msgDatabaseUsers_Yes">{-#msgDatabaseUsers_Yes#-}</span>
		<span id="msgDatabaseUsers_No">{-#msgDatabaseUsers_No#-}</span>
	</div>
</div>
