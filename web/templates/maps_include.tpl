{-if $desinventarHasInternet -}
	<link rel="stylesheet" type="text/css" href="/openlayers/theme/default/style.css?version={-$version-}" />
	<link rel="stylesheet" type="text/css" href="/openlayers/theme/default/google.css?version={-$version-}" />
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	{-if $hasVEMap -}
		<script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>
	{-/if-}
	<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers"></script>
{-/if-}
<script src="/openlayers/lib/OpenLayers.js?version={-$version-}"></script>
