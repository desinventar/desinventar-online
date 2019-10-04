import $ from 'jquery'

function updateCloneButton() {
  disableButton('btnDatacardClone')
  if (getStatus() === 'VIEW') {
    enableButton('btnDatacardClone')
  }
}

function disableButton(buttonId) {
  $('#' + buttonId)
    .prop('disabled', true)
    .removeClass('bb')
    .addClass('disabled')
}

function enableButton(buttonId) {
  $('#' + buttonId)
    .prop('disabled', false)
    .addClass('bb')
    .removeClass('disabled')
}

function getStatus() {
  return $('#DICard #Status').val()
}

function setStatus(status) {
  return $('#DICard #Status').val(status)
}

function enable() {
  var RecordNumber = parseInt($('#cardsRecordNumber').val(), 10)
  var RecordCount = parseInt($('#cardsRecordCount').val(), 10)

  disable()
  if (RecordCount < 1) {
    return true
  }
  if (RecordNumber < 1) {
    enableButton('btnDatacardGotoFirst')
    enableButton('btnDatacardGotoLast')
    return true
  }

  if (RecordNumber > 1) {
    enableButton('btnDatacardGotoFirst')
    enableButton('btnDatacardGotoPrev')
  }

  if (RecordNumber < RecordCount) {
    enableButton('btnDatacardGotoLast')
    enableButton('btnDatacardGotoNext')
  }
  updateCloneButton()
}

function disable() {
  $('input.DatacardNavButton').each(function() {
    disableButton($(this).attr('id'))
  })
}

function updateByUserRole() {
  if ($('#desinventarUserRoleValue').val() >= 2) {
    enableButton('btnDatacardEdit')
  }
  updateCloneButton()
}

function setEditMode() {
  disableButton('btnDatacardNew')
  disableButton('btnDatacardEdit')
  disableButton('btnDatacardClone')
  disableButton('btnDatacardFind')
  enableButton('btnDatacardSave')
  enableButton('btnDatacardCancel')
  disable()
}

function setViewMode() {
  enableButton('btnDatacardNew')
  enableButton('btnDatacardEdit')
  enableButton('btnDatacardFind')
  disableButton('btnDatacardSave')
  disableButton('btnDatacardCancel')
  disableButton('btnDatacardClone')
  enable()
  if ($('#DisasterId').val() === '') {
    disableButton('btnDatacardEdit')
  }
}
export default {
  setStatus,
  setViewMode,
  enable,
  updateByUserRole,
  getStatus,
  setEditMode
}
