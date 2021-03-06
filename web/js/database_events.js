function init() {
  //Attach main events
  jQuery('body').on('cmdDatabaseEventsShow', function() {
    doDatabaseEventsPopulateLists()
  })

  jQuery('div.DatabaseEvents span.status').hide()

  jQuery(
    '#tbodyDatabaseEvents_EventListCustom,#tbodyDatabaseEvents_EventListDefault'
  )
    .on('click', 'tr', function() {
      jQuery('#fldDatabaseEvents_EventId').val(jQuery('.EventId', this).text())
      jQuery('#fldDatabaseEvents_EventName').val(
        jQuery('.EventName', this).text()
      )
      jQuery('#fldDatabaseEvents_EventDesc').val(
        jQuery('.EventDesc', this).prop('title')
      )
      jQuery('#fldDatabaseEvents_EventActiveCheckbox')
        .prop('checked', jQuery('.EventActive :input', this).is(':checked'))
        .change()
      jQuery('#fldDatabaseEvents_EventPredefined').val(
        jQuery('.EventPredefined', this).text()
      )

      jQuery('#btnDatabaseEvents_Add').hide()
      doEventsFormSetup()
      jQuery('#divDatabaseEvents_Edit').show()
    })
    .on('mouseover', 'tr', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr', function() {
      jQuery(this).removeClass('highlight')
    })

  jQuery('#btnDatabaseEvents_Add').click(function() {
    jQuery('#divDatabaseEvents_Edit').show()
    jQuery(this).hide()
    jQuery('#fldDatabaseEvents_EventId').val('')
    jQuery('#fldDatabaseEvents_EventName').val('')
    jQuery('#fldDatabaseEvents_EventDesc').val('')
    jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false)
    jQuery('#fldDatabaseEvents_EventActiveCheckbox')
      .prop('checked', true)
      .change()
    jQuery('#fldDatabaseEvents_EventPredefined').val(0)
    doEventsFormSetup()
    return false
  })

  jQuery('#btnDatabaseEvents_Save').click(function() {
    jQuery('#frmDatabaseEvents_Edit').trigger('submit')
    return false
  })

  jQuery('#btnDatabaseEvents_Cancel').click(function() {
    jQuery('#divDatabaseEvents_Edit').hide()
    jQuery('#btnDatabaseEvents_Add').show()
    return false
  })

  jQuery('#fldDatabaseEvents_EventActiveCheckbox').change(function() {
    var v = 0
    if (jQuery(this).is(':checked')) {
      v = 1
    }
    jQuery('#fldDatabaseEvents_EventActive').val(v)
  })

  jQuery('#frmDatabaseEvents_Edit').on('submit', function() {
    if (jQuery.trim(jQuery('#fldDatabaseEvents_EventName').val()) === '') {
      jQuery('#fldDatabaseEvents_EventName').highlight()
      jQuery('#msgDatabaseEvents_ErrorEmtpyFields').show()
      setTimeout(function() {
        jQuery('#fldDatabaseEvents_EventName').unhighlight()
        jQuery('div.DatabaseEvents span.status').hide()
      }, 2500)
      return false
    }

    if (jQuery.trim(jQuery('#fldDatabaseEvents_EventDesc').val()) === '') {
      jQuery('#fldDatabaseEvents_EventDesc').highlight()
      jQuery('#msgDatabaseEvents_ErrorEmtpyFields').show()
      setTimeout(function() {
        jQuery('#fldDatabaseEvents_EventDesc').unhighlight()
        jQuery('div.DatabaseEvents span.status').hide()
      }, 2500)
      return false
    }

    jQuery('body').trigger('cmdMainWaitingShow')
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseEventsUpdate',
        RegionId: jQuery('#desinventarRegionId').val(),
        Event: jQuery('#frmDatabaseEvents_Edit').serializeObject()
      },
      function(data) {
        jQuery('body').trigger('cmdMainWaitingHide')
        if (parseInt(data.Status) > 0) {
          jQuery('#divDatabaseEvents_Edit').hide()
          jQuery('#btnDatabaseEvents_Add').show()
          jQuery('#msgDatabaseEvents_UpdateOk').show()
          doDatabaseEventsPopulateList(
            'tbodyDatabaseEvents_EventListCustom',
            data.EventListCustom
          )
          doDatabaseEventsPopulateList(
            'tbodyDatabaseEvents_EventListDefault',
            data.EventListDefault
          )
        } else {
          displayError(data.Status)
        }
        setTimeout(function() {
          jQuery('div.DatabaseEvents span.status').hide()
        }, 2500)
      },
      'json'
    )
    return false
  })
}

function displayError(status) {
  if (status === -15) {
    jQuery('#msgDatabaseEvents_ErrorCannotDelete').show()
    return
  }
  jQuery('#msgDatabaseEvents_UpdateError').show()
}

function doEventsFormSetup() {
  if (parseInt(jQuery('#fldDatabaseEvents_EventPredefined').val()) > 0) {
    jQuery('#divDatabaseEvents_Edit span.Custom').hide()
    jQuery('#divDatabaseEvents_Edit span.Predefined').show()
    jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', true)
    jQuery('#fldDatabaseEvents_EventDesc').addClass('disabled')
  } else {
    jQuery('#divDatabaseEvents_Edit span.Custom').show()
    jQuery('#divDatabaseEvents_Edit span.Predefined').hide()
    jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false)
    jQuery('#fldDatabaseEvents_EventDesc').removeClass('disabled')
  }
}

function doDatabaseEventsPopulateLists() {
  jQuery('body').trigger('cmdMainWaitingShow')
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseEventsGetList',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        doDatabaseEventsPopulateList(
          'tbodyDatabaseEvents_EventListCustom',
          data.EventListCustom
        )
        doDatabaseEventsPopulateList(
          'tbodyDatabaseEvents_EventListDefault',
          data.EventListDefault
        )
      }
      jQuery('body').trigger('cmdMainWaitingHide')
    },
    'json'
  )
}

function doDatabaseEventsPopulateList(tbodyId, EventList) {
  jQuery('#' + tbodyId)
    .find('tr:gt(0)')
    .remove()
  jQuery('#' + tbodyId)
    .find('tr')
    .removeClass('under')
  jQuery.each(EventList, function(index, value) {
    var clonedRow = jQuery('#tbodyDatabaseEvents_EventListCustom tr:last')
      .clone()
      .show()
    jQuery('.EventId', clonedRow).html(index)
    jQuery('.EventPredefined', clonedRow).html(value.EventPredefined)
    jQuery('.EventName', clonedRow).html(value.EventName)
    jQuery('.EventDesc', clonedRow).html(
      value.EventDesc ? value.EventDesc.substring(0, 150) : ''
    )
    jQuery('.EventDesc', clonedRow).prop('title', value.EventDesc)
    jQuery('.EventActive :input', clonedRow).prop(
      'checked',
      value.EventActive > 0
    )
    jQuery('#' + tbodyId).append(clonedRow)
  })
  jQuery('#' + tbodyId + ' .EventId').hide()
  jQuery('#' + tbodyId + ' .EventPredefined').hide()
  jQuery('#' + tbodyId + ' tr:even').addClass('under')
}

export default {
  init
}
