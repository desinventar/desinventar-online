/* global uploadMsg, updateList */
// eslint-disable-next-line no-unused-vars
function onReadyExtraEffects() {
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

  jQuery('table.database-admin-eefield-list')
    .on('mouseover', 'tr.extra-effect', function() {
      jQuery(this).addClass('highlight')
    })
    .on('mouseout', 'tr.extra-effect', function() {
      jQuery(this).removeClass('highlight')
    })
    .on('click', 'tr.extra-effect', function(e) {
      $('EEFieldCmd').value = 'cmdEEFieldUpdate'
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

function setExtraEff(id, label, def, type, size, active, isPublic) {
  // clear highlighted fields...
  jQuery.each(jQuery('.clsValidateField'), function() {
    jQuery(this).unhighlight()
  })

  // hide status msg
  jQuery('.msgEEFieldStatus').hide()

  // Show form
  jQuery('#extraeffaddsect').hide()

  var mod = 'extraeff'
  $(mod + 'addsect').style.display = 'block'
  $('EEFieldId').value = id
  $('EEFieldLabel').value = label
  $('EEFieldDesc').value = def
  $('EEFieldType').value = type
  $('EEFieldSize').value = size
  $('EEFieldActive').checked = parseInt(active, 10) > 0
  $('EEFieldPublic').checked = parseInt(isPublic, 10) > 0
}
