/* global
  Ext,
  DesInventar,
  showtip,
  sendGraphic,
  sendStatistic,
  doUpdateDatabaseListByUser,
  saveQuery,
  sendMap,
  sendList,
  doGetRegionInfo,
  updateList,
*/
import databaseCreate from './database_create.js'
import adminDatabase from './admin_database'
import databaseUpload from './database_upload'

export default {
  init: onReadyExtJS
}

function onReadyExtJS() {
  Ext.BLANK_IMAGE_URL =
    jQuery('#desinventarLibs')
      .val()
      .trim() + '/extjs/3.4.0/resources/images/default/s.gif'
  Ext.ns('DesInventar')

  // Hide Loading div...
  jQuery('#loading').hide()
  jQuery('#loading-mask').hide()

  // 2011-04-29 (jhcaiced) Fix for use of ExtJS in IE9 ?
  if (
    typeof Range !== 'undefined' &&
    !Range.prototype.createContextualFragment
  ) {
    Range.prototype.createContextualFragment = function(html) {
      var frag = document.createDocumentFragment(),
        div = document.createElement('div')
      frag.appendChild(div)
      div.outerHTML = html
      return frag
    }
  }

  jQuery('body').on('cmdViewportShow', function() {
    doViewportShow()
  })

  doDialogsCreate()
  doMainMenuCreate()
  doViewportCreate()
}

function doViewportCreate() {
  DesInventar.WestPanel = Ext.extend(Ext.Panel, {
    initComponent: function() {
      var config = {
        region: 'west',
        border: false,
        split: false,
        layout: 'fit',
        width: 350,
        title: jQuery('#msgQueryDesignTitle').text(),
        autoScroll: true,
        margins: '0 2 0 0',
        contentEl: 'divWestPanel',
        floatable: true
      }
      Ext.apply(this, config)
      Ext.apply(this.initialConfig, config)
      this.defwidth = this.width
      this.deftitle = this.title
      DesInventar.WestPanel.superclass.initComponent.call(this)
    }
  })
  DesInventar.Viewport = Ext.extend(Ext.Viewport, {
    initComponent: function() {
      var config = {
        contentEl: 'divViewport',
        layout: 'border',
        border: false,
        items: [
          {
            region: 'north',
            height: 30,
            border: false,
            contentEl: 'north',
            collapsible: false,
            tbar: new DesInventar.Toolbar({
              id: 'toolbar',
              MenuHandler: doMainMenuHandler
            })
          },
          new DesInventar.WestPanel({ id: 'westm', collapsible: true }),
          {
            region: 'south',
            id: 'southm',
            split: false,
            title: jQuery('#msgHelpTitle').text(),
            height: 80,
            minSize: 100,
            maxSize: 200,
            margins: '0 0 0 0',
            contentEl: 'south',
            collapsible: true
          },
          new Ext.Panel({
            region: 'center',
            id: 'centerm',
            contentEl: 'container',
            autoScroll: true
          })
        ]
      }
      Ext.apply(this, config)
      Ext.apply(this.initialConfig, config)
      DesInventar.Viewport.superclass.initComponent.call(this)
    }
  })
  var viewport = new DesInventar.Viewport({ id: 'viewport' })
  viewport.show()
  jQuery('#westm .x-panel-header-text').attr(
    'title',
    jQuery('#msgQueryDesignTooltip').text()
  )
  Ext.getCmp('westm').on('expand', function() {
    jQuery('#divRegionInfo').hide()
  })
}

function doViewportShow() {
  jQuery('body').trigger('cmdMainMenuUpdate')
  jQuery('body').trigger('cmdMainWaitingHide')
  var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val())
  var RegionId = jQuery('#desinventarRegionId').val()

  var title = jQuery('div.desinventarInfo span.title').text()

  jQuery('.contentBlock').hide()
  if (RegionId != '') {
    title = title + ' | ' + jQuery('#desinventarRegionLabel').val()
    if (UserRoleValue > 0) {
      Ext.getCmp('westm').show()
      Ext.getCmp('viewport').doLayout()
      Ext.getCmp('westm').expand()
      jQuery('#divQueryResults').show()
      jQuery('body').trigger('cmdQueryResultsButtonHide')
      jQuery('#dcr').hide()
    } else {
      Ext.getCmp('westm').hide()
      Ext.getCmp('viewport').doLayout()
      jQuery('#divDatabasePrivate').show()
    }
  } else {
    title =
      title + ' | ' + jQuery('div.desinventarInfo span.region_list').text()
    Ext.getCmp('westm').hide()
    Ext.getCmp('viewport').doLayout()
    jQuery('#divRegionList').show()
    doUpdateDatabaseListByUser()
  }
  jQuery(document).attr('title', title)
}

