/* global Ext */
function init() {
  jQuery('.clsDatabaseUsersStatus').hide()
  jQuery('#divDatabaseUsers_Edit').hide()

  jQuery('#frmDiffusion .RegionActiveText').click(function() {
    jQuery('#frmDiffusion .RegionActive').trigger('click')
    return false
  })
  jQuery('#frmDiffusion .RegionPublicText').click(function() {
    jQuery('#frmDiffusion .RegionPublic').trigger('click')
    return false
  })

  jQuery('#frmDiffusion .btnCancel').click(function() {
    var RegionInfo = new Array()
    RegionInfo.RegionStatus = jQuery('#fldDatabaseUsers_RegionStatus').val()
    doDatabaseUsersUpdateOptions(RegionInfo)
    return false
  })

  jQuery('#frmDiffusion .btnSave').click(function() {
    var RegionStatus = jQuery('#frmDiffusion .RegionStatus')
    RegionStatus.val(0)
    if (jQuery('#frmDiffusion .RegionActive').attr('checked')) {
      RegionStatus.val(parseInt(RegionStatus.val()) | 1)
    }
    if (jQuery('#frmDiffusion .RegionPublic').attr('checked')) {
      RegionStatus.val(parseInt(RegionStatus.val()) | 2)
    }
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdDatabaseUsersUpdateOptions',
        RegionId: jQuery('#desinventarRegionId').val(),
        RegionStatus: jQuery('#frmDiffusion .RegionStatus').val(),
        RegionOrder: jQuery('#frmDiffusion .RegionOrder').val()
      },
      function(data) {
        jQuery('.clsDatabaseUsersStatus').hide()
        if (data.Status) {
          doDatabaseUsersUpdateOptions(data.RegionInfo)
          jQuery('#txtDatabaseUsers_OptionsStatusOk').show()
        } else {
          jQuery('#txtDatabaseUsers_OptionsStatusError').show()
        }
        setTimeout(function() {
          jQuery('.clsDatabaseUsersStatus').hide()
        }, 3000)
      },
      'json'
    )
    return false
  })

  jQuery('#btnDatabaseUsers_Add').click(function() {
    doDatabaseUsersReset()
    jQuery('#divDatabaseUsers_Edit').show()
    return false
  })

  jQuery('#frmUsers .btnCancel').click(function() {
    doDatabaseUsersReset()
    jQuery('#divDatabaseUsers_Edit').hide()
    return false
  })

  jQuery('#frmUsers .btnSave').click(function() {
    var bContinue = true
    //Validation
    if (jQuery('#frmUsers .UserId').val() == '') {
      // Error Message
      jQuery('.clsDatabaseUsersStatus').hide()
      jQuery('#txtDatabaseUsers_RoleListEmptyFields').show()
      bContinue = false
      setTimeout(function() {
        jQuery('.clsDatabaseUsersStatus').hide()
      }, 3000)
    }
    if (bContinue) {
      jQuery('#frmUsers').data('ReloadPageAfter', false)
      // User cannot remove his/her own AdminRegion permission
      if (
        jQuery('#frmUsers .UserId').val() == jQuery('#desinventarUserId').val()
      ) {
        if (
          jQuery('#frmUsers .UserRolePrev').val() == 'ADMINREGION' &&
          jQuery('#frmUsers .UserRolePrev').val() !=
            jQuery('#frmUsers .UserRole').val()
        ) {
          bContinue = false
          jQuery('#frmUsers .UserRole').val('ADMINREGION')
          jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').show()
          setTimeout(function() {
            jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').hide()
          }, 2500)
        }
      }

      // If trying to assign a new AdminRegion confirm the change
      if (
        jQuery('#desinventarUserId').val() != 'root' &&
        jQuery('#desinventarUserId').val() != jQuery('#frmUsers .UserId').val()
      ) {
        if (jQuery('#frmUsers .UserRole').val() == 'ADMINREGION') {
          bContinue = false
          const sAdminNew = jQuery(
            '#frmUsers .UserId option[value="' +
              jQuery('#frmUsers .UserId').val() +
              '"]'
          ).text()

          var sAdminCurrent = ''
          jQuery('#tbodyDatabaseUsers_List tr:gt(0)').each(function() {
            if (jQuery('.UserRole', this).text() == 'ADMINREGION') {
              sAdminCurrent = jQuery('.UserName', this).text()
            }
          })

          const sConfirmMsg =
            jQuery('#msgDatabaseUsers_ConfirmManagerPrompt1').text() +
            ' ' +
            sAdminCurrent +
            ' ' +
            jQuery('#msgDatabaseUsers_ConfirmManagerPrompt2').text() +
            ' ' +
            sAdminNew +
            ' ?'
          Ext.Msg.show({
            title: jQuery('#msgDatabaseUsers_ConfirmManagerTitle').text(),
            msg: sConfirmMsg,
            buttons: {
              ok: jQuery('#msgDatabaseUsers_Yes').text(),
              cancel: jQuery('#msgDatabaseUsers_No').text()
            },
            fn: function(whichButton) {
              if (whichButton == 'ok') {
                jQuery('#frmUsers').data('ReloadPageAfter', true)
                jQuery('#frmUsers').trigger('submit')
              }
            }
          })
        }
      }
      if (bContinue) {
        jQuery('#frmUsers').trigger('submit')
      }
    }
    return false
  })

  jQuery('#frmUsers').submit(function() {
    doDatabaseUsersUpdateRole(
      jQuery('#frmUsers .UserId').val(),
      jQuery('#frmUsers .UserRole').val()
    )
    return false
  })

  jQuery('#tbodyDatabaseUsers_List')
    .on('click', 'a.delete', function(e) {
      e.stopPropagation()
      e.preventDefault()
      var row = jQuery(this).closest('tr')
      var userId = row.find('td.UserId').text()
      doDatabaseUsersUpdateRole(userId, 'NONE')
    })
    .on('click', 'tr', function(e) {
      var UserId = jQuery.trim(jQuery('.UserId', this).text())
      var UserRole = jQuery('.UserRole', this).text()
      jQuery('#frmUsers .UserId')
        .val(UserId)
        .trigger('change')
        .prop('disabled', false)
      jQuery('#frmUsers .UserRole')
        .val(UserRole)
        .trigger('change')
        .prop('disabled', false)
      jQuery('#frmUsers .UserRolePrev').val(UserRole)

      jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').hide()
      jQuery('#divDatabaseUsers_Edit').show()
      e.stopPropagation()
      e.preventDefault()
    })
    .on('mouseover', 'tr', function(event) {
      jQuery(this).addClass('highlight')
      event.preventDefault()
    })
    .on('mouseout', 'tr', function(event) {
      jQuery(this).removeClass('highlight')
      event.preventDefault()
    })

  jQuery('#frmUsers .UserId').change(function() {
    jQuery('#frmUsers .UserRole')
      .val('NONE')
      .trigger('change')
  })

  jQuery('body').on('cmdDatabaseUsersShow', function() {
    doDatabaseUsersPopulateLists()
  })
}

