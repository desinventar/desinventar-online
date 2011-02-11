{-if $desinventarHasInternet-}
	<link rel="stylesheet" type="text/css" href="/openlayers/theme/default/style.css?version={-$version-}" />
	<link rel="stylesheet" type="text/css" href="/openlayers/theme/default/google.css?version={-$version-}" />
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	{-if $hasVEMap-}
		<script type="text/javascript" src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>
	{-/if-}
	<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=euzuro-openlayers"></script>
{-/if-}
<script type="text/javascript" src="/openlayers/lib/OpenLayers.js?version={-$version-}"></script>
