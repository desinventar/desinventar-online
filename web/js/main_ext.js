/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
 - General ExtJS functions
*/
function onReadyExtJS()
{
	// Hide Loading div...
	jQuery('#loading').hide();
	jQuery('#loading-mask').hide();	

	// Initialize Ext.QuickTips
	Ext.QuickTips.init();
	Ext.apply(Ext.QuickTips.getQuickTip(), {maxWidth: 200, minWidth: 100, showDelay: 50, trackMouse: true});

	// 2011-04-29 (jhcaiced) Fix for use of ExtJS in IE9 ?
	if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
	{
		Range.prototype.createContextualFragment = function(html)
		{
			var frag = document.createDocumentFragment(), div = document.createElement("div");
			frag.appendChild(div);
			div.outerHTML = html;
			return frag;
		};
	}
	doCreateMainMenu();
	doCreateViewport();
	doCreateDialogs();
} //onReadyExtJS()

function doCreateViewport()
{
	// layout
	var viewport = new Ext.Viewport({
		id:'viewport',
		contentEl: 'divViewport',
		layout:'border',
		border: false,
		items:[
			{
				region:'north',
				height: 30,
				border: false,
				contentEl: 'north',
				collapsible: false
			},
			{
				region: 'west',
				id: 'westm',
				border: false,
				split: false,
				width: 350,
				title: jQuery('#msgQueryDesignTitle').text(),
				autoScroll: true,
				margins:'0 2 0 0',
				collapsible: true,
				contentEl: 'west'
			},
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
	}); //viewport

	if (jQuery('#desinventarRegionId').val() == '')
	{
		Ext.getCmp('westm').hide();
		viewport.doLayout();
	}
} // doCreateViewport()

function onMenuItem(item) {
	var RegionId = jQuery('#desinventarRegionId').val();
	switch (item.id) {
		case 'mnuRegionInfo':
			jQuery('#dcr').hide();
			doGetRegionInfo(jQuery('#desinventarRegionId').val());
			jQuery('#divRegionInfo').show();
		break;
		case 'mnuUserLogin':
		case 'mnuUserChangeLogin':
			//updateUserBar(jQuery('#desinventarURL').val() + '/user.php', '', '', '');
			Ext.getCmp('wndUserLogin').show();
		break;
		case 'mnuUserLogout':
			doUserLogout();
		break;
		case 'mnuUserEditAccount':
			jQuery('#dbl').load(jQuery('#desinventarURL').val() + '/user.php?cmd=changepasswd',function() { onReadyUserChangePasswd('dbl-win'); });
			Ext.getCmp('wndDatabaseList').show();
		break;
		case 'mnuFileQuit':
			self.close();
		break;
		// query menu
		case 'menuQueryToggle':
			w = Ext.getCmp('westm');
			jQuery('.contentBlock').hide();
			if (RegionId == '') {
				jQuery('#divQueryResults').hide();
				w.hide();
			} else {
				jQuery('#divQueryResults').show();
				w.show();
			}
			if (w.isVisible()) {
				w.collapse(); //hide()
			} else {
				w.expand(); //show()
			}
		break;
		case 'mnuQueryNew':
			// Just reload the current region window...(need a better solution!!)
			window.location = jQuery('#desinventarURL').val() + '/index.php?r=' + RegionId;
		break;
		case 'menuQuerySave':
			saveQuery();
		break;
		case 'mnuQueryOpen':
			var qryw;
			if (!qryw) {
				qryw = new Ext.Window({
					el:'qry-win',  layout:'fit',  width:300, height:200, 
					closeAction:'hide', plain: true, animCollapse: false,
					items: new Ext.Panel({
					contentEl: 'qry-cfg', autoScroll: true }),
					buttons: [{
						text: jQuery('#msgQueryOpenButtonClose').text(),
						handler: function() {
							qryw.hide();
						}
					}]
				});
			}
			qryw.show(this);
		break;
		// Datacards Menu Items
		case 'mnuDatacardView':
		case 'mnuDatacardInsertEdit':
			jQuery('#cardsRecordNumber').val(0);
			jQuery('#cardsRecordSource').val('');
			jQuery.post(jQuery('#desinventarURL').val() + '/index.php',
				{cmd      : 'getRegionRecordCount',
				 RegionId : jQuery('#desinventarRegionId').val()
				},
				function(data) {
					jQuery('#cardsRecordNumber').val(0);
					jQuery('#cardsRecordCount').val(data.RecordCount);
					$('DICard').reset();
					jQuery('#divDatacardWindow').trigger('display');
					doDatacardNavButtonsEnable();
					Ext.getCmp('wndDatacard').show();
				},
				'json'
			);
		break;
		case 'mnuDatacardImport':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatacardsImport').show();
			updateList('divDatacardsImport', jQuery('#desinventarURL').val() + '/import.php', 'r=' + RegionId);
		break;
		case 'mnuDatabaseExport':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatabaseExport').trigger('DBBackupRestart');
			jQuery('#divDatabaseExport').show();
		break;
		case 'mnuDatabaseImport':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatabaseImport').show();
		break;
		case 'mnuDatabaseConfig':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatabaseConfiguration').show();
			jQuery('#tabDatabaseConfiguration').show();
		break;
		// databases menu
		case 'mnuDatabaseFind':
			// Show database list
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			updateDatabaseListByUser();
		break;
		case 'mnuAdminUsers':
			//updateList('dbl', jQuery('#desinventarURL').val() + '/user.php', 'cmd=adminusr', 'onReadyUserAdmin');
			jQuery('#dbl').load(jQuery('#desinventarURL').val() + '/user.php?cmd=adminusr',function() { onReadyUserAdmin(); });
			Ext.getCmp('wndDatabaseList').show();
		break;
		case 'mnuAdminDatabases':
			jQuery('.contentBlock').hide();
			jQuery('#divAdminDatabase').show();
			doAdminDatabaseUpdateList();
		break;
		// help menu
		case 'mnuHelpAbout':
			Ext.getCmp('wndDialog').show();
		break;
		case 'mnuHelpWebsite':
			window.open('http://www.desinventar.org', '', '');
		break;
		case 'mnuHelpMethodology':
			var url = 'http://www.desinventar.org';
			if (jQuery('#desinventarLang').val() == 'spa') {
				url = url + '/es/metodologia';
			} else {
				url = url + '/en/methodology';
			}
			window.open(url, '', '');
		break;
		case 'mnuHelpDocumentation':
			var url = 'http://www.desinventar.org/';
			window.open(url, '', '');
		break;
	} //switch
} //onMenuItem()

function hideQueryDesign()
{
	// Hide Query Design Panel
	w = Ext.getCmp('westm');
	if (w != undefined)
	{
		w.hide();
		w.collapse();
	}
} //hideQueryDesign()

function doCreateMainMenu()
{
	// Main menu
	var mnuLang = new Ext.menu.Menu({id: 'langSubMenu',items: []});
	jQuery('[id|="mnuUserLanguage"]').each(function(index, Element) {
		var LangIsoCode = jQuery(this).attr('id').substr(-3);
		mnuLang.add({text: jQuery(this).text(), handler: function() {
			window.location = jQuery('#desinventarURL').val() + '/index.php?r=' + jQuery('#desinventarRegionId').val() + '&lang=' + LangIsoCode;
		}});
	});
	
	var muser = new Ext.menu.Menu({
		id: 'userMenu',
		items: [
			{id: 'mnuUserLogin'       , text: jQuery('#mnuUserLogin').text()       , handler: onMenuItem }, 
			{id: 'mnuUserChangeLogin' , text: jQuery('#mnuUserChangeLogin').text() , handler: onMenuItem, hidden: true },
			{id: 'mnuUserEditAccount' , text: jQuery('#mnuUserEditAccount').text() , handler: onMenuItem, hidden: true },
			{id: 'mnuUserLogout'      , text: jQuery('#mnuUserLogout').text()      , handler: onMenuItem, hidden: true }, 
			{id: 'mnuUserLanguage'    , text: jQuery('#mnuMenuUserLanguage').text(), menu: mnuLang },
			{id: 'mnuFileQuit'        , text: jQuery('#mnuUserQuit').text()        , handler: onMenuItem  }
		]
	});
	
	var mquery = new Ext.menu.Menu({
		id: 'queryMenu',
		items: [
			{id:'menuQueryToggle', text: jQuery('#mnuQueryToggle').text()    , handler: onMenuItem  },
			{id:'mnuQueryNew'    , text: jQuery('#mnuQueryNew').text() , handler: onMenuItem  },
			{id:'menuQuerySave'  , text: jQuery('#mnuQuerySave').text(), handler: onMenuItem  },
			{id:'mnuQueryOpen'   , text: jQuery('#mnuQueryOpen').text(), handler: onMenuItem  }
		]
	});

	var mcards = new Ext.menu.Menu({
		id: 'cardsMenu',
		items: [
			{id:'mnuDatacardView'      , text: jQuery('#mnuDatacardView').text()      , handler: onMenuItem },
			{id:'mnuDatacardInsertEdit', text: jQuery('#mnuDatacardInsertEdit').text(), handler: onMenuItem, hidden: true },
			{id:'mnuDatacardImport'    , text: jQuery('#mnuDatacardImport').text()    , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseExport'    , text: jQuery('#mnuDatabaseExport').text()    , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseImport'    , text: jQuery('#mnuDatabaseImport').text()    , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseConfig'    , text: jQuery('#mnuDatabaseConfig').text()    , handler: onMenuItem, hidden: true }
		]
	});

	var mbases = new Ext.menu.Menu({
		id: 'basesMenu',
		items: [
			{id:'mnuDatabaseFind'   , text: jQuery('#mnuDatabaseFind').text() , handler: onMenuItem },
			{id:'mnuAdminUsers'     , text: jQuery('#mnuAdminUsers').text()   , handler: onMenuItem, hidden: true },
			{id:'mnuAdminDatabases' , text: jQuery('#mnuAdminDatabase').text(), handler: onMenuItem, hidden: true }
		]
	});

	var mhelp = new Ext.menu.Menu({
		id: 'helpMenu',
		style: { overflow: 'visible' },
		items: [
			{id:'mnuHelpWebsite'      , text: jQuery('#mnuHelpWebsite').text()      , handler: onMenuItem  },
			{id:'mnuHelpMethodology'  , text: jQuery('#mnuHelpMethodology').text()  , handler: onMenuItem  },
			{id:'mnuHelpDocumentation', text: jQuery('#mnuHelpDocumentation').text(), handler: onMenuItem  },
			{id:'mnuRegionInfo'       , text: jQuery('#mnuRegionInfo').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuHelpAbout'        , text: jQuery('#mnuHelpAbout').text()        , handler: onMenuItem  }
		]
	});
	
	var tb = new Ext.Toolbar({renderTo: 'toolbar', items : [] });
	tb.add({ id:'mnuUser'       , text: jQuery('#mnuMenuUser').text()     , menu: muser });
	tb.add({ id:'mnuQuery'      , text: jQuery('#mnuMenuQuery').text()    , menu: mquery, hidden: true });
	tb.add({ id:'mnuCards'      , text: jQuery('#mnuMenuDatacards').text(), menu: mcards, hidden: true });
	tb.add({ id:'mnuDB'         , text: jQuery('#mnuMenuDatabase').text() , menu: mbases});
	tb.add({ id:'mnuHelp'       , text: jQuery('#mnuMenuHelp').text()     , menu: mhelp});
	tb.add('->',{id: 'mnuRegionInfoLabel', text: '', handler: onMenuItem });
	tb.add('->',{id: 'mnuHelpWebsite'    , text: '<img src="' + jQuery('#desinventarURL').val() + '/images/di_logo4.png" alt="" />',  handler: onMenuItem });

	// Configure Menu using current RoleValue
	
	// Add UserId to menu text when user is logged in
	if (jQuery('#desinventarUserId').val() != '')
	{
		Ext.getCmp('mnuUser').setText(Ext.getCmp('mnuUser').getText() + ' : ' + jQuery('#desinventarUserId').val());
		Ext.getCmp('mnuUserLogin').hide();
		Ext.getCmp('mnuUserChangeLogin').show();
		Ext.getCmp('mnuUserEditAccount').show();
		Ext.getCmp('mnuUserLogout').show();
	}

	// Configure which options are visible using RoleValue
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());

	if (UserRoleValue >= 5)
	{
		Ext.getCmp('mnuAdminUsers').show();
		Ext.getCmp('mnuAdminDatabases').show();
	}
	
	// Hide Menu items when no Region is Selected
	if (jQuery('#desinventarRegionId').val() != '')
	{
		Ext.getCmp('mnuRegionInfo').show();
		Ext.getCmp('mnuRegionInfoLabel').setText('[' + jQuery('#desinventarRegionLabel').val() + ']');
		Ext.getCmp('mnuQuery').show();
		Ext.getCmp('mnuCards').show();

		// Feeder/Supervisor/Admin
		if (UserRoleValue >= 2) 
		{
			// Edit datacards instead of only view them
			Ext.getCmp('mnuDatacardView').hide();
			Ext.getCmp('mnuDatacardInsertEdit').show();
			// Enable other functions
			Ext.getCmp('mnuDatacardImport').show();
			Ext.getCmp('mnuDatabaseExport').show();
			if (UserRoleValue >= 4)
			{
				Ext.getCmp('mnuDatabaseImport').show();
				Ext.getCmp('mnuDatabaseConfig').show();
			}
		}
		
	} //if
} //doCreateMainMenu()

function doCreateDialogs()
{
	// User Login Window
	usrw = new Ext.Window({id:'wndUserLogin',
		el:'usr-win', layout:'fit', x:300, y:100, width:500, height:300, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'usr', autoScroll: true })
	});
	// Database List - Database Search Window
	dblw = new Ext.Window({id:'wndDatabaseList',
		el:'dbl-win', layout:'fit', x:200, y:100, width:600, height:450, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dbl', autoScroll: true })
	});
	dlgw = new Ext.Window({id:'wndDialog',
		el:'dlg-win', layout:'fit', x:350, y:200, width:300, height:150, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
	});
	// Datacard View/Edit Window
	difw = new Ext.Window({id:'wndDatacard',
		el:'divDatacardWindow', layout:'fit', 
		x: 65, y: 0, width:960, height:638, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dif-cfg', autoScroll: true })
	});
	difw.on('hide',function() {
		jQuery('#divDatacardWindow').hide();
		showtip('');					
	});
} //doCreateDialogs()