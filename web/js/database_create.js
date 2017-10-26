/* global Ext */
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

const moment = require("moment");

module.exports = {
  init: onReadyDatabaseCreate,
  show: doDatabaseCreateShow
};

function onReadyDatabaseCreate() {
  doDatabaseCreateSetup();
}

function doDatabaseCreateSetup() {
  // Database Create
  new Ext.Window({
    id: "wndDatabaseCreate",
    el: "divDatabaseCreateWin",
    layout: "fit",
    width: 450,
    height: 300,
    modal: false,
    constrainHeader: true,
    closeAction: "hide",
    plain: false,
    animCollapse: true,
    items: new Ext.Panel({
      contentEl: "divDatabaseCreateContent",
      autoScroll: true
    }),
    buttons: [] //buttons
  });

  // Cancel Button - Hide Window and do nothing
  jQuery("#btnDatabaseCreateCancel").click(function() {
    Ext.getCmp("wndDatabaseCreate").hide();
    return false;
  });

  jQuery("#fldDatabaseEdit_CountryIso").change(function() {
    jQuery.post(
      jQuery("#desinventarURL").val() + "/",
      {
        cmd: "cmdRegionBuildRegionId",
        CountryIso: jQuery(this).val()
      },
      function(data) {
        if (parseInt(data.Status) > 0) {
          jQuery("#fldDatabaseEdit_RegionId").val(data.RegionId);
        }
      },
      "json"
    );
  });

  // Send Button - Validate data and send command to backend
  jQuery("#btnDatabaseCreateSend").click(function() {
    var regionId =
      jQuery("#fldDatabaseEdit_CountryIso").val() +
      "-" +
      moment().format("YMDHmmss");
    jQuery("#fldDatabaseEdit_RegionId").val(regionId);
    if (doDatabaseCreateValidate()) {
      jQuery(this).attr("readonly", true);
      var RegionStatus = jQuery("#fldDatabaseEdit_RegionStatus");
      RegionStatus.val(0);
      if (jQuery("#fldDatabaseEdit_RegionActive").attr("checked")) {
        RegionStatus.val(parseInt(RegionStatus.val()) | 1);
      }
      if (jQuery("#fldDatabaseEdit_RegionPublic").attr("checked")) {
        RegionStatus.val(parseInt(RegionStatus.val()) | 2);
      }
      jQuery("#fldDatabaseEdit_RegionId").prop("disabled", false);
      var params = jQuery("#frmDatabaseEdit").serializeObject();
      jQuery("#fldDatabaseEdit_RegionId").prop("disabled", true);

      jQuery("#frmDatabaseEdit :input").unhighlight();
      jQuery.post(
        jQuery("#desinventarURL").val() + "/",
        jQuery.extend(params, {
          cmd: "cmdDatabaseCreate"
        }),
        function(data) {
          jQuery(".clsDatabaseCreateStatus").hide();
          jQuery("#btnDatabaseCreateSend").attr("readonly", false);
          if (parseInt(data.Status) > 0) {
            jQuery("#txtDatabaseCreateOk").show();
            jQuery("#txtDatabaseCreate_RegionId").text(
              jQuery("#fldDatabaseEdit_RegionId").val()
            );
            jQuery("#lnkDatabaseCreateDBURL").attr(
              "href",
              jQuery("#desinventarURL").val() +
                "/" +
                jQuery("#txtDatabaseCreate_RegionId").text()
            );
            jQuery("#lnkDatabaseCreateOpenDB").attr(
              "href",
              jQuery("#desinventarURL").val() +
                "/" +
                jQuery("#txtDatabaseCreate_RegionId").text()
            );
            jQuery("#divDatabaseCreateParams").hide();
            jQuery("#divDatabaseCreateResult").show();
          } else {
            jQuery("#txtDatabaseCreateError").show();
          }
        },
        "json"
      );
    } else {
      jQuery("#txtDatabaseCreateFormError").show();
      setTimeout(function() {
        jQuery(".clsDatabaseCreateStatus").hide();
      }, 3000);
    }
    return false;
  });

  jQuery("div.DatabaseEdit input.RegionId")
    .prop("disabled", true)
    .hide();
  jQuery("div.DatabaseEdit").on("dblclick", function() {
    if (jQuery("#desinventarUserRoleValue").val() >= 5) {
      var input = jQuery("div.DatabaseEdit input.RegionId");
      if (input.is(":visible")) {
        input.hide();
      } else {
        input.show();
        input.prop("disabled", false);
      }
    }
  });

  // Hide Send button until the combobox has been populated
  jQuery("#btnDatabaseCreateSend").hide();
}

function doDatabaseCreateShow() {
  // Populate fields if neccesary
  var iCount = jQuery("#fldDatabaseEdit_CountryIso option").length;
  if (iCount < 2) {
    doDatabaseCreatePopulateLists();
  }

  // Set default values in form
  jQuery(".clsDatabaseCreateStatus").hide();
  jQuery("#fldDatabaseEdit_RegionId").val("");
  jQuery("#fldDatabaseEdit_CountryIso").val("");
  jQuery("#fldDatabaseEdit_RegionLabel").val("");
  jQuery("#fldDatabaseEdit_RegionActive").attr("checked", true);
  jQuery("#trDatabaseEdit_RegionActive").hide();
  jQuery("#fldDatabaseEdit_RegionPublic").attr("checked", false);
  jQuery("#fldDatabaseEdit_LangIsoCode").val(jQuery("#desinventarLang").val());

  jQuery("#divDatabaseCreateParams").show();
  jQuery("#divDatabaseCreateResult").hide();

  // Show form
  Ext.getCmp("wndDatabaseCreate").show();
  jQuery("#fldDatabaseEdit_CountryIso").focus();
}

function doDatabaseCreateValidate() {
  if (jQuery.trim(jQuery("#fldDatabaseEdit_RegionLabel").val()) === "") {
    return false;
  }
  if (jQuery("#fldDatabaseEdit_RegionId").val() === "") {
    return false;
  }
  return true;
}

function doDatabaseCreatePopulateLists() {
  // async Populate CountryIso - LanguageList fields
  jQuery.post(
    jQuery("#desinventarURL").val() + "/",
    {
      cmd: "cmdGetLocaleList"
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        jQuery("#fldDatabaseEdit_LangIsoCode")
          .empty()
          .select2({
            data: data.LanguageList,
            minimumResultsForSearch: Infinity
          });
        jQuery("#fldDatabaseEdit_LangIsoCode").val(
          jQuery("#desinventarLang").val()
        );
        jQuery("#fldDatabaseEdit_CountryIso")
          .empty()
          .select2({
            data: data.CountryList
          });
        jQuery("#btnDatabaseCreateSend").show();
        jQuery("#fldDatabaseEdit_CountryIso").focus();
      }
    },
    "json"
  );
}
