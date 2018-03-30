/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__main__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__user_login__ = __webpack_require__(5);
// portal - entry.js
var ready = __webpack_require__(2);



ready().then(function () {
  __WEBPACK_IMPORTED_MODULE_0__main__["a" /* default */].init();
  __WEBPACK_IMPORTED_MODULE_1__user_login__["a" /* default */].onReadyUserLogin();
  // user.onReadyPortal()
});

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__common__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__user_login__ = __webpack_require__(5);
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/




var me = {};
me.init = function () {
  var desinventarLang = jQuery('#desinventarLang').val();

  jQuery('#btnMainWindow').attr('href', jQuery('#desinventarURL').val() + '/');

  // Main button to open a default desinventar
  jQuery('#btnMainWindow').click(function () {
    jQuery(this).attr('href', jQuery('#desinventarURL').val() + '/' + '?lang=' + desinventarLang);
    return true;
  });

  // Update version number on screen
  jQuery('#txtVersion').text(jQuery('#desinventarVersion').val());

  // Handle clicks on mainpage map
  jQuery('area').click(function () {
    var country = jQuery(this).attr('alt');
    if (country != '') {
      __WEBPACK_IMPORTED_MODULE_0__common__["a" /* default */].updateDatabaseList(country, 1);
    }
    // Prevent default action
    return false;
  });

  // Handle clicks on RegionGroup Items
  jQuery('.RegionGroup').click(function () {
    var group = jQuery(this).attr('alt');
    if (group != '') {
      var expanded = jQuery(this).attr('expanded');
      if (expanded == 'yes') {
        jQuery('#sect' + group).hide();
        jQuery(this).attr('expanded', 'no');
      } else {
        jQuery('#sect' + group).show();
        jQuery(this).attr('expanded', 'yes');
      }
      //displayList(group);
    }
    return false;
  });

  // Expand some region groups at start
  jQuery('.RegionGroup[expand=yes]').trigger('click');

  // Handle clicks on countries that return a list of regions
  jQuery('.RegionList').click(function () {
    var country = jQuery(this).attr('alt');
    if (country != '') {
      __WEBPACK_IMPORTED_MODULE_0__common__["a" /* default */].updateDatabaseList(country, 1);
    }
    // Prevent default action
    return false;
  });

  // Handle clicks on countries that directly shows a Region
  jQuery('.RegionItem').click(function () {
    var RegionId = jQuery(this).attr('alt');
    if (RegionId != '') {
      showRegionInfo(RegionId);
    }
    // Prevent default action
    return false;
  });

  jQuery('body').bind('UserUpdateInfo', function () {
    var UserName = jQuery('#desinventarUserFullName').val();
    var UserId = jQuery('#desinventarUserId').val();
    if (UserId != '') {
      jQuery('#txtUserInfo').text(UserId + ' ' + UserName);
      jQuery('#txtUserFullName').text(UserName);
      jQuery('#txtUserId').text('(' + UserId + ')');
    } else {
      jQuery('#txtUserInfo').text('');
      jQuery('#txtUserFullName').text('');
      jQuery('#txtUserId').text('');
    }
  });

  jQuery('body').bind('UserLoggedIn', function () {
    jQuery('.lstUserMenu').show();
    jQuery('#lstUserLogin').hide();
    jQuery('#divUserIsLoggedIn').show();
    jQuery('#divUserIsLoggedOut').hide();
    __WEBPACK_IMPORTED_MODULE_0__common__["a" /* default */].updateDatabaseListByUser();
    jQuery('body').trigger('UserUpdateInfo');
  });

  jQuery('body').bind('UserLoggedOut', function () {
    // Update User Menu
    jQuery('.lstUserMenu').show();
    jQuery('#lstUserLogout').hide();
    jQuery('#divUserIsLoggedIn').hide();
    jQuery('#divUserIsLoggedOut').show();
    jQuery('#frmUserLogin').hide();
    jQuery('body').trigger('UserUpdateInfo');
    showMap();
  });

  // Handle clicks on right menu (Home/LangSelect)
  jQuery('.MenuItem').click(function () {
    var MenuItem = jQuery(this).attr('id');
    if (MenuItem == 'mnuShowMap') {
      displayPortal('portal');
      showMap();
    }
    // Prevent default action
    return false;
  });

  jQuery('#linkShowUserLogin').click(function () {
    jQuery('#frmUserLogin').toggle();
    return false;
  });

  jQuery('#linkUserRegionList').click(function () {
    __WEBPACK_IMPORTED_MODULE_0__common__["a" /* default */].updateDatabaseListByUser();
    return false;
  });

  jQuery('#linkUserLogout').click(function () {
    __WEBPACK_IMPORTED_MODULE_1__user_login__["a" /* default */].doUserLogout();
    return false;
  });

  // Top Menu/Language Select
  jQuery('#MainMenu').clickMenu();
  // Remove the black border from the menu
  jQuery('div.cmDiv').css('border', '0px solid black');

  displayPortal(jQuery('#desinventarPortalType').val());

  jQuery('#linkPortalGAR2011').click(function () {
    displayPortal('gar-isdr-2011');
    return false;
  });

  if (jQuery('#desinventarUserId').val() != '') {
    jQuery('body').trigger('UserLoggedIn');
  } else {
    jQuery('body').trigger('UserLoggedOut');
  }

  jQuery('.regionlink').on('click', function () {
    var RegionId = jQuery('#desinventarRegionId').val();
    window.open(jQuery('#desinventarURL').val() + '/#' + RegionId);
    return false;
  });

  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdGetVersion'
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      jQuery('#txtVersion').text(data.Version);
    }
  }, 'json');
  //Initialization code
  jQuery('body').trigger('cmdUserGetInfo');
};

