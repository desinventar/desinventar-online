{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
{-config_load file="$lg.conf" section="grpAdminUsers"-}
<div id="divAdminUsers">
	<div id="divUserList">
		{-include file="user_list.tpl" -}
	</div> {-* divUserList *-}
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
						{-* onBlur="updateList('chklogin', jQuery('#desinventarURL').val() + '/user.php', 'cmd=chklogin&UserId='+ $('UserId').value);" *-}
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
							{-foreach name=cnt key=key item=item from=$cnt-}
								<option value="{-$key-}">{-$item-}</option>
							{-/foreach-}
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
	</div> {-* divUserEdit *-}
</div>
