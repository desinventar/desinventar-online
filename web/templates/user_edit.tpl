{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
<link rel="stylesheet" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/user.js?version={-$jsversion-}"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		onReadyCommon();
		jQuery("#divUserEdit").show();
	});	
</script>
{-include file="user_editform.tpl" adminEdit=false-}

