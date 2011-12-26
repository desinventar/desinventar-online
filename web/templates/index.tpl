{-config_load file="$lg.conf" section="grpMainStrings"-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
{-config_load file="$lg.conf" section="querydef"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar Online Edition {-$majorversion-} | {-$RegionLabel-}</title>
		{-include file="jquery.tpl"-}
		{-include file="extjs.tpl"-}
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />
		{-if $desinventarRegionId != ''-}
			<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/datacards.css?version={-$jsversion-}" />
		{-/if-}

		<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>

		<script type="text/javascript" src="{-$desinventarURL-}/js/jquery.snippets.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/about_dialog.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>

		{-if $desinventarUserId != ''-}
			<script type="text/javascript" src="/fileuploader/fileuploader.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/user_account.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/admin_users.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/database_upload.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/database_create.js?version={-$jsversion-}"></script>
		{-/if-}

		{-if $desinventarRegionId != ''-}
			<script type="text/javascript" src="{-$desinventarURL-}/external/listMan.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/external/wd.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/external/accordion.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/external/palette.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/external/combo-box.js"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/external/checktree/checktree.js"></script>
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
			<script type="text/javascript" src="{-$desinventarURL-}/js/user.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/query_results.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_edit.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_export.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/database_users.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/admin_users.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/userperm_admin.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/database_config.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/diadmin.js?version={-$jsversion-}"></script>
			<script type="text/javascript" src="{-$desinventarURL-}/js/main.js?version={-$jsversion-}"></script>

			{-if $appOptions.UseRemoteMaps > 0-}
				<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=euzuro-openlayers"></script>
				<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
			{-/if-}
			<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/external/checktree/checktree.css"/>
			<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/accordion.css"/>
		{-/if-}
		<script type="text/javascript" src="{-$desinventarURL-}/js/main_ext.js?version={-$jsversion-}"></script>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				onReadyCommon();
				onReadyUserLogin();
				onReadyAboutDialog();
				{-if $desinventarUserId != ''-}
					onReadyUserAccount();
					onReadyDatabaseUpload();
					onReadyDatabaseCreate();
				{-/if-}
				{-if $desinventarRegionId != ''-}
					onReadyMain();
				{-/if-}
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
				{-include file="block_querydesign.tpl"-}
			</div>
			
			<!-- Central Content Area -->
			<div id="container">
				{-include file="about_dialog_ext.tpl"-}
				{-include file="user_login_ext.tpl"-}
				{-include file="database_list.tpl"-}
				{-if $desinventarUserId != ''-}
					{-include file="user_account_ext.tpl"-}
					{-include file="database_upload_ext.tpl"-}
					{-include file="database_create_ext.tpl"-}
				{-/if-}
				{-if $desinventarRegionId != ''-}
					{-include file="block_content.tpl"-}
				{-/if-}
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
