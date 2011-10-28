{-config_load file="$lg.conf" section="di8_index"-}
{-config_load file="$lg.conf" section="di8_user"-}
{-config_load file="$lg.conf" section="querydef"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>DesInventar Online Edition {-$majorversion-} | {-$RegionLabel-}</title>
	<link rel="stylesheet" type="text/css" href="css/desinventar.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="css/datacards.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="css/main.css?version={-$jsversion-}" />
	<script type="text/javascript" src="include/prototype.js"></script>
	<!-- jQuery -->
	{-include file="jquery.tpl"-}
	
	<script type="text/javascript" src="include/listMan.js"></script>
	<script type="text/javascript" src="include/md5.js"></script>
	<script type="text/javascript" src="include/wd.js"></script>
	<script type="text/javascript" src="include/accordion.js"></script>
	<script type="text/javascript" src="include/palette.js"></script>
	<script type="text/javascript" src="include/combo-box.js"></script>

	<script type="text/javascript" src="external/checktree/checktree.js"></script>
	<script type="text/javascript" src="external/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="external/jquery.swfupload.js"></script>
	<script type="text/javascript" src="external/Math.uuid.js"></script>

	<script type="text/javascript" src="js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/datacards.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/extraeffects.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/dbconfig_geography.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/dbconfig_events.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/dbconfig_causes.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/data.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/graphic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/thematicmap.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/statistic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/jquery.snippets.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user_login.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/desconsultar.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/database_backup.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/database_import.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/admin_database.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/database_config.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/common.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/main.js?version={-$jsversion-}"></script>

	<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=euzuro-openlayers"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

	{-*include file="maps_include.tpl"*-}
	<!-- ExtJS -->
	<link rel="stylesheet" href="external/checktree/checktree.css" type="text/css"/>
	<link rel="stylesheet" href="css/accordion.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/ext-all.css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/xtheme-gray.css"/>
	<script type="text/javascript" src="/extJS/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="/extJS/ext-all.js"></script>
	{-include file="main_javascript.tpl"-}
	<script type="text/javascript">
		jQuery(document).ready(function() {
			onReadyDatacards();
			onReadyData();
			onReadyGraphic();
			onReadyDatabaseBackup();
			onReadyDatabaseImport();
			onReadyAdminDatabase();
			onReadyExtraEffects();
			onReadyMain();		
		});
	</script>
</head>
<body>
	<div id="divMapHeader"></div>
	<div id="loading-mask"></div>
	<div id="loading">
		<div class="loading-indicator">Loading...</div>
	</div>
	<div id="divViewport">
		<!-- Top Menu Area - Toolbar -->
		<div id="north">
			<div id="toolbar"></div>
		</div>

		<!-- Query Design -->
		<div id="west">
			{-include file="main_querydesign.tpl"-}
		</div>
		
		<!-- Central Content Area -->
		<div id="container">
			{-include file="main_content.tpl"-}
		</div>
		

		<!-- Help Section -->
		<div id="south">
			{-include file="main_help.tpl"-}
		</div>
	</div>
	
	<!-- General App Information -->
	{-include file="desinventarinfo.tpl"-}
</body>
</html>
