function doUpdateDatabaseListByUser() {
  jQuery('.contentBlock').hide()
  jQuery('#divRegionList').show()
  // Hide everything at start...
  jQuery('.databaseTitle').hide()
  jQuery('.databaseList').hide()

  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdSearchDB',
      searchDBQuery: '',
      searchDBCountry: 0
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        if (parseInt(data.NoOfDatabases) > 0) {
          jQuery('#divDatabaseFindList').show()
          jQuery('#divDatabaseFindError').hide()
          RegionByRole = new Array(5)
          RegionByRole['ADMINREGION'] = new Array()
          RegionByRole['SUPERVISOR'] = new Array()
          RegionByRole['USER'] = new Array()
          RegionByRole['OBSERVER'] = new Array()
          RegionByRole['NONE'] = new Array()

          $RoleList = new Array(5)
          var iCount = 0
          jQuery('#divDatabaseFindList table.databaseList').each(function() {
            jQuery('tr:gt(0)', this).remove()
            jQuery('tr', this).hide()
          })
          jQuery.each(data.RegionList, function(RegionId, value) {
            jQuery('#divRegionList #title_' + value.Role).show()
            jQuery('#divRegionList #list_' + value.Role).show()
            var list = jQuery('#divRegionList #list_' + value.Role).show()
            var item = jQuery('tr:last', list)
              .clone()
              .show()
            jQuery('td.RegionId', item).text(RegionId)
            jQuery('td span.RegionLabel', item).text(value.RegionLabel)
            jQuery('td a.RegionLink', item).attr(
              'href',
              jQuery('#desinventarURL').val() + '/#' + RegionId + '/'
            )
            list.append(item)
            iCount++
          })
          jQuery('#divDatabaseFindList td.RegionDelete').hide()
          if (jQuery('#desinventarUserRoleValue').val() >= 5) {
            jQuery('#divDatabaseFindList td.RegionDelete').show()
          }
        } else {
          jQuery('#divDatabaseFindList').hide()
          jQuery('#divDatabaseFindError').show()
        }
      }
    },
    'json'
  )
}

function doGetRegionInfo(RegionId) {
  jQuery('#divRegionInfo #divRegionLogo').html(
    '<img src="' +
      jQuery('#desinventarURL').val() +
      '/images/loading.gif" alt="" />'
  )
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseGetInfo',
      RegionId: RegionId,
      LangIsoCode: jQuery('#desinventarLang').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        var i = data.RegionInfo
        jQuery('#divRegionInfo').show()
        jQuery('#divRegionInfo #divRegionLogo').html(
          '<img src="' +
            jQuery('#desinventarURL').val() +
            '/?cmd=cmdDatabaseGetLogo&RegionId=' +
            RegionId +
            '" alt="" />'
        )
        jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel)
        jQuery('#divRegionInfo #txtRegionPeriod').text(
          i.PeriodBeginDate + ' - ' + i.PeriodEndDate
        )
        jQuery('#divRegionInfo #txtRegionNumberOfRecords').text(
          i.NumberOfRecords
        )
        jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate)

        jQuery('div.RegionInfo div.InfoGeneral').hide()
        if (i.InfoGeneral != '') {
          jQuery('div.RegionInfo div.InfoGeneral span.text').html(i.InfoGeneral)
          jQuery('div.RegionInfo div.InfoGeneral').show()
        }
        jQuery('div.RegionInfo div.InfoCredits').hide()
        if (i.InfoCredits != '') {
          jQuery('div.RegionInfo div.InfoCredits span.text').html(i.InfoCredits)
          jQuery('div.RegionInfo div.InfoCredits').show()
        }
        jQuery('div.RegionInfo div.InfoSources').hide()
        if (i.InfoSources != '') {
          jQuery('div.RegionInfo div.InfoSources span.text').html(i.InfoSources)
          jQuery('div.RegionInfo div.InfoSources').show()
        }
        jQuery('div.RegionInfo div.InfoSynopsis').hide()
        if (i.InfoSynopsis != '') {
          jQuery('div.RegionInfo div.InfoSynopsis span.text').html(
            i.InfoSynopsis
          )
          jQuery('div.RegionInfo div.InfoSynopsis').show()
        }
      }
    },
    'json'
  )
}
