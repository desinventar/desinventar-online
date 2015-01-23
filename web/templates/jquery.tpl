{-if $desinventarLibs != ''-}
<link rel="stylesheet" href="{-$desinventarLibs-}/jquery-ui/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="{-$desinventarLibs-}/jquery-tooltip/jquery.tooltip.css" type="text/css" media="all" />
{-else-}
<link rel="stylesheet" href="/jquery/ui/1.8/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="/jquery/tooltip/css/jquery.tooltip.css" type="text/css" media="all" />
{-/if-}

{-if $desinventarMode=='normal'-}
	<script type="text/javascript" src="{-$desinventarURL-}/scripts/jquery.desinventar-{-$jsversion-}.js"></script>
{-else-}
	{-if $desinventarLibs != ''-}
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery/jquery.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-ui/ui/minified/jquery-ui.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-dimensions/jquery.dimensions.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-tooltip/jquery.tooltip.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-pluginsblog/jquery.stringQuery.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-pluginsblog/jquery.dataSelector.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-ba-bbq/jquery.ba-bbq.min.js"></script>
	{-else-}
		<script type="text/javascript" src="/jquery/jquery.js"></script>
		<script type="text/javascript" src="/jquery/ui/1.8/ui/minified/jquery-ui.min.js"></script>
		<!--
		<script type="text/javascript" src="/jquery/bgiframe/jquery.bgiframe.js"></script>
		-->
		<script type="text/javascript" src="/jquery/dimensions/jquery.dimensions.min.js"></script>
		<script type="text/javascript" src="/jquery/tooltip/jquery.tooltip.min.js"></script>
		<script type="text/javascript" src="/jquery/jquery.stringQuery.js"></script>
		<script type="text/javascript" src="/jquery/jquery.dataSelector.js"></script>
		<script type="text/javascript" src="/jquery/jquery.ba-bbq.min.js"></script>
	{-/if-}
{-/if-}
<script type="text/javascript">
	jQuery.noConflict();
</script>
