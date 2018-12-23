function onReadyCommon() {
  // Initialize tooltip for elements with title attribute
  jQuery('[title]').tooltip()

  jQuery('body').on('cmdWindowReload', function() {
    // Destroy viewport, the loading... message should stay.
    doViewportDestroy()
    // Reload document window
    window.location.reload(false)
  })
  // Create periodic task to keep session alive...
  setInterval(doKeepSessionAwake, 60000)
}

function doKeepSessionAwake() {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdSessionAwake'
    },
    function(data) {},
    'json'
  )
}

// jQuery Snippets Code

// http://css-tricks.com/snippets/jquery/serialize-form-to-json/
// Serialize Form to JSON
jQuery.fn.serializeObject = function() {
  var o = {}
  var a = this.serializeArray()
  jQuery.each(a, function() {
    if (o[this.name]) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]]
      }
      o[this.name].push(this.value || '')
    } else {
      o[this.name] = this.value || ''
    }
  })
  return o
}

jQuery.fn.highlight = function() {
  jQuery(this).attr('old-bg-color', jQuery(this).css('background-color'))
  jQuery(this).css('background-color', '#ffff66')
  return this
}

jQuery.fn.unhighlight = function() {
  if (jQuery(this).attr('old-bg-color') != '') {
    jQuery(this).css('background-color', jQuery(this).attr('old-bg-color'))
  }
  return this
}

jQuery.fn.disable = function() {
  jQuery(this).attr('disabled', true)
  jQuery(this).attr('readonly', true)
  jQuery(this).addClass('disabled')
  return this
}

jQuery.fn.enable = function() {
  jQuery(this).removeAttr('disabled')
  jQuery(this).removeAttr('readonly')
  jQuery(this).removeClass('disabled')
  return this
}

// Older Compatibility Functions

function checkForm(prmForm, prmFieldList, errmsg) {
  var bReturn = true
  jQuery.each(prmFieldList, function(index, value) {
    var selector = '#' + prmForm + ' #' + value
    if (jQuery(selector).val().length < 1) {
      jQuery(selector).highlight()
      bReturn = false
    }
  })
  return bReturn
}

function doViewportDestroy() {
  var viewport = Ext.getCmp('viewport')
  if (viewport != undefined) {
    viewport.destroy()
    jQuery('#loading').show()
    jQuery('#loading-mask').show()
  }
}

function showtip(
  prmText //prmText, prmColor
) {
  if (prmText != undefined) {
    var sColor = '#ffffff'
    if (arguments.length > 1) {
      sColor = arguments[1]
    }
    jQuery('#txtHelpArea')
      .val(prmText)
      .css('background-color', sColor)
  }
}

// Block characters according to type
function blockChars(e, value, type) {
  var key = window.event ? e.keyCode : e.which

  // Accept values in numeric keypad
  if (key >= 96 && key <= 105) {
    key = key - 48
  }
  var keychar = String.fromCharCode(key)
  if (key == 190 || key == 110 || key == 188) {
    keychar = '.'
  }
  var opt = type.split(':') // 0=type; 1=minlength; 2=minval-maxval
  // Accept keys: backspace, tab, shift, ctrl, insert, delete
  //        pagedown, pageup, rows, hyphen
  var spckey =
    key == 8 ||
    key == 9 ||
    key == 17 ||
    key == 20 ||
    key == 189 ||
    key == 45 ||
    key == 46 ||
    (key >= 33 && key <= 40) ||
    key == 0
  var chk = true
  var val = true // validate characters
  // Check max length
  if (value.length >= parseInt(opt[1])) {
    var len = false
  } else {
    var len = true
  }
  // Check datatype
  switch (opt[0]) {
    case 'date':
      reg = /^\d{4}-\d{0,2}-\d{0,2}$/
      chk = reg.test(keychar)
      break
    case 'alphanumber':
      reg = /^[a-z]|[A-Z]|[0-9]|[-_+.]+/
      chk = reg.test(keychar)
      break
    case 'integer':
      reg = /\d/
      chk = reg.test(keychar)
      break
    case 'double':
      reg = /^[-+]?[0-9]|[.]+$/
      chk = reg.test(keychar)
      break
    default:
  }
  // Block special characters: (like !@#$%^&'*" etc)
  val = !(key == 92 || key == 13 || key == 16)
  return val && ((chk && len) || spckey)
}

function onlyText(e) {
  var keynum
  var keychar
  var numcheck
  if (window.event) {
    // IE
    keynum = e.keyCode
  } else if (e.which) {
    // Netscape/Firefox/Opera
    keynum = e.which
  }
  keychar = String.fromCharCode(keynum)
  numcheck = /\d/
  return !numcheck.test(keychar)
}

function onlyNumber(e) {
  var keynum
  var keychar
  if (window.event) {
    // IE
    keynum = e.keyCode
  } else if (e.which) {
    // Netscape/Firefox/Opera
    keynum = e.which
  }
  if (e.keyCode < 48 || e.keyCode > 57) {
    return false
  }
  return true
}
