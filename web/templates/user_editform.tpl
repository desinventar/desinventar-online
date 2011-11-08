{-config_load file="$lg.conf" section="di8_user"-}
<div id="divUserEdit" style="display:none">
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
				<td colspan="2" align="center">
					<input type="hidden" id="txtUserEditCmd" name="cmd" />
					<input type="submit" id="btnUserEditSubmit" value="{-#bsave#-}" class="line" />
					<input type="reset"  id="btnUserEditCancel" value="{-#bcancel#-}" class="line" />
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
