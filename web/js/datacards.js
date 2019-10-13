/* global desinventar, Ext */
import navigation from './datacards_navigation'
import { showtip } from './common'

function init() {
  jQuery('#divDatacardWindow').hide()

  jQuery('div.Datacard').on('cmdInitialize', function() {
    doDatacardInitialize()
    return false
  })

  // Limit length of text area fields using a maxlength attribute...
  jQuery('#DICard textarea').keyup(function() {
    var maxlength = parseInt(jQuery(this).attr('maxlength'))
    if (!isNaN(maxlength)) {
      var value = jQuery(this).val()
      if (value.length > maxlength) {
        jQuery(this).val(value.substr(0, maxlength))
      }
    }
  })

  jQuery('#DICard')
    .unbind('submit')
    .submit(function() {
      jQuery('#RecordAuthor').val(jQuery('#desinventarUserId').val())
      showStatus('')
      var params = jQuery(this).serializeObject()
      jQuery
        .post(
          jQuery('#desinventarURL').val() + '/cards.php',
          jQuery.extend(params, {
            RegionId: jQuery('#desinventarRegionId').val()
          }),
          null,
          'json'
        )
        .then(function(data) {
          if (data.Status == 'OK') {
            jQuery('#DisasterId').val(data.DisasterId)
            jQuery('#RecordSerial').text(data.RecordSerial)
            jQuery('#RecordPublished').text(data.RecordPublished)
            jQuery('#RecordReady').text(data.RecordReady)
            switch (data.StatusCode) {
              case 'INSERTOK':
                showStatus('msgDatacardInsertOk')
                jQuery('#cardsRecordSource').val('')
                jQuery('#cardsRecordCount').val(data.RecordCount)
                jQuery('#cardsRecordNumber').val(data.RecordCount)
                jQuery('#divRecordStat').show()
                break
              case 'UPDATEOK':
                showStatus('msgDatacardUpdateOk')
                jQuery('#divRecordStat').show()
                break
            }
            navigation.setStatus('VIEW')
            toggleFormEdit('DICard', true)
            navigation.setViewMode()
            if (parseInt(jQuery('#cardsRecordNumber').val()) > 0) {
              jQuery('#RecordNumber').text(jQuery('#cardsRecordNumber').val())
              jQuery('#RecordCount').text(jQuery('#cardsRecordCount').val())
              jQuery('#divRecordNavigationInfo').show()
            }
          } else {
            switch (data.ErrorCode) {
              case -10:
              case -52:
                showStatus('msgDatacardNetworkError')
                break
              case -54:
                showStatus('msgDatacardDuplicatedSerial')
                break
              case -61:
                showStatus('msgDatacardWithoutEffects')
                break
              case -62:
                showStatus('msgDatacardOutsideOfPeriod')
                break
              default:
                showStatus('msgDatacard_ErrorSaving')
                break
            }
            navigation.setStatus('EDIT')
          }
          showtip('', '#ffffff')
        }, 'json')
      return false
    })

  // Enable/Disable related EffectSector fields based on value of other fields...
  jQuery('.clsEffectDouble').blur(function() {
    var altField = jQuery(this).attr('altField')
    var value = parseInt(jQuery(this).val())
    var field = jQuery('#DICard #' + altField)
    if (value > 0) {
      field.attr('oldValue', field.val())
      field.val(-1)
    } else {
      if (value == 0) {
        if (field.attr('oldValue') == '') {
          field.attr('oldValue', 0)
        }
        field.val(field.attr('oldValue'))
      }
    }
  })

  // Enable loading of geographic levels when editing...
  jQuery('#divDatacard .tblGeography').on(
    'change',
    '.GeoLevelSelect',
    function() {
      var GeographyLevel = parseInt(jQuery(this).data('GeographyLevel'))
      var NextGeographyLevel = GeographyLevel + 1
      var myGeographyId = jQuery(this).val()
      var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1

      // Clear values of following sublevels
      for (var i = NextGeographyLevel; i < GeoLevelCount; i++) {
        var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i)
        mySelect.empty()
        mySelect.append(jQuery('<option>', { value: '' }).text(''))
        mySelect.disable()
      }

      if (jQuery(this).val() == '') {
        var PrevGeographyLevel = GeographyLevel - 1
        if (PrevGeographyLevel >= 0) {
          myGeographyId = jQuery(
            '#divDatacard .tblGeography #GeoLevel' + PrevGeographyLevel
          ).val()
          jQuery('#divDatacard #GeographyId').val(myGeographyId)
        }
      } else {
        jQuery('#divDatacard #GeographyId').val(myGeographyId)
        if (NextGeographyLevel < GeoLevelCount) {
          updateGeoLevelSelect(jQuery(this).val())
        }
      }
      jQuery(this).focus()
    }
  )

  jQuery('#DisasterBeginTime1').blur(function() {
    if (
      jQuery(this).val() == '' ||
      parseInt(jQuery(this).val(), 10) < 1 ||
      parseInt(jQuery(this).val(), 10) > 12
    ) {
      jQuery(this).val('')
      jQuery('#DisasterBeginTime2').val('')
    }
  })

  jQuery('#DisasterBeginTime2').blur(function() {
    if (
      parseInt(jQuery(this).val(), 10) < 1 ||
      parseInt(jQuery(this).val(), 10) > 31
    ) {
      jQuery(this).val('')
    }
  })

  // Apply some validation for several types of input fields
  jQuery('div.Datacard').on('keydown', '.inputInteger', function(event) {
    return blockChars(
      event,
      jQuery(this).val(),
      'integer:' + jQuery(this).attr('MaxLength')
    )
  })

  jQuery('div.Datacard').on('blur', '.inputLatLon', function() {
    if (jQuery.trim(jQuery(this).val()) == '') {
      jQuery(this).val(0)
    }
  })

  jQuery('div.Datacard')
    .on('keydown', '.inputDouble', function(event) {
      return blockChars(
        event,
        jQuery(this).val(),
        'double:' + jQuery(this).attr('MaxLength')
      )
    })
    .on('blur', function() {
      if (jQuery.trim(jQuery(this).val()) == '') {
        jQuery(this).val(0)
      }
      return false
    })

  jQuery('div.Datacard').on('keydown', '.inputText', function(event) {
    return blockChars(event, jQuery(this).val(), 'text:')
  })

  jQuery('div.Datacard').on('keydown', '.inputAlphaNumber', function(event) {
    return blockChars(event, jQuery(this).val(), 'alphanumber:')
  })

  // Datacard New/Edit/Save Commands
  jQuery('#btnDatacardNew').click(function() {
    clear()
    create()
    jQuery('#txtDatacardFind').val('')
    jQuery('#GeographyId').val('')
    jQuery('#DisasterId').val('')
    return false
  })

  jQuery('#btnDatacardEdit').click(function() {
    jQuery('#txtDatacardFind').val('')
    doDatacardEdit()
    return false
  })

  jQuery('#btnDatacardSave').click(function() {
    doDatacardSave()
    return false
  })

  jQuery('#btnDatacardCancel').click(function() {
    doDatacardCancel()
    return false
  })

  jQuery('#btnDatacardPrint').click(function() {
    window.print()
    return false
  })

  // Datacard Navigation Functions
  jQuery('#btnDatacardGotoFirst').click(function() {
    jQuery('#divRecordStat').hide()
    jQuery('#txtDatacardFind').val('')
    doDatacardGotoFirst()
    return false
  })

  jQuery('#btnDatacardGotoLast').click(function() {
    jQuery('#divRecordStat').hide()
    jQuery('#txtDatacardFind').val('')
    doDatacardGotoLast()
    return false
  })

  jQuery('#btnDatacardGotoPrev').click(function() {
    jQuery('#divRecordStat').hide()
    jQuery('#txtDatacardFind').val('')
    doDatacardGotoPrev()
    return false
  })

  jQuery('#btnDatacardGotoNext').click(function() {
    jQuery('#divRecordStat').hide()
    jQuery('#txtDatacardFind').val('')
    doDatacardGotoNext()
    return false
  })

  // Datatacard Find
  jQuery('#txtDatacardFind').keydown(function(event) {
    if (event.keyCode == 13) {
      doDatacardFind()
    }
  })

  jQuery('#btnDatacardFind').click(function() {
    doDatacardFind()
    return false
  })

  // Switch between Basic and Additional Effects
  jQuery('#linkDatacardShowEffectsBasic').click(function() {
    jQuery('#divDatacardEffectsBasic').show()
    jQuery('#divDatacardEffectsAdditional').hide()
    return false
  })

  jQuery('#linkDatacardShowEffectsAditional').click(function() {
    jQuery('#divDatacardEffectsBasic').hide()
    jQuery('#divDatacardEffectsAdditional').show()
    return false
  })

  jQuery('#divDatacard .EventId').on('mouseenter', 'option', function() {
    showtip(jQuery(this).data('tooltip'), 'lightblue')
  })
  jQuery('#divDatacard .EventId').mouseleave(function() {
    showtip('', '#fff')
  })

  jQuery('#divDatacard .CauseId').on('mouseenter', 'option', function() {
    showtip(jQuery(this).data('tooltip'), '#ffffc0')
  })
  jQuery('#divDatacard .CauseId').mouseleave(function() {
    showtip('', '#fff')
  })

  jQuery('#DICard .show-help')
    .on('mouseenter', function() {
      showtip(jQuery(this).data('tooltip'), jQuery(this).data('tooltip-color'))
    })
    .on('mouseleave', function() {
      showtip('', '#ffffff')
    })

  // Dependency between fields
  jQuery('#DICard').on('blur', '#EffectRoads', function() {
    var v = jQuery.trim(jQuery(this).val())
    if (v != '' && parseFloat(v) > 0) {
      jQuery('#DICard #SectorTransport').val(-1)
    }
  })
  jQuery('#DICard').on('blur', '#EffectFarmingAndForest', function() {
    var v = jQuery.trim(jQuery(this).val())
    if (v != '' && parseFloat(v) > 0) {
      jQuery('#DICard #SectorAgricultural').val(-1)
    }
  })
  jQuery('#DICard').on('blur', '#EffectLiveStock', function() {
    var v = jQuery.trim(jQuery(this).val())
    if (v != '' && parseFloat(v) > 0) {
      jQuery('#DICard #SectorAgricultural').val(-1)
    }
  })
  jQuery('#DICard').on('blur', '#EffectEducationCenters', function() {
    var v = jQuery.trim(jQuery(this).val())
    if (v != '' && parseFloat(v) > 0) {
      jQuery('#DICard #SectorEducation').val(-1)
    }
  })
  jQuery('#DICard').on('blur', '#EffectMedicalCenters', function() {
    var v = jQuery.trim(jQuery(this).val())
    if (v != '' && parseFloat(v) > 0) {
      jQuery('#DICard #SectorHealth').val(-1)
    }
  })
  jQuery('div.Datacard .inputText').on('blur', function() {
    jQuery(this).val(
      jQuery(this)
        .val()
        .replace(/\n/, ' ')
    )
  })

  jQuery('#DICard').on('blur', '#EffectOtherLosses', function() {
    jQuery(this).val(
      jQuery(this)
        .val()
        .replace(/\n/, ' ')
    )
    if (jQuery.trim(jQuery(this).val()) != '') {
      jQuery('#DICard #SectorOther').val(-1)
    }
  })

  // Attach events to main body
  jQuery('body').on('cmdDatacardShow', function() {
    doDatacardShow()
  })

  jQuery('body').on('cmdDatacardGoto', function(
    event,
    prmDisasterId,
    prmRecordNumber,
    prmRecordCount
  ) {
    setDICardFromId(
      jQuery('#desinventarRegionId').val(),
      prmDisasterId,
      prmRecordNumber,
      prmRecordCount
    )
  })
  //Initialize components
  jQuery('#divDatacard .tblGeography tr:first').hide()
  jQuery('div.Datacard table.EffectList')
    .on('focus', 'select.value', function() {
      showtip(jQuery(this).data('helptext'), '#f1bd41')
    })
    .on('focus', 'input.value', function() {
      showtip(jQuery(this).data('helptext'), '#f1bd41')
    })

  jQuery('#btnDatacardClone').on('click', function(e) {
    e.preventDefault()
    if (navigation.getStatus() !== 'VIEW') {
      return false
    }
    clone()
  })

  // Validation of DisasterBeginTime and Suggest Serial for New Datacards
  jQuery('#DisasterBeginTime0').on('blur', function() {
    var cmd = jQuery('#DatacardCommand').val()
    if (
      cmd === 'insertDICard' &&
      jQuery(this).val() !== '' &&
      jQuery('#DisasterSerial').val() === ''
    ) {
      nextSerial(jQuery('#desinventarRegionId').val())
    }
    return false
  })

  jQuery('div.Datacard #linkDatacardSuggestSerial').on('click', function() {
    if (navigation.getStatus() === 'NEW') {
      // Suggest new serial
      return nextSerial(jQuery('#desinventarRegionId').val())
    }
    if (navigation.getStatus() === 'EDIT') {
      // Restore initial value when editing...
      jQuery('#DisasterSerial').val(jQuery('#PrevDisasterSerial').val())
    }
    return false
  })
}

