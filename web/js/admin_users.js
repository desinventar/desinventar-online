import md5 from 'md5'

export default {
  init: onReadyAdminUsers
}

function onReadyAdminUsers() {
  jQuery('div.AdminUsers table.UserList tbody')
    .on('mouseover', 'tr', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr', function() {
      jQuery(this).removeClass('highlight')
    })
    .on('click', 'tr', function() {
      var UserId = jQuery('.UserId', this).text().trim()
      jQuery.post(
        jQuery('#desinventarURL').val() + '/user.php',
        {
          cmd: 'getUserInfo',
          UserId: UserId
        },
        function(data) {
          jQuery('#divAdminUsers #txtUserId').attr('readonly', 'true')
          jQuery('#divAdminUsers #txtUserId').val(data.UserId)
          jQuery('#divAdminUsers #selCountryIso').val(data.CountryIso)
          jQuery('#divAdminUsers #txtUserEMail')
            .unhighlight()
            .val(data.UserEMail)
          jQuery('#divAdminUsers #txtUserFullName')
            .unhighlight()
            .val(data.UserFullName)
          jQuery('#divAdminUsers #txtUserCity').val(data.UserCity)
          jQuery('#divAdminUsers input.new_passwd')
            .unhighlight()
            .val('')
          jQuery('#divAdminUsers #chkUserActive').prop(
            'checked',
            data.UserActive
          )
          jQuery('#divAdminUsers #txtUserEditCmd').val('update')
        },
        'json'
      )
      UserEditFormUpdateStatus('')
      jQuery('#divAdminUsers #divUserEdit').show()
    })

  // Add new User...
  jQuery('body').on('click', '#divAdminUsers #btnUserAdd', function() {
    clearUserEditForm()
    jQuery('#divAdminUsers #txtUserId').removeAttr('readonly')
    jQuery('#divAdminUsers #txtUserEditCmd').val('insert')
    UserEditFormUpdateStatus('')
    jQuery('#divAdminUsers #divUserEdit').show()
  })

  // Cancel Edit, hide form
  jQuery('body').on('click', '#divAdminUsers #btnUserEditCancel', function() {
    jQuery('#divAdminUsers #divUserEdit').hide()
  })

  // Submit and hide form
  jQuery('body').on('click', '#divAdminUsers #btnUserEditSubmit', function() {
    jQuery('#divAdminUsers #frmUserEdit').trigger('submit')
  })

  // Submit - Finish edit, validate form and send data...
  jQuery('body').on('submit', '#divAdminUsers #frmUserEdit', function() {
    UserEditFormUpdateStatus('')
    // validate Form
    var bReturn = validateUserEditForm()
    if (bReturn > 0) {
      // Remove the readonly attribute, this way the data is sent to processing
      jQuery('#divAdminUsers #txtUserId').removeAttr('readonly')

      var newPasswd = jQuery('#txtUserPasswd1')
        .val()
        .trim()
      if (newPasswd !== '') {
        newPasswd = md5(newPasswd)
      }
      jQuery('#fldNewUserPasswd').val(newPasswd)

      // Create an object with the information to send
      var user = jQuery('#divAdminUsers #frmUserEdit').serializeObject()
      // Checkboxes not selected are not passed by default to server, so we need
      // to checkout and set a value here.
      if (!jQuery('#divAdminUsers #chkUserActive').prop('checked')) {
        user['User[UserActive]'] = 'off'
      }
      // Send AJAX request to update information
      jQuery.post(
        jQuery('#desinventarURL').val() + '/user.php',
        user,
        function(data) {
          if (parseInt(data.Status) > 0) {
            // Reload user list on success
            jQuery('div.AdminUsers').trigger('cmdLoadData')
          }
          UserEditFormUpdateStatus(data.Status)
        },
        'json'
      )
    }
    return false
  })
  doAdminUsersReset()
  // Populate Country List
  jQuery('#desinventarCountryList option').each(function() {
    jQuery('#selCountryIso').append(
      jQuery('<option>', { value: jQuery(this).attr('value') }).text(
        jQuery(this).text()
      )
    )
  })

  jQuery('div.AdminUsers').on('cmdLoadData', function() {
    jQuery.post(
      jQuery('#desinventarURL').val() + '/',
      {
        cmd: 'cmdAdminUsersGetList'
      },
      function(data) {
        if (parseInt(data.Status) > 0) {
          var user_list = jQuery('div.AdminUsers table.UserList tbody')
          user_list.find('tr:gt(0)').remove()
          jQuery.each(data.UserList, function(key, value) {
            var clone = jQuery('tr:first', user_list)
              .clone()
              .show()
            jQuery('.UserId', clone).text(value.UserId).attr('data-id', value.UserId)
            jQuery('.UserFullName', clone).text(value.UserFullName)
            jQuery('.UserEMail', clone).text(value.UserEMail)
            jQuery('.UserActive', clone).text(value.UserActive)
            jQuery('.UserActiveCheckbox', clone).prop(
              'checked',
              parseInt(value.UserActive) === 1
            )
            user_list.append(clone)
          })
        }
        doAdminUsersReset()
      },
      'json'
    )
  })
}

