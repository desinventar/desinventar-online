<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/datacards.css?version={-$jsversion-}" />
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />
		{-include file="jquery.tpl"-}
		{-include file="extjs.tpl"-}
		<script type="text/javascript" src="{-$desinventarURL-}/js/jquery.snippets.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_edit.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_export.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_database_import.js?version={-$jsversion-}"></script>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				onReadyAdminDatabase();
				//doAdminDatabaseUpdateList();
				//doAdminDatabaseExportCreate();
				jQuery('#btnDatabaseExport').click(function() {
					jQuery('.clsAdminDatabaseExport').hide();
					Ext.getCmp('wndDatabaseExport').show();
					doAdminDatabaseExportAction();
				});
			});
		</script>
	</head>
	<body>
		DesInventar Database Admin
		Version : {-$jsversion-}<br />
		Language : {-$lg-}<br />
		{-include file="database_export.tpl"-}
		{-include file="desinventarmenu.tpl"-}
		{-include file="desinventarinfo.tpl"-}
		<br />
		<input type="button" id="btnDatabaseExport" value="Export" />
	</body>
</html>
