{-config_load file=`$lg`.conf section="dc_thematicmap"-}
{-**** SHOW RESULTS ****-}
{-if $ctl_showres-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<meta http-equiv="Pragma" content="text/html; charset=utf-8; no-cache" />
{-if $shw_server-}
<!--
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAfQolBKtJvhOLwVfLoxEfMBQ77LACC71meKxbfZwyDLYGQlGiIRTFJ_UlTeqhUqMf6iE54G8kcN3sJQ"></script>
	<script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>
	<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers"></script>
-->
{-/if-}
	<script src="/openlayers/lib/OpenLayers.js"></script>
	<script type="text/javascript">
		var lon = {-if $lon != ''-}{-$lon-}{-else-}0{-/if-};
		var lat = {-if $lon != ''-}{-$lat-}{-else-}0{-/if-};
		var zoom = parseInt('{-$zoom-}');//{-if $isvreg-}4{-else-}6{-/if-};
		var map;
		
		function init() {
			var options = {
				projection    : new OpenLayers.Projection("EPSG:900913"),
				minResolution : "auto",
				minExtent     : new OpenLayers.Bounds(-1, -1, 1, 1),
				units         : "m",
				maxResolution : 156543.0339,
				maxExtent     : new OpenLayers.Bounds(-20037508.34, -20037508.34,
				                                       20037508.34,  20037508.34),
				controls: []};
			map = new OpenLayers.Map('map'); // options
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.LayerSwitcher({'ascending':false}));
			map.addControl(new OpenLayers.Control.KeyboardDefaults());
			map.addControl(new OpenLayers.Control.MousePosition());
			map.addControl(new OpenLayers.Control.NavToolbar());
			//map.addControl(new OpenLayers.Control.MouseToolbar());
			//map.addControl(new OpenLayers.Control.Permalink());
			//map.addControl(new OpenLayers.Control.OverviewMap({ minRatio: 10, maxRatio: 26 }));
			//map.addControl(new OpenLayers.Control.ScaleLine());
			// Effects layer(s)
{-foreach name=rgl key=k item=i from=$rgl-}
			var db{-$k-} = new OpenLayers.Layer.WMS("DI8 / {-$i.regname-}", 
					"/cgi-bin/{-$mps-}?", { map:'{-$i.map-}', layers:'{-$i.ly1-}', 'transparent':true, 'format':'png' },
					{'isBaseLayer':false});
			map.addLayer(db{-$k-});
 {-if !$isvreg-}
			// Admin layers
  {-foreach name=glev key=ky item=it from=$glev-}
			var adm{-$smarty.foreach.glev.iteration-} = new OpenLayers.Layer.WMS("{-$it[0]-}", 
					"/cgi-bin/{-$mps-}?", { map:'{-$i.map-}', layers:'admin0{-$ky-}', 'transparent':true, 'format':'png' },
					{'isBaseLayer':false});
			adm{-$smarty.foreach.glev.iteration-}.setVisibility(false);
			map.addLayer(adm{-$smarty.foreach.glev.iteration-});
  {-/foreach-}
 {-/if-}
{-/foreach-}
			// WMS Local Base Map
			var base = new OpenLayers.Layer.WMS("Local BaseMap",
					"/cgi-bin/{-$mps-}?", { map:'{-$basemap-}', layers:'base', 'transparent':false, 'format':'png' },
					{'isBaseLayer':true });
			map.addLayer(base);
/*
			// Microsoft Virtual Earth Base Layer
			var virtualearth = new OpenLayers.Layer.VirtualEarth("Microsoft Virtual Earth", { 'sphericalMercator': true });
			map.addLayer(virtualearth);
			// Yahoo Maps Base Layer
			var yahoo = new OpenLayers.Layer.Yahoo( "Yahoo Maps", { sphericalMercator: true });
			map.addLayer(yahoo);
			// maps.google.com
			var goog1 = new OpenLayers.Layer.Google("** Google Basic",
					{type: G_NORMAL_MAP, 'sphericalMercator': false});
			map.addLayer(goog1);
			var goog2 = new OpenLayers.Layer.Google("** Google Satellite",
					{type: G_SATELLITE_MAP});
			map.addLayer(goog2);
*/
			var met1 = new OpenLayers.Layer.WMS("Metacarta Basic",
				"http://labs.metacarta.com/wms/vmap0",
				{'layers': 'basic', 'transparent': true},
				{'isBaseLayer':true});
/*
			var met1 = new OpenLayers.Layer.WMS("** Metacarta Basic",
					"http://labs.metacarta.com/wms-c/Basic.py", {layers:'basic', 'transparent':true, 'format':'png' },
					{'isBaseLayer':true });
*/
			met1.setVisibility(false);
			map.addLayer(met1);
/*
			// 2009-02-06 (jhcaiced) Metacarta Satellite doesn't work with Spherical Mercator, this needs to be fixed !!
			var met2 = new OpenLayers.Layer.WMS("** Metacarta Satellite",
					"http://labs.metacarta.com/wms-c/Basic.py", {layers:'satellite', 'transparent':true, 'format':'png' },
					{'isBaseLayer':true });
			met2.setVisibility(false);
			map.addLayer(met2);
			// Change map
			vlayer = new OpenLayers.Layer.Vector("{-#tdrawpoint#-}");
			map.addLayer(vlayer);
			map.addControl(new OpenLayers.Control.EditingToolbar(vlayer));
			var pegeo = new OpenLayers.Layer.WMS("** Geología Perú",
					"http://per.geosemantica.net/services/mapserv.exe?MAP=2c1bc078-13e6-4734-863a-5636442a2e30_wms.map", 
					{'layers':'geoutm_shp', 'transparent':true, 'format':'png' },
					{'isBaseLayer':false});
			pegeo.setVisibility(false);
			map.addLayer(pegeo);
			var cosis = new OpenLayers.Layer.WMS("** Amenaza Sísmica Colombia",
					"http://col.geosemantica.net/services/mapserv.exe?map=8ec9a29a-f170-4023-9fe0-bf3d38c5ee2e.map",
					{'layers':'aisacel_1_shp', 'transparent':true, 'format':'png' },
					{'isBaseLayer':false});
			cosis.setVisibility(false);
			map.addLayer(cosis);
			var queryTarget = $('queryOut');
			WMSToolbar = new OpenLayers.Control.WMSToolbar({queryTarget: queryTarget});
			map.addControl(WMSToolbar);
			//parent.document.getElementById('frmwait').innerHTML='';
*/
			// Do a translation of map center coordinates to Spherical Mercator
			// 2008-03-02 (mayandar) OpenLayers-2.7 Not found...
/*			var proj = new OpenLayers.Projection("EPSG:4326");
			var point = new OpenLayers.LonLat(lon, lat);
			point.transform(proj, map.getProjectionObject());
			map.setCenter(point, zoom); 
*/
			map.setCenter(new OpenLayers.LonLat(lon, lat), zoom); 
		}
		window.onload = function() {
			var qrydet = parent.document.getElementById('querydetails');
			var qdet = "=> ";
{-foreach key=k item=i from=$qdet-}
 {-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-}";{-/if-}
 {-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-}";{-/if-}
 {-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-}";{-/if-}
 {-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-}";{-/if-}
 {-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-}";{-/if-}
 {-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-}";{-/if-}
 {-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-}";{-/if-}
 {-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-}";{-/if-}
{-/foreach-}
			qrydet.innerHTML = qdet;
			init();
		}
	</script>
	<link rel="stylesheet" href="../css/desinventar.css" type="text/css"/>
	<style type="text/css">
		#map {	width: 100%; height: 100%; border: 1px solid black; }
		#queryOut {	width:800px; height: 200px; border: 1px solid black;
								position:absolute; left:10px; top:500px; overflow:auto; }
	</style>
 </head>
 <body>
	<table class="grid" height="100%">
		 <tr><td>{-$mapfilename-}</td></tr>
		 <tr>
		 <td valign="top">
		  <div class="dwin" style="width:200px;">
				<p align="right">{-#trepnum#-}: {-$tot-}</p>
				<hr>
				<h4>{-#tmapof#-} {-$rgl[0].info.TITLE-}</h4>
				<p align="justify">{-#lev#-}: {-$rgl[0].info.LEVEL-}; 
{-foreach key=k item=i from=$rgl[0].info-}
 {-if $k == "GEO"-}<i>{-#geo#-}:</i> {-$i-}; {-/if-}
 {-if $k == "EVE"-}<i>{-#eve#-}:</i> {-$i-}; {-/if-}
 {-if $k == "CAU"-}<i>{-#cau#-}:</i> {-$i-}; {-/if-}
 {-if $k == "EFF"-}<i>{-#eff#-}:</i> {-$i-}; {-/if-}
 {-if $k == "BEG"-}<i>{-#beg#-}:</i> {-$i-}; {-/if-}
 {-if $k == "END"-}<i>{-#end#-}:</i> {-$i-}; {-/if-}
 {-if $k == "SOU"-}<i>{-#sou#-}:</i> {-$i-}; {-/if-}
 {-if $k == "SER"-}<i>{-#ser#-}:</i> {-$i-}; {-/if-}
{-/foreach-}
 {-if !$isvreg-}{-$rgl[0].regname-}{-/if-}</p>
				<hr>
{-if $isvreg-}
				<img src="/cgi-bin/{-$mps-}?map={-$rgl[0].map-}&SERVICE=WMS&VERSION=1.1.1&REQUEST=getlegendgraphic&LAYER={-$rgl[1].ly1-}&FORMAT=image/png">
{-else-}
 {-if $shw_server-}
			  <image src="../images/ge_icon.png"> <a href="thematicmap.php?r={-$reg-}&cmd=getkml">{-#tgetgearth#-}</a><hr><br>
 {-/if-}
				<img src="/cgi-bin/{-$mps-}?map={-$rgl[0].map-}&SERVICE=WMS&VERSION=1.1.1&REQUEST=getlegendgraphic&LAYER={-$rgl[0].ly1-}&FORMAT=image/png">
{-/if-}
		  </div>
		 </td>
		 <td valign="top">
		  <div id="map" class="dwin" style="width:700px; height:530px"></div>
		 </td>
	  </tr>
	</table>
 </body>
</html>
{-/if-}
