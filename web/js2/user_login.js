/* global define,desinventar,jQuery,doUserLoginUpdateMsg,Ext,doUserUpdateInfo,hex_md5
 */
(function(root, factory) {
  'use strict';
  if (typeof define === "function" && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory(require('jquery'));
  } else {
    jQuery.extend(true, desinventar, {
      userLogin: factory(root.jQuery)
    });
  }
}(this, function($) {
  'use strict';
  var me = {};

  function setupBindings() {
    $('#frmUserLogin').on('submit', function() {
      var UserId = $('#fldUserId').val();
      var UserPasswd = $('#fldUserPasswd').val();
      var url = desinventar.config.params.url;
      var sessionId = desinventar.util.getSessionId();

      if ((desinventar.config.flags.mode !== 'devel') &&
          (desinventar.config.flags.general_secure_login)) {
        // Force use of https/ssl for user operations
        url = url.replace(/^http:/g, 'https:');
      }
      doUserLoginUpdateMsg('');

      if (UserId === '' || UserPasswd === '') {
        doUserLoginUpdateMsg('msgEmptyFields');
        return false;
      }
      $.post(url, {
        cmd: 'cmdUserLogin',
        RegionId: $('#desinventarRegionId').val(),
        UserId: UserId,
        UserPasswd: hex_md5(UserPasswd),
        SessionId: sessionId
      }, function(data) {
        if (parseInt(data.Status, 10) < 0) {
          doUserLoginUpdateMsg('msgInvalidPasswd');
          return false;
        }
        doUserLoginUpdateMsg('msgUserLoggedIn');
        $('#fldUserId').val('');
        $('fldUserPasswd').val('');

        // Update UserInfo Fields...
        doUserUpdateInfo(data.User);

        Ext.getCmp('wndUserLogin').hide();
        // Trigger Event and Update User Menu etc.
        $('body').trigger('cmdMainWindowUpdate');
      },
      'json'
      ).fail(function() {
        doUserLoginUpdateMsg('msgConnectionError');
        return false;
      });
      return false;
    });
  }

  me.init = function() {
    setupBindings();
  };
  return me;
}));
