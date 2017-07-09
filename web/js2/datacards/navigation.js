/* global define,desinventar
 */
(function(root, factory) {
  'use strict';
  if (typeof define === "function" && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory(require('jquery'));
  } else {
    jQuery.extend(true, desinventar, {
      datacards: {
        navigation: factory(root.jQuery)
      }
    });
  }
}(this, function(jQuery) {
  'use strict';
  var me = {};

  me.toggleButton = function(butid, disab) {
    if (disab) {
      if (!butid) {
        butid.disable();
      }
      Element.removeClassName(butid, 'bb');
      Element.addClassName(butid, 'disabled');
    } else {
      if (!butid) {
        butid.enable();
      }
      Element.addClassName(butid, 'bb');
      Element.removeClassName(butid, 'disabled');
    }
  };

  me.enable = function() {
    var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val(), 10);
    var RecordCount = parseInt(jQuery('#cardsRecordCount').val(), 10);
    if (RecordNumber > 0) {
      if (RecordNumber > 1) {
        me.toggleButton($('btnDatacardGotoFirst'), false);
        me.toggleButton($('btnDatacardGotoPrev'), false);
      } else {
        me.toggleButton($('btnDatacardGotoFirst'), true);
        me.toggleButton($('btnDatacardGotoPrev'), true);
      }
      if (RecordNumber < RecordCount) {
        me.toggleButton($('btnDatacardGotoLast'), false);
        me.toggleButton($('btnDatacardGotoNext'), false);
      } else {
        me.toggleButton($('btnDatacardGotoLast'), true);
        me.toggleButton($('btnDatacardGotoNext'), true);
      }
    } else {
      me.toggleButton($('btnDatacardGotoPrev'), true);
      me.toggleButton($('btnDatacardGotoNext'), true);
      if (RecordCount > 0) {
        me.toggleButton($('btnDatacardGotoFirst'), false);
        me.toggleButton($('btnDatacardGotoLast'), false);
      } else {
        me.toggleButton($('btnDatacardGotoFirst'), true);
        me.toggleButton($('btnDatacardGotoLast'), true);
      }
    }
  };

  me.disable = function() {
    me.toggleButton($('btnDatacardGotoFirst'), true);
    me.toggleButton($('btnDatacardGotoPrev'), true);
    me.toggleButton($('btnDatacardGotoNext'), true);
    me.toggleButton($('btnDatacardGotoLast'), true);
  };

  me.update = function(but) {
    switch (but) {
      case "btnDatacardNew":
        me.toggleButton($('btnDatacardNew'), true);
        me.toggleButton($('btnDatacardSave'), false);
        me.toggleButton($('btnDatacardEdit'), true);
        me.toggleButton($('btnDatacardCancel'), false);
        me.disable();
        me.toggleButton($('btnDatacardFind'), true);
        break;
      case "btnDatacardEdit":
        me.toggleButton($('btnDatacardNew'), true);
        me.toggleButton($('btnDatacardSave'), false);
        me.toggleButton($('btnDatacardEdit'), true);
        me.toggleButton($('btnDatacardCancel'), false);
        me.disable();
        me.toggleButton($('btnDatacardFind'), true);
        break;
      case "btnDatacardSave":
        me.toggleButton($('btnDatacardNew'), false);
        me.toggleButton($('btnDatacardSave'), true);
        me.toggleButton($('btnDatacardEdit'), false);
        me.toggleButton($('btnDatacardCancel'), true);
        me.enable();
        me.toggleButton($('btnDatacardFind'), false);
        break;
      case "btnDatacardCancel":
        if ($('DisasterId').value === '') {
          me.toggleButton($('btnDatacardEdit'), true);
        } else {
          me.toggleButton($('btnDatacardEdit'), false);
        }
        me.toggleButton($('btnDatacardSave'), true);
        me.toggleButton($('btnDatacardCancel'), true);
        me.toggleButton($('btnDatacardNew'), false);
        me.enable();
        me.toggleButton($('btnDatacardFind'), false);
        break;
      default:
        me.toggleButton($('btnDatacardNew'), false);
        me.toggleButton($('btnDatacardSave'), true);
        me.toggleButton($('btnDatacardEdit'), true);
        me.toggleButton($('btnDatacardCancel'), true);
        break;
    }
  };

  return me;
}));