function doDatacardInitialize() {
  // Load EffectPeople List (ef1)
  jQuery('div.Datacard table.EffectListPeople select.value')
    .jec({
      maxLength: 15
    })
    .blur(function() {
      var value = parseInt(jQuery(this).val())
      var jecValue = parseInt(jQuery(this).jecValue())
      if (value > 0) {
        if (isNaN(jecValue) || jecValue < 0) {
          jecValue = 0
        }
        jQuery(this).jecValue(jecValue)
        jQuery(this).val(jecValue)
        if (jQuery(this).val() == '') {
          jQuery(this).val(0)
        }
      }
    })

  // EffectLosses2 List (ef3)
  jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(
    function() {
      var fieldname = jQuery(this).data('field')
      var field = jQuery('div.Datacard table.EffectListLosses2 tr.' + fieldname)
      jQuery('span.label', field).text(jQuery('span.label', this).text())
      jQuery('input.value', field).data(
        'helptext',
        jQuery('span.helptext', this).text()
      )
    }
  )

  // EffectOther List (ef4)
  jQuery('div.desinventarInfo div.EffectList div.EffectOther').each(function() {
    var fieldname = jQuery(this).data('field')
    var field = jQuery('div.Datacard table.EffectListOther tr.' + fieldname)
    jQuery('span.label', field).text(jQuery('span.label', this).text())
    jQuery('span.label', field).attr(
      'title',
      jQuery('span.tooltip', this).text()
    )
    jQuery('input.value', field).data(
      'helptext',
      jQuery('span.helptext', this).text()
    )
  })

  // Additional Effect List (EEFieldList);
  var effect_list = jQuery('div.Datacard table.EffectListAdditional')
  effect_list.find('div.EffectAdditional:gt(0)').remove()
  var fieldCount = 0
  var max_column = 3
  jQuery.each(jQuery('body').data('EEFieldList'), function(key, field) {
    var id = field['id']
    var label = field['name']
    var tooltip = field['description']
    var type = field['type']

    var clonedInput = jQuery('div.EffectAdditional:last', effect_list)
      .clone()
      .show()
    jQuery('span.label', clonedInput).text(label)
    jQuery('span.label', clonedInput).attr('title', tooltip)
    jQuery('input.value', clonedInput).hide()
    var className = ''
    var value
    switch (type) {
      case 'INTEGER':
        className = 'inputInteger'
        value = 0
        break
      case 'CURRENCY':
      case 'DOUBLE':
        className = 'inputDouble'
        value = 0
        break
      default:
        className = 'inputText'
        value = ''
        break
    }
    jQuery('input', clonedInput)
      .attr('class', 'value line')
      .addClass(className)
      .val(value)
      .attr('id', id)
      .attr('name', id)
      .attr('tabindex', 1000 + fieldCount)
      .data('helptext', tooltip)
      .show()
    const column = fieldCount % max_column
    jQuery('tr:last td:eq(' + column + ')', effect_list).append(clonedInput)
    fieldCount++
  })
}

