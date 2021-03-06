/* global qq */

var uploader

function init() {
  jQuery('div.Geolevels').on('cmdInitialize', function() {
    doGeolevelsUploaderCreate()
  })

  jQuery('#tbodyGeolevels_List')
    .on('click', 'tr', function() {
      jQuery('#frmGeolevel .GeoLevelId').val(jQuery('.GeoLevelId', this).text())
      jQuery('#frmGeolevel .GeoLevelName').val(
        jQuery('.GeoLevelName', this).text()
      )
      jQuery('#frmGeolevel .GeoLevelDesc').val(
        jQuery('.GeoLevelDesc', this).prop('title')
      )
      jQuery('#frmGeolevel .GeoLevelActiveLabel').hide()
      jQuery('#frmGeolevel .GeoLevelActiveCheckbox')
        .prop('checked', jQuery('.GeoLevelActive :input', this).is(':checked'))
        .change()
        .hide()
      jQuery('#divGeolevels_Edit').show()
      jQuery('#divGeolevels_Edit .GeocartoEdit').show()
      jQuery('#btnGeolevels_Add').hide()
      jQuery('#frmGeolevel .GeoLevelLayerName').val('')
      jQuery('#frmGeolevel .GeoLevelLayerCode').val('')
      jQuery('#frmGeolevel .GeoLevelLayerParentCode').val('')
      jQuery('#frmGeolevel tr.FileUploader input.filename').val('')
      jQuery('#frmGeolevel tr.FileUploader span.uploaded')
        .text('')
        .show()
    })
    .on('mouseover', 'tr', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr', function() {
      jQuery(this).removeClass('highlight')
    })

  jQuery('#btnGeolevels_Add').click(function() {
    jQuery('#divGeolevels_Edit').show()

    jQuery('#frmGeolevel .GeoLevelLayerName').val('')
    jQuery('#frmGeolevel .GeoLevelLayerCode').val('')
    jQuery('#frmGeolevel .GeoLevelLayerParentCode').val('')
    jQuery('#frmGeolevel tr.FileUploader input.filename').val('')
    jQuery('#frmGeolevel tr.FileUploader span.uploaded')
      .text('')
      .show()

    jQuery(this).hide()
    jQuery('#frmGeolevel .GeoLevelId').val('-1')
    jQuery('#frmGeolevel .GeoLevelName').val('')
    jQuery('#frmGeolevel .GeoLevelDesc').val('')
    jQuery('#frmGeolevel .GeoLevelActiveLabel').hide()
    jQuery('#frmGeolevel .GeoLevelActiveCheckbox')
      .prop('checked', true)
      .change()
      .hide()
    return false
  })

  jQuery('#frmGeolevel .btnSave').click(function() {
    jQuery('#frmGeolevel').trigger('submit')
    return false
  })

  jQuery('#frmGeolevel .btnCancel').click(function() {
    jQuery('#frmGeolevel .Filename').val('')
    jQuery('#frmGeolevel .uploaded').hide()
    jQuery('#divGeolevels_Edit').hide()
    jQuery('#btnGeolevels_Add').show()
    return false
  })

  jQuery('#frmGeolevel .OptionImportGeographyCheckbox').change(function() {
    var v = 0
    if (jQuery(this).is(':checked')) {
      v = 1
    }
    jQuery('#frmGeolevel .OptionImportGeography').val(v)
  })
  jQuery('#frmGeolevel .OptionImportGeographyText').click(function() {
    jQuery('#frmGeolevel .OptionImportGeographyCheckbox')
      .prop(
        'checked',
        !jQuery('#frmGeolevel .OptionImportGeographyCheckbox').prop('checked')
      )
      .change()
    return false
  })

  jQuery('#frmGeolevel .GeoLevelActiveCheckbox').change(function() {
    var v = 0
    if (jQuery(this).is(':checked')) {
      v = 1
    }
    jQuery('#frmGeolevel .GeoLevelActive').val(v)
  })

  jQuery('#frmGeolevel').submit(function() {
    jQuery(':input', this).each(function() {
      jQuery(this).val(jQuery.trim(jQuery(this).val()))
    })

    if (jQuery.trim(jQuery('#frmGeolevel .GeoLevelName').val()) === '') {
      jQuery('#frmGeolevel .GeoLevelName').highlight()
      jQuery('div.status .statusRequiredFields').show()
      return unhighlightGeoLevelFields()
    }

    var numberOfFileUploadControls = jQuery('#frmGeolevel .filename').size()
    var numberOfFilesUploaded = 0
    jQuery('#frmGeolevel .filename').each(function() {
      if (jQuery(this).val() != '') {
        numberOfFilesUploaded++
      }
    })
    var areAllFilesUploaded =
      numberOfFilesUploaded === numberOfFileUploadControls
    var isDbfFileUploaded =
      jQuery('input.filename[name="filename.DBF"]').val() !== ''
    if (numberOfFilesUploaded > 0) {
      if (!isDbfFileUploaded && !areAllFilesUploaded) {
        jQuery('div.status .statusMissingFiles').show()
        return unhighlightGeoLevelFields()
      }

      if (jQuery('#frmGeolevel .GeoLevelLayerCode').val() === '') {
        jQuery('#frmGeolevel .GeoLevelLayerCode').highlight()
        jQuery('div.status .statusRequiredFields').show()
        return unhighlightGeoLevelFields()
      }

      if (jQuery('#frmGeolevel .GeoLevelLayerName').val() == '') {
        jQuery('#frmGeolevel .GeoLevelLayerName').highlight()
        jQuery('div.status .statusRequiredFields').show()
        return unhighlightGeoLevelFields()
      }
    }

    jQuery('body').trigger('cmdMainWaitingShow')
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdGeolevelsUpdate',
        RegionId: jQuery('#desinventarRegionId').val(),
        GeoLevel: jQuery('#frmGeolevel').toObject()
      },
      function(data) {
        jQuery('body').trigger('cmdMainWaitingHide')
        if (parseInt(data.Status) > 0) {
          jQuery('#frmGeolevel .GeoLevelId').val(data.GeoLevelId)
          jQuery('#divGeolevels_Edit').hide()
          jQuery('#btnGeolevels_Add').show()
          jQuery('div.status .statusUpdateOk').show()
          doGeolevelsPopulateList(data.GeolevelsList)

          jQuery('div.status span.status').hide()
          jQuery('div.status span.statusCreatingGeography').show()
          jQuery.post(
            jQuery('#desinventarURL').val() + '/',
            {
              cmd: 'cmdGeolevelsImportGeography',
              RegionId: jQuery('#desinventarRegionId').val(),
              GeoLevel: jQuery('#frmGeolevel').toObject()
            },
            function() {
              jQuery('div.status span.statusCreatingGeography').hide()
              jQuery('div.status .statusUpdateOk').show()
              setTimeout(function() {
                jQuery('div.status span.status').hide()
              }, 3000)
            },
            'json'
          )
        } else {
          jQuery('div.status .statusUpdateError').show()
        }
      },
      'json'
    )
    return unhighlightGeoLevelFields()
  })
  // Attach events to main page
  jQuery('body').on('cmdGeolevelsShow', function() {
    jQuery('body').trigger('cmdMainWaitingShow')
    jQuery('.clsGeolevelsStatus').hide()
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdGeolevelsGetList',
        RegionId: jQuery('#desinventarRegionId').val()
      },
      function(data) {
        jQuery('body').trigger('cmdMainWaitingHide')
        if (parseInt(data.Status) > 0) {
          jQuery('#divGeolevels_Edit').hide()
          jQuery('#btnGeolevels_Add').show()
          doGeolevelsPopulateList(data.GeolevelsList)
        }
      },
      'json'
    )
  })
}

