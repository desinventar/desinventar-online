{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="di8_user"-}
<link rel="stylesheet" href="css/desinventar.css?version={-$jsversion-}" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="{-$desinventarURL-}/include/md5.js"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/jquery.snippets.js?version={-$jsversion-}"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/user.js?version={-$jsversion-}"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		onReadyUserAdmin();
	});	
</script>
<div id="divUserList">
	{-include file="user_list.tpl" -}
</div> {-* divUserList *-}
<div id="divUserStatus">
	<br />
	<input id="btnUserAdd" type="button" value="{-#baddoption#-}" class="line" />
	<br />
	<span id="lblUserStatusMsg" class="dlgmsg"></span>
	<br />
</div>
{-include file="user_editform.tpl" -}