function updateGeoLevelSelect(prmGeographyId) {
  var GeographyList = jQuery('body').data('GeographyList-' + prmGeographyId)

  if (GeographyList === undefined) {
    // Load GeographyList using POST
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdGeographyGetItemsById',
        RegionId: jQuery('#desinventarRegionId').val(),
        GeographyId: prmGeographyId
      },
      function(data) {
        if (parseInt(data.Status) > 0) {
          jQuery.each(data.GeographyList, function(key, value) {
            // Store result for later use from cache
            var NextGeographyLevel = parseInt(key.length) / 5
            jQuery('body').data('GeographyList-' + key, value)
            doUpdateGeoLevelSelect(NextGeographyLevel, value)
          })
        }
      },
      'json'
    )
  } else {
    // Enable sublevels and reuse data from local cache
    var GeoLevelCount = prmGeographyId.length / 5 + 1
    for (
      var GeographyLevel = 1;
      GeographyLevel < GeoLevelCount;
      GeographyLevel++
    ) {
      var GeographyParent = prmGeographyId.substr(0, GeographyLevel * 5)
      var myGeographyList = jQuery('body').data(
        'GeographyList-' + GeographyParent
      )
      doUpdateGeoLevelSelect(GeographyLevel, myGeographyList)
    }
  }
}