function displayPortal(myPortal) {
  // Select which portal to display : main, gar2009, gar2011
  jQuery('.divBlock').hide();
  jQuery('#desinventarPortalType').val(myPortal);
  switch (myPortal) {
    case 'gar2009':
    case 'gar-isdr-2009':
      jQuery('.divBlockGAR2009').show();
      break;
    case 'gar-isdr-2011':
    case 'gar2011':
      jQuery('.divBlockGAR2011').show();
      break;
    default:
      jQuery('.divBlockSouthAmerica').show();
      // At start, display the map
      showMap();
      break;
  }
}

function showRegionInfo(RegionId) {
  jQuery('.contentBlock').hide();
  jQuery('.contentRegionBlock').hide();
  __WEBPACK_IMPORTED_MODULE_0__common__["a" /* default */].displayRegionInfo(RegionId);
}

function showMap() {
  jQuery('.contentBlock').hide();
  jQuery('#pagemap').show();
}

// personalization List menu..
me.displayList = function (elem) {
  var lst = 7;
  for (var i = 1; i <= lst; i++) {
    if (i == elem) jQuery('#sect' + i).show();else jQuery('#sect' + i).hide();
  }
};

/* harmony default export */ __webpack_exports__["a"] = (me);

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

(function (document, promise) {
  if (true) module.exports = promise
  else document.ready = promise
})(window.document, function (chainVal) {
  'use strict'

  var d = document,
      w = window,
      loaded = /^loaded|^i|^c/.test(d.readyState),
      DOMContentLoaded = 'DOMContentLoaded',
      load = 'load'

  return new Promise(function (resolve) {
    if (loaded) return resolve(chainVal)

    function onReady () {
      resolve(chainVal)
      d.removeEventListener(DOMContentLoaded, onReady)
      w.removeEventListener(load, onReady)
    }

    d.addEventListener(DOMContentLoaded, onReady)
    w.addEventListener(load, onReady)
  })
})


/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

var me = {};

