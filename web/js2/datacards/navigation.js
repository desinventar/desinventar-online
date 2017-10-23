/* global define,desinventar
 */
(function(root, factory) {
  "use strict";
  if (typeof define === "function" && define.amd) {
    define(["jquery"], factory);
  } else if (typeof exports === "object") {
    module.exports = factory(require("jquery"));
  } else {
    jQuery.extend(true, desinventar, {
      datacards: {
        navigation: factory(root.jQuery)
      }
    });
  }
})(this, function($) {
  "use strict";
  var me = {};

  function updateCloneButton() {
    me.disableButton("btnDatacardClone");
    if (me.getStatus() === "VIEW") {
      me.enableButton("btnDatacardClone");
    }
  }

  me.disableButton = function(buttonId) {
    $("#" + buttonId)
      .prop("disabled", true)
      .removeClass("bb")
      .addClass("disabled");
  };

  me.enableButton = function(buttonId) {
    $("#" + buttonId)
      .prop("disabled", false)
      .addClass("bb")
      .removeClass("disabled");
  };

  me.toggleButton = function(butid, disab) {
    if (disab) {
      me.disableButton(Element.identify(butid));
    } else {
      me.enableButton(Element.identify(butid));
    }
  };

  me.getStatus = function() {
    return $("#DICard #Status").val();
  };

  me.setStatus = function(status) {
    return $("#DICard #Status").val(status);
  };

  me.enable = function() {
    var RecordNumber = parseInt($("#cardsRecordNumber").val(), 10);
    var RecordCount = parseInt($("#cardsRecordCount").val(), 10);

    me.disable();
    if (RecordCount < 1) {
      return true;
    }
    if (RecordNumber < 1) {
      me.enableButton("btnDatacardGotoFirst");
      me.enableButton("btnDatacardGotoLast");
      return true;
    }

    if (RecordNumber > 1) {
      me.enableButton("btnDatacardGotoFirst");
      me.enableButton("btnDatacardGotoPrev");
    }

    if (RecordNumber < RecordCount) {
      me.enableButton("btnDatacardGotoLast");
      me.enableButton("btnDatacardGotoNext");
    }
    updateCloneButton();
  };

  me.disable = function() {
    $("input.DatacardNavButton").each(function() {
      me.disableButton($(this).attr("id"));
    });
  };

  me.updateByUserRole = function() {
    if ($("#desinventarUserRoleValue").val() >= 2) {
      me.enableButton("btnDatacardEdit");
    }
    updateCloneButton();
  };

  me.setEditMode = function() {
    me.disableButton("btnDatacardNew");
    me.disableButton("btnDatacardEdit");
    me.disableButton("btnDatacardClone");
    me.disableButton("btnDatacardFind");
    me.enableButton("btnDatacardSave");
    me.enableButton("btnDatacardCancel");
    me.disable();
  };

  me.setViewMode = function() {
    me.enableButton("btnDatacardNew");
    me.enableButton("btnDatacardEdit");
    me.enableButton("btnDatacardFind");
    me.disableButton("btnDatacardSave");
    me.disableButton("btnDatacardCancel");
    me.disableButton("btnDatacardClone");
    me.enable();
    if ($("#DisasterId").val() === "") {
      me.disableButton("btnDatacardEdit");
    }
  };

  return me;
});