function doUpdateGeoLevelSelect(prmGeographyLevel, prmGeographyList) {
  var mySelect = jQuery(
    '#divDatacard .tblGeography #GeoLevel' + prmGeographyLevel
  )
  var myPrevValue = mySelect.val()
  mySelect.empty()
  mySelect.append(jQuery('<option>', { value: '' }).text(''))
  jQuery.each(prmGeographyList, function(index, value) {
    mySelect.append(
      jQuery('<option>', { value: value.GeographyId }).text(value.GeographyName)
    )
  })
  if (myPrevValue != '') {
    mySelect.val(myPrevValue)
  }
  mySelect.enable()
}

function doDatacardShow() {
  //GeoLevel
  jQuery('#divDatacard .tblGeography tr:gt(0)').remove()
  jQuery('#divDatacard .tblGeography tr:first').hide()
  var GeolevelsList = jQuery('body').data('GeolevelsList')
  if (GeolevelsList == undefined) {
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseLoadData',
        RegionId: jQuery('#desinventarRegionId').val()
      },
      function(data) {
        jQuery('body').data('GeolevelsList', data.GeolevelsList)
        jQuery('body').data('EventList', data.EventList)
        jQuery('body').data('CauseList', data.CauseList)
        jQuery('body').data('RecordCount', data.RecordCount)
        var dataItems = jQuery('body').data()
        jQuery.each(dataItems, function(index) {
          if (index.substr(0, 13) === 'GeographyList') {
            jQuery('body').removeData(index)
          }
        })
        jQuery('body').data('GeographyList', data.GeographyList)
        doDatacardUpdateDisplay()
      },
      'json'
    )
  } else {
    doDatacardUpdateDisplay()
  }
  var UserRoleValue = jQuery('#desinventarUserRoleValue').val()
  if (UserRoleValue <= 2) {
    jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').disable()
    jQuery('#DICard select.RecordStatus option[value="DELETED"]').disable()
  } else {
    jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').enable()
    jQuery('#DICard select.RecordStatus option[value="DELETED"]').enable()
  }
}

function doDatacardUpdateDisplay() {
  var GeolevelsList = jQuery('body').data('GeolevelsList')
  if (GeolevelsList != undefined) {
    jQuery.each(GeolevelsList, function(index, value) {
      var clonedRow = jQuery('#divDatacard .tblGeography tr:last')
        .clone()
        .show()
      jQuery('.GeoLevelId', clonedRow).text(index)
      jQuery('.GeoLevelName', clonedRow).text(value.GeoLevelName)
      jQuery('select', clonedRow)
        .attr('id', 'GeoLevel' + index)
        .attr('level', index)
        .data('GeographyLevel', index)
      jQuery('.tblGeography').append(clonedRow)
    })
  }

  var FirstRow = jQuery(
    '#divDatacard .tblGeography select:data("GeographyLevel=0")'
  )
  FirstRow.empty()
  FirstRow.append(jQuery('<option>', { value: '' }).text(''))
  var GeographyList = jQuery('body').data('GeographyList')
  if (GeographyList != undefined) {
    jQuery.each(GeographyList, function(index, value) {
      FirstRow.append(
        jQuery('<option>', { value: index }).text(value.GeographyName)
      )
    })
  }

  jQuery('#divDatacard .EventId').empty()
  jQuery('#divDatacard .EventId').append(
    jQuery('<option>', { value: '' }).text('')
  )
  var EventList = jQuery('body').data('EventList')
  if (EventList != undefined) {
    jQuery.each(EventList, function(index, value) {
      jQuery('#divDatacard .EventId').append(
        jQuery('<option>', { value: index })
          .text(value.EventName)
          .data('tooltip', value.EventDesc)
      )
    })
  }

  jQuery('#divDatacard .CauseId').empty()
  jQuery('#divDatacard .CauseId').append(
    jQuery('<option>', { value: '' }).text('')
  )
  var CauseList = jQuery('body').data('CauseList')
  if (CauseList != undefined) {
    jQuery.each(CauseList, function(index, value) {
      jQuery('#divDatacard .CauseId').append(
        jQuery('<option>', { value: index })
          .text(value.CauseName)
          .data('tooltip', value.CauseDesc)
      )
    })
  }

  jQuery('#divDatacard #cardsRecordNumber').val(0)
  jQuery('#divDatacard #cardsRecordCount').val(
    jQuery('body').data('RecordCount')
  )

  // Initialize controls in form when it is displayed
  // Reset buttons
  clear()
  // Hide StatusMessages
  showStatus('')
  jQuery('#divDatacardStatusMsg').show()
  // Hide window's parameters
  jQuery('#divDatacardParameter').hide()
  jQuery('#divRecordNavigationInfo').hide()

  toggleFormEdit('DICard', true)
  navigation.setViewMode()

  // Start with Basic Effects show
  jQuery('#linkDatacardShowEffectsBasic').trigger('click')

  //Show Command Buttons only for Role>=USER
  jQuery('.DatacardCmdButton').hide()
  jQuery('#btnDatacardPrint').show()
  if (parseInt(jQuery('#desinventarUserRoleValue').val()) >= 2) {
    jQuery('.DatacardCmdButton').show()
    showStatus('msgDatacardStartNew')
  }
  navigation.enable()

  var w = Ext.getCmp('wndDatacard')
  if (w != undefined) {
    w.show()
  }
}