function doMainChangeLanguage(LangIsoCode) {
  jQuery.post(
    jQuery('#desinventarURL').val() + '/session/change-language',
    {
      language: LangIsoCode
    },
    function() {
      jQuery('body').trigger('cmdWindowReload')
    },
    'json'
  )
}

function doMainMenuHandler(item) {
  var menuCmd = ''
  if (item.itemid != undefined) {
    menuCmd = item.itemid
  }
  if (item.id != undefined) {
    menuCmd = item.id
  }
  var RegionId = jQuery('#desinventarRegionId').val()
  menuHandlerForUser(menuCmd)
  menuHandlerForQuery(menuCmd)
  switch (menuCmd) {
    case 'mnuDatacardEdit':
      jQuery('#cardsRecordNumber').val(0)
      jQuery('#cardsRecordSource').val('')
      jQuery('body').trigger('cmdDatacardShow')
      break
    case 'mnuDatacardImport':
      hideQueryDesign()
      jQuery('.contentBlock').hide()
      jQuery('#divDatacardsImport').show()
      updateList(
        'divDatacardsImport',
        jQuery('#desinventarURL').val() + '/import.php',
        'r=' + RegionId
      )
      break
    case 'mnuDatacardSetup':
      hideQueryDesign()
      doMainMenuToggle(false)
      Ext.getCmp('mnuDatacard').enable()
      Ext.getCmp('mnuDatacardEdit').hide()
      Ext.getCmp('mnuDatacardSetup').hide()
      Ext.getCmp('mnuDatacardSetupEnd').show()
      Ext.getCmp('mnuDatacardSetupEnd').enable()
      jQuery('.contentBlock').hide()
      jQuery('.classDBConfig_tabs:first').trigger('click')
      jQuery('#divDatabaseConfiguration').show()
      jQuery('#tabDatabaseConfiguration').show()
      break
    case 'mnuDatacardSetupEnd':
      doMainMenuToggle(true)
      jQuery('body').trigger('cmdMainMenuUpdate')
      jQuery('body').trigger('cmdDatabaseLoadData')
      break
    case 'mnuUserAccountManagement':
      jQuery('div.AdminUsers').trigger('cmdLoadData')
      Ext.getCmp('wndAdminUsers').show()
      break
    case 'mnuAdminDatabases':
      jQuery('.contentBlock').hide()
      jQuery('#divAdminDatabase').show()
      adminDatabase.updateList()
      break
    case 'mnuHelpAbout':
      Ext.getCmp('wndDialog').show()
      break
    case 'mnuHelpWebsite':
      window.open('http://www.desinventar.org', '', '')
      break
    case 'mnuHelpMethodology':
      var url = 'http://www.desinventar.org'
      if (jQuery('#desinventarLang').val() == 'spa') {
        url = url + '/es/metodologia'
      } else {
        url = url + '/en/methodology'
      }
      window.open(url, '', '')
      break
    case 'mnuHelpDocumentation':
      window.open('http://www.desinventar.org/', '', '')
      break
  }
}

function menuHandlerForUser(menuCmd) {
  switch (menuCmd) {
    case 'mnuUserLogin':
    case 'mnuUserChangeLogin':
      jQuery('body').trigger('cmdUserLoginShow')
      break
    case 'mnuFileLogout':
      jQuery('body').trigger('cmdUserLogout')
      break
    case 'mnuUserChangePasswd':
      jQuery('body').trigger('cmdUserAccountShow')
      break
    case 'mnuFileLanguageEnglish':
      doMainChangeLanguage('en')
      break
    case 'mnuFileLanguageSpanish':
      doMainChangeLanguage('es')
      break
    case 'mnuFileLanguagePortuguese':
      doMainChangeLanguage('pt')
      break
    case 'mnuFileLanguageFrench':
      doMainChangeLanguage('fr')
      break
    case 'mnuFileInfo':
      jQuery('.contentBlock').hide()
      jQuery('#divQueryResults').show()
      jQuery('#dcr').hide()
      jQuery('#divRegionInfo').show()
      doGetRegionInfo(jQuery('#desinventarRegionId').val())
      Ext.getCmp('westm').collapse()
      break
    case 'mnuFileOpen':
      window.location.hash = ''
      break
    case 'mnuFileDownload':
      jQuery('.clsAdminDatabaseExport').hide()
      Ext.getCmp('wndDatabaseExport').show()
      jQuery('body').trigger('cmdDatabaseExport')
      break
    case 'mnuFileUploadCopy':
      doDatabaseUploadShow('Copy')
      break
    case 'mnuFileUploadReplace':
      doDatabaseUploadShow('Replace')
      break
    case 'mnuFileCreate':
      databaseCreate.show()
      break
  }
}

