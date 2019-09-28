/* global Ext, OpenLayers, google */

// Do not remove this line, initialize map=null
// to avoid an error with IE and the maps
var map = null

function init() {
  jQuery('body').on('cmdViewMapParams', function() {
    Ext.getCmp('wndViewMapParams').show()
  })
  //Initialize
  jQuery('div.ViewMapParams').on('cmdInitialize', function() {
    doViewMapParamsInitialize()
  })
}

function doViewMapParamsInitialize() {
  // Level of Representation
  var geolevel_list = jQuery('div.ViewMapParams select.Geolevel')
  geolevel_list.find('option').remove()
  jQuery.each(jQuery('body').data('GeolevelsList'), function(key, value) {
    if (parseInt(value.HasMap) > 0) {
      geolevel_list.append(
        jQuery('<option>', {
          value: value.GeoLevelId + '|D.GeographyId|'
        }).text(value.GeoLevelName)
      )
    }
  })
  geolevel_list.val(jQuery('option:first', geolevel_list).val())

  // Variable to be represented
  var field_list = jQuery('div.ViewMapParams select.Field')
  field_list.find('option').remove()
  field_list.append(
    jQuery('<option>', { value: 'D.DisasterId||' }).text(
      jQuery('#RepNumLabel').text()
    )
  )
  // EffectPeople (ef1)
  jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + 'Q|>|-1' }).text(label)
      )
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|=|-1' }).text(
          jQuery('#AuxHaveLabel').text() + ' ' + label
        )
      )
    }
  )
  // EffectLosses1 List (ef2)
  jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label)
      )
    }
  )
  // EffectLosses2 List (ef3)
  jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label)
      )
    }
  )
  // EffectSector (sec)
  jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|=|-1' }).text(
          jQuery('#AuxAffectLabel').text() + ' ' + label
        )
      )
    }
  )
  field_list.append(
    jQuery('<option>', { value: '', disabled: 'disabled' }).text('---')
  )
  // EEFieldList
  jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
    var field = value['id']
    var label = value['name']
    field_list.append(
      jQuery('<option>', { value: 'E.' + field + '|>|-1' }).text(label)
    )
  })
  field_list.val(jQuery('option:first', field_list).val())

  jQuery('div.ViewMapParams').on('click', '#viewMapParamsGenColors', function(
    event
  ) {
    genColors()
    event.preventDefault()
  })
}

