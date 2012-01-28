{-config_load file="$lg.conf" section="grpMainStrings"-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
{-config_load file="$lg.conf" section="querydef"-}
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar Online Edition {-$majorversion-} | {-$RegionLabel-}</title>
		<!-- jQuery -->
		{-include file="jquery.tpl"-}
		<!-- ExtJS -->
		{-include file="extjs.tpl"-}

		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />

		<script type="text/javascript" src="{-$desinventarURL-}/external/json2.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/form2js.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/js2form.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.toObject.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/listMan.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/wd.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/accordion.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/palette.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/combo-box.js"></script>

		<script type="text/javascript" src="{-$desinventarURL-}/external/checktree/checktree.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/Math.uuid.js"></script>

		<script type="text/javascript" src="{-$desinventarURL-}/js/datacards.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/extraeffects.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/dbconfig_geography.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/dbconfig_causes.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/data.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/graphic.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/thematicmap.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/statistic.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/user_account.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/query_results.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/stat_params.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_edit.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_export.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_upload.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_create.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_users.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_events.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_geolevels.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_users.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/userperm_admin.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_config.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/dboperations.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/diadmin.js?version={-$jsversion-}"></script>

		<script type="text/javascript" src="{-$desinventarURL-}/js/main.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/main_ext.js?version={-$jsversion-}"></script>

		<script type="text/javascript" src="/fileuploader/fileuploader.js"></script>

		{-include file="maps_include.tpl"-}
		{-if $appOptions.UseRemoteMaps > 0-}
			<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=euzuro-openlayers"></script>
			<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		{-/if-}
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/external/checktree/checktree.css"/>
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/accordion.css"/>

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
			<div id="divWestPanel">
				{-include file="block_querydesign.tpl"-}
			</div>
			
			<!-- Central Content Area -->
			<div id="container" style="heigth:100%;">
				{-include file="block_content.tpl"-}
			</div>

			<!-- Help Section -->
			<div id="south">
				{-include file="block_help.tpl"-}
			</div>
		</div>
		
		<!-- General App Information -->
		{-include file="desinventarinfo.tpl"-}
		{-include file="desinventarmenu.tpl"-}
	</body>
</html>