function menuHandlerForQuery(menuCmd) {
  switch (menuCmd) {
    // DesConsultar Menu Options
    case 'mnuQueryViewDesign':
      if (jQuery('#desinventarRegionId').val() != '') {
        jQuery('.contentBlock').hide()
        jQuery('#divQueryResults').show()
        Ext.getCmp('westm').expand()
      }
      break
    case 'mnuQueryViewData':
      jQuery('body').trigger('cmdViewDataParams')
      break
    case 'mnuQueryViewMap':
      jQuery('body').trigger('cmdViewMapParams')
      break
    case 'mnuQueryViewGraph':
      jQuery('body').trigger('cmdViewGraphParams')
      break
    case 'mnuQueryViewStd':
      jQuery('body').trigger('cmdViewStdParams')
      break
    case 'mnuQueryOptionNew':
      // Just reload the current region window...(need a better solution!!)
      window.location.reload()
      break
    case 'mnuQueryOptionSave':
      saveQuery()
      break
    case 'mnuQueryOptionOpen':
      Ext.getCmp('wndQueryOpen').show()
      break
    case 'mnuQueryResultSave':
      jQuery('#btnResultSave').trigger('click')
      break
    case 'mnuQueryResultSaveAsXLS':
      jQuery('#btnResultSaveXLS').trigger('click')
      break
    case 'mnuQueryResultSaveAsCSV':
      jQuery('#btnResultSaveCSV').trigger('click')
      break
    case 'mnuQueryResultPrint':
      jQuery('#btnResultPrint').trigger('click')
      break
  }
}

function doDatabaseUploadShow(prmMode) {
  jQuery('#fldDatabaseUploadMode').val(prmMode)
  if (prmMode == 'Copy') {
    Ext.getCmp('wndDatabaseUpload').setTitle(jQuery('#mnuDatabaseCopy').text())
  } else {
    Ext.getCmp('wndDatabaseUpload').setTitle(
      jQuery('#mnuDatabaseReplace').text()
    )
  }
  jQuery('.clsDatabaseUpload').hide()
  databaseUpload.reset(true)
  Ext.getCmp('wndDatabaseUpload').show()
}

function hideQueryDesign() {
  // Hide Query Design Panel
  Ext.getCmp('westm').collapse()
}

