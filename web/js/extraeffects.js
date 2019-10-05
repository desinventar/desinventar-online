/* global uploadMsg, updateList */

import { showtip } from './common'

function init() {
  jQuery('.msgEEFieldStatus').hide()

  jQuery('#btnEEFieldAdd')
    .unbind('click')
    .click(function() {
      setExtraEff('', '', '', '', '', '', '')
      jQuery('#EEFieldCmd').val('cmdEEFieldInsert')
      return false
    })

  jQuery('#btnEEFieldReset')
    .unbind('click')
    .click(function() {
      jQuery('#extraeffaddsect').hide()
      uploadMsg('')
      return false
    })

  jQuery('#frmEEFieldEdit')
    .unbind('submit')
    .submit(function() {
      var params = jQuery(this).serialize()
      if (jQuery('#EEFieldLabel').val() == '') {
        jQuery('#EEFieldLabel')
          .highlight()
          .focus()
        return false
      }
      if (jQuery('#EEFieldDesc').val() == '') {
        jQuery('#EEFieldDesc')
          .highlight()
          .focus()
        return false
      }
      if (jQuery('#EEFieldType').val() == '') {
        jQuery('#EEFieldType')
          .highlight()
          .focus()
        return false
      }
      jQuery.post(
        jQuery('#desinventarURL').val() + '/extraeffects.php',
        params,
        function(data) {
          jQuery('.msgEEFieldStatus').hide()
          if (data.Status == 'OK') {
            jQuery('#msgEEFieldStatusOk').show()
            updateList(
              'lst_eef',
              jQuery('#desinventarURL').val() + '/extraeffects.php',
              'cmd=cmdEEFieldList+&RegionId=' +
                jQuery('#desinventarRegionId').val()
            )
            jQuery('#extraeffaddsect').hide()
          } else {
            jQuery('#msgEEFieldStatusError').show()
          }
        },
        'json'
      )
      return false
    })
    .on('click', '#btnCancel', function(event) {
      jQuery('#extraeffaddsect').hide()
      event.preventDefault()
    })
    .on('mouseenter', '.show-help', function() {
      showtip(jQuery(this).data('tooltip'))
    })
    .on('mouseleave', '.show-help', function() {
      showtip('')
    })

  jQuery('table.database-admin-eefield-list')
    .on('mouseover', 'tr.extra-effect', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr.extra-effect', function() {
      jQuery(this).removeClass('highlight')
    })
    .on('click', 'tr.extra-effect', function(e) {
      jQuery('#EEFieldCmd').val('cmdEEFieldUpdate')
      setExtraEff(
        jQuery(this).data('id'),
        jQuery(this).data('name'),
        jQuery(this).data('description'),
        jQuery(this).data('type'),
        jQuery(this).data('size'),
        jQuery(this).data('active'),
        jQuery(this).data('public')
      )
      e.preventDefault()
    })
}

function setExtraEff(id, name, description, type, size, isActive, isPublic) {
  // clear highlighted fields...
  jQuery.each(jQuery('.clsValidateField'), function() {
    jQuery(this).unhighlight()
  })

  // hide status msg
  jQuery('.msgEEFieldStatus').hide()

  // Show form
  jQuery('#extraeffaddsect').show()

  jQuery('#EEFieldId').val(id)
  jQuery('#EEFieldLabel').val(name)
  jQuery('#EEFieldDesc').val(description)
  jQuery('#EEFieldType')
    .val(type)
    .prop('disabled', type && type !== '')
    .toggleClass('disabled', type && type !== '')
  jQuery('#EEFieldSize').val(size)
  jQuery('#EEFieldActive').prop('checked', parseInt(isActive, 10) > 0)
  jQuery('#EEFieldPublic').prop('checked', parseInt(isPublic, 10) > 0)
}

export default {
  init
}
