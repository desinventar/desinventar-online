/* global desinventar, Ext */

import queryDesign from './queryDesign'

function init() {
  jQuery('body').on('cmdDatabaseLoadData', function(e, params) {
    doDatabaseLoadData(params)
  })

  jQuery(window).bind('hashchange', function() {
    // @ts-ignore
    var url = jQuery.param.fragment()
    var options = url.split('/')
    var RegionId = options[0]
    jQuery('#desinventarRegionId').val(RegionId)
    jQuery('body').trigger('cmdDatabaseLoadData')
  })

  jQuery('#openquery')
    .find('#ofile')
    .on('change', async function(evt) {
      // @ts-ignore
      Ext.getCmp('wndQueryOpen').hide()
      // @ts-ignore
      var files = evt.target.files
      var file = files[0] || false
      if (!file) {
        return
      }
      const response = await queryDesign.loadQueryFromFile(file)
      if (response.data) {
        const data = response.data.queryDefinition
        const vue = queryDesign.getQueryDesignInstance()
        vue._data.beginYear = data.D_DisasterBeginTime[0]
        vue._data.beginMonth = data.D_DisasterBeginTime[1]
        vue._data.beginDay = data.D_DisasterBeginTime[2]
        vue._data.endYear = data.D_DisasterEndTime[0]
        vue._data.endMonth = data.D_DisasterEndTime[1]
        vue._data.endDay = data.D_DisasterEndTime[2]
      }
    })
}

function doDatabaseLoadData(params) {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdDatabaseLoadData',
      RegionId: jQuery('#desinventarRegionId').val()
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        jQuery('body').data('params', data.params)
        // @ts-ignore
        desinventar.info = data.info
        //Compatibility with old methods desinventarinfo.tpl
        jQuery('#desinventarUserId').val(data.params.UserId)
        jQuery('#desinventarUserFullName').val(data.params.UserFullName)
        jQuery('#desinventarUserRole').val(data.params.UserRole)
        jQuery('#desinventarUserRoleValue').val(data.params.UserRoleValue)
        if (data.RegionId != '') {
          // Initialize data-* components for body
          jQuery('body').data('RegionId', data.RegionId)
          jQuery('body').data('GeolevelsList', data.GeolevelsList)
          jQuery('body').data('EventList', data.EventList)
          jQuery('body').data('CauseList', data.CauseList)
          jQuery('body').data('EEFieldList', data.EEFieldList)
          jQuery('body').data('RecordCount', data.RecordCount)

          var dataItems = jQuery('body').data()
          jQuery.each(dataItems, function(index) {
            if (`${index}`.substr(0, 13) === 'GeographyList') {
              jQuery('body').removeData(`${index}`)
            }
          })
          jQuery('body').data('GeographyList', data.GeographyList)

          jQuery('#desinventarLang').val(data.params.LangIsoCode)
          jQuery('#desinventarRegionId').val(data.params.RegionId)
          jQuery('#desinventarRegionLabel').val(data.params.RegionLabel)
          jQuery('#desinventarNumberOfRecords').val(data.RecordCount)
          // Trigger event on mainblock components to update them
          jQuery('.mainblock').trigger('cmdInitialize')
        }
      } else {
        jQuery('#desinventarRegionId').val('')
        window.location.hash = ''
      }
      var updateViewport = true
      if (
        typeof params !== 'undefined' &&
        typeof params.updateViewport !== 'undefined'
      ) {
        updateViewport = params.updateViewport
      }
      if (updateViewport) {
        // Trigger ViewportShow
        jQuery('body').trigger('cmdViewportShow')
      }
      if (params && typeof params.callback === 'function') {
        params.callback()
      }
    },
    'json'
  )
}

export default {
  init
}