function doMainMenuCreate() {
  DesInventar.Toolbar = Ext.extend(Ext.Toolbar, {
    initComponent: function() {
      var config = {
        overflow: 'visible',
        items: []
      }
      Ext.apply(this, config)
      Ext.apply(this.initialConfig, config)
      DesInventar.Toolbar.superclass.initComponent.call(this)
      this.initializeToolbar()
    },
    initializeToolbar: function() {
      var mnuFileUpload = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuFileUploadCopy',
            text: jQuery('span#msgFileUploadCopy').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileUploadReplace',
            text: jQuery('span#msgFileUploadReplace').text(),
            handler: this.MenuHandler
          }
        ]
      })
      var mnuFileLanguage = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuFileLanguageEnglish',
            text: jQuery('span#msgFileLanguageEnglish').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileLanguageSpanish',
            text: jQuery('span#msgFileLanguageSpanish').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileLanguagePortuguese',
            text: jQuery('span#msgFileLanguagePortuguese').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileLanguageFrench',
            text: jQuery('span#msgFileLanguageFrench').text(),
            handler: this.MenuHandler
          }
        ]
      })

      var mnuFile = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuFileCreate',
            text: jQuery('span#msgFileCreate').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileOpen',
            text: jQuery('span#msgFileOpen').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileDownload',
            text: jQuery('span#msgFileDownload').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileUpload',
            text: jQuery('span#msgFileUpload').text(),
            menu: mnuFileUpload
          },
          '-',
          {
            id: 'mnuFileInfo',
            text: jQuery('span#msgFileInfo').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuFileLanguage',
            text: jQuery('span#msgFileLanguage').text(),
            menu: mnuFileLanguage
          },
          {
            id: 'mnuFileLogout',
            text: jQuery('span#msgFileLogout').text(),
            handler: this.MenuHandler
          }
        ]
      })

      var mnuUser = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuUserLogin',
            text: jQuery('span#msgUserLogin').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuUserChangeLogin',
            text: jQuery('span#msgUserChangeLogin').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuUserChangePasswd',
            text: jQuery('span#msgUserChangePasswd').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuUserAccountManagement',
            text: jQuery('span#msgUserAccountManagement').text(),
            handler: this.MenuHandler
          }
        ]
      })

      var mnuQueryOption = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuQueryOptionNew',
            text: jQuery('span#msgQueryOptionNew').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryOptionSave',
            text: jQuery('span#msgQueryOptionSave').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryOptionOpen',
            text: jQuery('span#msgQueryOptionOpen').text(),
            handler: this.MenuHandler
          }
        ]
      })

      var mnuQueryResultSaveAs = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuQueryResultSaveAsXLS',
            text: jQuery('span#msgQueryResultSaveAsXLS').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryResultSaveAsCSV',
            text: jQuery('span#msgQueryResultSaveAsCSV').text(),
            handler: this.MenuHandler
          }
        ]
      })

      var mnuQuery = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuQueryViewDesign',
            text: jQuery('span#msgQueryViewDesign').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryViewData',
            text: jQuery('span#msgQueryViewData').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryViewMap',
            text: jQuery('span#msgQueryViewMap').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryViewGraph',
            text: jQuery('span#msgQueryViewGraph').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryViewStd',
            text: jQuery('span#msgQueryViewStd').text(),
            handler: this.MenuHandler
          },
          '-',
          {
            id: 'mnuQueryResultSave',
            text: jQuery('span#msgQueryResultSave').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryResultSaveAs',
            text: jQuery('span#msgQueryResultSaveAs').text(),
            menu: mnuQueryResultSaveAs
          },
          {
            id: 'mnuQueryResultPrint',
            text: jQuery('span#msgQueryResultPrint').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuQueryOption',
            text: jQuery('span#msgQueryOption').text(),
            menu: mnuQueryOption
          }
        ]
      })
      var mnuDatacard = new Ext.menu.Menu({
        items: [
          {
            id: 'mnuDatacardEdit',
            text: jQuery('span#msgDatacardEdit').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuDatacardSetup',
            text: jQuery('span#msgDatacardSetup').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuDatacardSetupEnd',
            text: jQuery('span#msgDatacardSetupEnd').text(),
            handler: this.MenuHandler
          }
        ]
      })
      var mnuHelp = new Ext.menu.Menu({
        style: { overflow: 'visible' },
        items: [
          {
            id: 'mnuHelpDocumentation',
            text: jQuery('span#msgHelpDocumentation').text(),
            handler: this.MenuHandler
          },
          {
            id: 'mnuHelpMethodology',
            text: jQuery('span#msgHelpMethodology').text(),
            handler: this.MenuHandler
          },
          '-',
          {
            id: 'mnuHelpWebsite',
            text: jQuery('span#msgHelpWebsite').text(),
            handler: this.MenuHandler
          },
          '-',
          {
            id: 'mnuHelpAbout',
            text: jQuery('span#msgHelpAbout').text(),
            handler: this.MenuHandler
          }
        ]
      })
      this.add({
        id: 'mnuFile',
        text: jQuery('span#msgFile').text(),
        menu: mnuFile
      })
      this.add({
        id: 'mnuUser',
        text: jQuery('span#msgUser').text(),
        menu: mnuUser
      })
      this.add({
        id: 'mnuQuery',
        text: jQuery('span#msgQuery').text(),
        menu: mnuQuery
      })
      this.add({
        id: 'mnuDatacard',
        text: jQuery('span#msgDatacard').text(),
        menu: mnuDatacard
      })
      this.add({
        id: 'mnuHelp',
        text: jQuery('span#msgHelp').text(),
        menu: mnuHelp
      })

      // This elements appear on reverse order on screen (?)
      this.add('->', {
        id: 'mnuHelpWebsiteLabel',
        text:
          '<img src="' +
          jQuery('#desinventarURL').val() +
          '/images/di_logo4.png" alt="" />'
      })
      this.add('->', { id: 'mnuRegionLabel', text: '' })
      //this.add('->',{id: 'mnuWaiting'         , text: '<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />', hidden: true });
      this.add('->', { id: 'mnuWaiting', text: '' })
    }
  })

  // Attach main events to body
  jQuery('body').on('cmdMainWaitingShow', function() {
    Ext.getCmp('mnuWaiting').show()
  })
  jQuery('body').on('cmdMainWaitingHide', function() {
    Ext.getCmp('mnuWaiting').hide()
  })

  jQuery('body').on('cmdMainMenuUpdate', function() {
    doMainMenuUpdate()
  })
  jQuery('body').on('cmdMainMenuResultButtonsEnable', function() {
    if (jQuery('#DCRes').val() == 'D' || jQuery('#DCRes').val() == 'S') {
      Ext.getCmp('mnuQueryResultSave').hide()
      Ext.getCmp('mnuQueryResultSaveAs').show()
      Ext.getCmp('mnuQueryResultSaveAs').enable()
      Ext.getCmp('mnuQueryResultSaveAsXLS').show()
      Ext.getCmp('mnuQueryResultSaveAsXLS').enable()
      Ext.getCmp('mnuQueryResultSaveAsCSV').show()
      Ext.getCmp('mnuQueryResultSaveAsCSV').enable()
    } else {
      Ext.getCmp('mnuQueryResultSave').show()
      Ext.getCmp('mnuQueryResultSave').enable()
      Ext.getCmp('mnuQueryResultSaveAs').hide()
      Ext.getCmp('mnuQueryResultSaveAsXLS').hide()
      Ext.getCmp('mnuQueryResultSaveAsCSV').hide()
    }
    Ext.getCmp('mnuQueryResultPrint').show()
    Ext.getCmp('mnuQueryResultPrint').enable()
  })
  jQuery('body').on('cmdMainMenuResultButtonsDisable', function() {
    Ext.getCmp('mnuQueryResultSave').hide()
    Ext.getCmp('mnuQueryResultSaveAs').hide()
    Ext.getCmp('mnuQueryResultSaveAsXLS').hide()
    Ext.getCmp('mnuQueryResultSaveAsCSV').hide()
    Ext.getCmp('mnuQueryResultPrint').hide()
  })
  jQuery('body').on('cmdMainWindowUpdate', function() {
    if (jQuery('#desinventarRegionId').val() != '') {
      Ext.getCmp('westm').collapse()
    }
    jQuery('.contentBlock').hide()
    jQuery('#divLoading').show()
    setTimeout(function() {
      jQuery('body').trigger('cmdDatabaseLoadData')
      jQuery('body').trigger('cmdMainMenuUpdate')
      if (jQuery('#desinventarRegionId').val() != '') {
        jQuery('body').trigger('cmdMainQueryUpdate')
      }
      jQuery('.contentBlock').hide()
      jQuery('#divLoading').hide()
      doViewportShow()
    }, 2000)
  })
}

