/* global
  createThematicMap,
  onReadyStatistic,
  showtip,
  onReadyDBConfigCauses,
	onReadyDatabaseList,
	onReadyDatabaseUpload,
	onReadyDatabaseUsers,
	onReadyQueryDesign,
	onReadyGeography,
	onReadyGeolevels,
	onReadyDatabaseEvents,
	onReadyDatabaseCauses,
	onReadyUserPermAdmin,
	onReadyCommon,
	onReadyUserLogin,
	onReadyUserAccount,
	onReadyDatacards,
	onReadyAdminDatabase,
	onReadyExtraEffects,
	onReadyQueryResults,
	onReadyData,
	onReadyGraphic,
	onReadyThematicMap,
	onReadyStatParams
*/

import 'jquery-ui/ui/widgets/tabs'
import mainExt from './main_ext'
import databaseCreate from './database_create'
import adminUsers from './admin_users'

export default {
  init: onReadyMain
}

function onReadyMain() {
  mainExt.init()
  onReadyDatabaseList()
  onReadyDatabaseUpload()
  databaseCreate.init()
  onReadyDatabaseUsers()
  onReadyQueryDesign()
  onReadyGeography()
  onReadyGeolevels()
  onReadyDatabaseEvents()
  onReadyDatabaseCauses()
  adminUsers.init()
  onReadyUserPermAdmin()
  onReadyCommon()
  onReadyUserLogin()
  onReadyUserAccount()
  onReadyDatacards()
  onReadyAdminDatabase()
  onReadyExtraEffects()
  onReadyQueryResults()
  onReadyData()
  onReadyGraphic()
  onReadyThematicMap()
  onReadyStatParams()

  jQuery('#frmMainQuery').submit(function() {
    var myURL = jQuery(this).attr('action')
    var myCmd = jQuery('#prmQueryCommand').val()
    if (
      myCmd == 'cmdGridSave' ||
      myCmd == 'cmdGraphSave' ||
      myCmd == 'cmdMapSave' ||
      myCmd == 'cmdStatSave' ||
      myCmd == 'cmdQuerySave'
    ) {
      return true
    } else {
      //jQuery('body').trigger('cmdMainWaitingShow');
      jQuery('#divRegionInfo').hide()
      jQuery('#dcr').show()
      jQuery('#dcr').html(
        '<img src="' +
          jQuery('#desinventarURL').val() +
          '/images/loading.gif" alt="" />'
      )
      jQuery.post(myURL, jQuery(this).serialize(), function(data) {
        //jQuery('body').trigger('cmdMainWaitingHide');
        jQuery('#dcr').html(data)
        switch (myCmd) {
          case 'cmdGridShow':
            jQuery('body').trigger('cmdViewDataUpdate')
            break
          case 'cmdMapShow':
            createThematicMap()
            break
          case 'cmdGraphShow':
            break
          case 'cmdStatShow':
            onReadyStatistic()
            break
          default:
            break
        }
      })
      return false
    }
  })

  jQuery('#DBConfig_Geolevels').on('show', function() {
    jQuery('body').trigger('cmdGeolevelsShow')
  })
  jQuery('#DBConfig_Geography').on('show', function() {
    jQuery('body').trigger('cmdGeographyShow')
  })

  jQuery('#DBConfig_Events').on('show', function() {
    jQuery('body').trigger('cmdDatabaseEventsShow')
  })

  jQuery('#DBConfig_Causes').on('show', function() {
    jQuery('body').trigger('cmdDatabaseCausesShow')
  })

  jQuery('#DBConfig_Users').on('show', function() {
    jQuery('body').trigger('cmdDatabaseUsersShow')
  })

  // Tabs for Database Configuration
  jQuery('#DBConfig_tabs').tabs()
  jQuery('.classDBConfig_tabs').on('click', function() {
    var me = jQuery(jQuery(this).attr('href'))
    showtip(me.find('.helptext').text())
    var cmd = jQuery(this).data('cmd')
    if (cmd == undefined || cmd == '') {
      jQuery(me).trigger('show')
    } else {
      me
        .find('.content')
        .html(
          '<img src="' +
            jQuery('#desinventarURL').val() +
            '/images/loading.gif" alt="" />'
        )
      jQuery.post(
        jQuery(this).data('url'),
        {
          cmd: cmd,
          RegionId: jQuery('#desinventarRegionId').val(),
          lang: jQuery('#desinventarLang').val()
        },
        function(data) {
          me.find('.content').html(data)
          switch (cmd) {
            case 'cmdDBInfoCause':
              onReadyDBConfigCauses()
              break
            default:
              onReadyExtraEffects()
              break
          }
        }
      )
    }
    return false
  })
  jQuery(window).trigger('hashchange')
}
