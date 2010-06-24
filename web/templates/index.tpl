{-config_load file=`$lg`.conf section="di8_index"-}
{-config_load file=`$lg`.conf section="di8_user"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-} | {-$regname-}</title>
	<link rel="stylesheet" type="text/css" href="css/desinventar.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="css/datacards.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="css/main.css?version={-$jsversion-}" />
	<script type="text/javascript" src="include/prototype.js"></script>
	<!-- jQuery -->
	{-include file="jquery.tpl" -}
	
	<script type="text/javascript" src="include/listMan.js"></script>
	<script type="text/javascript" src="include/md5.js"></script>
	<script type="text/javascript" src="include/checktree.js"></script>
	<script type="text/javascript" src="include/wd.js"></script>
	<script type="text/javascript" src="include/accordion.js"></script>
	<script type="text/javascript" src="include/palette.js"></script>
	<script type="text/javascript" src="include/combo-box.js"></script>

	<script type="text/javascript" src="js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/cards.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/data.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/graphic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/thematicmap.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/statistic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/jquery.snippets.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user_login.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/desconsultar.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/database_backup.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/main.js?version={-$jsversion-}"></script>

	{-if $desinventarHasInternet -}
		{-if $desinventarGoogleMapsKey != '' -}
			<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={-$desinventarGoogleMapsKey-}"></script>
		{-/if-}
		{-if $hasVEMap -}
			<script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>
		{-/if-}
		<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers"></script>
	{-/if-}
	<script src="/openlayers/lib/OpenLayers.js"></script>

	<!-- ExtJS -->
	<link rel="stylesheet" href="css/checktree.css" type="text/css"/>
	<link rel="stylesheet" href="css/accordion.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/ext-all.css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/xtheme-gray.css"/>
	<script type="text/javascript" src="/extJS/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="/extJS/ext-all.js"></script>
	{-include file="main_javascript.tpl" -}
	<script type="text/javascript" src="include/tabber.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			onReadyDatacards();
			onReadyData();
			onReadyGraphic();
			onReadyDatabaseBackup();
			onReadyMain();
			
			if (jQuery('#desinventarRegionId').val() != '') {
				jQuery('#dcr').load('index.php?cmd=getRegionFullInfo&r=' + jQuery('#desinventarRegionId').val());
			} else {
				// Hide button toolbar if no region is selected
				jQuery("#ResultHeaderText").hide();
				jQuery("#ResultHeaderButtonToolbar").hide();
				// Hide Query Design if no region is selected
				jQuery("#dcr").hide();
				// Show database list
				jQuery("#divDatabaseList").show();
			}
		});
	</script>
</head>
<body>
	<div id="loading-mask"></div>
	<div id="loading">
		<div class="loading-indicator">Loading...</div>
	</div>
	
	<!-- Top Menu Area - Toolbar -->
	<div id="north">
		<div id="toolbar"></div>
	</div>
	
	<!-- Central Content Area -->
	<div id="container">
		{-include file="main_content.tpl" -}
	</div>
	
	<!-- Query Design -->
	<div id="west">
		{-include file="main_querydesign.tpl" -}
	</div>

	<!-- Help Section -->
	<div id="south">
		{-include file="main_help.tpl" -}
	</div>
	
	<!-- General App Information -->
	{-include file="desinventarinfo.tpl"-}
</body>
</html>