function doGetRegionInfo(RegionId) {
  jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" />');
  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdDatabaseGetInfo',
    RegionId: RegionId,
    LangIsoCode: jQuery('#desinventarLang').val()
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      var i = data.RegionInfo;
      jQuery('#title_COUNTRY').text(i.CountryName).show();
      jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/?cmd=cmdDatabaseGetLogo&RegionId=' + RegionId + '" alt="" />');
      jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel);
      jQuery('#divRegionInfo #txtRegionPeriod').text(i.PeriodBeginDate + ' - ' + i.PeriodEndDate);
      jQuery('#divRegionInfo #txtRegionNumDatacards').text(i.NumberOfRecords);
      jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate);

      jQuery('#divRegionInfo #divInfoGeneral').hide();
      if (i.InfoGeneral != '') {
        jQuery('#divRegionInfo #divInfoGeneral #Text').html(i.InfoGeneral);
        jQuery('#divRegionInfo #divInfoGeneral').show();
      }
      jQuery('#divRegionInfo #divInfoCredits').hide();
      if (i.InfoCredits != '') {
        jQuery('#divRegionInfo #divInfoCredits #Text').html(i.InfoCredits);
        jQuery('#divRegionInfo #divInfoCredits').show();
      }
      jQuery('#divRegionInfo #divInfoSources').hide();
      if (i.InfoSources != '') {
        jQuery('#divRegionInfo #divInfoSources #Text').html(i.InfoSources);
        jQuery('#divRegionInfo #divInfoSources').show();
      }
      jQuery('#divRegionInfo #divInfoSynopsis').hide();
      if (i.InfoSynopsis != '') {
        jQuery('#divRegionInfo #divInfoSynopsis #Text').html(i.InfoSynopsis);
        jQuery('#divRegionInfo #divInfoSynopsis').show();
      }
    }
  }, 'jsonp');
}

me.updateDatabaseList = function (CountryIsoCode) {
  jQuery('.contentBlock').hide();
  // Hide everything at start...
  jQuery('.databaseTitle').hide();
  jQuery('.databaseList').hide();
  jQuery('.contentRegionBlock').hide();
  jQuery('#divRegionList').hide();
  jQuery.get(jQuery('#desinventarURL').val() + '/', {
    cmd: 'getCountryName',
    CountryIso: CountryIsoCode
  }, function (data) {
    jQuery('#title_COUNTRY').text(data.CountryName).show();
  }, 'jsonp');
  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdSearchDB',
    searchDBQuery: CountryIsoCode,
    searchDBCountry: 1
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      var iCount = 0;
      var RegionId = '';

      // Hide everything at start...
      jQuery('.databaseTitle').hide();
      jQuery('.databaseList').hide();

      var jList = jQuery('#list_COUNTRY');
      var myRegionId = '';
      jList.empty();
      jQuery.each(data.RegionList, function (RegionId, value) {
        iCount++;
        jList.append('<a href="#" id="' + RegionId + '" class="databaseLink">' + value.RegionLabel + '</a><br />');
        myRegionId = RegionId;
      });
      if (iCount == 1) {
        // If only one region is in list, show directly info instead of list
        this.displayRegionInfo(myRegionId);
      } else {
        jQuery('#title_COUNTRY').show();
        jQuery('#list_COUNTRY').show();
        jQuery('.databaseLink').addClass('alt').unbind('click').click(function () {
          RegionId = jQuery(this).attr('id');
          this.displayRegionInfo(RegionId);
          return false;
        });
        jQuery('#regionBlock').show();
        jQuery('#divRegionList').show();
      }
    }
  }, 'jsonp');
};

me.updateDatabaseListByUser = function () {
  jQuery('.contentBlock').hide();
  jQuery('#divRegionList').show();
  // Hide everything at start...
  jQuery('.databaseTitle').hide();
  jQuery('.databaseList').hide();
  jQuery('.contentRegionBlock').hide();

  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdSearchDB',
    searchDBQuery: '',
    searchDBCountry: 0
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      var RegionByRole = new Array(5);
      RegionByRole['ADMINREGION'] = new Array();
      RegionByRole['SUPERVISOR'] = new Array();
      RegionByRole['USER'] = new Array();
      RegionByRole['OBSERVER'] = new Array();
      RegionByRole['NONE'] = new Array();

      jQuery('.databaseList').empty();
      jQuery.each(data.RegionList, function (RegionId, value) {
        jQuery('#divRegionList #title_' + value.Role).show();
        jQuery('#divRegionList #list_' + value.Role).show().append('<a href="#" id="' + RegionId + '" class="databaseLink">' + value.RegionLabel + '</a><br />');
      });

      jQuery('.databaseLink').addClass('alt').unbind('click').click(function () {
        var RegionId = jQuery(this).attr('id');
        if (jQuery('#desinventarPortalType').val() != '') {
          this.displayRegionInfo(RegionId);
        } else {
          window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
        }
        return false;
      });
      jQuery('#title_COUNTRY').text('');
      jQuery('#regionBlock').show();
      jQuery('#divRegionList').show();
    }
  }, 'jsonp');
};

