/* global Ext */
function init() {
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

// Older Compatibility Functions

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

module.exports = exports = {
  init,
  showtip
}
