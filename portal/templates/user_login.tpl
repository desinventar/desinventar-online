<!-- Display User Login Form -->
{-config_load file="$lang.conf" section="user"-}

	<form id="frmUserLogin" method="post" action="">
		<br />
		<table border="0" align="center" style="margin-top:1px">
			<tr>
				<td align="right">
					{-#msgUser_Username#-}
				</td>
				<td>
					<input type="text" id="fldUserId" name="userid" value="" size="10" class="f" />
				</td>
			</tr>
			<tr>
				<td align="right">
					{-#msgUser_Password#-}
				</td>
				<td>
					<input type="password" name="password" id="fldUserPasswd" value="" size="8" class="f" />
				</td>
			</tr>
			<tr>
				<td align="right" colspan="2">
					<input type="submit" value="{-#msgUser_Login#-}" class="line" />&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>

	<span id="msgEmptyFields"   class="status">{-#msgUser_EmptyFields#-}</span>
	<span id="msgUserLoggedIn"  class="status">{-#msgUser_LoggedIn#-}</span>
	<span id="msgInvalidPasswd" class="status">{-#msgUser_InvalidPasswd#-}</span>

	<form id="uf" action="">
		<!-- method="get"
		action="javascript:updateUserBar('user.php', 'login', $('userid').value, $('password').value);">
		-->
	</form>