me.displayRegionInfo = function (RegionId) {
  jQuery('.contentBlock').hide();
  jQuery('#pageinfo').hide();
  doGetRegionInfo(RegionId);
  jQuery('#desinventarRegionId').val(RegionId);
  jQuery('#regionBlock').show();
  jQuery('#pageinfo').show();
};

/* harmony default export */ __webpack_exports__["a"] = (me);

/***/ }),
/* 4 */
/***/ (function(module, exports) {

var charenc = {
  // UTF-8 encoding
  utf8: {
    // Convert a string to a byte array
    stringToBytes: function(str) {
      return charenc.bin.stringToBytes(unescape(encodeURIComponent(str)));
    },

    // Convert a byte array to a string
    bytesToString: function(bytes) {
      return decodeURIComponent(escape(charenc.bin.bytesToString(bytes)));
    }
  },

  // Binary encoding
  bin: {
    // Convert a string to a byte array
    stringToBytes: function(str) {
      for (var bytes = [], i = 0; i < str.length; i++)
        bytes.push(str.charCodeAt(i) & 0xFF);
      return bytes;
    },

    // Convert a byte array to a string
    bytesToString: function(bytes) {
      for (var str = [], i = 0; i < bytes.length; i++)
        str.push(String.fromCharCode(bytes[i]));
      return str.join('');
    }
  }
};

module.exports = charenc;


/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_md5__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_md5___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_md5__);
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/



var me = {};

me.onReadyUserLogin = function () {
  // hide all status messages on start
  updateUserLoginMsg('');

  // submit form validation and process..
  jQuery('#frmUserLogin').submit(function () {
    this.doUserLogin();
    return false;
  });

  jQuery('body').on('cmdUserGetInfo', function () {
    this.doUserGetInfo();
  });
};

me.doUserGetInfo = function () {
  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdUserGetInfo'
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      updateUserLoginMsg('#msgUserLoggedIn');
      // After login, clear passwd field
      jQuery('#fldUserId').val('');
      jQuery('#fldUserPasswd').val('');

      // Update UserInfo Fields...
      jQuery('#desinventarUserId').val(data.User.Id);
      jQuery('#desinventarUserFullName').val(data.User.FullName);

      // Trigger Event and Update User Menu etc.
      jQuery('body').trigger('UserLoggedIn');
    }
  }, 'json');
};

me.doUserLogin = function () {
  var UserId = jQuery('#fldUserId').val();
  var UserPasswd = jQuery('#fldUserPasswd').val();
  if (UserId == '' || UserPasswd == '') {
    updateUserLoginMsg('#msgEmptyFields');
  } else {
    jQuery.post(jQuery('#desinventarURL').val() + '/', {
      cmd: 'cmdUserLogin',
      UserId: UserId,
      UserPasswd: __WEBPACK_IMPORTED_MODULE_0_md5___default()(UserPasswd)
    }, function (data) {
      if (parseInt(data.Status) > 0) {
        updateUserLoginMsg('#msgUserLoggedIn');
        // After login, clear passwd field
        jQuery('#fldUserId').val('');
        jQuery('#fldUserPasswd').val('');

        // Update UserInfo Fields...
        jQuery('#desinventarUserId').val(data.User.Id);
        jQuery('#desinventarUserFullName').val(data.User.FullName);

        // Trigger Event and Update User Menu etc.
        jQuery('body').trigger('UserLoggedIn');
      } else {
        updateUserLoginMsg('#msgInvalidPasswd');
      }
    }, 'json');
  }
};

