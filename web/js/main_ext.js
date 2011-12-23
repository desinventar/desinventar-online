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
	doMainMenuCreate();
	doViewportCreate();
	doDialogsCreate();
} //onReadyExtJS()

function doViewportCreate()
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

	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());
	var RegionId = jQuery('#desinventarRegionId').val();
	if ( (RegionId == '') || (UserRoleValue < 1) )
	{
		Ext.getCmp('westm').hide();
		viewport.doLayout();
	}

	jQuery('.contentBlock').hide();
	if (RegionId != '')
	{
		if (UserRoleValue > 0)
		{
			jQuery('#divQueryResults').show();
			// Load Database Info and Show
			doGetRegionInfo(jQuery('#desinventarRegionId').val());
			jQuery('#divRegionInfo').show();
			jQuery('#dcr').hide();
			jQuery('#divQueryResults').show();
		}
		else
		{
			jQuery('#divDatabasePrivate').show();
		}
	}
	else
	{
		jQuery('#divRegionList').show();
		// Show database list
		doUpdateDatabaseListByUser();
	}
} // doViewportCreate()

function onMenuItem(item) {
	var RegionId = jQuery('#desinventarRegionId').val();
	switch (item.id)
	{
		case 'mnuDatabaseRegionInfo':
			jQuery('#dcr').hide();
			doGetRegionInfo(jQuery('#desinventarRegionId').val());
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').expand();
			jQuery('#divRegionInfo').show();
		break;
		case 'mnuUserLogin':
		case 'mnuUserChangeLogin':
			doUserLoginShow();
		break;
		case 'mnuUserLogout':
			doUserLogout();
		break;
		case 'mnuUserEditAccount':
			jQuery('#dbl').load(jQuery('#desinventarURL').val() + '/user.php?cmd=changepasswd',function() { onReadyUserChangePasswd('dbl-win'); });
			Ext.getCmp('wndDatabaseList').show();
		break;
		case 'mnuUserLanguage-spa':
		case 'mnuUserLanguage-eng':
		case 'mnuUserLanguage-por':
		case 'mnuUserLanguage-fre':
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd : 'cmdUserLanguageChange',
					LangIsoCode : item.langisocode
				},
				function(data)
				{
					if (parseInt(data.Status) > 0)
					{
						doViewportDestroy();
						window.location.reload(false);
					}
					else
					{
						console.error('cmdUserLanguageChange error : ' + data.Status + ' ' + item.langisocode);
					}
				},
				'json'
			);
		break;
		// query menu
		case 'menuQueryToggle':
			w = Ext.getCmp('westm');
			jQuery('.contentBlock').hide();
			if (RegionId == '')
			{
				jQuery('#divQueryResults').hide();
				w.hide();
			}
			else
			{
				jQuery('#divQueryResults').show();
				w.show();
			}
			if (w.isVisible())
			{
				w.collapse(); //hide()
			}
			else
			{
				w.expand(); //show()
			}
		break;
		case 'mnuQueryNew':
			// Just reload the current region window...(need a better solution!!)
			window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
		break;
		case 'menuQuerySave':
			saveQuery();
		break;
		case 'mnuQueryOpen':
			Ext.getCmp('wndQueryOpen').show();
		break;
		// Datacards Menu Items
		case 'mnuDatabaseRecordView':
		case 'mnuDatabaseRecordEdit':
			jQuery('#cardsRecordNumber').val(0);
			jQuery('#cardsRecordSource').val('');
			jQuery.post(jQuery('#desinventarURL').val() + '/',
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
			jQuery('.clsAdminDatabaseExport').hide();
			Ext.getCmp('wndDatabaseExport').show();
			doAdminDatabaseExportAction();
		break;
		case 'mnuDatabaseUpload':
			doDatabaseUploadShow();
		break;
		case 'mnuDatabaseConfig':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('.classDBConfig_tabs:first').click();
			jQuery('#divDatabaseConfiguration').show();
			jQuery('#tabDatabaseConfiguration').show();
		break;
		// databases menu
		case 'mnuDatabaseSelect':
		case 'mnuDatabaseSelectAnother':
			// Show database list
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			doUpdateDatabaseListByUser();
		break;
		case 'mnuDatabaseCreate':
			doDatabaseCreateShow();
		break;
		case 'mnuUserAccountManagement':
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
	return false;
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

function doMainMenuCreate()
{
	// Main menu
	var mnuLang = new Ext.menu.Menu({id: 'langSubMenu',items: []});
	jQuery('[id|="mnuUserLanguage"]').each(function(index, Element) {
		var LangIsoCode = jQuery(this).attr('id').substr(-3);
		mnuLang.add({id: jQuery(this).attr('id'), langisocode: LangIsoCode, text: jQuery(this).text(), handler: onMenuItem });
	});
	
	var muser = new Ext.menu.Menu({
		id: 'userMenu',
		items: [
			{id: 'mnuUserLogin'            , text: jQuery('#mnuUserLogin').text()            , handler: onMenuItem }, 
			{id: 'mnuUserChangeLogin'      , text: jQuery('#mnuUserChangeLogin').text()      , handler: onMenuItem, hidden: true },
			{id: 'mnuUserEditAccount'      , text: jQuery('#mnuUserEditAccount').text()      , handler: onMenuItem, hidden: true },
			{id: 'mnuUserAccountManagement', text: jQuery('#mnuUserAccountManagement').text(), handler: onMenuItem, hidden: true },
			{id: 'mnuUserLanguage'         , text: jQuery('#mnuMenuUserLanguage').text()     , menu: mnuLang },
			{id: 'mnuUserLogout'           , text: jQuery('#mnuUserLogout').text()           , handler: onMenuItem, hidden: true }
		]
	});
	
	var mquery = new Ext.menu.Menu({
		id: 'queryMenu',
		items: [
			{id:'menuQueryToggle', text: jQuery('#mnuQueryToggle').text(), handler: onMenuItem  },
			{id:'mnuQueryNew'    , text: jQuery('#mnuQueryNew').text()   , handler: onMenuItem  },
			{id:'menuQuerySave'  , text: jQuery('#mnuQuerySave').text()  , handler: onMenuItem  },
			{id:'mnuQueryOpen'   , text: jQuery('#mnuQueryOpen').text()  , handler: onMenuItem  }
		]
	});

	var mnuMenuDatabase = new Ext.menu.Menu({
		id: 'cardsMenu',
		items: [
			{id:'mnuDatabaseRecordView'   , text: jQuery('#mnuDatabaseRecordView').text()   , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseRecordEdit'   , text: jQuery('#mnuDatabaseRecordEdit').text()   , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseRegionInfo'   , text: jQuery('#mnuDatabaseRegionInfo').text()   , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseExport'       , text: jQuery('#mnuDatabaseExport').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseUpload'       , text: jQuery('#mnuDatabaseUpload').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseCreate'       , text: jQuery('#mnuDatabaseCreate').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseConfig'       , text: jQuery('#mnuDatabaseConfig').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseSelect'       , text: jQuery('#mnuDatabaseSelect').text()       , handler: onMenuItem, hidden: true },
			{id:'mnuDatabaseSelectAnother', text: jQuery('#mnuDatabaseSelectAnother').text(), handler: onMenuItem, hidden: true }
		]
	});

	var mbases = new Ext.menu.Menu({
		id: 'basesMenu',
		items: [
			{id:'mnuDatacardImport'       , text: jQuery('#mnuDatacardImport').text()    , handler: onMenuItem, hidden: true },
			{id:'mnuAdminDatabases'       , text: jQuery('#mnuAdminDatabases').text() , handler: onMenuItem, hidden: true }
		]
	});

	var mhelp = new Ext.menu.Menu({
		id: 'helpMenu',
		style: { overflow: 'visible' },
		items: [
			{id:'mnuHelpWebsite'       , text: jQuery('#mnuHelpWebsite').text()       , handler: onMenuItem  },
			{id:'mnuHelpMethodology'   , text: jQuery('#mnuHelpMethodology').text()   , handler: onMenuItem  },
			{id:'mnuHelpDocumentation' , text: jQuery('#mnuHelpDocumentation').text() , handler: onMenuItem  },
			{id:'mnuHelpAbout'         , text: jQuery('#mnuHelpAbout').text()         , handler: onMenuItem  }
		]
	});
	
	var tb = new Ext.Toolbar({renderTo: 'toolbar', items : [] });
	tb.add({ id:'mnuUser'       , text: jQuery('#mnuMenuUser').text()     , menu: muser });
	tb.add({ id:'mnuQuery'      , text: jQuery('#mnuMenuQuery').text()    , menu: mquery, hidden: true });
	tb.add({ id:'mnuCards'      , text: jQuery('#mnuMenuDatabase').text() , menu: mnuMenuDatabase, hidden: false });
	tb.add({ id:'mnuHelp'       , text: jQuery('#mnuMenuHelp').text()     , menu: mhelp});
	tb.add('->',{id: 'mnuRegionLabel', text: '', handler: onMenuItem });
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
		Ext.getCmp('mnuDatabaseCreate').show();
		Ext.getCmp('mnuDatabaseUpload').show();
	}

	// Configure which options are visible using RoleValue
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());

	if (UserRoleValue >= 5)
	{
		Ext.getCmp('mnuUserAccountManagement').show();
		//Ext.getCmp('mnuAdminDatabases').show();
	}
	
	// Show some menu items when a Region is Selected
	if (jQuery('#desinventarRegionId').val() != '')
	{
		Ext.getCmp('mnuDatabaseSelectAnother').show();
		Ext.getCmp('mnuRegionLabel').setText('[' + jQuery('#desinventarRegionLabel').val() + ']');
		if (UserRoleValue > 0)
		{
			Ext.getCmp('mnuDatabaseRegionInfo').show();
			Ext.getCmp('mnuQuery').show();
			Ext.getCmp('mnuDatabaseRecordView').show();
		}

		// Feeder/Supervisor/Admin
		if (UserRoleValue >= 2) 
		{
			// Edit datacards instead of only view them
			Ext.getCmp('mnuDatabaseRecordView').hide();
			Ext.getCmp('mnuDatabaseRecordEdit').show();
			// Enable other functions
			Ext.getCmp('mnuDatacardImport').hide(); // Disabled for now
			Ext.getCmp('mnuDatabaseExport').show();

			if (UserRoleValue >= 4)
			{
				Ext.getCmp('mnuDatabaseConfig').show();
			}
		}		
	}
	else
	{
		Ext.getCmp('mnuDatabaseSelect').show();
	}
} //doCreateMainMenu()

