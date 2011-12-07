{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
<link rel="stylesheet" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
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
	<a class="button" id="btnUserAdd"><span>{-#baddoption#-}</span></a>
	<br />
	<span id="lblUserStatusMsg" class="dlgmsg"></span>
	<br />
</div>
{-include file="user_editform.tpl" -}
