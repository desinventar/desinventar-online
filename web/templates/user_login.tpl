<!-- Display User Login Form -->
<form id="frmUserLogin" method="post" action="">
	<br />
	<table border="0" align="center" style="margin-top:1px;">
		<tr>
			<td align="right">
				{-#tuser#-}
			</td>
			<td>
				<input type="text" id="fldUserId" name="userid" value="" size="10" class="f" />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#tpassword#-}
			</td>
			<td>
				<input type="password" name="password" id="fldUserPasswd" value="" size="8" class="f" />
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2">
				<a class="button" id="btnUserLoginSend"><span>{-#benter#-}</span></a>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</form>

<span id="msgEmptyFields"   class="status">{-#msgEmptyFields#-}</span>
<span id="msgUserLoggedIn"  class="status">{-#msgUserLoggedIn#-}</span>
<span id="msgInvalidPasswd" class="status">{-#msgInvalidPasswd#-}</span>

<form id="uf" action="">
	<!-- method="get" 
	action="javascript:updateUserBar(jQuery('#desinventarURL').val() + '/user.php', 'login', $('userid').value, $('password').value);"> 
	-->
</form>
