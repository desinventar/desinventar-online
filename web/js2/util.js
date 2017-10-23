/* global desinventar */
desinventar.util = (function() {
  "use strict";
  var me = {};

  me.getSessionId = function() {
    var cookie = document.cookie;
    var sessionId = "";
    var ca = cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i].split("=", 2);
      var key = c[0].trim();
      var value = c[1].trim();
      if (key === "DESINVENTAR_SSID") {
        sessionId = value;
        break;
      }
    }
    return sessionId;
  };
  return me;
})();
