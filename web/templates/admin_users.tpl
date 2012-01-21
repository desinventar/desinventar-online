{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
{-config_load file="$lg.conf" section="grpAdminUsers"-}
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
{-include file="user_editform.tpl" -}
