<!-- Display User Login Form -->
{-config_load file=`$confdir``$lg`.conf section="di8_user"-}
<form id="frmUserLogin" method="post">
	<br />
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table border="0" align="center" style="margin-top:1px">
					<tr>
						<td align="right">{-#tuser#-}     <input type="text" id="fldUserId" name="userid" value="" size="16" /></td>
						<td align="right">{-#tpassword#-} <input type="password" name="password" id="fldUserPasswd" value="" size="8" /></td>
					</tr>
				</table>
			</td>
			<td>
				<input type="submit" value="{-#benter#-}" class="line" />&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</form>
<div class="status" id="divUserLoginMsg">
	<span id="msgEmptyFields"   class="status">{-#msgEmtpyFields#-}</span>
	<span id="msgUserLoggedIn"  class="status">{-#msgUserLoggedIn#-}</span>
	<span id="msgInvalidPasswd" class="status">{-#msgInvalidPasswd#-}</span>
</div>

<form id="uf">
	<!-- method="get" 
	action="javascript:updateUserBar('user.php', 'login', $('userid').value, $('password').value);"> 
	-->
</form>
