// @ts-ignore
const Ext = global.Ext

import { showtip } from './common'
import { enab, disab } from './diadmin'

function init() {
  // 2010-02-21 (jhcaiced) This jQuery calls ensures that the Period and Stat
  // parameters are not empty at the same time.
  jQuery('#prmGraphPeriod').change(function() {
    var Value = jQuery(this).val()
    if (Value != '') {
      jQuery('#prmGraphStat').val('')
    } else {
      jQuery('#prmGraphStat').val('MONTH')
    }
  })

  jQuery('#prmGraphStat').change(function() {
    var Value = jQuery(this).val()
    if (Value != '') {
      jQuery('#prmGraphPeriod').val('')
    } else {
      jQuery('#prmGraphPeriod').val('YEAR')
    }
  })

  jQuery('#prmGraphField1').change(function() {
    if (jQuery(this).val() != '') {
      enab($('prmGraphScale1'))
      enab($('prmGraphData1'))
      enab($('prmGraphMode1'))
      // Bug #112 - BAR Graphs with two variables are 2D by default
      doUpdateGraphParameters()
    } else {
      disab($('prmGraphScale1'))
      disab($('prmGraphData1'))
      disab($('prmGraphMode1'))
    }
  })

  jQuery('#prmGraphTypeHistogram').change(function() {
    jQuery('#prmGraphType').val('HISTOGRAM')
    var grp = parseInt(String(jQuery(this).val()))
    jQuery('#prmGraphSubType').val(grp)
    // Histogram Type
    disab($('_G+K_pie'))
    jQuery('#prmGraphKind')
      .val('BAR')
      .trigger('change')
    enab($('prmGraphPeriod'))
    jQuery('prmGraphPeriod').val('YEAR')
    enab($('prmGraphStat'))
    if (grp > 0) {
      disabAxis2()
      jQuery('#prmGraphMode0').val('NORMAL')
      disab($('prmGraphModeCummulative0'))
      // Bug #148 : Disable Stacked Mode
      enab($('prmGraphModeStacked0'))
      enab($('prmGraphModeStacked1'))
    } else {
      enabAxis2()
      jQuery('#prmGraphMode0').val('NORMAL')
      enab($('prmGraphModeCummulative0'))
      disab($('prmGraphModeStacked0'))
      disab($('prmGraphModeStacked1'))
    }
    disab($('_G+D_perc'))
    disab($('_G+D_perc2'))
    if (jQuery('#prmGraphTypeComparative').val() != '') {
      jQuery('#prmGraphTypeComparative').val('')
    }
  })

  jQuery('#prmGraphTypeComparative').change(function() {
    jQuery('#prmGraphType').val('COMPARATIVE')
    var grp = parseInt(String(jQuery(this).val()))
    jQuery('#prmGraphSubType').val(grp)
    // Comparatives
    disabAxis2()
    enab($('_G+K_pie'))
    jQuery('#prmGraphKind')
      .val('PIE')
      .trigger('change')
    jQuery('prmGraphPeriod').val('')
    disab($('prmGraphPeriod'))
    jQuery('prmGraphStat').val('')
    disab($('prmGraphStat'))
    jQuery('#prmGraphMode0').val('NORMAL')
    disab($('prmGraphModeCummulative0'))
    disab($('prmGraphModeStacked0'))
    disab($('prmGraphModeStacked1'))
    enab($('_G+D_perc'))
    enab($('_G+D_perc2'))
    if (jQuery('#prmGraphTypeHistogram').val() != '') {
      jQuery('#prmGraphTypeHistogram').val('')
    }
  })

  jQuery('#prmGraphKind').change(function() {
    let subType = parseInt(jQuery('#prmGraphSubType').val() + '') || 0
    var kind = jQuery(this).val() + ''
    if (subType > 0) {
      if (kind != 'PIE') {
        disab($('_G+D_perc'))
        disab($('_G+D_perc2'))
        if (jQuery('#prmGraphData0').val() == 'PERCENT') {
          jQuery('#prmGraphData0').val('NONE')
        }
      } else {
        enab($('_G+D_perc'))
        enab($('_G+D_perc2'))
      }
    }
    if (
      subType < 1 ||
      ((subType >= 200 || [3, 4].indexOf(subType) >= 0) &&
        ['BAR', 'LINE'].indexOf(kind) >= 0)
    ) {
      enabAxis2()
      enab($('prmGraphModeCummulative0'))
      disab($('prmGraphModeStacked0'))
      disab($('prmGraphModeStacked1'))
    } else {
      disabAxis2()
      disab($('prmGraphModeCummulative0'))
      enab($('prmGraphModeStacked0'))
      enab($('prmGraphModeStacked1'))
    }
    if (kind === 'PIE') {
      jQuery('#prmGraphScale0')
        .val('textint')
        .prop('disabled', true)
        .addClass('disabled')
    } else {
      jQuery('#prmGraphScale0')
        .val('textint')
        .prop('disabled', false)
        .removeClass('disabled')
    }

    jQuery('#prmGraphFeel option.3D').prop('disabled', false)
    jQuery('#prmGraphFeel').val('3D')
    if (kind == 'LINE') {
      jQuery('#prmGraphFeel').val('2D')
      jQuery('#prmGraphFeel option.3D').prop('disabled', true)
    }
    // Bug #112: BAR graphs with two variables are 2D by default
    if (kind == 'BAR') {
      doUpdateGraphParameters()
    }
  })

  jQuery('div.ViewGraphParams')
    .on('mouseover', 'select', function() {
      showtip(jQuery(this).data('helptext'))
    })
    .on('mouseover', 'b', function() {
      showtip(jQuery(this).data('helptext'))
    })

  // Initialize Controls on Load
  jQuery('#prmGraphTypeComparative')
    .val('')
    .trigger('change')
  jQuery('#prmGraphTypeHistogram')
    .val('D.DisasterBeginTime')
    .trigger('change')

  jQuery('body').on('cmdViewGraphParams', function() {
    Ext.getCmp('wndViewGraphParams').show()
  })

  jQuery('div.ViewGraphParams').on('cmdInitialize', function() {
    doViewGraphParamsInitialize()
  })
}

