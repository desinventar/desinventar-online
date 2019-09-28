/* global Ext */

function init() {
  jQuery('#fldStatParam_FirstLev').change(function() {
    setTotalize('fldStatParam_FirstLev', 'fldStatParam_SecondLev')
    jQuery('#fldStatParam_ThirdLev').empty()
  })

  jQuery('#fldStatParam_SecondLev').change(function() {
    setTotalize('fldStatParam_SecondLev', 'fldStatParam_ThirdLev')
  })

  jQuery('body').on('cmdViewStdParams', function() {
    Ext.getCmp('wndViewStdParams').show()
    jQuery('#fldStatParam_FirstLev').trigger('change')
  })

  jQuery('div.ViewStatParams').on('cmdInitialize', function() {
    doViewStatParamsInitialize()
  })
}

function setTotalize(lnow, lnext) {
  var sour = $(lnow)
  var dest = $(lnext)
  // clean dest list
  for (let i = dest.length - 1; i >= 0; i--) {
    dest.remove(i)
  }
  for (let i = 0; i < sour.length; i++) {
    if (!sour[i].selected) {
      var opt = document.createElement('option')
      opt.value = sour[i].value
      opt.text = sour[i].text
      var pto = dest.options[i]
      try {
        dest.add(opt, pto)
      } catch (ex) {
        dest.add(opt, i)
      }
    }
  }
}

function doViewStatParamsInitialize() {
  var statlevel_list = jQuery('div.ViewStatParams select.StatlevelFirst')
  statlevel_list.find('option').remove()
  jQuery.each(jQuery('body').data('GeolevelsList'), function(key, value) {
    statlevel_list.append(
      jQuery('<option>', { value: value.GeoLevelId + '|D.GeographyId' }).text(
        value.GeoLevelName
      )
    )
  })
  statlevel_list.append(
    jQuery('<option>', { value: '|D.EventId' }).text(
      jQuery('#ViewStatParamsLabelEvent').text()
    )
  )
  statlevel_list.append(
    jQuery('<option>', { value: 'YEAR|D.DisasterBeginTime' }).text(
      jQuery('#ViewStatParamsLabelYear').text()
    )
  )
  statlevel_list.append(
    jQuery('<option>', { value: 'MONTH|D.DisasterBeginTime' }).text(
      jQuery('#ViewStatParamsLabelMonth').text()
    )
  )
  statlevel_list.append(
    jQuery('<option>', { value: '|D.CauseId' }).text(
      jQuery('#ViewStatParamsLabelCause').text()
    )
  )
  statlevel_list.val(jQuery('option:first', statlevel_list).val())

  var field_list = jQuery('div.ViewStatParams select.FieldsAvailable')
  field_list.find('option').remove()
  // EffectPeople (ef1)
  jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(
          jQuery('#StatLabelAuxHave').text() + ' ' + label
        )
      )
    }
  )
  // EffectLosses1 List (ef2)
  jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label)
      )
    }
  )
  // EffectLosses2 List (ef3)
  jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label)
      )
    }
  )
  // EffectSector (sec)
  jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      field_list.append(
        jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(
          jQuery('#StatLabelAuxAffect').text() + ' ' + label
        )
      )
    }
  )
  field_list.append(
    jQuery('<option>', { value: '', disabled: 'disabled' }).text('---')
  )
  // EEFieldList
  jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
    var field = value['id']
    var label = value['name']
    var type = value['type']
    if (type == 'INTEGER' || type == 'DOUBLE' || type === 'CURRENCY') {
      field_list.append(
        jQuery('<option>', { value: 'E.' + field + '|>|-1' }).text(label)
      )
    }
  })
  field_list.append(
    jQuery('<option>', { value: 'D.EventDuration|S|-1' }).text(
      jQuery('#StatLabelEventDuration').text()
    )
  )

  var selectFieldShow = jQuery('div.ViewStatParams select.FieldsShow')
  selectFieldShow.find('option').remove()

  // EffectPeople (ef1)
  jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(
    function() {
      var field = jQuery('span.field', this).text()
      var label = jQuery('span.label', this).text()
      selectFieldShow.append(
        jQuery('<option>', { value: 'D.' + field + 'Q|>|-1' }).text(label)
      )
    }
  )
}

export default {
  init
}
