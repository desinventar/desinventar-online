/* global define,desinventar
 */
;(function(root, factory) {
  'use strict'
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory)
  } else if (typeof exports === 'object') {
    module.exports = factory(require('jquery'))
  } else {
    jQuery.extend(true, desinventar, {
      regionInfo: factory(root.jQuery)
    })
  }
})(this, function($) {
  'use strict'
  var me = {}

  function setupBindings() {
    $('body').on('submit', '#frmDatabaseInfo', function() {
      $.post($(this).attr('action'), $(this).serialize(), function(data) {
        $('#ifinfo')
          .html(data)
          .show()
        $('body').trigger('cmdDatabaseLoadData', {
          updateViewport: false,
          callback: function() {
            jQuery('.classDBConfig_tabs:first').trigger('click')
          }
        })
        setTimeout(function() {
          $('#ifinfo').html('')
        }, 2000)
      })
      return false
    })
  }

  me.init = function() {
    setupBindings()
  }
  return me
})
