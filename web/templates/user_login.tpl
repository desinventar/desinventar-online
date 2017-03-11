{-config_load file="$lg.conf" section="grpMenuUser"-}
<!-- Display User Login Form -->
<div class="UserLogin">
	<form id="frmUserLogin" method="post" action="#">
		<br />
		<table class="center" style="margin-top:1px;">
			<tr>
				<td class="right">
					<div class="right">
						{-#tuser#-}
					</div>
				</td>
				<td>
					<div class="left">
						<input type="text" id="fldUserId" name="userid" value="" size="10" class="f" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="right">
					<div class="right">
						{-#tpassword#-}
					</div>
				</td>
				<td>
					<div class="left">
						<input type="password" name="password" id="fldUserPasswd" value="" size="10" class="f" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="center" colspan="2">
					<br />
					<a class="button Send"  ><span>{-#benter#-}</span></a>&nbsp;&nbsp;
					<a class="button Cancel"><span>{-#bcancel#-}</span></a>&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>
	<div class="center">
		<span class="status hidden msgEmptyFields">{-#msgEmptyFields#-}</span>
		<span class="status hidden msgUserLoggedIn">{-#msgUserLoggedIn#-}</span>
		<span class="status hidden msgInvalidPasswd">{-#msgInvalidPasswd#-}</span>
		<span class="status hidden msgConnectionError">{-#msgConnectionError#-}</span>
	</div>
	<form id="uf" action="#">
		<!-- method="get" 
		action="javascript:updateUserBar(jQuery('#desinventarURL').val() + '/user.php', 'login', $('userid').value, $('password').value);"> 
		-->
	</form>
</div>