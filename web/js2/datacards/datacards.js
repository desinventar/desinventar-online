/* global define,desinventar
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
    navigation.update('btnDatacardNew');
    jQuery('#divRecordNavigationInfo').hide();
    jQuery('#DICard #Status').val('NEW');

    // Clear values of following sublevels
    var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
    for (var i = 1; i < GeoLevelCount; i++) {
      var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
      mySelect.empty();
      mySelect.append(jQuery('<option>', {value: ''}).text(''));
      mySelect.disable();
    }
  }

  me.init = function() {
    setupBindings();
  };

  me.getStatus = function() {
    return jQuery('#DICard #Status').val();
  };

  me.setStatus = function(value) {
    return jQuery('#DICard #Status').val(value);
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
    $('DICard').reset();
    jQuery('#DatacardCommand').val('insertDICard');
    jQuery('#cardsRecordNumber').val(0);
    me.clearEffects();
    jQuery('#DICard #DisasterBeginTime0').val('');
    jQuery('#DICard #DisasterBeginTime1').val('');
    jQuery('#DICard #DisasterBeginTime2').val('');
    jQuery('#DICard #EventDuration').val(0);
  };

  me.clearEffects = function() {
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
  };

  me.create = create;
  me.toggleFormEdit = toggleFormEdit;
  me.showStatus = showStatus;
  return me;
}));
