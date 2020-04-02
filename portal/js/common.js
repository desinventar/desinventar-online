const showdown = require('showdown')
const markdown = new showdown.Converter()

const common = {}

function doGetRegionInfo(RegionId) {
  jQuery('#divRegionInfo #divRegionLogo').html(
    '<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" />'
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
        jQuery('#title_COUNTRY')
          .text(i.CountryName)
          .show()
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
        jQuery('#divRegionInfo #txtRegionNumDatacards').text(i.NumberOfRecords)
        jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate)

        jQuery('#divRegionInfo #divInfoGeneral').hide()
        if (i.InfoGeneral != '') {
          jQuery('#divRegionInfo #divInfoGeneral #Text').html(
            markdown.makeHtml(i.InfoGeneral)
          )
          jQuery('#divRegionInfo #divInfoGeneral').show()
        }
        jQuery('#divRegionInfo #divInfoCredits').hide()
        if (i.InfoCredits != '') {
          jQuery('#divRegionInfo #divInfoCredits #Text').html(
            markdown.makeHtml(i.InfoCredits)
          )
          jQuery('#divRegionInfo #divInfoCredits').show()
        }
        jQuery('#divRegionInfo #divInfoSources').hide()
        if (i.InfoSources != '') {
          jQuery('#divRegionInfo #divInfoSources #Text').html(
            markdown.makeHtml(i.InfoSources)
          )
          jQuery('#divRegionInfo #divInfoSources').show()
        }
        jQuery('#divRegionInfo #divInfoSynopsis').hide()
        if (i.InfoSynopsis != '') {
          jQuery('#divRegionInfo #divInfoSynopsis #Text').html(
            markdown.makeHtml(i.InfoSynopsis)
          )
          jQuery('#divRegionInfo #divInfoSynopsis').show()
        }
      }
    },
    'jsonp'
  )
}

common.updateDatabaseList = CountryIsoCode => {
  jQuery('.contentBlock').hide()
  // Hide everything at start...
  jQuery('.databaseTitle').hide()
  jQuery('.databaseList').hide()
  jQuery('.contentRegionBlock').hide()
  jQuery('#divRegionList').hide()
  jQuery.get(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'getCountryName',
      CountryIso: CountryIsoCode
    },
    function(data) {
      jQuery('#title_COUNTRY')
        .text(data.CountryName)
        .show()
    },
    'jsonp'
  )
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdSearchDB',
      searchDBQuery: CountryIsoCode,
      searchDBCountry: 1
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        var iCount = 0

        // Hide everything at start...
        jQuery('.databaseTitle').hide()
        jQuery('.databaseList').hide()

        var jList = jQuery('#list_COUNTRY')
        var myRegionId = ''
        jList.empty()
        jQuery.each(data.RegionList, function(RegionId, value) {
          iCount++
          jList.append(
            '<a href="#" id="' +
              RegionId +
              '" class="databaseLink">' +
              value.RegionLabel +
              '</a><br />'
          )
          myRegionId = RegionId
        })
        if (iCount == 1) {
          // If only one region is in list, show directly info instead of list
          displayRegionInfo(myRegionId)
        } else {
          jQuery('#title_COUNTRY').show()
          jQuery('#list_COUNTRY').show()
          jQuery('.databaseLink')
            .addClass('alt')
            .off('click')
            .on('click', function() {
              displayRegionInfo(jQuery(this).attr('id'))
              return false
            })
          jQuery('#regionBlock').show()
          jQuery('#divRegionList').show()
        }
      }
    },
    'jsonp'
  )
}

common.updateDatabaseListByUser = () => {
  jQuery('.contentBlock').hide()
  jQuery('#divRegionList').show()
  // Hide everything at start...
  jQuery('.databaseTitle').hide()
  jQuery('.databaseList').hide()
  jQuery('.contentRegionBlock').hide()

  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdSearchDB',
      searchDBQuery: '',
      searchDBCountry: 0
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        jQuery('.databaseList').empty()
        jQuery.each(data.RegionList, function(RegionId, value) {
          jQuery('#divRegionList #title_' + value.Role).show()
          jQuery('#divRegionList #list_' + value.Role)
            .show()
            .append(
              '<a href="#" id="' +
                RegionId +
                '" class="databaseLink">' +
                value.RegionLabel +
                '</a><br />'
            )
        })

        jQuery('.databaseLink')
          .addClass('alt')
          .unbind('click')
          .click(function() {
            const RegionId = jQuery(this).attr('id')
            if (jQuery('#desinventarPortalType').val() != '') {
              displayRegionInfo(RegionId)
            } else {
              window.location = jQuery('#desinventarURL').val() + '/' + RegionId
            }
            return false
          })
        jQuery('#title_COUNTRY').text('')
        jQuery('#regionBlock').show()
        jQuery('#divRegionList').show()
      }
    },
    'jsonp'
  )
}

function displayRegionInfo(RegionId) {
  jQuery('.contentBlock').hide()
  jQuery('#pageinfo').hide()
  doGetRegionInfo(RegionId)
  jQuery('#desinventarRegionId').val(RegionId)
  jQuery('#regionBlock').show()
  jQuery('#pageinfo').show()
}

common.displayRegionInfo = displayRegionInfo
common.doGetRegionInfo = doGetRegionInfo

export default common
