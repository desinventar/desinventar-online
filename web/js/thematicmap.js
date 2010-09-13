//var map = null;

function onReadyThematicMap() {
	var lon = parseFloat(jQuery('#prmMapLat').val());
	var lat = parseFloat(jQuery('#prmMapLon').val());
	var zoom = parseInt(jQuery('#prmMapZoom').val());
	var minx = parseFloat(jQuery('#prmMapMinX').val());
	var maxx = parseFloat(jQuery('#prmMapMaxX').val());
	var miny = parseFloat(jQuery('#prmMapMinY').val());
	var maxy = parseFloat(jQuery('#prmMapMaxY').val());

	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	OpenLayers.Util.onImageLoadErrorColor = "transparent";
	var prj1 = new OpenLayers.Projection("EPSG:4326");
	var prj2 = new OpenLayers.Projection("EPSG:900913");
	var options = {
		projection        : prj2,
		displayProjection : prj2,
		minResolution     : "auto",
		minExtent         : new OpenLayers.Bounds(-1, -1, 1, 1),
		units             : "m",
		maxResolution     : 156543.0339,
		maxExtent         : new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34,  20037508.34),
		controls          : [],
		numZoomLevels     : 22
	};
	map = new OpenLayers.Map('map', options);
	map.addControl(new OpenLayers.Control.PanZoomBar({zoomStopHeight:3,zoomWorldIcon:true}));
	map.addControl(new OpenLayers.Control.LayerSwitcher({ascending:false}));
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.NavToolbar());
	
	// WMS Local Base Map
	mapServer = jQuery('#prmMapServer').val();
	var base = new OpenLayers.Layer.WMS("Local BaseMap",
			"/cgi-bin/" + mapServer + "?", { map:jQuery('#prmMapBase').val(), layers:'base', 'transparent':false, 'format':'png' },
			{'isBaseLayer':true });
	map.addLayer(base);

	// Add Remote Layers	
	if (parseInt(jQuery('#desinventarHasInternet').val()) > 0) {
		// Yahoo Maps Base Layer
		var yahoo = new OpenLayers.Layer.Yahoo( "Yahoo Maps", { 'sphericalMercator': true });
		map.addLayer(yahoo);
	
		// Metacarta Basic Base Layer (not working with OpenLayers 3...
		/*
		var met1 = new OpenLayers.Layer.WMS("Metacarta Basic",
			"http://labs.metacarta.com/wms/vmap0", {'layers': 'basic', 'transparent': true}, {'isBaseLayer':true});
		met1.setVisibility(false);
		map.addLayer(met1);
		*/
		var gphy = new OpenLayers.Layer.Google("Google Physical" , {type: google.maps.MapTypeId.TERRAIN});
		map.addLayer(gphy);
		var gmap = new OpenLayers.Layer.Google("Google Basic"    , {numZoomLevels: 20 });
		map.addLayer(gmap);
		var ghyb = new OpenLayers.Layer.Google("Google Hybrid"   , {type: google.maps.MapTypeId.HYBRID, numZoomLevels:20});
		map.addLayer(ghyb);
		var gsat = new OpenLayers.Layer.Google("Google Satellite", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels:22});
		map.addLayer(gsat);
	} //if
	
	// Effects and Admin layer(s)
	jQuery('#MapEffectLayers div').each(function() {
		var MapFile = jQuery(this).find(':eq(1)').text();
		var layer = new OpenLayers.Layer.WMS(
			'DI8/' + jQuery(this).find(':eq(0)').text(),
			'/cgi-bin/' + jQuery('#prmMapServer').val() + '?',
			{map         : MapFile,
			 transparent : true,
			 format      : 'png',
			 layers      : jQuery(this).find(':eq(2)').text().trim()
			},
			{isBaseLayer :false
			}
		);
		map.addLayer(layer);
		jQuery('#MapAdminLayers div').each(function() {
			var layer = new OpenLayers.Layer.WMS(
				jQuery(this).find(':eq(0)').text(),
				'/cgi-bin/' + jQuery('#prmMapServer').val() + "?", 
				{map         : MapFile,
				 transparent : true,
				 format      : 'png',
				 layers      : jQuery(this).find(':eq(1)').text().trim()
				},
				{isBaseLayer : false}
			);
			layer.setVisibility(false);
			map.addLayer(layer);
		});
	});

	// 2009-08-07 (jhcaiced) Calculate Zoom of Area an Show Map Centered
	var pt1 = new OpenLayers.LonLat(minx, miny);
	var pt2 = new OpenLayers.LonLat(maxx, maxy);
	var bounds = new OpenLayers.Bounds();
	pt1.transform(prj1, map.getProjectionObject());
	pt2.transform(prj1, map.getProjectionObject());
	bounds.extend(pt1);
	bounds.extend(pt2);
	zoom = base.getZoomForExtent(bounds);
	map.setCenter(bounds.getCenterLonLat(), zoom);
	if (lon == 0 && lat == 0) {
		map.zoomToMaxExtent();
	}
	
	jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text());
	jQuery('#linkRestoreMapTitle').click(function() {
		jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text());
	});
} //onReadyThematicMap()

	/*
	// Microsoft Virtual Earth Base Layer
	var virtualearth = new OpenLayers.Layer.VirtualEarth("Microsoft Virtual Earth", { 'sphericalMercator': true });
	map.addLayer(virtualearth);
	*/


	/*
	// Use a Global Risk Data Platform (http://preview.grid.unep.ch/) WMS..
	var bk1 = new OpenLayers.Layer.WMS("Flood Risk..",
		"http://preview.grid.unep.ch:8080/geoserver/wms",
		{'layers': 'preview:fl_risk', 'transparent': true},
		{'isBaseLayer':true});
	bk1.setVisibility(false);
	map.addLayer(bk1);
	*/

	/*
	// 2009-07-11 (jhcaiced) This layer doesn't work with Spherical Mercator projection (????)
	var bk2 = new OpenLayers.Layer.WMS("GRDP - Population 2007",
		"http://metafunctions.grid.unep.ch/cgi-bin/mapserv",
		{ map:'/www/preview/previewims/etc/preview_ims.map', 'transparent':true, 'format':'png', 
			 'sphericalMercator': true, layers:'popdens'},
		{'isBaseLayer':true});
	bk2.setVisibility(false);
	map.addLayer(bk2);
	*/
	
	/* Metacarta Base Layers			
	// 2009-02-06 (jhcaiced) Metacarta Satellite doesn't work with Spherical Mercator, this needs to be fixed !!
	var met2 = new OpenLayers.Layer.WMS("** Metacarta Satellite",
			"http://labs.metacarta.com/wms-c/Basic.py", {layers:'satellite', 'transparent':true, 'format':'png' },
			{'isBaseLayer':true });
	met2.setVisibility(false);
	map.addLayer(met2);
	*/
	
	/* 
	// Layers from georiesgo/geosemantica
	// Change map
	vlayer = new OpenLayers.Layer.Vector("{-#tdrawpoint#-}");
	map.addLayer(vlayer);
	map.addControl(new OpenLayers.Control.EditingToolbar(vlayer));
	var pegeo = new OpenLayers.Layer.WMS("** Geología Perú",
			"http://per.geosemantica.net/services/mapserv.exe?MAP=2c1bc078-13e6-4734-863a-5636442a2e30_wms.map", 
			{'layers':'geoutm_shp', 'transparent':true, 'format':'png' }, {'isBaseLayer':false});
	pegeo.setVisibility(false);
	map.addLayer(pegeo);
	var cosis = new OpenLayers.Layer.WMS("** Amenaza Sísmica Colombia",
			"http://col.geosemantica.net/services/mapserv.exe?map=8ec9a29a-f170-4023-9fe0-bf3d38c5ee2e.map",
			{'layers':'aisacel_1_shp', 'transparent':true, 'format':'png' }, {'isBaseLayer':false});
	cosis.setVisibility(false);
	map.addLayer(cosis);
	var queryTarget = $('queryOut');
	WMSToolbar = new OpenLayers.Control.WMSToolbar({queryTarget: queryTarget});
	map.addControl(WMSToolbar);
	//parent.document.getElementById('frmwait').innerHTML='';
	
	*/