function doDialogsCreate()
{
	var w;
	// User Login Window
	w = new Ext.Window({id:'wndUserLogin',
		el:'usr-win', layout:'fit', x:300, y:100, width:500, height:300, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'usr', autoScroll: true })
	});

	// Query Open Window
	w = new Ext.Window({id:'wndQueryOpen',
		el:'qry-win', layout:'fit', width:300, height:200,
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({contentEl: 'qry-cfg', autoScroll: true }),
		buttons:
		[
			{
				text: jQuery('#msgQueryOpenButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndQueryOpen').hide();
				}
			}
		]
	});

	// Database List - Database Search Window
	w = new Ext.Window({id:'wndDatabaseList',
		el:'dbl-win', layout:'fit', x:200, y:100, width:600, height:450, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dbl', autoScroll: true })
	});
	w = new Ext.Window({id:'wndDialog',
		el:'dlg-win', layout:'fit', x:350, y:200, width:300, height:150, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
	});
	// Datacard View/Edit Window
	w = new Ext.Window({id:'wndDatacard',
		el:'divDatacardWindow', layout:'fit', 
		x: 65, y: 0, width:960, height:638, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'dif-cfg', autoScroll: true })
	});
	w.on('hide',function() {
		jQuery('#divDatacardWindow').hide();
		showtip('');					
	});

	w = new Ext.Window({id:'wndViewDataParams', 
		el:'dat-win', layout:'fit',
		width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({contentEl: 'dat-cfg', autoScroll: true }),
		buttons: [
			{text: jQuery('#msgViewDataButtonClear').text(),
				handler: function() {
					$('CD').reset();
					} //handler
			},
			{text: jQuery('#msgViewDataButtonSend').text(),
				handler: function() {
					if (sendList("result")) {
						$('DCRes').value = "D";
						$('bsave').style.visibility = 'visible';
						$('bprint').style.visibility = 'visible';
						Ext.getCmp('wndViewDataParams').hide();
					} else {
						console.debug('Error while executing function ViewData');
					}
				} //handler
			},
			{text: jQuery('#msgViewDataButtonClose').text(),
				handler: function() {
					Ext.getCmp('wndViewDataParams').hide();
				} //handler
			}
		] //button
	});

	w = new Ext.Window({id:'wndViewMapParams',
		el:'map-win',  layout:'fit',  width:650, height:420, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({contentEl: 'map-cfg', autoScroll: true }),
		buttons: [
			{text: jQuery('#msgViewMapButtonClear').text(),
				handler: function() {
					$('CM').reset();
				}
			},
			{text: jQuery('#msgViewMapButtonSend').text(),
				handler: function() {
					if (sendMap("result")) {
						$('DCRes').value = "M";
						Ext.getCmp('wndViewMapParams').hide();
						$('bsave').style.visibility = 'visible';
						$('bprint').style.visibility = 'visible';
					} else {
						console.debug('Error while executing function ViewMap');
					}
				}
			},
			{text: jQuery('#msgViewMapButtonClose').text(),
				handler: function() {
					Ext.getCmp('wndViewMapParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewGraphParams',
		el:'divGraphParameters',  layout:'fit',  width:750, height:420, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({contentEl: 'grp-cfg', autoScroll: true }),
		buttons: [
			{text: jQuery('#msgViewGraphButtonClear').text(),
				handler: function() {
					$('CG').reset();
				}
			},
			{text: jQuery('#msgViewGraphButtonSend').text(),
				handler: function() {
					sendGraphic('result');
					$('DCRes').value = "G";
					Ext.getCmp('wndViewGraphParams').hide();
					$('bsave').style.visibility = 'visible';
					$('bprint').style.visibility = 'visible';
				}
			},
			{text: jQuery('#msgViewGraphButtonClose').text(),
				handler: function() {
					Ext.getCmp('wndViewGraphParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewStdParams',
		el:'std-win',  layout:'fit',  width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({contentEl: 'std-cfg', autoScroll: true }),
		buttons: [
			{text: jQuery('#msgViewStdButtonClear').text(),
				handler: function() {
					$('CS').reset();
				}
			},
			{text: jQuery('#msgViewStdButtonSend').text(),
				handler: function() {
					if (sendStatistic("result")) {
						$('DCRes').value = "S";
						Ext.getCmp('wndViewStdParams').hide();
						$('bsave').style.visibility = 'visible';
						$('bprint').style.visibility = 'visible';
					} else {
						console.debug('Error while executing ViewStd function');
					}
				} //handler
			},
			{text: jQuery('#msgViewStdButtonClose').text(),
				handler: function() {
					Ext.getCmp('wndViewStdParams').hide();
				}
			}
		]
	});

} //doDialogsCreate()

