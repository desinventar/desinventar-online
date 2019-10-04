/* global desinventar, Ext */

import util from '../js/util'
import md5 from 'md5'

function init() {
  // hide all status messages on start
  doUserLoginUpdateMsg('')

  jQuery('body').on('cmdUserLoginShow', function() {
    doUserLoginShow()
  })

  jQuery('body').on('cmdUserLogout', function() {
    doUserLogout()
  })

  jQuery('body').on('UserLoggedIn', function() {
    jQuery('body').trigger('cmdWindowReload')
  })

  jQuery('body').on('UserLoggedOut', function() {
    jQuery('body').trigger('cmdWindowReload')
  })

  jQuery('div.UserLogin')
    .on('click', 'a.Send', function(e) {
      jQuery('#frmUserLogin').trigger('submit')
      e.stopPropagation()
    })
    .on('click', 'a.Cancel', function(e) {
      var w = Ext.getCmp('wndUserLogin')
      if (w != undefined) {
        w.hide()
      }
      e.stopPropagation()
    })

  jQuery('#fldUserPasswd').keypress(function(e) {
    var code = e.keyCode ? e.keyCode : e.which
    if (code == 13) {
      jQuery('div.UserLogin a.Send').trigger('click')
    }
  })

  jQuery('#frmUserLogin').on('submit', function() {
    var UserId = jQuery('#fldUserId').val()
    var UserPasswd = jQuery('#fldUserPasswd').val()
    var url = desinventar.config.params.url
    var sessionId = util.getSessionId()

    if (
      desinventar.config.flags.mode !== 'devel' &&
      desinventar.config.flags.general_secure_login
    ) {
      // Force use of https/ssl for user operations
      url = url.replace(/^http:/g, 'https:')
    }
    doUserLoginUpdateMsg('')

    if (UserId === '' || UserPasswd === '') {
      doUserLoginUpdateMsg('msgEmptyFields')
      return false
    }
    jQuery
      .post(
        url,
        {
          cmd: 'cmdUserLogin',
          RegionId: jQuery('#desinventarRegionId').val(),
          UserId: UserId,
          UserPasswd: md5(UserPasswd),
          SessionId: sessionId
        },
        function(data) {
          if (parseInt(data.Status, 10) < 0) {
            doUserLoginUpdateMsg('msgInvalidPasswd')
            return false
          }
          doUserLoginUpdateMsg('msgUserLoggedIn')
          jQuery('#fldUserId').val('')
          jQuery('fldUserPasswd').val('')

          // Update UserInfo Fields...
          doUserUpdateInfo(data.User)

          Ext.getCmp('wndUserLogin').hide()
          // Trigger Event and Update User Menu etc.
          jQuery('body').trigger('cmdMainWindowUpdate')
        },
        'json'
      )
      .fail(function() {
        doUserLoginUpdateMsg('msgConnectionError')
        return false
      })
    return false
  })

  //Initialization code
  doUserLoginCreate()
}

function doUserLoginCreate() {
  // User Login Window
  jQuery('#divUserLoginWindow').each(function() {
    new Ext.Window({
      id: 'wndUserLogin',
      el: 'divUserLoginWindow',
      layout: 'fit',
      x: 300,
      y: 100,
      width: 500,
      height: 300,
      closeAction: 'hide',
      plain: true,
      animCollapse: false,
      constrainHeader: true,
      items: new Ext.Panel({
        contentEl: 'divUserLoginContent',
        autoScroll: true
      })
    })
  })
}

function doUserUpdateInfo(User) {
  jQuery('#desinventarUserId').val(User.Id)
  jQuery('#desinventarUserFullName').val(User.FullName)
  jQuery('#desinventarUserRole').val(User.Role)
  jQuery('#desinventarUserRoleValue').val(User.RoleValue)
}

function doUserLogout() {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdUserLogout',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        doUserLoginUpdateMsg('msgUserLoggedOut')
        // After login, clear passwd field
        jQuery('#fldUserId').val('')
        jQuery('#fldUserPasswd').val('')
        doUserUpdateInfo(data.User)
        // Trigger Event, used to update menu or reload page...
        jQuery('body').trigger('cmdMainWindowUpdate')
      } else {
        doUserLoginUpdateMsg('msgInvalidLogout')
      }
    },
    'json'
  )
}

function doUserLoginUpdateMsg(classId) {
  // Hide all status Msgs (class="status")
  jQuery('div.UserLogin span.status').hide()
  if (classId != '') {
    // Show specified message(s)
    jQuery('div.UserLogin span.' + classId).show()
  }
  return true
}

function doUserLoginShow() {
  doUserLoginUpdateMsg()
  jQuery('#fldUserId').val('')
  jQuery('#fldUserPasswd').val('')
  var w = Ext.getCmp('wndUserLogin')
  if (w != undefined) {
    w.show()
  }
}

export default {
  init
}
