desinventar.util = (function () {
    'use strict';
    var me = {};

    me.getSessionId = function() {
        //var cookie = "__utma=9878891.594890843.1442926712.1456443486.1456449813.12; __utmz=9878891.1442926712.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utma=180782520.972382592.1449794482.1449794482.1452009495.2; __utmz=180782520.1449794482.1.1.utmcsr=centos|utmccn=(referral)|utmcmd=referral|utmcct=/; __utmb=9878891.12.10.1456449813; DESINVENTAR_SSID=lg4jnjrvhfeofde0n9g996di13; __utmc=9878891; __utmt=1";
        var cookie = document.cookie;
        var sessionId = '';
        var ca = cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i].split('=',2);
            var key = c[0].trim();
            var value = c[1].trim();
            if (key === 'DESINVENTAR_SSID') {
                sessionId = value;
                break;
            }
        }
        return sessionId;
    };
    return me;
}());
