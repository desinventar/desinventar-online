{-config_load file="$lg.conf" section="grpMenuUser"-}
{-config_load file="$lg.conf" section="grpAdminUsers"-}
<div id="divAdminUsers" class="AdminUsers">
	<div id="divUserList">
		<h2>{-#tuserprefer#-}</h2>
		<div class="dwin" style="width:550px;height:180px;">
			<table id="tblUserList" class="UserList">
				<thead>
					<tr>
						<th class="header"><b>{-#tuser#-}</b></th>
						<th class="header"><b>{-#tname#-}</b></th>
						<th class="header"><b>{-#temail#-}</b></th>
						<th class="header"><b>{-#tactive#-}</b></th>
					</tr>
				</thead>
				<tbody id="lst_userpa">
						<tr style="display:none;">
							<td>
								<span class="UserId" id="UserId"></span>
							</td>
							<td>
								<span class="UserFullName"></span>
							</td>
							<td>
								<span class="UserEMail"></span>
							</td>
							<td>
								<span class="UserActive hidden"></span>
								<input class="UserActiveCheckbox" type="checkbox" disabled="disabled" />
							</td>
						</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id="divUserStatus">
		<br />
		<a class="button" id="btnUserAdd"><span>{-#baddoption#-}</span></a>
		<br />
		<span id="lblUserStatusMsg" class="dlgmsg"></span>
		<br />
	</div>
	<div id="divUserEdit" class="hidden">
		<form id="frmUserEdit" name="userpafrm">
			<table class="grid">
				<tr>
					<td>
						<b style="color:darkred;">{-#tuser#-}</b>
					</td>
					<td>
						<input type="text" id="txtUserId" name="User[UserId]" size="15" maxlength="15" class="line" />
						<span id="chklogin" style="display:inline"></span>
						<input type="hidden" id="txtUserPasswd" name="User[UserPasswd]" value="" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#tname#-}</b></td>
					<td>
						<input type="text" id="txtUserFullName" name="User[UserFullName]" size="40" maxlength="40" class="line" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#temail#-}</b>
					</td>
					<td>
						<input type="text" id="txtUserEMail" name="User[UserEMail]" size="50" maxlength="50" class="line" />
					</td>
				</tr>
				<tr>
					<td>{-#tcountry#-}</td>
					<td>
						<select id="selCountryIso" name="User[CountryIso]">
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						{-#tcity#-}
					</td>
					<td>
						<input type="text" id="txtUserCity" name="User[UserCity]" size="20" maxlength="50" class="line" />
					</td>
				</tr>
				<tr>
					<td>
						<b>{-#tactive#-}</b>
					</td>
					<td>
						<input id="chkUserActive" name="User[UserActive]" type="checkbox" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
							<input type="hidden" id="txtUserEditCmd" name="cmd" />
						<div class="center">
							<a class="button" id="btnUserEditSubmit"><span>{-#bsave#-}</span></a>
							<a class="button" id="btnUserEditCancel"><span>{-#bcancel#-}</span></a>
						</div>
					</td>
				</tr>
			</table>
		</form>
		<div id="divUserEditFormStatus">
			<span class="UserEditFormStatus" id="UserEditFormStatusOk">{-#msgUserEditFormStatusOk#-}</span>
			<span class="UserEditFormStatus" id="UserEditFormStatusError">{-#msgUserEditFormStatusError#-}</span>
			<span class="UserEditFormStatus" id="UserEditFormStatusDuplicateId">{-#msgUserEditFormStatusDuplicateId#-}</span>
			<span class="UserEditFormStatus" id="UserEditFormStatusEmptyFields">{-#msgUserEditFormStatusEmptyFields#-}</span>
		</div>
	</div>
</div>