function doMainMenuToggle(bEnable) {
  jQuery('#divMainMenu span.menu').each(function() {
    var w = Ext.getCmp(
      jQuery(this)
        .attr('id')
        .replace('msg', 'mnu')
    )
    if (w != undefined) {
      if (bEnable) {
        w.enable()
      } else {
        w.disable()
      }
    }
  })
}

function doMainMenuDisable() {
  jQuery('#divMainMenu span.item').each(function() {
    var id = jQuery(this)
      .attr('id')
      .replace('msg', 'mnu')
    var w = Ext.getCmp(id)
    if (w != undefined) {
      w.disable()
    }
  })
  jQuery('#divMainMenu span.submenu').each(function() {
    var w = Ext.getCmp(
      jQuery(this)
        .attr('id')
        .replace('msg', 'mnu')
    )
    if (w != undefined) {
      w.disable()
    }
  })
}

function doMainMenuUpdate() {
  doMainMenuDisable()

  // Menu items that are always enabled
  jQuery('#divMainMenu span.clsMenuAlwaysOn').each(function() {
    Ext.getCmp(
      jQuery(this)
        .attr('id')
        .replace('msg', 'mnu')
    ).enable()
  })
  Ext.getCmp('mnuUser').setText(jQuery('span#msgUser').text())
  jQuery('#divDatacardWindow span.UserInfo').text('')

  // Enable menu items when a User is logged in
  if (jQuery('#desinventarUserId').val() == '') {
    jQuery('#divMainMenu span.clsMenuWithoutUser').each(function() {
      Ext.getCmp(
        jQuery(this)
          .attr('id')
          .replace('msg', 'mnu')
      ).enable()
    })
    Ext.getCmp('mnuUserLogin').show()
    Ext.getCmp('mnuUserChangeLogin').hide()
  } else {
    jQuery('#divMainMenu span.clsMenuWithUser').each(function() {
      Ext.getCmp(
        jQuery(this)
          .attr('id')
          .replace('msg', 'mnu')
      ).enable()
    })
    Ext.getCmp('mnuUserLogin').hide()
    Ext.getCmp('mnuUserChangeLogin').show()
    Ext.getCmp('mnuUser').setText(
      jQuery('span#msgUser').text() + ' : ' + jQuery('#desinventarUserId').val()
    )
    jQuery('#divDatacardWindow span.UserInfo').text(
      jQuery('#desinventarUserId').val() +
        ' - ' +
        jQuery('#desinventarUserRole').val()
    )
  }

  // Configure which options are visible using RoleValue
  var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val())

  if (UserRoleValue >= 5) {
    Ext.getCmp('mnuUserAccountManagement').show()
    Ext.getCmp('mnuUserAccountManagement').enable()
  } else {
    Ext.getCmp('mnuUserAccountManagement').hide()
  }

  Ext.getCmp('mnuFileUploadReplace').hide()

  Ext.getCmp('mnuDatacard').hide()
  Ext.getCmp('mnuDatacardSetup').hide()
  Ext.getCmp('mnuDatacardSetupEnd').hide()
  Ext.getCmp('mnuDatacardEdit').hide()

  Ext.getCmp('mnuRegionLabel').setText('')
  // Show some menu items when a Region is Selected
  if (jQuery('#desinventarRegionId').val() != '') {
    Ext.getCmp('mnuRegionLabel').setText(
      '[' + jQuery('#desinventarRegionLabel').val() + ']'
    )
    if (UserRoleValue > 0) {
      jQuery('#divMainMenu span.clsMenuWithRegion').each(function() {
        Ext.getCmp(
          jQuery(this)
            .attr('id')
            .replace('msg', 'mnu')
        ).enable()
      })
      Ext.getCmp('mnuDatacardEdit').hide()

      //2012-03-01 Bug #55 -  Enable download function for any user that can view the database
      Ext.getCmp('mnuFileDownload').enable()
    }
    if (UserRoleValue >= 2) {
      // Edit datacards instead of only view them
      Ext.getCmp('mnuDatacard').show()
      Ext.getCmp('mnuDatacardEdit').show()
      Ext.getCmp('mnuDatacardEdit').enable()

      if (UserRoleValue >= 4) {
        Ext.getCmp('mnuFileUploadReplace').show()
        Ext.getCmp('mnuFileUploadReplace').enable()
        Ext.getCmp('mnuDatacardSetup').show()
        Ext.getCmp('mnuDatacardSetup').enable()
      }
    }
  }
  jQuery('body').trigger('cmdMainMenuResultButtonsDisable')
}

