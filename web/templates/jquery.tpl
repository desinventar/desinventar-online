{-if $desinventarLibs != ''-}
<link rel="stylesheet" href="{-$desinventarLibs-}/jquery-ui/1.8.24/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="{-$desinventarLibs-}/jquery-tooltip/1.3.1/jquery.tooltip.css" type="text/css" media="all" />
{-else-}
<link rel="stylesheet" href="/jquery/ui/1.8/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="/jquery/tooltip/css/jquery.tooltip.css" type="text/css" media="all" />
{-/if-}

{-if $desinventarMode=='normal'-}
	<script type="text/javascript" src="{-$desinventarURL-}/scripts/jquery.desinventar-{-$jsversion-}.js"></script>
{-else-}
	{-if $desinventarLibs != ''-}
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery/1.8.3/jquery.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-ui/1.8.24/ui/minified/jquery-ui.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-dimensions/1.1.2/jquery.dimensions.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-tooltip/1.3.1/jquery.tooltip.min.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-pluginsblog/20090512/jquery.stringQuery.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-pluginsblog/20090512/jquery.dataSelector.js"></script>
		<script type="text/javascript" src="{-$desinventarLibs-}/jquery-ba-bbq/1.2.1/jquery.ba-bbq.min.js"></script>
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
