import 'jquery-ui/ui/widgets/tabs'
import mainExt from './main_ext'
import databaseCreate from './database_create'
import adminUsers from './admin_users'
import adminDatabase from './admin_database'
import databaseCauses from './database_causes'
import databaseEvents from './database_events'
import databaseGeography from './database_geography'
import databaseGeoLevels from './database_geolevels'
import databaseUsers from './database_users'
import databaseUpload from './database_upload'
import datacards from './datacards'
import userPermAdmin from './userperm_admin'
import thematicMap from './thematicmap'
import statParams from './stat_params'
import statistic from './statistic'
import queryDesign from './query_design'
import initialize from './init'
import graphics from './graphic'
import viewData from './data'
import userLogin from './user_login'
import regionInfo from './region_info'
import databaseList from './database_list'
import userAccount from './user_account'
import extraEffects from './extraeffects'
import queryResults from './query_results'
import common from './common'

function init() {
  initialize.init()
  mainExt.init()
  databaseList.init()
  databaseUpload.init()
  databaseCreate.init()
  databaseUsers.init()
  queryDesign.init()
  databaseGeography.init()
  databaseGeoLevels.init()
  databaseEvents.init()
  databaseCauses.init()
  adminUsers.init()
  userPermAdmin.init()
  common.init()
  userLogin.init()
  userAccount.init()
  datacards.init()
  adminDatabase.init()
  extraEffects.init()
  queryResults.init()
  viewData.init()
  graphics.init()
  thematicMap.init()
  statParams.init()
  regionInfo.init()

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
            thematicMap.createThematicMap()
            break
          case 'cmdGraphShow':
            break
          case 'cmdStatShow':
            statistic.init()
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
    common.showtip(me.find('.helptext').text())
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
          initTabEvents(cmd)
        }
      )
    }
    return false
  })
  jQuery(window).trigger('hashchange')
}

function initTabEvents(cmd) {
  if (cmd === 'cmdDBInfoEEField') {
    extraEffects.init()
    return
  }
}

export default {
  init
}
