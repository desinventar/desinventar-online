/* global Ext, qq */
import Swal from 'sweetalert2'

var uploader

function init() {
  jQuery('#divDatabaseUploadControl').show()
  jQuery('#divDatabaseUploadParameters').hide()
  jQuery('#txtDatabaseUploadFilename').attr('readonly', true)
  doDatabaseUploadStatusMsg('')

  doDatabaseUploadCreate()

  jQuery('div.DatabaseUpload').on('cmdInitialize', function() {
    doAdminDatabaseCreateUploader()
  })

  jQuery('#btnDatabaseUploadStart').click(function() {
    jQuery('#divDatabaseUploadParameters').hide()
    jQuery('#divDatabaseUploadControl').show()
    return false
  })

  jQuery('#btnDatabaseUploadReplace').click(function() {
    jQuery('.clsDatabaseUploadButtons').hide()
    doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForUpdate')
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseReplace',
        RegionId: jQuery('#desinventarRegionId').val(),
        Filename: jQuery('#txtDatabaseUploadFilename').val()
      },
      function(data) {
        doDatabaseUploadStatusMsg('')
        if (parseInt(data.Status) > 0) {
          jQuery('#divDatabaseUploadParameters').hide()
          doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateOk')
          displayConfirmationDialog(
            jQuery('#msgDatabaseUploadReplaceComplete').val(),
            function() {
              jQuery('body').trigger('cmdWindowReload')
            }
          )
        } else {
          jQuery('.clsDatabaseUploadButtons').show()
          doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateError')
        }
      },
      'json'
    )
    return false
  })

  jQuery('#btnDatabaseUploadCopy').click(function() {
    jQuery('.clsDatabaseUploadButtons').hide()
    doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForCopy')
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseCopy',
        RegionId: jQuery('#txtDatabaseUploadRegionId').text(),
        RegionLabel: jQuery('#txtDatabaseUploadRegionLabel').text(),
        Filename: jQuery('#txtDatabaseUploadFilename').val()
      },
      function(data) {
        doDatabaseUploadStatusMsg('')
        if (parseInt(data.Status) > 0) {
          jQuery('#divDatabaseUploadParameters').hide()
          doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateOk')
          displayConfirmationDialog(
            jQuery('#msgDatabaseUploadCopyComplete').val(),
            function() {
              window.location =
                jQuery('#desinventarURL').val() +
                '/#' +
                jQuery('#txtDatabaseUploadRegionId').text()
              jQuery('body').trigger('cmdWindowReload')
            }
          )
        } else {
          jQuery('.clsDatabaseUploadButtons').show()
          doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateError')
        }
      },
      'json'
    )
    return false
  })

  jQuery('#btnDatabaseUploadReplaceCancel').click(function() {
    if (jQuery('#txtDatabaseUploadFilename').val() != '') {
      jQuery.post(
        jQuery('#desinventarURL').val() + '/',
        {
          cmd: 'cmdDatabaseReplaceCancel',
          RegionId: jQuery('#desinventarRegionId').val(),
          Filename: jQuery('#txtDatabaseUploadFilename').val()
        },
        function() {
          reset(false)
          Ext.getCmp('wndDatabaseUpload').hide()
        },
        'json'
      )
    } else {
      Ext.getCmp('wndDatabaseUpload').hide()
    }
    return false
  })
}

function displayConfirmationDialog(message, onAfterCloseCallback) {
  Swal.fire({
    title: '',
    text: message,
    type: 'info',
    confirmationButtonText: jQuery('#msgDatabaseUploadButtonClose').text(),
    allowOutsideClick: false,
    customClass: {
      confirmButton: 'sweetalert2-confirm-button'
    },
    onAfterClose: onAfterCloseCallback
  })
}

function doAdminDatabaseCreateUploader() {
  jQuery('#divFileUploaderControl').each(function() {
    uploader = new qq.FileUploader({
      element: document.getElementById(jQuery(this).attr('id')),
      action: jQuery('#desinventarURL').val() + '/',
      params: {
        cmd: 'cmdDatabaseUpload',
        UploadMode: jQuery('#fldDatabaseUploadMode').val(),
        RegionId: jQuery('#desinventarRegionId').val()
      },
      debug: false,
      multiple: false,
      allowedExtensions: ['zip'],
      onSubmit: function(id, Filename) {
        jQuery('#txtDatabaseUploadFilename').val(Filename)
        jQuery('#txtDatabaseUploadId').val(id)
        jQuery('#prgDatabaseUploadProgressBar').show()
        jQuery('#prgDatabaseUploadProgressMark').css('width', '0px')
        jQuery('#divFileUploaderControl .qq-upload-button-text').hide()
        jQuery('#btnDatabaseUploadCancel').show()
        doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForUpload')
      },
      onProgress: function(id, Filename, loaded, total) {
        var maxWidth = jQuery('#prgDatabaseUploadProgressBar').width()
        var percent = parseInt(loaded / total * 100)
        var width = parseInt(percent * maxWidth / 100)
        jQuery('#prgDatabaseUploadProgressMark').css('width', width)
      },
      onComplete: function(id, Filename, data) {
        doDatabaseUploadStatusMsg('')
        jQuery('#btnDatabaseUploadCancel').hide()
        jQuery('#txtDatabaseUploadFilename').val(data.filename)
        if (parseInt(data.Status) > 0) {
          jQuery('.clsDatabaseUploadType').hide()
          if (jQuery('#fldDatabaseUploadMode').val() == 'Copy') {
            jQuery('#txtDatabaseUploadConfirmCopy').show()
            jQuery('#btnDatabaseUploadCopy').show()
          } else {
            jQuery('#txtDatabaseUploadConfirmReplace').show()
            jQuery('#btnDatabaseUploadReplace').show()
          }
          doDatabaseUploadSetParameters(data.RegionInfo)
          doDatabaseUploadStatusMsg('')
          jQuery('#divDatabaseUploadControl').hide()
          jQuery('#divDatabaseUploadParameters').show()
        } else {
          reset(false)
          displayError(data.Status)
        }
      },
      onCancel: function() {}
    })
  })
  jQuery('#divFileUploaderControl .qq-upload-button-text').html(
    jQuery('#msgDatabaseUploadChooseFile').val()
  )
  jQuery('#divFileUploaderControl .qq-upload-list').hide()

  jQuery('#btnDatabaseUploadCancel')
    .unbind('click')
    .click(function() {
      reset(true)
      uploader.cancel(jQuery('#txtDatabaseUploadId').val())
      return false
    })
}

