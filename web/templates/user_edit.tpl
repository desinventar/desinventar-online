{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file=`$lg`.conf section="di8_user"-}
<link rel="stylesheet" href="css/desinventar.css" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/jquery.snippets.js"></script>
<script type="text/javascript" src="js/user.js?version={-$jsversion-}"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#divUserEdit").show();
		//onReadyUserAdmin();
	});	
</script>
{-include file="user_editform.tpl" adminEdit=false-}

