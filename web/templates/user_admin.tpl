{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file=`$lg`.conf section="di8_user"-}
<link rel="stylesheet" href="css/desinventar.css" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/jquery.snippets.js"></script>
<script type="text/javascript" src="js/useradmin.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		onReadyUserAdmin();
	});	
</script>
<div id="divUserList">
	{-include file="userlist.tpl" -}
</div> {-* divUserList *-}
<div id="divUserStatus">
	<br />
	<input id="btnUserAdd" type="button" value="{-#baddoption#-}" class="line" />
	<br />
	<span id="lblUserStatusMsg" class="dlgmsg"></span>
	<br />
</div>
<div id="divUserEdit" style="display:block">
	<form id="frmUserEdit" name="userpafrm">
		<table class="grid">
			<tr>
				<td><b style="color:darkred;">{-#tuser#-}</b>
				</td>
				<td>
					<input type="text" id="txtUserId" name="User[UserId]" size="15" maxlength="15" class="line" />
					{-* onBlur="updateList('chklogin', 'user.php', 'cmd=chklogin&UserId='+ $('UserId').value);" *-}
					<span id="chklogin" style="display:inline"></span>
					<input type="hidden" id="txtUserPasswd" name="User[UserPasswd]" value="" />
				</td>
			</tr>
			<tr>
				<td>{-#tcountry#-}</td>
				<td><select id="selCountryIso" name="User[CountryIso]">
						<option value=""></option>
						{-foreach name=cnt key=key item=item from=$cnt-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#tname#-}</b></td>
				<td>
					<input type="text" id="txtUserFullName" name="User[UserFullName]" size="40" maxlength="40" class="line" />
				</td>
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#temail#-}</b>
				</td>
				<td><input type="text" id="txtUserEMail" name="User[UserEMail]" size="50" maxlength="50" class="line" />
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
</div> {-* divUserEdit *-}
