<!-- Display User Login Form -->
{-config_load file=`$lg`.conf section="di8_user"-}
<form action="javascript:updateUserBar('user.php', 'login', $('userid').value, $('password').value);" id="uf" method="get">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>
		<table border="0" align="center" style="margin-top:1px">
			<tr>
			<td align="right">{-#tuser#-} <input type="text" id="userid" name="userid" value="" size="16" /></td>
			<td align="right">{-#tpassword#-} <input type="password" name="password" id="password" value="" size="8" /></td>
			</tr>
		</table>
	</td>
	<td>
		<input type="submit" value="{-#benter#-}" class="line" />&nbsp;&nbsp;
		<span class="rememberpasswordlink">
			<a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=passlost');">{-#tpasslost#-}</a>
		</span>
	</td>
	</tr>
</table>
</form>
