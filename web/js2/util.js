desinventar.util = (function () {
    'use strict';
    var me = {};

    me.getSessionId = function() {
        var sessionId = '';
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i].split('=',2);
            var key = c[0];
            var value = c[1];
            if (key === 'DESINVENTAR_SSID') {
                sessionId = value;
                break;
            }
        }
        return sessionId;
    };
    return me;
}());