function requestDatacard(myCmd, myValue) {
  var bReturn = true
  var RegionId = jQuery('#desinventarRegionId').val()
  jQuery('#dostat').html(
    '<img src="' +
      jQuery('#desinventarURL').val() +
      '/images/loading.gif' +
      '" alt="" />'
  )
  jQuery.post(
    jQuery('#desinventarURL').val() + '/cards.php',
    {
      cmd: myCmd,
      value: myValue,
      r: RegionId
    },
    function(data) {
      jQuery('#dostat').html('')
      if (data.Status == 'OK') {
        showStatus('')
        if (data.DisasterId != '') {
          jQuery('#cardsRecordSource').val('')
          setDICardFromId(
            RegionId,
            data.DisasterId,
            data.RecordNumber,
            data.RecordCount
          )
          navigation.updateByUserRole()
          if (myCmd == 'getDisasterIdFromSerial') {
            showStatus('msgDatacardFound')
          }
        } else {
          showStatus('msgDatacardNotFound')
          bReturn = false
        }
      } else {
        bReturn = false
      }
    },
    'json'
  )
  jQuery('#dostat').html('')
  return bReturn
}

function doDatacardFind() {
  // We can only search datacards when in VIEW mode
  var status = navigation.getStatus()
  if (!(status === '' || status === 'VIEW')) {
    return false
  }
  if (jQuery('#txtDatacardFind').val() != '') {
    requestDatacard('getDisasterIdFromSerial', jQuery('#txtDatacardFind').val())
  }
}

function doDatacardEdit() {
  showStatus('')
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatacardLock',
      RegionId: jQuery('#desinventarRegionId').val(),
      DisasterId: jQuery('#DisasterId').val()
    },
    function(data) {
      if (data.DatacardStatus == 'RESERVED') {
        toggleFormEdit('DICard', false)
        jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val())
        jQuery('#DisasterBeginTime0').focus()
        jQuery('#DatacardCommand').val('updateDICard')
        showStatus('msgDatacardFill')
        navigation.setEditMode()

        // Clear values of following sublevels
        var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1
        for (var i = 1; i < GeoLevelCount; i++) {
          var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i)
          mySelect.disable()
        }
        updateGeoLevelSelect(jQuery('#DICard #GeographyId').val())
        navigation.setStatus('EDIT')
      } else {
        showStatus('msgDatacardIsLocked')
      }
    },
    'json'
  )
}

function doDatacardSave() {
  var bContinue = 1
  var cmd = jQuery('#DatacardCommand').val()
  var DisasterSerial = jQuery('#DisasterSerial').val()
  var PrevDisasterSerial = jQuery('#PrevDisasterSerial').val()
  var Status = navigation.getStatus()
  if (bContinue > 0) {
    var error_count = 0
    var answer = 1
    // Bug #136 : Add validation for Numeric Effect fields
    jQuery('#DICard .clsEffectNumeric').each(function() {
      answer = validateInputDouble(jQuery(this).val())
      if (answer > 0) {
        if (jQuery(this).attr('old-bg-color') != '') {
          jQuery(this).unhighlight()
        }
      } else {
        jQuery(this).highlight()
        error_count++
      }
    })
    if (error_count > 0) {
      bContinue = 0
      showStatus('msgDatacardInvalidIntegerNumber')
    }
    if (error_count < 1) {
      jQuery('#DICard .inputDouble').each(function() {
        answer = validateInputDouble(jQuery(this).val())
        if (answer > 0) {
          if (jQuery(this).attr('old-bg-color') != '') {
            jQuery(this).unhighlight()
          }
        } else {
          jQuery(this).highlight()
          error_count++
        }
      })
      jQuery('div.Datacard .inputLatLon').each(function() {
        answer = validateInputDouble(jQuery(this).val())
        if (answer > 0) {
          if (jQuery(this).attr('old-bg-color') != '') {
            jQuery(this).unhighlight()
          }
        } else {
          jQuery(this).highlight()
          error_count++
        }
      })
      if (error_count > 0) {
        bContinue = 0
        showStatus('msgDatacardInvalidFloatNumber')
      }
    }
  }

  if (bContinue > 0) {
    // Validate Record Status
    if (jQuery('#DICard #RecordStatus').val() == '') {
      showStatus('msgDatacardWithoutStatus')
      jQuery('#DICard #RecordStatus')
        .highlight()
        .focus()
      bContinue = 0
    }
  }

  if (bContinue > 0) {
    if (jQuery('#DICard #RecordStatus').val() == 'PUBLISHED') {
      jQuery('#DICard #DisasterSource').unhighlight()
      jQuery('#DICard #RecordStatus').unhighlight()
      var DisasterSource = jQuery('#DICard #DisasterSource').val()
      DisasterSource = jQuery.trim(DisasterSource)
      if (DisasterSource == '') {
        showStatus('msgDatacardWithoutSource')
        jQuery('#DICard #DisasterSource')
          .highlight()
          .focus()
        jQuery('#DICard #RecordStatus').highlight()
        bContinue = 0
      }
    }
  }

  if (bContinue > 0) {
    // Validate Record Status
    if (
      jQuery('#DICard #RecordStatus').val() == 'PUBLISHED' ||
      jQuery('#DICard #RecordStatus').val() == 'DELETED'
    ) {
      if (jQuery('#desinventarUserRoleValue').val() <= 2) {
        showStatus('msgDatacardInvalidStatus')
        jQuery('#DICard #RecordStatus')
          .highlight()
          .focus()
        bContinue = 0
      }
    }
  }

  if (bContinue > 0 && jQuery('#GeographyId').val() == '') {
    showStatus('msgDatacardInvalidGeography')
    jQuery('.GeoLevelSelect').highlight()
    jQuery('#GeoLevel0').focus()
    bContinue = 0
  }

  jQuery('#DICard #EventId').unhighlight()
  if (bContinue > 0 && jQuery('#DICard #EventId').val() == '') {
    jQuery('#DICard #EventId')
      .highlight()
      .focus()
    bContinue = 0
  }

  jQuery('#DICard #CauseId').unhighlight()
  if (bContinue > 0 && jQuery('#DICard #CauseId').val() == '') {
    jQuery('#DICard #CauseId')
      .highlight()
      .focus()
    bContinue = 0
  }

  // Use AJAX to save datacard
  if (bContinue > 0) {
    if (navigation.getStatus() == 'SAVING') {
      // Do Nothing.. already saving datacard...
    } else {
      navigation.setStatus('SAVING')
      jQuery.post(
        jQuery('#desinventarURL').val() + '/cards.php',
        {
          cmd: 'existDisasterSerial',
          RegionId: jQuery('#desinventarRegionId').val(),
          DisasterSerial: DisasterSerial
        },
        function(data) {
          bContinue = 1
          if (cmd == 'insertDICard' && data.DisasterSerial != '') {
            // Serial of new datacard already exists...
            bContinue = 0
          }
          if (cmd == 'updateDICard') {
            if (
              DisasterSerial != PrevDisasterSerial &&
              data.DisasterSerial != ''
            ) {
              // Edited Serial exists in database...
              bContinue = 0
            }
          }
          if (bContinue < 1) {
            showStatus('msgDatacardDuplicatedSerial')
            navigation.setStatus(Status)
            jQuery('#DICard #DisasterSerial')
              .highlight()
              .focus()
          }
          if (bContinue > 0) {
            //'DisasterSource',
            var fl = new Array(
              'DisasterSerial',
              'DisasterBeginTime0',
              'GeoLevel0',
              'EventId',
              'CauseId'
            )
            if (checkForm('DICard', fl)) {
              jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val())
              jQuery('#DICard').submit()
            } else {
              showStatus('msgDatacardFieldsError')
            }
          }
        },
        'json'
      )
    }
  }
}