function displayError(status) {
  //ERR_INVALID_ZIPFILE
  if (status === -130) {
    doDatabaseUploadStatusMsg('msgDatabaseUploadErrorNoInfo')
    return
  }
  doDatabaseUploadStatusMsg('msgDatabaseUploadErrorOnUpload')
  return
}

function reset(prmShowRegionInfo) {
  doAdminDatabaseCreateUploader()
  doDatabaseUploadStatusMsg('')
  jQuery('#txtDatabaseUploadFilename').val('')
  jQuery('#txtDatabaseUploadRegionId').text('')
  jQuery('#txtDatabaseUploadRegionLabel').text('')
  jQuery('#txtDatabaseUploadCountryIso').text('')
  jQuery('#txtDatabaseUploadRegionLastUpdate').text('')
  jQuery('#txtDatabaseUploadNumberOfRecords').text('')

  jQuery('#prgDatabaseUploadProgressBar').hide()
  jQuery('#prgDatabaseUploadProgressMark').css('width', '0px')
  jQuery('#btnDatabaseUploadCancel').hide()
  jQuery('.clsDatabaseUploadButtons').show()
  jQuery('#divFileUploaderControl .qq-upload-button-text').show()

  jQuery('#divDatabaseUploadControl').hide()
  jQuery('#divDatabaseUploadParameters').hide()

  if (
    jQuery('#fldDatabaseUploadMode').val() == 'Copy' ||
    prmShowRegionInfo == false
  ) {
    jQuery('#divDatabaseUploadControl').show()
  } else {
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseGetInfo',
        RegionId: jQuery('#desinventarRegionId').val()
      },
      function(data) {
        if (parseInt(data.Status) > 0) {
          doDatabaseUploadSetParameters(data.RegionInfo)
          jQuery('#divDatabaseUploadParameters').show()
          jQuery('.clsDatabaseUploadType').hide()
          jQuery('#txtDatabaseUploadConfirmStart').show()
          jQuery('#btnDatabaseUploadStart').show()
          jQuery('#btnDatabaseUploadReplaceCancel').show()
        }
      },
      'json'
    )
  }
}

function doDatabaseUploadSetParameters(RegionInfo) {
  jQuery('#txtDatabaseUploadRegionId').text(RegionInfo.RegionId)
  jQuery('#txtDatabaseUploadRegionLabel').text(RegionInfo.RegionLabel)
  jQuery('#txtDatabaseUploadCountryIso').text(
    RegionInfo.CountryIso + ' - ' + RegionInfo.CountryName
  )
  jQuery('#txtDatabaseUploadRegionLastUpdate').text(RegionInfo.RegionLastUpdate)
  jQuery('#trDatabaseUploadNumberOfRecords').show()
  jQuery('#txtDatabaseUploadNumberOfRecords').text(RegionInfo.NumberOfRecords)
  if (parseInt(RegionInfo.NumberOfRecords) < 1) {
    jQuery('#trDatabaseUploadNumberOfRecords').hide()
  }
}

function doDatabaseUploadStatusMsg(Id) {
  jQuery('.clsDatabaseUploadStatusMsg').hide()
  if (Id != '') {
    jQuery('.clsDatabaseUploadStatusMsg#' + Id).show()
  }
}

function doDatabaseUploadCreate() {
  // Database Upload
  var w = new Ext.Window({
    id: 'wndDatabaseUpload',
    el: 'divDatabaseUploadWin',
    layout: 'fit',
    width: 400,
    height: 220,
    modal: false,
    constrainHeader: true,
    plain: false,
    animCollapse: false,
    closeAction: 'hide',
    items: new Ext.Panel({
      contentEl: 'divDatabaseUploadContent',
      autoScroll: true
    })
  })
  w.on('hide', function() {
    if (jQuery('#txtDatabaseUploadFilename').val() != '') {
      jQuery('#btnDatabaseUploadReplaceCancel').trigger('click')
    }
  })

  jQuery('#fldDatabaseUploadSave').val(1)
  jQuery('.clsDatabaseUploadStatusMsg').hide()
}

export default {
  init,
  reset
}