function unhighlightGeoLevelFields() {
  setTimeout(function() {
    jQuery('div.status .status').hide()
    jQuery('#frmGeolevel .GeoLevelName').unhighlight()
    jQuery('#frmGeolevel .GeoLevelLayerCode').unhighlight()
    jQuery('#frmGeolevel .GeoLevelLayerName').unhighlight()
  }, 2500)
  return false
}

function doGeolevelsPopulateList(GeolevelsList) {
  jQuery('#divGeolevels_Edit').hide()
  jQuery('#tbodyGeolevels_List')
    .find('tr:gt(0)')
    .remove()
  jQuery('#tbodyGeolevels_List')
    .find('tr:first')
    .hide()
  jQuery('#tbodyGeolevels_List')
    .find('tr')
    .removeClass('under')
  jQuery.each(GeolevelsList, function(index, value) {
    var clonedRow = jQuery('#tbodyGeolevels_List tr:last')
      .clone()
      .show()
    jQuery('.GeoLevelId', clonedRow).html(index)
    jQuery('.GeoLevelName', clonedRow).html(value.GeoLevelName)
    jQuery('.GeoLevelDesc', clonedRow).html(
      value.GeoLevelDesc.substring(0, 150)
    )
    jQuery('.GeoLevelDesc', clonedRow).prop('title', value.GeoLevelDesc)
    jQuery('.GeoLevelActive :input', clonedRow).prop(
      'checked',
      value.GeoLevelActive > 0
    )
    var HasMap =
      value.GeoLevelLayerFile != undefined && value.GeoLevelLayerFile != ''
    jQuery('.HasMap :input', clonedRow).prop('checked', HasMap)
    jQuery('.GeoLevelLayerFile', clonedRow).html(value.GeoLevelLayerFile)
    jQuery('#tbodyGeolevels_List').append(clonedRow)
  })
  jQuery('#tblGeolevels_List .GeoLevelId').hide()
  jQuery('#tblGeolevels_List .GeoLevelActive').hide()
  jQuery('#tbodyGeolevels_List tr:even').addClass('under')
}

