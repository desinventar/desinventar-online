/* global onReadyDatabaseExport */
import adminDatabaseEdit from './admin_database_edit'

function init() {
  adminDatabaseEdit.init()
  onReadyDatabaseExport()

  // Highlight row on mouseOver
  jQuery('#tblDatabaseList tr').live({
    mouseenter: function() {
      jQuery(this).addClass('highlight')
    },
    mouseleave: function() {
      jQuery(this).removeClass('highlight')
    },
    click: function() {
      jQuery('#divAdminDatabaseList').hide()
      jQuery('#divAdminDatabaseUpdate .RegionLabel').text(
        jQuery('.RegionLabel', this).html()
      )
      jQuery('#divAdminDatabaseUpdate .RegionId').text(
        jQuery('.RegionId', this).html()
      )
      jQuery('.clsAdminDatabaseButton').show()
      jQuery('#btnAdminDatabaseNew').hide()
      jQuery('.clsAdminDatabase').hide()
      jQuery('#divAdminDatabaseUpdate').show()
    }
  })

  jQuery('#btnAdminDatabaseEdit').click(function() {
    jQuery('.clsAdminDatabase').hide()
    var RegionId = jQuery('#divAdminDatabaseUpdate .RegionId').text()
    adminDatabaseEdit.getInfo(RegionId)
    jQuery('#divAdminDatabaseEdit').show()
    return false
  })

  jQuery('#btnAdminDatabaseImport').click(function() {
    jQuery('.clsAdminDatabase').hide()
    jQuery('#divAdminDatabaseImport').show()
    return false
  })

  // Add New Region
  jQuery('#btnAdminDatabaseNew')
    .live('click', function() {
      jQuery('#regionpaaddsect').show()
      setRegionPA('', '', '', '', '', true, false)
      jQuery('#frmRegionEdit_Cmd').val('cmdDatabaseCreate')
    })
    .hide()

  jQuery('.clsAdminDatabaseButton').hide()

  // Select Database from List
  jQuery('#btnAdminDatabaseSelect').live('click', function() {
    jQuery('#divAdminDatabaseUpdate').hide()
    jQuery('#divAdminDatabaseList').show()
  })

  jQuery('#divAdminDatabaseUpdate').on('evAdminDatabaseCancel', function() {
    jQuery('.clsAdminDatabase').hide()
  })
}

function updateList() {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdAdminDatabaseGetList'
    },
    function(data) {
      jQuery('#divAdminDatabaseList').show()
      jQuery.each(data.RegionList, function(index, value) {
        var clonedRow = jQuery('#tbodyDatabaseList tr:last')
          .clone()
          .show()
        jQuery('.CountryIso', clonedRow).html(value.CountryIso)
        jQuery('.RegionLabel', clonedRow).html(value.RegionLabel)
        jQuery('.RegionAdminUserId', clonedRow).text(value.RegionAdminUserId)
        jQuery('.RegionAdminUserFullName', clonedRow)
          .text(value.RegionAdminUserFullName)
          .show()
        jQuery('.RegionActive', clonedRow)
          .attr('checked', value.RegionActive)
          .attr('disabled', true)
        jQuery('.RegionPublic', clonedRow)
          .attr('checked', value.RegionPublic)
          .attr('disabled', true)
        jQuery('.RegionId', clonedRow)
          .html(value.RegionId)
          .hide()
        jQuery('.LangIsoCode', clonedRow)
          .html(value.LangIsoCode)
          .hide()
        jQuery('#tbodyDatabaseList').append(clonedRow)
      })
      // Table Stripes...
      jQuery('#tblDatabaseList tr:odd')
        .removeClass('normal')
        .addClass('normal')
      jQuery('#tblDatabaseList tr:even')
        .removeClass('normal')
        .addClass('under')

      jQuery('#tblDatabaseList #RegionId').hide()
      jQuery('#tblDatabaseList #LangIsoCode').hide()
      if (jQuery('#desinventarUserId').val() == 'root') {
        jQuery('#btnAdminDatabaseNew').show()
      }
    },
    'json'
  )
}

function setRegionPA(
  prmRegionId,
  prmCountryIso,
  prmRegionLabel,
  prmLangIsoCode,
  prmUserId_AdminRegion,
  prmRegionActive,
  prmRegionPublic
) {
  jQuery('#regionpaaddsect').show()
  jQuery('#frmRegionEdit #RegionId').val(prmRegionId)
  jQuery('#frmRegionEdit #CountryIso').val(prmCountryIso)
  jQuery('#frmRegionEdit #RegionLabel').val(prmRegionLabel)
  jQuery('#frmRegionEdit #LangIsoCode').val(prmLangIsoCode)
  jQuery('#frmRegionEdit #RegionUserAdmin').val(prmUserId_AdminRegion)
  jQuery('#frmRegionEdit #RegionActive').attr('checked', prmRegionActive)
  jQuery('#frmRegionEdit #RegionPublic').attr('checked', prmRegionPublic)
  // RegionId is readonly by default
  jQuery('#frmRegionEdit #RegionId').attr('disabled', 'disabled')
}

export default {
  init,
  updateList
}
