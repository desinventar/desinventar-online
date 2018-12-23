import md5 from 'md5'

const me = {}

me.onReadyUserLogin = () => {
  // hide all status messages on start
  updateUserLoginMsg('')

  // submit form validation and process..
  jQuery('#frmUserLogin').submit(function() {
    doUserLogin()
    return false
  })

  jQuery('body').on('cmdUserGetInfo', function() {
    doUserGetInfo()
  })
}

function doUserGetInfo() {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdUserGetInfo'
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        updateUserLoginMsg('#msgUserLoggedIn')
        // After login, clear passwd field
        jQuery('#fldUserId').val('')
        jQuery('#fldUserPasswd').val('')

        // Update UserInfo Fields...
        jQuery('#desinventarUserId').val(data.User.Id)
        jQuery('#desinventarUserFullName').val(data.User.FullName)

        // Trigger Event and Update User Menu etc.
        jQuery('body').trigger('UserLoggedIn')
      }
    },
    'json'
  )
}

function doUserLogin() {
  var UserId = jQuery('#fldUserId').val()
  var UserPasswd = jQuery('#fldUserPasswd').val()
  if (UserId == '' || UserPasswd == '') {
    updateUserLoginMsg('#msgEmptyFields')
  } else {
    const url = jQuery('#desinventarURL').val() + '/'
    jQuery.post(
      url,
      {
        cmd: 'cmdUserLogin',
        UserId: UserId,
        UserPasswd: md5(UserPasswd)
      },
      function(data) {
        if (parseInt(data.Status) > 0) {
          updateUserLoginMsg('#msgUserLoggedIn')
          // After login, clear passwd field
          jQuery('#fldUserId').val('')
          jQuery('#fldUserPasswd').val('')

          // Update UserInfo Fields...
          jQuery('#desinventarUserId').val(data.User.Id)
          jQuery('#desinventarUserFullName').val(data.User.FullName)

          // Trigger Event and Update User Menu etc.
          jQuery('body').trigger('UserLoggedIn')
        } else {
          updateUserLoginMsg('#msgInvalidPasswd')
        }
      },
      'json'
    )
  }
}

me.doUserLogout = () => {
  var Answer = 0
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdUserLogout'
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        Answer = 1
        updateUserLoginMsg('#msgUserLoggedOut')
        // After login, clear passwd field
        jQuery('#fldUserId').val('')
        jQuery('#fldUserPasswd').val('')

        // Update UserInfo Fields...
        jQuery('#desinventarUserId').val('')
        jQuery('#desinventarUserFullName').val('')

        // Trigger Event, used to update menu or reload page...
        jQuery('body').trigger('UserLoggedOut')
      } else {
        updateUserLoginMsg('#msgInvalidLogout')
        Answer = 0
      }
    },
    'json'
  )
  return Answer
}

function updateUserLoginMsg(msgId) {
  // Hide all status Msgs (class="status")
  jQuery('.status').hide()
  if (msgId != '') {
    // Show specified message(s)
    jQuery(msgId).show()
  }
  return true
}

export default me