function checkForm(prmForm, prmFieldList) {
  var bReturn = true
  jQuery.each(prmFieldList, function(index, value) {
    var selector = '#' + prmForm + ' #' + value
    if (jQuery(selector).val().length < 1) {
      jQuery(selector).highlight()
      bReturn = false
    }
  })
  return bReturn
}

// Block characters according to type
function blockChars(e, value, type) {
  var key = window.event ? e.keyCode : e.which

  // Accept values in numeric keypad
  if (key >= 96 && key <= 105) {
    key = key - 48
  }
  var keychar = String.fromCharCode(key)
  if (key == 190 || key == 110 || key == 188) {
    keychar = '.'
  }
  var opt = type.split(':') // 0=type; 1=minlength; 2=minval-maxval
  // Accept keys: backspace, tab, shift, ctrl, insert, delete
  //        pagedown, pageup, rows, hyphen
  var spckey =
    key == 8 ||
    key == 9 ||
    key == 17 ||
    key == 20 ||
    key == 189 ||
    key == 45 ||
    key == 46 ||
    (key >= 33 && key <= 40) ||
    key == 0
  var chk = true
  var val = true // validate characters
  // Check max length
  var len = true
  if (value.length >= parseInt(opt[1])) {
    len = false
  }
  var reg
  // Check datatype
  switch (opt[0]) {
    case 'date':
      reg = /^\d{4}-\d{0,2}-\d{0,2}$/
      chk = reg.test(keychar)
      break
    case 'alphanumber':
      reg = /^[a-z]|[A-Z]|[0-9]|[-_+.]+/
      chk = reg.test(keychar)
      break
    case 'integer':
      reg = /\d/
      chk = reg.test(keychar)
      break
    case 'double':
      reg = /^[-+]?[0-9]|[.]+$/
      chk = reg.test(keychar)
      break
    default:
  }
  // Block special characters: (like !@#$%^&'*" etc)
  val = !(key == 92 || key == 13 || key == 16)
  return val && ((chk && len) || spckey)
}

function doDatacardCancel() {
  if (navigation.getStatus() == 'EDIT') {
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatacardRelease',
        RegionId: jQuery('#desinventarRegionId').val(),
        DisasterId: jQuery('#DisasterId').val()
      },
      function() {
        toggleFormEdit('DICard', true)
        navigation.setViewMode()
        // clear Help text area
        showtip('', '#ffffff')

        setDICardFromId(
          jQuery('#desinventarRegionId').val(),
          jQuery('#DisasterId').val(),
          jQuery('#cardsRecordNumber').val(),
          jQuery('#cardsRecordCount').val()
        )
        navigation.setStatus('VIEW')
        navigation.updateByUserRole()
        showStatus('')
        navigation.enable()
      },
      'json'
    )
  } else {
    var form = document.getElementById('DICard')
    form.reset()
    toggleFormEdit('DICard', true)
    navigation.setViewMode()
    // clear Help text area
    showtip('', '#ffffff')
    showStatus('msgDatacardStartNew')
    navigation.enable()
    navigation.setStatus('')
  }
  if (jQuery('div.Datacard #DisasterId').val() == '') {
    jQuery('div.Datacard select.clsEffectSector').each(function() {
      jQuery(this).val(0)
    })
  }
}