function doUpdateGraphParameters() {
  let kind = jQuery('#prmGraphKind').val()
  let field0 = jQuery('#prmGraphField0').val()
  let field1 = jQuery('#prmGraphField1').val()
  if (kind == 'BAR') {
    if (field0 != '' && field1 != '') {
      jQuery('#prmGraphFeel option.3D').prop('disabled', false)
      jQuery('#prmGraphFeel').val('2D')
    }
  }
}

function doViewGraphParamsInitialize() {
  // GraphTypeHistogram - By Geolevels
  var geolevel_list = jQuery('body').data('GeolevelsList')
  var select = jQuery('div.ViewGraphParams select.TypeHistogram')
  jQuery(select)
    .find('option.Geolevel')
    .remove()
  jQuery.each(geolevel_list, function(key, value) {
    var clone = jQuery('<option>', {
      value: 100 + parseInt(value.GeoLevelId)
    }).text(
      value.GeoLevelName +
        jQuery('div.ViewGraphParams span.HistogramByGeography').text()
    )
    jQuery(clone).addClass('Geolevel')
    jQuery(select).append(clone)
  })

  // GraphTypeComparative - By Geolevels
  var geolevelList = jQuery('body').data('GeolevelsList')
  var selectTypeComparative = jQuery(
    'div.ViewGraphParams select.TypeComparative'
  )
  jQuery(selectTypeComparative)
    .find('option.Geolevel')
    .remove()
  jQuery.each(geolevelList, function(key, value) {
    var clone = jQuery('<option>', {
      value: 200 + parseInt(value.GeoLevelId)
    }).text(
      jQuery('div.ViewGraphParams span.ComparativeByGeography').text() +
        ' ' +
        value.GeoLevelName
    )
    jQuery(clone).addClass('Geolevel')
    jQuery(selectTypeComparative).append(clone)
  })
}

function disabAxis2() {
  jQuery('#divVerticalAxis2').hide()
  jQuery('#prmGraphField1').val('')
}

function enabAxis2() {
  jQuery('#divVerticalAxis2').show()
}

export default {
  init
}
