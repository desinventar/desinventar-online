/* global Ext, selectall, combineForms, map */

function init() {
  jQuery('#queryBeginYear').blur(function() {
    validateBeginYear()
  })
  jQuery('#queryEndYear').blur(function() {
    validateEndYear()
  })
  jQuery('#btnViewData').click(function() {
    jQuery('body').trigger('cmdViewDataParams')
    return false
  })
  jQuery('#btnViewMap').click(function() {
    jQuery('body').trigger('cmdViewMapParams')
    return false
  })
  jQuery('#btnViewGraph').click(function() {
    jQuery('body').trigger('cmdViewGraphParams')
    return false
  })
  jQuery('#btnViewStd').click(function() {
    jQuery('body').trigger('cmdViewStdParams')
    return false
  })

  jQuery('body').on('cmdQueryResultsButtonShow', function() {
    jQuery('#btnResultSave').show()
    jQuery('#btnResultPrint').show()
    jQuery('body').trigger('cmdMainMenuResultButtonsEnable')
  })
  jQuery('body').on('cmdQueryResultsButtonHide', function() {
    jQuery('#btnResultSave').hide()
    jQuery('#btnResultPrint').hide()
    jQuery('body').trigger('cmdMainMenuResultButtonsDisable')
  })

  jQuery('#btnResultSave')
    .click(function() {
      if (jQuery('#DCRes').val() == 'M' || jQuery('#DCRes').val() == 'G') {
        saveRes('export', '')
      }
    })
    .mouseover(function() {
      if (jQuery('#DCRes').val() == 'D' || jQuery('#DCRes').val() == 'S') {
        jQuery('#btnResultSaveOptions').show()
        jQuery('#btnResultShow').val(1)
      }
      return false
    })
    .mouseout(function() {
      jQuery('#btnResultShow').val('')
    })
  jQuery('#btnResultSaveOptions').mouseout(function() {
    setTimeout(function() {
      if (jQuery('#btnResultShow').val() != '') {
        jQuery('#btnResultSaveOptions').hide()
      }
    }, 4000)
  })

  jQuery('#btnResultSaveXLS')
    .click(function() {
      saveRes('export', 'xls')
      return false
    })
    .mouseover(function() {
      jQuery('#btnResultShow').val(1)
    })
  jQuery('#btnResultSaveCSV')
    .click(function() {
      saveRes('export', 'csv')
      return false
    })
    .mouseover(function() {
      jQuery('#btnResultShow').val(1)
    })
  jQuery('#btnResultPrint').click(function() {
    printRes()
    return false
  })

  // Initialize code
  jQuery('body').trigger('cmdMainQueryUpdate')
}

function saveRes(cmd, typ) {
  if ($('DCRes').value != '') {
    switch ($('DCRes').value) {
      case 'D':
        $('_D+saveopt').value = typ
        sendList(cmd)
        break
      case 'M':
        // SaveMap to PNG Format
        sendMap(cmd)
        break
      case 'G':
        sendGraphic(cmd)
        break
      case 'S':
        $('_S+saveopt').value = typ
        sendStatistic(cmd)
        break
    }
  }
}

function validateBeginYear() {
  var prmQueryMinYear = jQuery('#prmQueryMinYear').val()
  var MinYear = jQuery('#queryBeginYear').val()
  if (parseInt(MinYear) != MinYear - 0) {
    jQuery('#queryBeginYear').val(prmQueryMinYear)
  }
}

function validateEndYear() {
  var prmQueryMaxYear = jQuery('#prmQueryMaxYear').val()
  var MaxYear = jQuery('#queryEndYear').val()
  if (parseInt(MaxYear) != MaxYear - 0) {
    jQuery('#queryEndYear').val(prmQueryMaxYear)
  }
}

function printRes() {
  window.print()
}

function sendList(cmd) {
  if (cmd == 'result') {
    jQuery('#prmQueryCommand').val('cmdGridShow')
  } else {
    jQuery('#prmQueryCommand').val('cmdGridSave')
  }
  if ($('_D+Field[]').length > 0) {
    $('_D+cmd').value = cmd
    selectall('_D+Field[]')
    var ob = $('_D+Field[]')
    var mystr = ''
    for (var i = 0; i < ob.length; i++) {
      mystr += ob[i].value + ','
    }
    mystr += 'D.DisasterId'
    $('_D+FieldH').value = mystr
    combineForms('frmMainQuery', 'CD')
    Ext.getCmp('westm').show()
    Ext.getCmp('westm').collapse()
    $('frmMainQuery').action = jQuery('#desinventarURL').val() + '/data.php'
    jQuery('#frmMainQuery').attr('target', 'dcr')
    if (cmd != 'result') {
      jQuery('#frmMainQuery').attr('target', 'iframeDownload')
    }
    jQuery('#frmMainQuery').submit()
    //hideMap();
    return true
  } else {
    return false
  }
}