function doDatabaseUsersReset() {
  jQuery('#frmDiffusion .RegionActive').prop('checked', true)
  jQuery('#frmDiffusion .RegionPublic').prop('checked', true)
  jQuery('#frmUsers .UserId').val('')
  jQuery('#frmUsers .UserRole').val('')
  jQuery('#frmUsers .UserId').prop('disabled', false)
  jQuery('#frmUsers .UserRole').prop('disabled', false)
  jQuery('#tblDatabaseUsers_List .UserId').hide()
  jQuery('#tblDatabaseUsers_List .UserRole').hide()
  jQuery('.clsDatabaseUsersStatus').hide()
}

function doDatabaseUsersPopulateUserRoleList(UserRoleList) {
  jQuery('#tbodyDatabaseUsers_List')
    .find('tr:gt(0)')
    .remove()
  jQuery('#tbodyDatabaseUsers_List')
    .find('tr')
    .hide()

  jQuery.each(UserRoleList, function(index, value) {
    var clonedRow = jQuery('#tbodyDatabaseUsers_List tr:last')
      .clone()
      .show()
    jQuery('.UserId', clonedRow).html(index)
    jQuery('.UserName', clonedRow).html(value.UserName)
    jQuery('.UserRole', clonedRow).html(value.UserRole)
    jQuery('.UserRoleLabel', clonedRow).html(
      jQuery(
        '#frmUsers .UserRole option[value="' + value.UserRole + '"]'
      ).text()
    )
    jQuery('#tbodyDatabaseUsers_List').append(clonedRow)
  })
  jQuery('#tblDatabaseUsers_List .UserId').hide()
  jQuery('#tblDatabaseUsers_List .UserRole').hide()

  jQuery('#tbodyDatabaseUsers_List tr:even').addClass('under')
}

function doDatabaseUsersUpdateOptions(RegionInfo) {
  jQuery('#frmDiffusion .RegionStatus').val(RegionInfo.RegionStatus)
  // RegionActive/RegionPublic are set based on RegionStatus value
  jQuery('#frmDiffusion .RegionActive').prop('checked', false)
  if (parseInt(RegionInfo.RegionStatus) & 1) {
    jQuery('#frmDiffusion .RegionActive').prop('checked', true)
  }
  jQuery('#frmDiffusion .RegionPublic').prop('checked', false)
  if (parseInt(RegionInfo.RegionStatus) & 2) {
    jQuery('#frmDiffusion .RegionPublic').prop('checked', true)
  }
  jQuery('#frmDiffusion .RegionOrder').val(RegionInfo.RegionOrder)
}

function doDatabaseUsersPopulateLists() {
  jQuery('body').trigger('cmdMainWaitingShow')
  doDatabaseUsersReset()
  jQuery('#frmUsers .UserId').empty()
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseUsersGetList',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        doDatabaseUsersUpdateOptions(data.RegionInfo)
        doDatabaseUsersPopulateUserRoleList(data.UserRoleList)
        var list = []
        jQuery.each(data.UserList, function(key, value) {
          list.push({ id: key, text: value })
        })
        jQuery('#frmUsers .UserId').select2({
          data: list,
          placeHolder: 'Select a user'
        })
        jQuery('#frmUsers .UserRole').select2({
          placeHolder: 'Select a role'
        })
      }
      jQuery('body').trigger('cmdMainWaitingHide')
    },
    'json'
  )
}

function doDatabaseUsersUpdateRole(userId, userRole) {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseUsersSetRole',
      RegionId: jQuery('#desinventarRegionId').val(),
      UserId: userId,
      UserRole: userRole
    },
    function(data) {
      jQuery('.clsDatabaseUsersStatus').hide()
      if (parseInt(data.Status) > 0) {
        doDatabaseUsersPopulateUserRoleList(data.UserRoleList)
        jQuery('#divDatabaseUsers_Edit').hide()
        jQuery('#txtDatabaseUsers_RoleListStatusOk').show()
      } else {
        jQuery('#txtDatabaseUsers_RoleListStatusError').show()
      }
      setTimeout(function() {
        jQuery('.clsDatabaseUsersStatus').hide()
      }, 3000)
      var ReloadPageAfter = jQuery('#frmUsers').data('ReloadPageAfter')
      if (ReloadPageAfter) {
        window.location.reload(false)
      }
    },
    'json'
  )
}

export default {
  init
}
