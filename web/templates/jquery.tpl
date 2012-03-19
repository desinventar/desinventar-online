<link rel="stylesheet" href="/jquery/ui/css/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="/jquery/tooltip/css/jquery.tooltip.css" type="text/css" media="all" />
{-if $desinventarMode=='normal'-}
	<script type="text/javascript" src="{-$desinventarURL-}/scripts/jquery.desinventar-{-$jsversion-}.js"></script>
{-else-}
	<script type="text/javascript" src="/jquery/jquery.js"></script>
	<script type="text/javascript" src="/jquery/ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="/jquery/dimensions/jquery.dimensions.min.js"></script>
	<script type="text/javascript" src="/jquery/tooltip/jquery.tooltip.min.js"></script>
	<script type="text/javascript" src="/jquery/jquery.stringQuery.js"></script>
	<script type="text/javascript" src="/jquery/jquery.dataSelector.js"></script>
	<script type="text/javascript" src="/jquery/jquery.ba-bbq.min.js"></script>
{-/if-}
<script type="text/javascript">
	jQuery.noConflict();
</script>
