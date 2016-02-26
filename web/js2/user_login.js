/*global desinventar,jQuery
 */
desinventar.user_login = (function () {
    'use strict';
    var me = {};

    function setupBindings() {
        jQuery('#frmUserLogin').on('submit', function () {
            var UserId     = jQuery('#fldUserId').val(),
                UserPasswd = jQuery("#fldUserPasswd").val(),
                url = desinventar.config.params.url,
                sessionId = desinventar.util.getSessionId();

            if (desinventar.config.flags.general_secure_login) {
                // Force use of https/ssl for user operations
                url = url.replace(/^http:/g,"https:");
            }
            doUserLoginUpdateMsg('');

            if (UserId === '' || UserPasswd === '') {
                doUserLoginUpdateMsg('msgEmptyFields');
                return false;
            }
            jQuery.post(url,
                {
                    'cmd'        : 'cmdUserLogin',
                    'RegionId'   : jQuery('#desinventarRegionId').val(),
                    'UserId'     : UserId,
                    'UserPasswd' : hex_md5(UserPasswd),
                    'SessionId'  : sessionId
                },
                function (data) {
                    if (parseInt(data.Status, 10) < 0) {
                        doUserLoginUpdateMsg('msgInvalidPasswd');
                        return false;
                    }
                    doUserLoginUpdateMsg('msgUserLoggedIn');
                    jQuery('#fldUserId').val('');
                    jQuery("#fldUserPasswd").val('');

                    // Update UserInfo Fields...
                    doUserUpdateInfo(data.User);

                    Ext.getCmp('wndUserLogin').hide();
                    // Trigger Event and Update User Menu etc.
                    jQuery('body').trigger('cmdMainWindowUpdate');
                },
                'json'
            );
            return false;
        });
    }

    me.init = function () {
        setupBindings();
        //do other important setup things
    };
    return me;
}());
