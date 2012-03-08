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

		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar-{-$jsversion-}.css" />

		<script type="text/javascript" src="{-$desinventarURL-}/external/json2.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/form2js.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/js2form.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.toObject.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.jec-1.3.3.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/listMan.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/wd.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/accordion.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/palette.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/checktree/checktree.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/Math.uuid.js"></script>

		<script type="text/javascript" src="/fileuploader/fileuploader.js"></script>
		{-if $desinventarMode=='normal'-}
			<script type="text/javascript" src="{-$desinventarURL-}/scripts/desinventar-{-$jsversion-}.js"></script>
		{-else-}
			{-include file="js.tpl"-}
		{-/if-}

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