function doDatacardGotoFirst() {
  showStatus('')
  requestDatacard('getDisasterIdFirst', jQuery('#DisasterId').val())
  navigation.updateByUserRole()
}

function doDatacardGotoLast() {
  showStatus('')
  if (jQuery('#cardsRecordSource').val() == 'data') {
    var RecordCount = parseInt(jQuery('#cardsRecordCount').val())
    var DisasterId = jQuery(
      '.linkGridGotoCard[rowindex=' + RecordCount + ']'
    ).attr('DisasterId')
    setDICardFromId(
      jQuery('#desinventarRegionId').val(),
      DisasterId,
      RecordCount,
      RecordCount
    )
  } else {
    requestDatacard('getDisasterIdLast', jQuery('#DisasterId').val())
  }
  navigation.updateByUserRole()
}

function doDatacardGotoPrev() {
  showStatus('')
  if (jQuery('#cardsRecordSource').val() == 'data') {
    var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val())
    if (RecordNumber > 1) {
      RecordNumber--
      var DisasterId = jQuery(
        '.linkGridGotoCard[rowindex=' + RecordNumber + ']'
      ).attr('DisasterId')
      setDICardFromId(
        jQuery('#desinventarRegionId').val(),
        DisasterId,
        RecordNumber,
        jQuery('#cardsRecordCount').val()
      )
    }
  } else {
    var bFound = requestDatacard(
      'getDisasterIdPrev',
      jQuery('#cardsRecordNumber').val()
    )
    if (bFound == false) {
      showStatus('msgDatacardNotFound')
    }
  }
  navigation.updateByUserRole()
}

function doDatacardGotoNext() {
  showStatus('')
  if (jQuery('#cardsRecordSource').val() == 'data') {
    var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val())
    if (RecordNumber < parseInt(jQuery('#cardsRecordCount').val())) {
      RecordNumber = RecordNumber + 1
      var DisasterId = jQuery(
        '.linkGridGotoCard[rowindex=' + RecordNumber + ']'
      ).attr('DisasterId')
      setDICardFromId(
        jQuery('#desinventarRegionId').val(),
        DisasterId,
        RecordNumber,
        jQuery('#cardsRecordCount').val()
      )
    }
  } else {
    var bFound = requestDatacard(
      'getDisasterIdNext',
      jQuery('#cardsRecordNumber').val()
    )
    if (bFound == false) {
      showStatus('msgDatacardNotFound')
    }
  }
  navigation.updateByUserRole()
}

// SET DATACARD FORM
function setElementValue(formElement, value) {
  switch (formElement.type) {
    case 'undefined':
      return
    case 'radio':
      formElement.checked = value
      break
    case 'checkbox':
      formElement.checked = value
      break
    case 'select-one':
      var unk = true
      for (var w = 0; w < formElement.length; w++) {
        if (formElement.options[w].value == value) {
          formElement.selectedIndex = w
          unk = false
        }
      }
      if (unk) formElement[3] = new Option(value, value, false, true)
      break
    case 'select-multiple':
      for (var x = 0; x < formElement.length; x++)
        formElement[x].selected = value[x]
      break
    default:
      formElement.value = value
      break
  }
}

function setDICardFromId(
  prmRegionId,
  prmDisasterId,
  prmRecordNumber,
  prmRecordCount
) {
  jQuery('#cardsRecordNumber').val(prmRecordNumber)
  jQuery('#cardsRecordCount').val(prmRecordCount)
  read(prmRegionId, prmDisasterId).then(function(data) {
    jQuery('#DICard .clsEffectNumeric').each(function() {
      jQuery(this).jecValue(data[jQuery(this).attr('id')], true)
    })
    setDICard(data)
    jQuery('#divRecordNavigationInfo').hide()
    var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val())
    var RecordCount = parseInt(jQuery('#cardsRecordCount').val())
    if (RecordNumber > 0) {
      jQuery('#divRecordNavigationInfo').show()
      jQuery('#RecordNumber').text(RecordNumber)
      jQuery('#RecordCount').text(RecordCount)
    }
    navigation.setStatus('VIEW')
    navigation.enable()
    return true
  })
  return false
}

function setDICard(arr) {
  var diform = null
  var myForm = null
  var varName
  diform = document.getElementById('DICard')
  myForm = jQuery('div.Datacard')

  var objElems = diform.elements // DICard is DesInventar form..
  for (var i = 0; i < objElems.length; i++) {
    if (
      objElems[i].id !== 'GeoLevel0' &&
      objElems[i].id !== 'GeoLevel1' &&
      objElems[i].id !== 'GeoLevel2'
    ) {
      if (objElems[i].id !== '') {
        varName = jQuery(myForm)
          .find('#' + objElems[i].id)
          .attr('name')
        setElementValue(objElems[i], arr[varName])
      }
    }
  }

  jQuery('#PrevDisasterSerial', myForm).val(
    jQuery('#DisasterSerial', myForm).val()
  )

  //Set GeographyItem info into hidden fields
  jQuery('#divDatacard .tblGeography select:gt(1)')
    .empty()
    .disable()
  jQuery(arr['GeographyItems']).each(function(key, value) {
    var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + key)
    if (parseInt(key) == 0) {
      mySelect.val(value.GeographyId)
    } else {
      mySelect.append(
        jQuery('<option>', { value: value.GeographyId }).text(
          value.GeographyName
        )
      )
    }
  })

  navigation.updateByUserRole()
}

