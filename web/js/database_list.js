/* global Ext */

function init() {
  doDatabaseDeleteCreate()

  jQuery('#divDatabaseFindList table.databaseList')
    .on('mouseover', 'td.RegionDelete', function() {
      jQuery(this)
        .parent()
        .highlight()
    })
    .on('mouseout', 'td.RegionDelete', function() {
      jQuery(this)
        .parent()
        .unhighlight()
    })
    .on('click', 'td.RegionDelete', function(event) {
      var RegionId = jQuery(this)
        .parent()
        .find('td.RegionId')
        .text()
      var RegionLabel = jQuery(this)
        .parent()
        .find('span.RegionLabel')
        .text()
      jQuery('#divDatabaseDeleteContent span.RegionId').text(RegionId)
      jQuery('#divDatabaseDeleteContent span.RegionLabel').text(RegionLabel)
      doDatabaseDeleteShow()
      event.preventDefault()
    })
  jQuery('#divDatabaseDeleteContent').on('click', 'a.buttonOk', function(
    event
  ) {
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseDelete',
        RegionId: jQuery('div.DatabaseDelete span.RegionId').text()
      },
      function(data) {
        jQuery('div.DatabaseDelete span.status').hide()
        if (parseInt(data.Status) > 0) {
          jQuery('div.DatabaseDelete input.HasDeleted').val(1)
          jQuery('div.DatabaseDelete span.StatusOk').show()
          jQuery('div.DatabaseDelete a.button').hide()
          jQuery('div.DatabaseDelete a.buttonClose').show()
        } else {
          jQuery('div.DatabaseDelete input.HasDeleted').val(0)
          jQuery('div.DatabaseDelete span.StatusError').show()
          setTimeout(function() {
            jQuery('div.DatabaseDelete span.status').hide()
          }, 3000)
        }
      },
      'json'
    )
    event.preventDefault()
  })
  jQuery('div.DatabaseDelete').on('click', 'a.buttonCancel', function(event) {
    jQuery('div.DatabaseDelete input.HasDeleted').val(0)
    Ext.getCmp('wndDatabaseDelete').hide()
    event.preventDefault()
  })
  jQuery('div.DatabaseDelete').on('click', 'a.buttonClose', function(event) {
    Ext.getCmp('wndDatabaseDelete').hide()
    event.preventDefault()
  })
}

function doDatabaseDeleteCreate() {
  var w = new Ext.Window({
    id: 'wndDatabaseDelete',
    el: 'divDatabaseDeleteWin',
    layout: 'fit',
    width: 450,
    height: 200,
    modal: false,
    constrainHeader: true,
    plain: false,
    animCollapse: false,
    closeAction: 'hide',
    items: new Ext.Panel({
      contentEl: 'divDatabaseDeleteContent',
      autoScroll: true
    })
  })
  w.on('hide', function() {
    var HasDeleted = parseInt(
      jQuery('div.DatabaseDelete input.HasDeleted').val()
    )
    if (HasDeleted > 0) {
      doUpdateDatabaseListByUser()
    }
  })
}

function doDatabaseDeleteShow() {
  // Initialization
  jQuery('div.DatabaseDelete span.status').hide()
  jQuery('div.DatabaseDelete a.button').show()
  jQuery('div.DatabaseDelete a.buttonClose').hide()
  jQuery('div.DatabaseDelete input.HasDeleted').val(0)
  //Show
  Ext.getCmp('wndDatabaseDelete').show()
}

function doUpdateDatabaseListByUser() {
  jQuery('.contentBlock').hide()
  jQuery('#divRegionList').show()
  // Hide everything at start...
  jQuery('.databaseTitle').hide()
  jQuery('.databaseList').hide()

  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdSearchDB',
      searchDBQuery: '',
      searchDBCountry: 0
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        if (parseInt(data.NoOfDatabases) > 0) {
          jQuery('#divDatabaseFindList').show()
          jQuery('#divDatabaseFindError').hide()

          jQuery('#divDatabaseFindList table.databaseList').each(function() {
            jQuery('tr:gt(0)', this).remove()
            jQuery('tr', this).hide()
          })
          jQuery.each(data.RegionList, function(RegionId, value) {
            jQuery('#divRegionList #title_' + value.Role).show()
            jQuery('#divRegionList #list_' + value.Role).show()
            var list = jQuery('#divRegionList #list_' + value.Role).show()
            var item = jQuery('tr:last', list)
              .clone()
              .show()
            jQuery('td.RegionId', item).text(RegionId)
            jQuery('td span.RegionLabel', item).text(value.RegionLabel)
            jQuery('td a.RegionLink', item).attr(
              'href',
              jQuery('#desinventarURL').val() + '/#' + RegionId + '/'
            )
            list.append(item)
          })
          jQuery('#divDatabaseFindList td.RegionDelete').hide()
          if (jQuery('#desinventarUserRoleValue').val() >= 5) {
            jQuery('#divDatabaseFindList td.RegionDelete').show()
          }
        } else {
          jQuery('#divDatabaseFindList').hide()
          jQuery('#divDatabaseFindError').show()
        }
      }
    },
    'json'
  )
}

export default {
  init,
  doUpdateDatabaseListByUser
}
