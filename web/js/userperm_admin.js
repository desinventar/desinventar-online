function onReadyUserPermAdmin() {
  doUserPermAdminSetup()
}

function doUserPermAdminSetup() {
  // Database Create
  var w = new Ext.Window({
    id: 'wndUserPermAdmin',
    el: 'divUserPermAdminWin',
    layout: 'fit',
    width: 500,
    height: 250,
    modal: false,
    closeAction: 'hide',
    plain: false,
    animCollapse: true,
    constrainHeader: true,
    items: new Ext.Panel({
      contentEl: 'divUserPermAdminContent',
      autoScroll: true
    }),
    buttons: [] //buttons
  })

  // Cancel Button - Hide Window and do nothing
  jQuery('#btnUserPermAdminCancel').click(function() {
    Ext.getCmp('wndUserPermAdmin').hide()
    return false
  })

  // Send Button - Validate data and send command to backend
  jQuery('#btnUserPermAdminSend').click(function() {
    iReturn = doUserPermAdminValidate()
    if (iReturn > 0) {
      jQuery('#btnUserPermAdminSend').attr('readonly', true)
      jQuery.post(
        jQuery('#desinventarURL').val() + '/',
        {
          cmd: 'cmdDatabaseSetUserAdmin',
          RegionId: jQuery('#desinventarRegionId').val(),
          UserId: jQuery('#fldUserPermAdmin_UserId').val()
        },
        function(data) {
          jQuery('.clsUserPermAdminStatus').hide()
          if (parseInt(data.Status) > 0) {
            doUserPermAdminUpdateUserAdmin(data.UserAdmin)
            jQuery('#btnUserPermAdminSend').attr('readonly', false)
            jQuery('#txtUserPermAdminOk').show()
          } else {
            jQuery('#txtUserPermAdminError').show()
          }
          setTimeout(function() {
            jQuery('.clsUserPermAdminStatus').hide()
          }, 2500)
        },
        'json'
      )
    } else {
      jQuery('#txtUserPermAdminFormError').show()
      setTimeout(function() {
        jQuery('.clsUserPermAdminStatus').hide()
      }, 2500)
    }
    return false
  })

  // Hide Send button until the combobox has been populated
  jQuery('#btnUserPermAdminSend').hide()
}

function doUserPermAdminShow() {
  jQuery('.clsUserPermAdminStatus').hide()

  // In this windows, always update information
  doUserPermAdminPopulateLists()

  Ext.getCmp('wndUserPermAdmin').show()
  jQuery('#fldUserPermAdmin_UserId').focus()
}

function doUserPermAdminValidate() {
  var iReturn = 1
  if (iReturn > 0 && jQuery('#fldUserPermAdmin_UserId').val() == '') {
    iReturn = -1
  }
  return iReturn
}

function doUserPermAdminUpdateUserAdmin(UserAdmin) {
  jQuery('#fldUserPermAdmin_UserId').val(UserAdmin.UserId)
  var txtUserAdmin = UserAdmin.UserFullName
  if (UserAdmin.UserEMail != '') {
    txtUserAdmin = txtUserAdmin + '<br />' + UserAdmin.UserEMail
  }
  jQuery('#txtUserPermAdminCurrent').html(txtUserAdmin)
}

function doUserPermAdminPopulateLists() {
  // async UserList field
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdGetUserPermList',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        jQuery('#fldUserPermAdmin_UserId').empty()
        jQuery.each(data.UserList, function(key, value) {
          jQuery('#fldUserPermAdmin_UserId').append(
            jQuery('<option>', { value: key }).text(value)
          )
        })
        doUserPermAdminUpdateUserAdmin(data.UserAdmin)
        jQuery('#btnUserPermAdminSend').show()
      }
    },
    'json'
  )
}
