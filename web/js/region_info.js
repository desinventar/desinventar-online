/* global uploadMsg */
import $ from 'jquery'

function init() {
  $('body')
    .on('submit', '#frmDatabaseInfo', function() {
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
    .on('click', '#frmDatabaseInfo .cancel', function() {
      uploadMsg('')
    })

  $('body').on('click', '#frmDatabaseInfo .region-info-edit-label', function() {
    const field = $(this)
      .parents('.region-info-edit-row')
      .find('.region-info-edit-field')
    field.hasClass('expanded')
      ? field.removeClass('expanded')
      : field.addClass('expanded')
  })
}

export default {
  init
}