function sendMap(cmd) {
  jQuery('#prmQueryCommand').val('cmdMapShow')
  if ($('_M+Type').length > 0) {
    $('_M+cmd').value = cmd
    if (cmd == 'export') {
      jQuery('#prmQueryCommand').val('cmdMapSave')

      // to export image save layers and extend..
      var mm = map
      //var mm = dcr.map;
      var extent = mm.getExtent()
      var layers = mm.layers
      var activelayers = []
      for (var i in layers) {
        if (
          layers[i].visibility &&
          layers[i].calculateInRange() &&
          !layers[i].isBaseLayer
        ) {
          activelayers[activelayers.length] = layers[i].params['LAYERS']
        }
      }

      jQuery('form.MapSave').attr(
        'action',
        jQuery('#desinventarURL').val() + '/thematicmap.php'
      )
      jQuery('form.MapSave').attr('target', 'iframeDownload')
      jQuery('form.MapSave input.Extent').val(
        [extent.left, extent.bottom, extent.right, extent.top].join(',')
      )
      jQuery('form.MapSave input.Layers').val(activelayers)
      jQuery('form.MapSave input.Id').val(jQuery('#prmMapId').val())
      jQuery('form.MapSave input.Title').val(jQuery('#MapTitle').val())
      jQuery('form.MapSave').trigger('submit')
    } else {
      combineForms('frmMainQuery', 'CM')
      Ext.getCmp('westm').show()
      Ext.getCmp('westm').collapse()
      $('frmMainQuery').action =
        jQuery('#desinventarURL').val() + '/thematicmap.php'
      jQuery('#frmMainQuery').attr('target', 'dcr')
      if (cmd != 'result') {
        jQuery('#frmMainQuery').attr('target', 'iframeDownload')
      }
      jQuery('#frmMainQuery').submit()
      //hideMap();
    }
    return true
  } else {
    return false
  }
}

function sendGraphic(cmd) {
  if (cmd == 'result') {
    jQuery('#prmQueryCommand').val('cmdGraphShow')
  } else {
    jQuery('#prmQueryCommand').val('cmdGraphSave')
  }
  jQuery('#prmGraphCommand').val(cmd)
  jQuery('#frmGraphParams input.TendencyLabel0').val(
    jQuery('#frmGraphParams #prmGraphTendency0 option:selected').text()
  )
  jQuery('#frmGraphParams #prmGraphFieldLabel0').val(
    jQuery('#frmGraphParams #prmGraphField0 option:selected').text()
  )
  jQuery('#frmGraphParams #prmGraphFieldLabel1').val(
    jQuery('#frmGraphParams #prmGraphField1 option:selected').text()
  )

  combineForms('frmMainQuery', 'frmGraphParams')
  Ext.getCmp('westm').show()
  Ext.getCmp('westm').collapse()
  $('frmMainQuery').action = jQuery('#desinventarURL').val() + '/'
  jQuery('#frmMainQuery').attr('target', 'dcr')

  if (cmd != 'result') {
    jQuery('#frmMainQuery').attr('target', 'iframeDownload')
  }
  jQuery('#frmMainQuery').submit()
  //hideMap();
}

function sendStatistic(cmd) {
  if (cmd == 'result') {
    jQuery('#prmQueryCommand').val('cmdStatShow')
  } else {
    jQuery('#prmQueryCommand').val('cmdStatSave')
  }
  if (
    jQuery('#fldStatParam_FirstLev').val() != '' &&
    $('fldStatFieldSelect').length > 0
  ) {
    $('_S+cmd').value = cmd
    var field = 'D.DisasterId||'
    var fieldlabel = jQuery('#txtStatRecords').text()
    jQuery('#fldStatFieldSelect option').each(function() {
      field +=
        ',' +
        jQuery(this)
          .val()
          .replace(/,/, '')
      fieldlabel +=
        ',' +
        jQuery(this)
          .text()
          .replace(/,/, '')
    })
    jQuery('#fldStatField').val(field)
    jQuery('#fldStatFieldLabel').val(fieldlabel)

    jQuery('#frmStatParams td.StatGroup').each(function() {
      jQuery('input', this).val(jQuery('select option:selected', this).text())
    })
    combineForms('frmMainQuery', 'frmStatParams')
    Ext.getCmp('westm').show()
    Ext.getCmp('westm').collapse()
    $('frmMainQuery').action =
      jQuery('#desinventarURL').val() + '/statistic.php'
    jQuery('#frmMainQuery').attr('target', 'dcr')
    if (cmd != 'result') {
      jQuery('#frmMainQuery').attr('target', 'iframeDownload')
    }
    jQuery('#frmMainQuery').submit()
    return true
  } else {
    return false
  }
}

export default {
  init,
  sendList,
  sendMap,
  sendGraphic,
  sendStatistic
}