function doAdminUsersReset() {
  // Start with Edit form hidden
  jQuery('#divAdminUsers #divUserEdit').hide()
  // Create table stripes
  jQuery('#divAdminUsers #tblUserList tr:odd').addClass('normal')
  jQuery('#divAdminUsers #tblUserList tr:even').addClass('under')
  jQuery('#divAdminUsers #txtUserId').removeAttr('readonly')
}

function UserEditFormUpdateStatus(value) {
  jQuery('#divAdminUsers .UserEditFormStatus').hide()
  var MsgId = ''
  switch (value) {
    case 1:
      MsgId = 'UserEditFormStatusOk'
      break
    case -1:
      MsgId = 'UserEditFormStatusError'
      break
    case -100:
      MsgId = 'UserEditFormStatusDuplicateId'
      break
    case -101:
      MsgId = 'UserEditFormStatusEmptyId'
      break
  }
  if (MsgId != '') {
    jQuery('#divAdminUsers #' + MsgId).show()
  }
}

function validateUserEditForm() {
  var bReturn = 1
  jQuery('#divAdminUsers #txtUserId').unhighlight()
  if (jQuery('#divAdminUsers #txtUserId').val() == '') {
    bReturn = -101
    jQuery('#divAdminUsers #txtUserId').highlight()
  }
  jQuery('#divAdminUsers #txtUserFullName').unhighlight()
  if (jQuery('#divAdminUsers #txtUserFullName').val() == '') {
    bReturn = -101
    jQuery('#divAdminUsers #txtUserFullName').highlight()
  }
  jQuery('#divAdminUsers #txtUserEMail').unhighlight()
  if (jQuery('#divAdminUsers #txtUserEMail').val() == '') {
    bReturn = -101
    jQuery('#divAdminUsers #txtUserEMail').highlight()
  }
  jQuery('#divAdminUsers input.new_passwd').unhighlight()
  if (
    jQuery('#txtUserPasswd1')
      .val()
      .trim() != '' &&
    jQuery('#txtUserPasswd1')
      .val()
      .trim() !=
      jQuery('#txtUserPasswd2')
        .val()
        .trim()
  ) {
    bReturn = -101
    jQuery('#divAdminUsers input.new_passwd').highlight()
  }

  UserEditFormUpdateStatus(bReturn)
  return bReturn
}

function clearUserEditForm() {
  jQuery('#divAdminUsers #txtUserId').val('')
  jQuery('#divAdminUsers #selCountryIso').val('')
  jQuery('#divAdminUsers #txtUserEMail').val('')
  jQuery('#divAdminUsers #txtUserFullName').val('')
  jQuery('#divAdminUsers #txtUserCity').val('')
  jQuery('#divAdminUsers #chkUserActive').prop('checked', '')
  jQuery('#divAdminUsers input.new_passwd')
    .unhighlight()
    .val('')
}