me.doUserLogout = function () {
  var Answer = 0;
  jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdUserLogout'
  }, function (data) {
    if (parseInt(data.Status) > 0) {
      Answer = 1;
      updateUserLoginMsg('#msgUserLoggedOut');
      // After login, clear passwd field
      jQuery('#fldUserId').val('');
      jQuery('#fldUserPasswd').val('');

      // Update UserInfo Fields...
      jQuery('#desinventarUserId').val('');
      jQuery('#desinventarUserFullName').val('');

      // Trigger Event, used to update menu or reload page...
      jQuery('body').trigger('UserLoggedOut');
    } else {
      updateUserLoginMsg('#msgInvalidLogout');
      Answer = 0;
    }
  }, 'json');
  return Answer;
};

function updateUserLoginMsg(msgId) {
  // Hide all status Msgs (class="status")
  jQuery('.status').hide();
  if (msgId != '') {
    // Show specified message(s)
    jQuery(msgId).show();
  }
  return true;
}

/* harmony default export */ __webpack_exports__["a"] = (me);

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

(function(){
  var crypt = __webpack_require__(7),
      utf8 = __webpack_require__(4).utf8,
      isBuffer = __webpack_require__(8),
      bin = __webpack_require__(4).bin,

  // The core
  md5 = function (message, options) {
    // Convert to byte array
    if (message.constructor == String)
      if (options && options.encoding === 'binary')
        message = bin.stringToBytes(message);
      else
        message = utf8.stringToBytes(message);
    else if (isBuffer(message))
      message = Array.prototype.slice.call(message, 0);
    else if (!Array.isArray(message))
      message = message.toString();
    // else, assume byte array already

    var m = crypt.bytesToWords(message),
        l = message.length * 8,
        a =  1732584193,
        b = -271733879,
        c = -1732584194,
        d =  271733878;

    // Swap endian
    for (var i = 0; i < m.length; i++) {
      m[i] = ((m[i] <<  8) | (m[i] >>> 24)) & 0x00FF00FF |
             ((m[i] << 24) | (m[i] >>>  8)) & 0xFF00FF00;
    }

    // Padding
    m[l >>> 5] |= 0x80 << (l % 32);
    m[(((l + 64) >>> 9) << 4) + 14] = l;

    // Method shortcuts
    var FF = md5._ff,
        GG = md5._gg,
        HH = md5._hh,
        II = md5._ii;

    for (var i = 0; i < m.length; i += 16) {

      var aa = a,
          bb = b,
          cc = c,
          dd = d;

      a = FF(a, b, c, d, m[i+ 0],  7, -680876936);
      d = FF(d, a, b, c, m[i+ 1], 12, -389564586);
      c = FF(c, d, a, b, m[i+ 2], 17,  606105819);
      b = FF(b, c, d, a, m[i+ 3], 22, -1044525330);
      a = FF(a, b, c, d, m[i+ 4],  7, -176418897);
      d = FF(d, a, b, c, m[i+ 5], 12,  1200080426);
      c = FF(c, d, a, b, m[i+ 6], 17, -1473231341);
      b = FF(b, c, d, a, m[i+ 7], 22, -45705983);
      a = FF(a, b, c, d, m[i+ 8],  7,  1770035416);
      d = FF(d, a, b, c, m[i+ 9], 12, -1958414417);
      c = FF(c, d, a, b, m[i+10], 17, -42063);
      b = FF(b, c, d, a, m[i+11], 22, -1990404162);
      a = FF(a, b, c, d, m[i+12],  7,  1804603682);
      d = FF(d, a, b, c, m[i+13], 12, -40341101);
      c = FF(c, d, a, b, m[i+14], 17, -1502002290);
      b = FF(b, c, d, a, m[i+15], 22,  1236535329);

      a = GG(a, b, c, d, m[i+ 1],  5, -165796510);
      d = GG(d, a, b, c, m[i+ 6],  9, -1069501632);
      c = GG(c, d, a, b, m[i+11], 14,  643717713);
      b = GG(b, c, d, a, m[i+ 0], 20, -373897302);
      a = GG(a, b, c, d, m[i+ 5],  5, -701558691);
      d = GG(d, a, b, c, m[i+10],  9,  38016083);
      c = GG(c, d, a, b, m[i+15], 14, -660478335);
      b = GG(b, c, d, a, m[i+ 4], 20, -405537848);
      a = GG(a, b, c, d, m[i+ 9],  5,  568446438);
      d = GG(d, a, b, c, m[i+14],  9, -1019803690);
      c = GG(c, d, a, b, m[i+ 3], 14, -187363961);
      b = GG(b, c, d, a, m[i+ 8], 20,  1163531501);
      a = GG(a, b, c, d, m[i+13],  5, -1444681467);
      d = GG(d, a, b, c, m[i+ 2],  9, -51403784);
      c = GG(c, d, a, b, m[i+ 7], 14,  1735328473);
      b = GG(b, c, d, a, m[i+12], 20, -1926607734);

      a = HH(a, b, c, d, m[i+ 5],  4, -378558);
      d = HH(d, a, b, c, m[i+ 8], 11, -2022574463);
      c = HH(c, d, a, b, m[i+11], 16,  1839030562);
      b = HH(b, c, d, a, m[i+14], 23, -35309556);
      a = HH(a, b, c, d, m[i+ 1],  4, -1530992060);
      d = HH(d, a, b, c, m[i+ 4], 11,  1272893353);
      c = HH(c, d, a, b, m[i+ 7], 16, -155497632);
      b = HH(b, c, d, a, m[i+10], 23, -1094730640);
      a = HH(a, b, c, d, m[i+13],  4,  681279174);
      d = HH(d, a, b, c, m[i+ 0], 11, -358537222);
      c = HH(c, d, a, b, m[i+ 3], 16, -722521979);
      b = HH(b, c, d, a, m[i+ 6], 23,  76029189);
      a = HH(a, b, c, d, m[i+ 9],  4, -640364487);
      d = HH(d, a, b, c, m[i+12], 11, -421815835);
      c = HH(c, d, a, b, m[i+15], 16,  530742520);
      b = HH(b, c, d, a, m[i+ 2], 23, -995338651);

      a = II(a, b, c, d, m[i+ 0],  6, -198630844);
      d = II(d, a, b, c, m[i+ 7], 10,  1126891415);
      c = II(c, d, a, b, m[i+14], 15, -1416354905);
      b = II(b, c, d, a, m[i+ 5], 21, -57434055);
      a = II(a, b, c, d, m[i+12],  6,  1700485571);
      d = II(d, a, b, c, m[i+ 3], 10, -1894986606);
      c = II(c, d, a, b, m[i+10], 15, -1051523);
      b = II(b, c, d, a, m[i+ 1], 21, -2054922799);
      a = II(a, b, c, d, m[i+ 8],  6,  1873313359);
      d = II(d, a, b, c, m[i+15], 10, -30611744);
      c = II(c, d, a, b, m[i+ 6], 15, -1560198380);
      b = II(b, c, d, a, m[i+13], 21,  1309151649);
      a = II(a, b, c, d, m[i+ 4],  6, -145523070);
      d = II(d, a, b, c, m[i+11], 10, -1120210379);
      c = II(c, d, a, b, m[i+ 2], 15,  718787259);
      b = II(b, c, d, a, m[i+ 9], 21, -343485551);

      a = (a + aa) >>> 0;
      b = (b + bb) >>> 0;
      c = (c + cc) >>> 0;
      d = (d + dd) >>> 0;
    }

    return crypt.endian([a, b, c, d]);
  };

  // Auxiliary functions
  md5._ff  = function (a, b, c, d, x, s, t) {
    var n = a + (b & c | ~b & d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._gg  = function (a, b, c, d, x, s, t) {
    var n = a + (b & d | c & ~d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._hh  = function (a, b, c, d, x, s, t) {
    var n = a + (b ^ c ^ d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._ii  = function (a, b, c, d, x, s, t) {
    var n = a + (c ^ (b | ~d)) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };

  // Package private blocksize
  md5._blocksize = 16;
  md5._digestsize = 16;

  module.exports = function (message, options) {
    if (message === undefined || message === null)
      throw new Error('Illegal argument ' + message);

    var digestbytes = crypt.wordsToBytes(md5(message, options));
    return options && options.asBytes ? digestbytes :
        options && options.asString ? bin.bytesToString(digestbytes) :
        crypt.bytesToHex(digestbytes);
  };

})();


/***/ }),
/* 7 */
/***/ (function(module, exports) {

(function() {
  var base64map
      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',

  crypt = {
    // Bit-wise rotation left
    rotl: function(n, b) {
      return (n << b) | (n >>> (32 - b));
    },

    // Bit-wise rotation right
    rotr: function(n, b) {
      return (n << (32 - b)) | (n >>> b);
    },

    // Swap big-endian to little-endian and vice versa
    endian: function(n) {
      // If number given, swap endian
      if (n.constructor == Number) {
        return crypt.rotl(n, 8) & 0x00FF00FF | crypt.rotl(n, 24) & 0xFF00FF00;
      }

      // Else, assume array and swap all items
      for (var i = 0; i < n.length; i++)
        n[i] = crypt.endian(n[i]);
      return n;
    },

    // Generate an array of any length of random bytes
    randomBytes: function(n) {
      for (var bytes = []; n > 0; n--)
        bytes.push(Math.floor(Math.random() * 256));
      return bytes;
    },

    // Convert a byte array to big-endian 32-bit words
    bytesToWords: function(bytes) {
      for (var words = [], i = 0, b = 0; i < bytes.length; i++, b += 8)
        words[b >>> 5] |= bytes[i] << (24 - b % 32);
      return words;
    },

    // Convert big-endian 32-bit words to a byte array
    wordsToBytes: function(words) {
      for (var bytes = [], b = 0; b < words.length * 32; b += 8)
        bytes.push((words[b >>> 5] >>> (24 - b % 32)) & 0xFF);
      return bytes;
    },

    // Convert a byte array to a hex string
    bytesToHex: function(bytes) {
      for (var hex = [], i = 0; i < bytes.length; i++) {
        hex.push((bytes[i] >>> 4).toString(16));
        hex.push((bytes[i] & 0xF).toString(16));
      }
      return hex.join('');
    },

    // Convert a hex string to a byte array
    hexToBytes: function(hex) {
      for (var bytes = [], c = 0; c < hex.length; c += 2)
        bytes.push(parseInt(hex.substr(c, 2), 16));
      return bytes;
    },

    // Convert a byte array to a base-64 string
    bytesToBase64: function(bytes) {
      for (var base64 = [], i = 0; i < bytes.length; i += 3) {
        var triplet = (bytes[i] << 16) | (bytes[i + 1] << 8) | bytes[i + 2];
        for (var j = 0; j < 4; j++)
          if (i * 8 + j * 6 <= bytes.length * 8)
            base64.push(base64map.charAt((triplet >>> 6 * (3 - j)) & 0x3F));
          else
            base64.push('=');
      }
      return base64.join('');
    },

    // Convert a base-64 string to a byte array
    base64ToBytes: function(base64) {
      // Remove non-base-64 characters
      base64 = base64.replace(/[^A-Z0-9+\/]/ig, '');

      for (var bytes = [], i = 0, imod4 = 0; i < base64.length;
          imod4 = ++i % 4) {
        if (imod4 == 0) continue;
        bytes.push(((base64map.indexOf(base64.charAt(i - 1))
            & (Math.pow(2, -2 * imod4 + 8) - 1)) << (imod4 * 2))
            | (base64map.indexOf(base64.charAt(i)) >>> (6 - imod4 * 2)));
      }
      return bytes;
    }
  };

  module.exports = crypt;
})();


/***/ }),
/* 8 */
/***/ (function(module, exports) {

/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <feross@feross.org> <http://feross.org>
 * @license  MIT
 */

// The _isBuffer check is for Safari 5-7 support, because it's missing
// Object.prototype.constructor. Remove this eventually
module.exports = function (obj) {
  return obj != null && (isBuffer(obj) || isSlowBuffer(obj) || !!obj._isBuffer)
}

function isBuffer (obj) {
  return !!obj.constructor && typeof obj.constructor.isBuffer === 'function' && obj.constructor.isBuffer(obj)
}

// For Node v0.10 support. Remove this eventually.
function isSlowBuffer (obj) {
  return typeof obj.readFloatLE === 'function' && typeof obj.slice === 'function' && isBuffer(obj.slice(0, 0))
}


/***/ })
/******/ ]);