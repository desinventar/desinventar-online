{-config_load file="$lg.conf" section="grpMenuUser"-}
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
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
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_upload.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_create.js?version={-$jsversion-}"></script>
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
				onReadyExtJS();
			});
		</script>
	</head>
	<body>
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
			</div>

			<!-- Help Section -->
			<div id="south">
				{-include file="block_help.tpl"-}
			</div>
		</div>
		{-include file="desinventarinfo.tpl"-}
		{-include file="desinventarmenu.tpl"-}
	</body>
</html>