function doGeolevelsPopulateFieldList(prmSelector, prmValues) {
  jQuery(prmSelector).empty()
  jQuery(prmSelector).append(jQuery('<option>', { value: '' }).text('--'))
  jQuery.each(prmValues, function(key, value) {
    jQuery(prmSelector).append(jQuery('<option>', { value: value }).text(value))
  })
}

function doGeolevelsUploaderCreate() {
  jQuery('#frmGeolevel tr.FileUploader').each(function() {
    var fileExt = jQuery(this).data('ext')
    var fileUploaderControlId = jQuery(this)
      .find('.FileUploaderControl')
      .attr('id')
    uploader = new qq.FileUploader({
      element: document.getElementById(fileUploaderControlId),
      action: jQuery('#desinventarURL').val() + '/',
      params: {
        cmd: 'cmdGeolevelsUpload',
        RegionId: jQuery('#desinventarRegionId').val(),
        UploadExt: fileExt
      },
      debug: false,
      multiple: false,
      allowedExtensions: [fileExt],
      onSubmit: function(id) {
        var ext = this.allowedExtensions[0]
        var row = jQuery('#frmGeolevel tr:data("ext=' + ext + '")')
        jQuery('.UploadId', row).val(id)
        jQuery('.uploaded', row).hide()
        jQuery('#frmGeolevel .ProgressBar').show()
        jQuery('#frmGeolevel .ProgressMark').css('width', '0px')
        jQuery('.FileUploaderControl .qq-upload-button-text', this).hide()
        jQuery('#frmGeolevel .btnUploadCancel').show()
      },
      onProgress: function(id, Filename, loaded, total) {
        var maxWidth = jQuery('#frmGeolevel .ProgressBar').width()
        var percent = parseInt(loaded / total * 100)
        var width = parseInt(percent * maxWidth / 100)
        jQuery('#frmGeolevel .ProgressMark').css('width', width)
      },
      onComplete: function(id, Filename, data) {
        var ext = this.allowedExtensions[0]
        var row = jQuery('#frmGeolevel tr:data("ext=' + ext + '")')
        doGeolevelsUploaderReset()
        jQuery('div.status .status').hide()
        jQuery('#frmGeolevel .btnUploadCancel').hide()
        if (parseInt(data.Status) > 0) {
          jQuery('.filename', row).val(data.filename)
          jQuery('.uploaded', row)
            .text(data.filename_orig)
            .show()
          jQuery('div.status .statusuploadOk').show()
          if (data.DBFFields != undefined) {
            doGeolevelsPopulateFieldList(
              '#frmGeolevel .GeoLevelLayerName',
              data.DBFFields
            )
            doGeolevelsPopulateFieldList(
              '#frmGeolevel .GeoLevelLayerCode',
              data.DBFFields
            )
            doGeolevelsPopulateFieldList(
              '#frmGeolevel .GeoLevelLayerParentCode',
              data.DBFFields
            )
          }
          setTimeout(function() {
            jQuery('div.status .status').hide()
          }, 2000)
        } else {
          jQuery('div.status .statusUploadError').show()
          setTimeout(function() {
            jQuery('div.status .status').hide()
          }, 2000)
        }
      },
      onCancel: function() {
        doGeolevelsUploaderReset()
      }
    })
  })
  jQuery('#frmGeolevel .FileUploaderControl .qq-upload-button-text').html(
    jQuery('#msgGeolevels_UploadChooseFile').text()
  )
  jQuery('#frmGeolevel .FileUploaderControl .qq-upload-list').hide()
  jQuery('#frmGeolevel .btnUploadCancel')
    .click(function() {
      jQuery('#frmGeolevel .UploadId').each(function() {
        uploader.cancel(jQuery(this).val())
      })
      return false
    })
    .hide()
  jQuery('#frmGeolevel .uploaded').hide()
  jQuery('div.status .status').hide()
}

function doGeolevelsUploaderReset() {
  jQuery('#frmGeolevel .ProgressBar').hide()
  jQuery('#frmGeolevel .ProgressMark').css('width', '0px')
  jQuery('#frmGeolevel .UploadCancel').hide()
  jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').show()
}

export default {
  init
}
