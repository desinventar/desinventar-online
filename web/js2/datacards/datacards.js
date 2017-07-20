/* global define,desinventar, setDICard
 */
(function(root, factory) {
  'use strict';
  if (typeof define === "function" && define.amd) {
    define(['jquery', 'prototype'], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory(require('jquery'), require('prototype'));
  } else {
    jQuery.extend(true, desinventar, {
      datacards: factory(root.jQuery, root.$)
    });
  }
}(this, function(jQuery, $) {
  'use strict';
  var me = {};
  var navigation = desinventar.datacards.navigation;

  function setupBindings() {
    jQuery('#btnDatacardClone').on('click', function(e) {
      e.preventDefault();
      if (navigation.getStatus() !== 'VIEW') {
        return false;
      }
      clone();
    });

    // Validation of DisasterBeginTime and Suggest Serial for New Datacards
    jQuery('#DisasterBeginTime0').on('blur', function() {
      var cmd = jQuery('#DatacardCommand').val();
      if ((cmd === 'insertDICard') &&
          (jQuery(this).val() !== '') &&
          (jQuery('#DisasterSerial').val() === '')) {
        nextSerial(jQuery('#desinventarRegionId').val());
      }
      return false;
    });

    jQuery('div.Datacard #linkDatacardSuggestSerial').on('click', function() {
      if (navigation.getStatus() === 'NEW') {
        // Suggest new serial
        return nextSerial(jQuery('#desinventarRegionId').val());
      }
      if (navigation.getStatus() === 'EDIT') {
        // Restore initial value when editing...
        jQuery('#DisasterSerial').val(jQuery('#PrevDisasterSerial').val());
      }
      return false;
    });
  }

  function toggleFormEdit(xForm, disab) {
    if (xForm === null) {
      return false;
    }
    var objElems = xForm.elements;
    var col = '#fff';
    if (disab) {
      col = '#eee';
    }
    for (var i = 0; i < objElems.length; i++) {
      var myname = String(objElems[i].name);
      if (myname.substring(0, 1) !== "_") {
        objElems[i].disabled = disab;
        objElems[i].style.backgroundColor = col;
      }
    }
    jQuery('#txtDatacardFind', xForm)
      .prop('readonly', disab).prop('disabled', disab);
    jQuery('#btnDatacardFind', xForm)
      .prop('readonly', disab).prop('disabled', disab);
  }

  function showStatus(msgId) {
    // First hide all items
    jQuery('.datacardStatusMsg').hide();
    // Show a specific message
    if (msgId !== '') {
      jQuery('#' + msgId).show();
    }
  }

  function create() {
    me.toggleFormEdit($('DICard'), false);
    jQuery('#DisasterBeginTime0').focus();
    me.showStatus('msgDatacardFill');
    navigation.setEditMode();
    jQuery('#divRecordNavigationInfo').hide();
    navigation.setStatus('NEW');
    updateStatus('DRAFT');

    // Clear values of following sublevels
    var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
    for (var i = 1; i < GeoLevelCount; i++) {
      var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
      mySelect.empty();
      mySelect.append(jQuery('<option>', {value: ''}).text(''));
      mySelect.disable();
    }
  }

  function clone() {
    var regionId = jQuery('#desinventarRegionId').val();
    var id = jQuery('#DisasterId').val();
    me.read(regionId, id).then(function(data) {
      data.RecordStatus = 'DRAFT';
      data.DisasterId = '';
      data.DisasterSiteNotes = '';
      data.DisasterLatitude = '0.0';
      data.DisasterLongitude = '0.0';
      if (data.GeographyItems.length > 1) {
        data.GeographyItems.pop(); // Remove last element from Geography
      }
      data.GeographyId = data.GeographyItems[data.GeographyItems.length - 1].GeographyId;
      me.clear();
      me.create();
      setDICard(data.RegionId, data);
      clearEffects();
      jQuery('#DICard #EffectNotes').val(data.EffectNotes);
      jQuery.each(data.GeographyItems, function(key, value) {
        jQuery('select#GeoLevel' + key).val(value.GeographyId).change();
      });
      var prefix = data.DisasterSerial.split(':').slice(0, 1).join('');
      jQuery('#DatacardPrefix').val(prefix);
      navigation.setStatus('NEW');
      jQuery('#DatacardCommand').val('insertDICard');
      nextSerial(regionId);
      return data;
    });
  }

  function clearEffects() {
    var effects = jQuery('div.divDatacardEffects');
    effects.find('table.EffectListPeople .clsEffectNumeric').each(function() {
      jQuery(this).val(0);
      jQuery(this).jecValue('', false);
    });
    effects.find('select.clsEffectSector').each(function() {
      jQuery(this).val(0); // There weren't by default
    });
    effects.find('.inputDouble').each(function() {
      jQuery(this).val(0);
    });
    effects.find('.inputInteger').each(function() {
      jQuery(this).val(0);
    });
    effects.find('.inputText').each(function() {
      jQuery(this).val('');
    });
  }

  function nextSerial(regionId) {
    var payload = {
      cmd: 'getNextSerial',
      r: regionId,
      value: jQuery('#DisasterBeginTime0').val(),
      length: 5,
      separator: '-'
    };
    if (jQuery('#DatacardPrefix').val() !== '') {
      payload.value = jQuery('#DatacardPrefix').val();
      payload.length = 3;
      payload.separator = ':';
    }
    if (payload.value === '') {
      return false;
    }
    jQuery.post(
      jQuery('#desinventarURL').val() + '/cards.php',
      payload,
      null,
      'json'
    ).then(function(data) {
      if (data.DisasterSerial) {
        jQuery('#DisasterSerial').val(data.DisasterSerial);
      }
    });
    return false;
  }

  function updateStatus(status) {
    jQuery('#DICard #RecordStatus').val(status);
  }

  me.init = function() {
    setupBindings();
  };

  me.read = function(regionId, disasterId) {
    return jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
      {
        cmd: 'getDatacard',
        RegionId: regionId,
        DisasterId: disasterId
      },
      null,
      'json'
    );
  };

  me.clear = function() {
    jQuery('#DisasterId').val('');
    jQuery('#DatacardPrefix').val('');
    $('DICard').reset();
    jQuery('#DatacardCommand').val('insertDICard');
    jQuery('#cardsRecordNumber').val(0);
    clearEffects();
    jQuery('#DICard #DisasterBeginTime0').val('');
    jQuery('#DICard #DisasterBeginTime1').val('');
    jQuery('#DICard #DisasterBeginTime2').val('');
    jQuery('#DICard #EventDuration').val(0);
  };

  me.create = create;
  me.toggleFormEdit = toggleFormEdit;
  me.showStatus = showStatus;
  return me;
}));