function createThematicMap() {
  var lon = parseFloat(jQuery('#prmMapLat').val())
  var lat = parseFloat(jQuery('#prmMapLon').val())
  var minx = parseFloat(jQuery('#prmMapMinX').val())
  var maxx = parseFloat(jQuery('#prmMapMaxX').val())
  var miny = parseFloat(jQuery('#prmMapMinY').val())
  var maxy = parseFloat(jQuery('#prmMapMaxY').val())
  OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3
  OpenLayers.ImgPath = jQuery('#desinventarOpenLayersURL').val() + '/img/'

  OpenLayers.Util.onImageLoadErrorColor = 'transparent'
  var prj1 = new OpenLayers.Projection('EPSG:4326')
  var prj2 = new OpenLayers.Projection('EPSG:900913')
  var options = {
    projection: prj2,
    displayProjection: prj1,
    minResolution: 'auto',
    minExtent: new OpenLayers.Bounds(-1, -1, 1, 1),
    units: 'm',
    maxResolution: 156543.0339,
    maxExtent: new OpenLayers.Bounds(
      -20037508.34,
      -20037508.34,
      20037508.34,
      20037508.34
    ),
    controls: [],
    numZoomLevels: 22
  }
  map = new OpenLayers.Map('map', options)
  map.addControl(
    new OpenLayers.Control.PanZoomBar({
      zoomStopHeight: 3,
      zoomWorldIcon: true
    })
  )
  map.addControl(new OpenLayers.Control.LayerSwitcher({ ascending: false }))
  map.addControl(
    new OpenLayers.Control.MousePosition({ displayClass: 'MapMousePosition' })
  )
  map.addControl(new OpenLayers.Control.NavToolbar())

  // WMS Local Base Map
  var base = new OpenLayers.Layer.WMS(
    'Local BaseMap',
    jQuery('#desinventarURL').val() + '/wms/',
    {},
    { isBaseLayer: true }
  )
  map.addLayer(base)

  // Add Remote Layers
  if (parseInt(jQuery('#optionUseRemoteMaps').val()) > 0) {
    // Google Layers
    var gphy = new OpenLayers.Layer.Google('Google Physical', {
      type: google.maps.MapTypeId.TERRAIN
    })
    map.addLayer(gphy)
    var gmap = new OpenLayers.Layer.Google('Google Basic', {
      numZoomLevels: 20
    })
    map.addLayer(gmap)
    var ghyb = new OpenLayers.Layer.Google('Google Hybrid', {
      type: google.maps.MapTypeId.HYBRID,
      numZoomLevels: 20
    })
    map.addLayer(ghyb)
    var gsat = new OpenLayers.Layer.Google('Google Satellite', {
      type: google.maps.MapTypeId.SATELLITE,
      numZoomLevels: 22
    })
    map.addLayer(gsat)

    // Metacarta Basic Base Layer (not working with OpenLayers 3...
    //var met1 = new OpenLayers.Layer.WMS("Metacarta Basic", "http://labs.metacarta.com/wms/vmap0", {'layers': 'basic', 'transparent': true}, {'isBaseLayer':true});
    //met1.setVisibility(false);
    //map.addLayer(met1);
  }

  // Effects and Admin layer(s)
  jQuery('#MapEffectLayers div').each(function() {
    var LayerName = jQuery(this)
      .find(':eq(2)')
      .text()
      .trim()
    var effectLayer = new OpenLayers.Layer.WMS(
      'DesInventar/' +
        jQuery(this)
          .find(':eq(0)')
          .text(),
      jQuery('#desinventarURL').val() +
        '/wms/' +
        jQuery('#prmMapId').val() +
        '/effects/',
      {
        format: 'png',
        layers: LayerName
      },
      {
        isBaseLayer: false
      }
    )
    map.addLayer(effectLayer)

    jQuery('#MapAdminLayers div').each(function() {
      var layername = jQuery(this)
        .find(':eq(1)')
        .text()
        .trim()
      var layer = new OpenLayers.Layer.WMS(
        jQuery(this)
          .find(':eq(0)')
          .text(),
        jQuery('#desinventarURL').val() +
          '/wms/' +
          jQuery('#prmMapId').val() +
          '/' +
          layername +
          '/',
        {
          format: 'png',
          layers: layername
        },
        {
          isBaseLayer: false
        }
      )
      layer.setVisibility(false)
      map.addLayer(layer)
    })
  })

  // 2009-08-07 (jhcaiced) Calculate Zoom of Area an Show Map Centered
  var pt1 = new OpenLayers.LonLat(minx, miny)
  var pt2 = new OpenLayers.LonLat(maxx, maxy)
  var bounds = new OpenLayers.Bounds()
  pt1.transform(prj1, map.getProjectionObject())
  pt2.transform(prj1, map.getProjectionObject())
  bounds.extend(pt1)
  bounds.extend(pt2)
  const zoom = base.getZoomForExtent(bounds)
  map.setCenter(bounds.getCenterLonLat(), zoom)
  if (lon == 0 && lat == 0) {
    map.zoomToMaxExtent()
  }

  jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text())
  jQuery('#linkRestoreMapTitle').click(function() {
    jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text())
    return false
  })
}

function dechex(dec) {
  var Char_hexadecimales = '0123456789ABCDEF'
  var low = dec % 16
  var high = (dec - low) / 16
  return '' + Char_hexadecimales.charAt(high) + Char_hexadecimales.charAt(low)
}

function hexdec(hex) {
  return parseInt(hex, 16)
}

function genColors() {
  var tbl = $('tbl_range')
  var cnt = tbl.rows.length - 2
  var a = $('_M+color[0]').value
  var z = $('_M+color[' + cnt + ']').value
  var a1 = hexdec(a.substring(1, 3))
  var z1 = hexdec(z.substring(1, 3))
  var a2 = hexdec(a.substring(3, 5))
  var z2 = hexdec(z.substring(3, 5))
  var a3 = hexdec(a.substring(5, 7))
  var z3 = hexdec(z.substring(5, 7))
  var m1 = (z1 - a1) / cnt
  var m2 = (z2 - a2) / cnt
  var m3 = (z3 - a3) / cnt
  var h1, h2, h3, val
  for (var i = 0; i <= cnt; i++) {
    h1 = dechex(a1 + m1 * i)
    h2 = dechex(a2 + m2 * i)
    h3 = dechex(a3 + m3 * i)
    val = '#' + h1 + h2 + h3
    $('_M+color[' + i + ']').value = val
    $('_M+ic[' + i + ']').style.backgroundColor = val
  }
}

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

export default {
  init,
  createThematicMap
}
