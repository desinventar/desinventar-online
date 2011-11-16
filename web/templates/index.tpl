{-config_load file="$lg.conf" section="di8_index"-}
{-config_load file="$lg.conf" section="di8_user"-}
{-config_load file="$lg.conf" section="querydef"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>DesInventar Online Edition {-$majorversion-} | {-$RegionLabel-}</title>
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/datacards.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />
	<!-- jQuery -->
	{-include file="jquery.tpl"-}

	{-*include file="main_javascript.tpl"*-}

	<script type="text/javascript" src="{-$desinventarURL-}/external/listMan.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/wd.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/accordion.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/palette.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/combo-box.js"></script>

	<script type="text/javascript" src="{-$desinventarURL-}/external/checktree/checktree.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.swfupload.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/Math.uuid.js"></script>

	<script type="text/javascript" src="{-$desinventarURL-}/js/datacards.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/extraeffects.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/dbconfig_geography.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/dbconfig_events.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/dbconfig_causes.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/data.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/graphic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/thematicmap.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/statistic.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/jquery.snippets.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/user.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/query_results.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_edit.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_export.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_import.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/database_config.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/main.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/main_ext.js?version={-$jsversion-}"></script>

	<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=euzuro-openlayers"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/external/checktree/checktree.css"/>
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/accordion.css"/>

	<!-- ExtJS -->
	<!-- Make sure prototype.js is loaded before ExtJS -->
	<script type="text/javascript" src="{-$desinventarURL-}/external/prototype.js"></script>
	{-include file="extjs.tpl"-}
	<script type="text/javascript">
		jQuery(document).ready(function() {
			onReadyMain();
			onReadyExtJS();
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
	{-include file="desinventarmenu.tpl"-}
</body>
</html>
