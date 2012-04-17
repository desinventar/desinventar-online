{-config_load file="$lg.conf" section="grpMenuUser"-}
<!-- Display User Login Form -->
<div class="UserLogin">
	<form id="frmUserLogin" method="post" action="#">
		<br />
		<table class="center" style="margin-top:1px;">
			<tr>
				<td class="right">
					{-#tuser#-}
				</td>
				<td>
					<input type="text" id="fldUserId" name="userid" value="" size="10" class="f" />
				</td>
			</tr>
			<tr>
				<td class="right">
					{-#tpassword#-}
				</td>
				<td>
					<input type="password" name="password" id="fldUserPasswd" value="" size="8" class="f" />
				</td>
			</tr>
			<tr>
				<td class="right" colspan="2">
					<a class="button" id="btnUserLoginSend"><span>{-#benter#-}</span></a>&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>
	<div class="center">
		<span id="msgEmptyFields"   class="status">{-#msgEmptyFields#-}</span>
		<span id="msgUserLoggedIn"  class="status">{-#msgUserLoggedIn#-}</span>
		<span id="msgInvalidPasswd" class="status">{-#msgInvalidPasswd#-}</span>
	</div>
	<form id="uf" action="#">
		<!-- method="get" 
		action="javascript:updateUserBar(jQuery('#desinventarURL').val() + '/user.php', 'login', $('userid').value, $('password').value);"> 
		-->
	</form>
</div>