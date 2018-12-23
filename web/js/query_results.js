function onReadyQueryResults() {
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

function validateQueryDefinition() {
  var iReturn = 1
  return iReturn
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
