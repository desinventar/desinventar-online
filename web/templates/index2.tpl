<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8" />
	<title>DesInventar Online Edition {-$majorversion-} | {-$RegionLabel-}</title>
	<!-- jQuery -->
	{-include file="jquery.tpl"-}
	<!-- ExtJS -->
	<!-- Make sure prototype.js is loaded before ExtJS -->
	<script type="text/javascript" src="{-$desinventarURL-}/external/prototype.js"></script>
	{-include file="extjs.tpl"-}

	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/datacards.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />

	<script type="text/javascript" src="{-$desinventarURL-}/js/main_ext.js?version={-$jsversion-}"></script>

	<script type="text/javascript">
		function doMainMenuHandler(item) 
		{
			console.log('MenuItem : ' + item.id);
		}
		jQuery(document).ready(function() {
			doViewportCreate();
			doMainMenuCreate();
			/*
			var tb = new Ext.Toolbar();
			var menu = new Ext.menu.Menu({
				id: 'basicMenu',
				items:
				[
					{ id: 'mnuHelpAbout'  , text: 'About'  , handler: onMenuItem },
					{ id: 'mnuHelpWebsite', text: 'Website', handler: onMenuItem }
				]
			});
			tb.render('toolbar');
			tb.add({id:'mnuHelp', text:'Help', handler: onMenuItem, menu: menu});
			*/
		});
	</script>
</head>
<body>
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
