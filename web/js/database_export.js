/* global Ext */
function init() {
  doDatabaseExportCreate()
  jQuery('body').on('cmdDatabaseExport', function() {
    doDatabaseExportAction()
  })
}

function doDatabaseExportCreate() {
  // Database Export
  new Ext.Window({
    id: 'wndDatabaseExport',
    el: 'divDatabaseExportWin',
    layout: 'fit',
    width: 400,
    height: 200,
    modal: false,
    constrainHeader: true,
    closeAction: 'hide',
    plain: false,
    animCollapse: false,
    items: new Ext.Panel({
      contentEl: 'divDatabaseExportContent',
      autoScroll: true
    }),
    buttons: []
  })
  jQuery('#fldDatabaseExportSave').val(1)
}

function doDatabaseExportAction() {
  jQuery('.clsDatabaseExport').hide()
  Ext.getCmp('wndDatabaseExport').show()
  jQuery('.clsDatabaseExport').hide()
  jQuery('#divDatabaseExportProgress').show()

  jQuery('#imgDatabaseExportWait').attr(
    'src',
    jQuery('#fldDatabaseExportImage').val()
  )
  jQuery('#imgDatabaseExportWait').show()

  jQuery('#fldDatabaseExportSave').val(1)
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseExport',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      jQuery('.clsDatabaseExport').hide()
      if (parseInt(data.Status) > 0) {
        jQuery('#divDatabaseExportResults').show()
        jQuery('#imgDatabaseExportWait')
          .attr('src', '')
          .hide()
        // Hide Ext.Window
        Ext.getCmp('wndDatabaseExport').hide()
        if (parseInt(jQuery('#fldDatabaseExportSave').val()) > 0) {
          // Open the backup file for download
          window.location = data.URL
        }
      } else {
        jQuery('#divDatabaseExportError').show()
      }
    },
    'json'
  )
}

module.exports = exports = {
  init
}