function validateInputDouble(prmValue) {
  var answer = 1
  var value = prmValue
  if (isNaN(value)) {
    answer = 0
  }
  if (value == '') {
    answer = 0
  }
  if (answer > 0) {
    if (occurrences(value, ',') > 0) {
      answer = 0
    }
  }
  if (answer > 0) {
    if (occurrences(value, '.') > 1) {
      answer = 0
    }
  }
  return answer
}

function occurrences(string, substring) {
  var n = 0
  var pos = 0
  // eslint-disable-next-line no-constant-condition
  while (true) {
    pos = string.indexOf(substring, pos)
    if (pos != -1) {
      n++
      pos += substring.length
    } else {
      break
    }
  }
  return n
}

function toggleFormEdit(id, disab) {
  var xForm = document.getElementById(id)
  if (xForm === null) {
    return false
  }
  var objElems = xForm.elements
  var col = '#fff'
  if (disab) {
    col = '#eee'
  }
  for (var i = 0; i < objElems.length; i++) {
    var myname = String(objElems[i].name)
    if (myname.substring(0, 1) !== '_') {
      objElems[i].disabled = disab
      objElems[i].style.backgroundColor = col
    }
  }
  jQuery('#txtDatacardFind', xForm)
    .prop('readonly', disab)
    .prop('disabled', disab)
  jQuery('#btnDatacardFind', xForm)
    .prop('readonly', disab)
    .prop('disabled', disab)
}

function showStatus(msgId) {
  // First hide all items
  jQuery('.datacardStatusMsg').hide()
  // Show a specific message
  if (msgId !== '') {
    jQuery('#' + msgId).show()
  }
}

function create() {
  toggleFormEdit('DICard', false)
  jQuery('#DisasterBeginTime0').focus()
  showStatus('msgDatacardFill')
  navigation.setEditMode()
  jQuery('#divRecordNavigationInfo').hide()
  navigation.setStatus('NEW')
  updateStatus('DRAFT')

  // Clear values of following sublevels
  var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1
  for (var i = 1; i < GeoLevelCount; i++) {
    var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i)
    mySelect.empty()
    mySelect.append(jQuery('<option>', { value: '' }).text(''))
    mySelect.disable()
  }
}

function clone() {
  var regionId = jQuery('#desinventarRegionId').val()
  var id = jQuery('#DisasterId').val()
  read(regionId, id).then(function(data) {
    data.RecordStatus = 'DRAFT'
    data.DisasterId = ''
    data.DisasterSiteNotes = ''
    data.DisasterLatitude = '0.0'
    data.DisasterLongitude = '0.0'
    if (data.GeographyItems.length > 1) {
      data.GeographyItems.pop() // Remove last element from Geography
    }
    data.GeographyId =
      data.GeographyItems[data.GeographyItems.length - 1].GeographyId
    clear()
    create()
    setDICard(data)
    clearEffects()
    jQuery('#DICard #EffectNotes').val(data.EffectNotes)
    jQuery.each(data.GeographyItems, function(key, value) {
      jQuery('select#GeoLevel' + key)
        .val(value.GeographyId)
        .change()
    })
    var prefix = data.DisasterSerial.split(':')
      .slice(0, 1)
      .join('')
    jQuery('#DatacardPrefix').val(prefix)
    navigation.setStatus('NEW')
    jQuery('#DatacardCommand').val('insertDICard')
    nextSerial(regionId)
    return data
  })
}

function clearEffects() {
  var effects = jQuery('div.divDatacardEffects')
  effects.find('table.EffectListPeople .clsEffectNumeric').each(function() {
    jQuery(this).val(0)
    jQuery(this).jecValue('', false)
  })
  effects.find('select.clsEffectSector').each(function() {
    jQuery(this).val(0) // There weren't by default
  })
  effects.find('.inputDouble').each(function() {
    jQuery(this).val(0)
  })
  effects.find('.inputInteger').each(function() {
    jQuery(this).val(0)
  })
  effects.find('.inputText').each(function() {
    jQuery(this).val('')
  })
}

function nextSerial(regionId) {
  var payload = {
    cmd: 'getNextSerial',
    r: regionId,
    year: jQuery('#DisasterBeginTime0').val(),
    value: jQuery('#DisasterBeginTime0').val(),
    length: desinventar.info.SerialSuffixSize,
    separator: '-'
  }
  if (jQuery('#DatacardPrefix').val() !== '') {
    payload.value = jQuery('#DatacardPrefix').val()
    payload.length = desinventar.info.SerialCloneSuffixSize
    payload.separator = ':'
  }
  if (payload.value === '') {
    return false
  }
  jQuery
    .post(jQuery('#desinventarURL').val() + '/cards.php', payload, null, 'json')
    .then(function(data) {
      if (data.DisasterSerial) {
        jQuery('#DisasterSerial').val(data.DisasterSerial)
      }
    })
  return false
}

function updateStatus(status) {
  jQuery('#DICard #RecordStatus').val(status)
}

function read(regionId, disasterId) {
  return jQuery.post(
    jQuery('#desinventarURL').val() + '/cards.php',
    {
      cmd: 'getDatacard',
      RegionId: regionId,
      DisasterId: disasterId
    },
    null,
    'json'
  )
}

function clear() {
  jQuery('#DisasterId').val('')
  jQuery('#DatacardPrefix').val('')
  var form = document.getElementById('DICard')
  form.reset()
  jQuery('#DatacardCommand').val('insertDICard')
  jQuery('#cardsRecordNumber').val(0)
  clearEffects()
  jQuery('#DICard #DisasterBeginTime0').val('')
  jQuery('#DICard #DisasterBeginTime1').val('')
  jQuery('#DICard #DisasterBeginTime2').val('')
  jQuery('#DICard #EventDuration').val(0)
}

export default {
  init
}
