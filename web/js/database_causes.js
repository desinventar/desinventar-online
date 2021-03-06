function init() {
  //Attach main events
  jQuery('body').on('cmdDatabaseCausesShow', function() {
    doDatabaseCausesPopulateLists()
  })

  jQuery('div.DatabaseCauses span.status').hide()

  jQuery(
    '#tbodyDatabaseCauses_CauseListCustom,#tbodyDatabaseCauses_CauseListDefault'
  )
    .on('click', 'tr', function() {
      jQuery('#fldDatabaseCauses_CauseId').val(jQuery('.CauseId', this).text())
      jQuery('#fldDatabaseCauses_CauseName').val(
        jQuery('.CauseName', this).text()
      )
      jQuery('#fldDatabaseCauses_CauseDesc').val(
        jQuery('.CauseDesc', this).prop('title')
      )
      jQuery('#fldDatabaseCauses_CauseActiveCheckbox')
        .prop('checked', jQuery('.CauseActive :input', this).is(':checked'))
        .change()
      jQuery('#fldDatabaseCauses_CausePredefined').val(
        jQuery('.CausePredefined', this).text()
      )

      jQuery('#btnDatabaseCauses_Add').hide()
      doCausesFormSetup()
      jQuery('#divDatabaseCauses_Edit').show()
    })
    .on('mouseover', 'tr', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr', function() {
      jQuery(this).removeClass('highlight')
    })

  jQuery('#btnDatabaseCauses_Add').click(function() {
    jQuery('#divDatabaseCauses_Edit').show()
    jQuery(this).hide()
    jQuery('#fldDatabaseCauses_CauseId').val('')
    jQuery('#fldDatabaseCauses_CauseName').val('')
    jQuery('#fldDatabaseCauses_CauseDesc').val('')
    jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', false)
    jQuery('#fldDatabaseCauses_CauseActiveCheckbox')
      .prop('checked', true)
      .change()
    jQuery('#fldDatabaseCauses_CausePredefined').val(0)
    doCausesFormSetup()
    return false
  })

  jQuery('#btnDatabaseCauses_Save').click(function() {
    jQuery('#frmDatabaseCauses_Edit').trigger('submit')
    return false
  })

  jQuery('#btnDatabaseCauses_Cancel').click(function() {
    jQuery('#divDatabaseCauses_Edit').hide()
    jQuery('#btnDatabaseCauses_Add').show()
    return false
  })

  jQuery('#fldDatabaseCauses_CauseActiveCheckbox').change(function() {
    var v = 0
    if (jQuery(this).is(':checked')) {
      v = 1
    }
    jQuery('#fldDatabaseCauses_CauseActive').val(v)
  })

  jQuery('#frmDatabaseCauses_Edit').on('submit', function() {
    if (jQuery.trim(jQuery('#fldDatabaseCauses_CauseName').val()) === '') {
      return highligthCauseError()
    }

    if (jQuery.trim(jQuery('#fldDatabaseCauses_CauseDesc').val()) === '') {
      return highligthCauseError()
    }

    jQuery('body').trigger('cmdMainWaitingShow')
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseCausesUpdate',
        RegionId: jQuery('#desinventarRegionId').val(),
        Cause: jQuery('#frmDatabaseCauses_Edit').serializeObject()
      },
      function(data) {
        jQuery('body').trigger('cmdMainWaitingHide')
        if (parseInt(data.Status) > 0) {
          jQuery('#divDatabaseCauses_Edit').hide()
          jQuery('#btnDatabaseCauses_Add').show()
          jQuery('#msgDatabaseCauses_UpdateOk').show()
          doDatabaseCausesPopulateList(
            'tbodyDatabaseCauses_CauseListCustom',
            data.CauseListCustom
          )
          doDatabaseCausesPopulateList(
            'tbodyDatabaseCauses_CauseListDefault',
            data.CauseListDefault
          )
        } else {
          displayError(data.Status)
        }
        setTimeout(function() {
          jQuery('div.DatabaseCauses span.status').hide()
        }, 2500)
      },
      'json'
    )
    return false
  })
}

function displayError(status) {
  if (status == -15) {
    jQuery('#msgDatabaseCauses_ErrorCannotDelete').show()
    return
  }
  jQuery('#msgDatabaseCauses_UpdateError').show()
}

function highligthCauseError() {
  jQuery('#fldDatabaseCauses_CauseName').highlight()
  jQuery('#msgDatabaseCauses_ErrorEmtpyFields').show()
  setTimeout(function() {
    jQuery('#fldDatabaseCauses_CauseName').unhighlight()
    jQuery('div.DatabaseCauses span.status').hide()
  }, 2500)
  return false
}

function doCausesFormSetup() {
  if (parseInt(jQuery('#fldDatabaseCauses_CausePredefined').val()) > 0) {
    jQuery('#divDatabaseCauses_Edit span.Custom').hide()
    jQuery('#divDatabaseCauses_Edit span.Predefined').show()
    jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', true)
    jQuery('#fldDatabaseCauses_CauseDesc').addClass('disabled')
  } else {
    jQuery('#divDatabaseCauses_Edit span.Custom').show()
    jQuery('#divDatabaseCauses_Edit span.Predefined').hide()
    jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', false)
    jQuery('#fldDatabaseCauses_CauseDesc').removeClass('disabled')
  }
}

function doDatabaseCausesPopulateLists() {
  jQuery('body').trigger('cmdMainWaitingShow')
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseCausesGetList',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        doDatabaseCausesPopulateList(
          'tbodyDatabaseCauses_CauseListCustom',
          data.CauseListCustom
        )
        doDatabaseCausesPopulateList(
          'tbodyDatabaseCauses_CauseListDefault',
          data.CauseListDefault
        )
      }
      jQuery('body').trigger('cmdMainWaitingHide')
    },
    'json'
  )
}

function doDatabaseCausesPopulateList(tbodyId, CauseList) {
  jQuery('#' + tbodyId)
    .find('tr:gt(0)')
    .remove()
  jQuery('#' + tbodyId)
    .find('tr')
    .removeClass('under')
  jQuery.each(CauseList, function(index, value) {
    var clonedRow = jQuery('#tbodyDatabaseCauses_CauseListCustom tr:last')
      .clone()
      .show()
    jQuery('.CauseId', clonedRow).html(index)
    jQuery('.CausePredefined', clonedRow).html(value.CausePredefined)
    jQuery('.CauseName', clonedRow).html(value.CauseName)
    jQuery('.CauseDesc', clonedRow).html(
      value.CauseDesc ? value.CauseDesc.substring(0, 150) : ''
    )
    jQuery('.CauseDesc', clonedRow).prop('title', value.CauseDesc)
    jQuery('.CauseActive :input', clonedRow).prop(
      'checked',
      value.CauseActive > 0
    )
    jQuery('#' + tbodyId).append(clonedRow)
  })
  jQuery('#' + tbodyId + ' .CauseId').hide()
  jQuery('#' + tbodyId + ' .CausePredefined').hide()
  jQuery('#' + tbodyId + ' tr:odd').addClass('under')
}

export default {
  init
}
