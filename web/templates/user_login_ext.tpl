<!-- Show User login/logout window -->
{-config_load file="$lg.conf" section="grpMenuUser"-}
<div id="divUserLoginWin" class="x-hidden">
	<div class="x-window-header">
		{-$desinventarUserId-} - {-$role-}
	</div>
	<div id="divUserLoginContent">
		{-include file="user_login.tpl"-}
		<br />
		<!--
		<a href="javascript:void(null)" class="rememberpasswordlink" onclick="updateList('passlost', jQuery('#desinventarURL').val() + '/user.php', 'cmd=passlost');">{-#tpasslost#-}</a>
		<div id="passlost">
		</div>
		-->
	</div>
</div>