function doDialogsCreate() {
  // Query Open Window
  new Ext.Window({
    id: 'wndQueryOpen',
    el: 'qry-win',
    layout: 'fit',
    width: 300,
    height: 200,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'qry-cfg', autoScroll: true }),
    buttons: [
      {
        text: jQuery('#msgQueryOpenButtonClose').text(),
        handler: function() {
          Ext.getCmp('wndQueryOpen').hide()
        }
      }
    ]
  })

  // Database List - Database Search Window
  new Ext.Window({
    id: 'wndAdminUsers',
    el: 'divAdminUsersWin',
    layout: 'fit',
    x: 200,
    y: 100,
    width: 600,
    height: 480,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({
      contentEl: 'divAdminUsersContent',
      autoScroll: true
    })
  })

  new Ext.Window({
    id: 'wndDialog',
    el: 'dlg-win',
    layout: 'fit',
    x: 350,
    y: 200,
    width: 300,
    height: 150,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
  })

  // Datacard View/Edit Window
  var wndDatacard = new Ext.Window({
    id: 'wndDatacard',
    el: 'divDatacardWindow',
    layout: 'fit',
    width: 960,
    height: 638, //x: 65, y: 0,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'divDatacardContent', autoScroll: true })
  })
  wndDatacard.on('hide', function() {
    // If we were editing a datacard, this sould cancel the edit and release
    // the lock in the datacard
    jQuery('#btnDatacardCancel').trigger('click')
    // Hide the datacard dialog
    jQuery('#divDatacardWindow').hide()
    showtip('')
  })

  new Ext.Window({
    id: 'wndViewDataParams',
    el: 'divViewDataParamsWindow',
    layout: 'fit',
    width: 600,
    height: 420,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({
      contentEl: 'divViewDataParamsContent',
      autoScroll: true
    }),
    buttons: [
      {
        text: jQuery('#msgViewDataButtonClear').text(),
        handler: function() {
          resetForm('CD')
        }
      },
      {
        text: jQuery('#msgViewDataButtonSend').text(),
        handler: function() {
          if (sendList('result')) {
            $('DCRes').value = 'D'
            jQuery('body').trigger('cmdQueryResultsButtonShow')
            Ext.getCmp('wndViewDataParams').hide()
          } else {
            throw new Error('Error while executing function ViewData')
          }
        }
      },
      {
        text: jQuery('#msgViewDataButtonClose').text(),
        handler: function() {
          Ext.getCmp('wndViewDataParams').hide()
        }
      }
    ] //button
  })

  new Ext.Window({
    id: 'wndViewMapParams',
    el: 'map-win',
    layout: 'fit',
    width: 650,
    height: 420,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'map-cfg', autoScroll: true }),
    buttons: [
      {
        text: jQuery('#msgViewMapButtonClear').text(),
        handler: function() {
          resetForm('CM')
        }
      },
      {
        text: jQuery('#msgViewMapButtonSend').text(),
        handler: function() {
          if (sendMap('result')) {
            $('DCRes').value = 'M'
            Ext.getCmp('wndViewMapParams').hide()
            jQuery('body').trigger('cmdQueryResultsButtonShow')
          } else {
            throw new Error('Error while executing function ViewMap')
          }
        }
      },
      {
        text: jQuery('#msgViewMapButtonClose').text(),
        handler: function() {
          Ext.getCmp('wndViewMapParams').hide()
        }
      }
    ]
  })

  new Ext.Window({
    id: 'wndViewGraphParams',
    el: 'divGraphParameters',
    layout: 'fit',
    width: 750,
    height: 420,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'grp-cfg', autoScroll: true }),
    buttons: [
      {
        text: jQuery('#msgViewGraphButtonClear').text(),
        handler: function() {
          resetForm('frmGraphParams')
          jQuery('#prmGraphTypeHistogram').change()
        }
      },
      {
        text: jQuery('#msgViewGraphButtonSend').text(),
        handler: function() {
          sendGraphic('result')
          $('DCRes').value = 'G'
          Ext.getCmp('wndViewGraphParams').hide()
          jQuery('body').trigger('cmdQueryResultsButtonShow')
        }
      },
      {
        text: jQuery('#msgViewGraphButtonClose').text(),
        handler: function() {
          Ext.getCmp('wndViewGraphParams').hide()
        }
      }
    ]
  })

  new Ext.Window({
    id: 'wndViewStdParams',
    el: 'std-win',
    layout: 'fit',
    width: 600,
    height: 420,
    closeAction: 'hide',
    plain: true,
    animCollapse: false,
    constrainHeader: true,
    items: new Ext.Panel({ contentEl: 'std-cfg', autoScroll: true }),
    buttons: [
      {
        text: jQuery('#msgViewStdButtonClear').text(),
        handler: function() {
          resetForm('frmStatParams')
        }
      },
      {
        text: jQuery('#msgViewStdButtonSend').text(),
        handler: function() {
          if (sendStatistic('result')) {
            $('DCRes').value = 'S'
            Ext.getCmp('wndViewStdParams').hide()
            jQuery('body').trigger('cmdQueryResultsButtonShow')
          } else {
            throw new Error('Error while executing ViewStd function')
          }
        }
      },
      {
        text: jQuery('#msgViewStdButtonClose').text(),
        handler: function() {
          Ext.getCmp('wndViewStdParams').hide()
        }
      }
    ]
  })
}

function resetForm(id) {
  var form = document.getElementById(id)
  form.reset()
}
