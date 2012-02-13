/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
 - General ExtJS functions
*/
function onReadyExtJS()
{
	Ext.BLANK_IMAGE_URL = '/extJS/resources/images/default/s.gif';
	Ext.ns('DesInventar');

	// Hide Loading div...
	jQuery('#loading').hide();
	jQuery('#loading-mask').hide();	

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
	doDialogsCreate();
	doMainMenuCreate();
	doViewportCreate();
	jQuery('body').trigger('cmdMainMenuUpdate');
	jQuery('body').trigger('cmdMainWaitingHide');
	doViewportShow();
} //onReadyExtJS()

function doViewportCreate()
{
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
				margins:'0 2 0 0',
				contentEl: 'divWestPanel'
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			DesInventar.WestPanel.superclass.initComponent.call(this);
		}
	});
	DesInventar.Viewport = Ext.extend(Ext.Viewport, {
		initComponent: function() {
			var config = {
				contentEl: 'divViewport',
				layout:'border',
				border: false,
				items:[
					{
						region:'north',
						height: 30,
						border: false,
						contentEl: 'north',
						collapsible: false,
						tbar: new DesInventar.Toolbar({id:'toolbar', MenuHandler: doMainMenuHandler})
					},
					new DesInventar.WestPanel({id:'westm', collapsible: true}),
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
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			DesInventar.Viewport.superclass.initComponent.call(this);
		}
	});
	var viewport = new DesInventar.Viewport({id:'viewport'});
	viewport.show();
	jQuery('#westm .x-panel-header-text').attr('title', jQuery('#msgQueryDesignTooltip').text());
	Ext.getCmp('westm').on('expand', function() {
		jQuery('#divRegionInfo').hide();
	});
} // doViewportCreate()

function doViewportShow()
{
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());
	var RegionId = jQuery('#desinventarRegionId').val();
	jQuery('.contentBlock').hide();
	if (RegionId != '')
	{
		if (UserRoleValue > 0)
		{
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').expand();
			jQuery('#divQueryResults').show();
			jQuery('body').trigger('cmdQueryResultsButtonHide');
			jQuery('#dcr').hide();
		}
		else
		{
			Ext.getCmp('westm').hide();
			Ext.getCmp('viewport').doLayout();
			jQuery('#divDatabasePrivate').show();
		}
	}
	else
	{
		Ext.getCmp('westm').hide();
		Ext.getCmp('viewport').doLayout();
		jQuery('#divRegionList').show();
		doUpdateDatabaseListByUser();
	}
} //doViewportShow()

function doMainChangeLanguage(LangIsoCode)
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdUserLanguageChange',
			LangIsoCode : LangIsoCode
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('body').trigger('cmdWindowReload');
			}
			else
			{
				console.error('cmdUserLanguageChange error : ' + data.Status + ' ' + item.langisocode);
			}
		},
		'json'
	);
} //doMainChangeLanguage()

function doMainMenuHandler(item)
{
	var menuCmd = '';
	if (item.itemid != undefined)
	{
		menuCmd = item.itemid;
	}
	if (item.id != undefined)
	{
		menuCmd = item.id;
	}
	var RegionId = jQuery('#desinventarRegionId').val();
	switch (menuCmd)
	{
		case 'mnuUserLogin':
		case 'mnuUserChangeLogin':
			jQuery('body').trigger('cmdUserLoginShow');
		break;
		case 'mnuFileLogout':
			jQuery('body').trigger('cmdUserLogout');
		break;
		case 'mnuUserChangePasswd':
			jQuery('body').trigger('cmdUserAccountShow');
		break;
		case 'mnuFileLanguageEnglish':
			doMainChangeLanguage('eng');
		break;
		case 'mnuFileLanguageSpanish':
			doMainChangeLanguage('spa');
		break;
		case 'mnuFileLanguagePortuguese':
			doMainChangeLanguage('por');
		break;
		case 'mnuFileLanguageFrench':
			doMainChangeLanguage('fre');
		break;
		// DesConsultar Menu Options
		case 'mnuQueryViewDesign':
			if (jQuery('#desinventarRegionId').val() != '')
			{
				jQuery('.contentBlock').hide();
				jQuery('#divQueryResults').show();
				Ext.getCmp('westm').expand();
			}
		break;
		case 'mnuQueryViewData':
			jQuery('body').trigger('cmdViewDataParams');
		break;
		case 'mnuQueryViewMap':
			jQuery('body').trigger('cmdViewMapParams');
		break;
		case 'mnuQueryViewGraph':
			jQuery('body').trigger('cmdViewGraphParams');
		break;
		case 'mnuQueryViewStd':
			jQuery('body').trigger('cmdViewStdParams');
		break;
		case 'mnuQueryOptionNew':
			// Just reload the current region window...(need a better solution!!)
			window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
		break;
		case 'mnuQueryOptionSave':
			saveQuery();
		break;
		case 'mnuQueryOptionOpen':
			Ext.getCmp('wndQueryOpen').show();
		break;
		case 'mnuQueryResultSave':
			jQuery('#btnResultSave').trigger('click');
		break;
		case 'mnuQueryResultSaveAsXLS':
			jQuery('#btnResultSaveXLS').trigger('click');
		break;
		case 'mnuQueryResultSaveAsCSV':
			jQuery('#btnResultSaveCSV').trigger('click');
		break;
		case 'mnuQueryResultPrint':
			jQuery('#btnResultPrint').trigger('click');
		break;
		case 'mnuFileInfo':
			jQuery('.contentBlock').hide();
			jQuery('#divQueryResults').show();
			jQuery('#dcr').hide();
			jQuery('#divRegionInfo').show();
			doGetRegionInfo(jQuery('#desinventarRegionId').val());
			Ext.getCmp('westm').collapse();
		break;
		case 'mnuFileOpen':
			// Show database list
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			doUpdateDatabaseListByUser();
		break;
		// Datacards Menu Items
		case 'mnuDatacardView':
		case 'mnuDatacardEdit':
			jQuery('#cardsRecordNumber').val(0);
			jQuery('#cardsRecordSource').val('');
			jQuery('body').trigger('cmdDatacardShow');
		break;
		case 'mnuDatacardImport':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatacardsImport').show();
			updateList('divDatacardsImport', jQuery('#desinventarURL').val() + '/import.php', 'r=' + RegionId);
		break;
		case 'mnuFileDownload':
			jQuery('.clsAdminDatabaseExport').hide();
			Ext.getCmp('wndDatabaseExport').show();
			doAdminDatabaseExportAction();
		break;
		case 'mnuFileCopy':
			doDatabaseUploadShow('Copy');
		break;
		case 'mnuFileReplace':
			doDatabaseUploadShow('Replace');
		break;
		case 'mnuDatacardSetup':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('.classDBConfig_tabs:first').click();
			jQuery('#divDatabaseConfiguration').show();
			jQuery('#tabDatabaseConfiguration').show();
		break;
		case 'mnuFileCreate':
			doDatabaseCreateShow();
		break;
		case 'mnuUserAccountManagement':
			jQuery('#dbl').load(jQuery('#desinventarURL').val() + '/user.php?cmd=adminusr', function() {
				doAdminUsersReset();
			});
			Ext.getCmp('wndDatabaseList').show();
		break;
		case 'mnuAdminDatabases':
			jQuery('.contentBlock').hide();
			jQuery('#divAdminDatabase').show();
			doAdminDatabaseUpdateList();
		break;
		case 'mnuHelpAbout':
			Ext.getCmp('wndDialog').show();
		break;
		case 'mnuHelpWebsite':
			window.open('http://www.desinventar.org', '', '');
		break;
		case 'mnuHelpMethodology':
			var url = 'http://www.desinventar.org';
			if (jQuery('#desinventarLang').val() == 'spa')
			{
				url = url + '/es/metodologia';
			}
			else
			{
				url = url + '/en/methodology';
			}
			window.open(url, '', '');
		break;
		case 'mnuHelpDocumentation':
			var url = 'http://www.desinventar.org/';
			window.open(url, '', '');
		break;
	} //switch
} //doMainMenuHandler()

function hideQueryDesign()
{
	// Hide Query Design Panel
	Ext.getCmp('westm').collapse();
} //hideQueryDesign()

function doMainMenuCreate()
{
	DesInventar.Toolbar = Ext.extend(Ext.Toolbar, {
		initComponent: function() {
			var config = {
				overflow: 'visible',
				items: []
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			DesInventar.Toolbar.superclass.initComponent.call(this);
			this.initializeToolbar();
		},
		initializeToolbar: function()
		{
			var mnuFileUpload = new Ext.menu.Menu({
				items: [
					{id:'mnuFileCopy'   , text: jQuery('span#msgFileCopy').text()   , handler: this.MenuHandler },
					{id:'mnuFileReplace', text: jQuery('span#msgFileReplace').text(), handler: this.MenuHandler }
				]
			});

			// Main menu
			var mnuFileLanguage = new Ext.menu.Menu({
				items: [
					{id:'mnuFileLanguageEnglish'   , text: jQuery('span#msgFileLanguageEnglish').text()   , handler: this.MenuHandler },
					{id:'mnuFileLanguageSpanish'   , text: jQuery('span#msgFileLanguageSpanish').text()   , handler: this.MenuHandler },
					{id:'mnuFileLanguagePortuguese', text: jQuery('span#msgFileLanguagePortuguese').text(), handler: this.MenuHandler },
					{id:'mnuFileLanguageFrench'    , text: jQuery('span#msgFileLanguageFrench').text()    , handler: this.MenuHandler }
				]
			});
			
			var mnuFile = new Ext.menu.Menu({
				items: [
					{id:'mnuFileCreate'     , text: jQuery('span#msgFileCreate').text()     , handler: this.MenuHandler },
					{id:'mnuFileOpen'       , text: jQuery('span#msgFileOpen').text()       , handler: this.MenuHandler },
					{id:'mnuFileDownload'   , text: jQuery('span#msgFileDownload').text()   , handler: this.MenuHandler },
					{id:'mnuFileUpload'     , text: jQuery('span#msgFileUpload').text()     , menu: mnuFileUpload        },
					'-',
					{id:'mnuFileInfo'       , text: jQuery('span#msgFileInfo').text()       , handler: this.MenuHandler },
					{id:'mnuFileLanguage'   , text: jQuery('span#msgFileLanguage').text()   , menu: mnuFileLanguage      },
					{id:'mnuFileLogout'     , text: jQuery('span#msgFileLogout').text()     , handler: this.MenuHandler }
				]
			});
			
			var mnuUser = new Ext.menu.Menu({
				items: [
					{id: 'mnuUserLogin'            , text: jQuery('span#msgUserLogin').text()            , handler: this.MenuHandler }, 
					{id: 'mnuUserChangeLogin'      , text: jQuery('span#msgUserChangeLogin').text()      , handler: this.MenuHandler },
					{id: 'mnuUserChangePasswd'     , text: jQuery('span#msgUserChangePasswd').text()     , handler: this.MenuHandler },
					{id: 'mnuUserAccountManagement', text: jQuery('span#msgUserAccountManagement').text(), handler: this.MenuHandler }
				]
			});

			var mnuQueryOption = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryOptionNew'  , text: jQuery('span#msgQueryOptionNew').text() , handler: this.MenuHandler },
					{id:'mnuQueryOptionSave' , text: jQuery('span#msgQueryOptionSave').text(), handler: this.MenuHandler },
					{id:'mnuQueryOptionOpen' , text: jQuery('span#msgQueryOptionOpen').text(), handler: this.MenuHandler }
				]
			});

			var mnuQueryResultSaveAs = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryResultSaveAsXLS', text: jQuery('span#msgQueryResultSaveAsXLS').text(), handler: this.MenuHandler },
					{id:'mnuQueryResultSaveAsCSV', text: jQuery('span#msgQueryResultSaveAsCSV').text(), handler: this.MenuHandler }
				]
			});

			var mnuQuery = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryViewDesign'           , text: jQuery('span#msgQueryViewDesign').text()           , handler: this.MenuHandler },
					{id:'mnuQueryViewData'             , text: jQuery('span#msgQueryViewData').text()             , handler: this.MenuHandler },
					{id:'mnuQueryViewMap'              , text: jQuery('span#msgQueryViewMap').text()              , handler: this.MenuHandler },
					{id:'mnuQueryViewGraph'            , text: jQuery('span#msgQueryViewGraph').text()            , handler: this.MenuHandler },
					{id:'mnuQueryViewStd'              , text: jQuery('span#msgQueryViewStd').text()              , handler: this.MenuHandler },
					'-',
					{id:'mnuQueryResultSave'           , text: jQuery('span#msgQueryResultSave').text()           , handler: this.MenuHandler },
					{id:'mnuQueryResultSaveAs'         , text: jQuery('span#msgQueryResultSaveAs').text()         , menu: mnuQueryResultSaveAs },
					{id:'mnuQueryResultPrint'          , text: jQuery('span#msgQueryResultPrint').text()          , handler: this.MenuHandler },
					{id:'mnuQueryOption'               , text: jQuery('span#msgQueryOption').text()               , menu: mnuQueryOption       }
				]
			});
			var mnuDatacard = new Ext.menu.Menu({
				items: [
					{id:'mnuDatacardView' , text: jQuery('span#msgDatacardView').text() , handler: this.MenuHandler },
					{id:'mnuDatacardEdit' , text: jQuery('span#msgDatacardEdit').text() , handler: this.MenuHandler },
					{id:'mnuDatacardSetup', text: jQuery('span#msgDatacardSetup').text(), handler: this.MenuHandler }
				]
			});
			var mnuHelp = new Ext.menu.Menu({
				style: { overflow: 'visible' },
				items: [
					{id:'mnuHelpDocumentation' , text: jQuery('span#msgHelpDocumentation').text() , handler: this.MenuHandler },
					{id:'mnuHelpMethodology'   , text: jQuery('span#msgHelpMethodology').text()   , handler: this.MenuHandler },
					'-',
					{id:'mnuHelpWebsite'       , text: jQuery('span#msgHelpWebsite').text()       , handler: this.MenuHandler },
					'-',
					{id:'mnuHelpAbout'         , text: jQuery('span#msgHelpAbout').text()         , handler: this.MenuHandler }
				]
			});
			this.add({id:'mnuFile'     , text: jQuery('span#msgFile').text()    , menu: mnuFile     });
			this.add({id:'mnuUser'     , text: jQuery('span#msgUser').text()    , menu: mnuUser     });
			this.add({id:'mnuQuery'    , text: jQuery('span#msgQuery').text()   , menu: mnuQuery    });
			this.add({id:'mnuDatacard' , text: jQuery('span#msgDatacard').text(), menu: mnuDatacard });
			this.add({id:'mnuHelp'     , text: jQuery('span#msgHelp').text()    , menu: mnuHelp     });
			
			// This elements appear on reverse order on screen (?)
			this.add('->',{id: 'mnuHelpWebsiteLabel', text: '<img src="' + jQuery('#desinventarURL').val() + '/images/di_logo4.png" alt="" />' });
			this.add('->',{id: 'mnuRegionLabel'     , text: '' });
			this.add('->',{id: 'mnuWaiting'         , text: '<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />', hidden: true });
		}
	});

	// Attach main events to body
	jQuery('body').on('cmdMainWaitingShow', function() {
		Ext.getCmp('mnuWaiting').show();
	});
	jQuery('body').on('cmdMainWaitingHide', function() {
		Ext.getCmp('mnuWaiting').hide();
	});

	jQuery('body').on('cmdMainMenuUpdate', function() {
		doMainMenuUpdate();
	});
	jQuery('body').on('cmdMainMenuResultButtonsEnable', function() {
		if (jQuery('#DCRes').val() == 'D' || jQuery('#DCRes').val() == 'S')
		{
			Ext.getCmp('mnuQueryResultSave').hide();
			Ext.getCmp('mnuQueryResultSaveAs').show();
			Ext.getCmp('mnuQueryResultSaveAs').enable();
			Ext.getCmp('mnuQueryResultSaveAsXLS').show();
			Ext.getCmp('mnuQueryResultSaveAsXLS').enable();
			Ext.getCmp('mnuQueryResultSaveAsCSV').show();
			Ext.getCmp('mnuQueryResultSaveAsCSV').enable();
		}
		else
		{
			Ext.getCmp('mnuQueryResultSave').show();
			Ext.getCmp('mnuQueryResultSave').enable();
			Ext.getCmp('mnuQueryResultSaveAs').hide();			
			Ext.getCmp('mnuQueryResultSaveAsXLS').hide();
			Ext.getCmp('mnuQueryResultSaveAsCSV').hide();
		}
		Ext.getCmp('mnuQueryResultPrint').show();
		Ext.getCmp('mnuQueryResultPrint').enable();
	});
	jQuery('body').on('cmdMainMenuResultButtonsDisable', function() {
		Ext.getCmp('mnuQueryResultSave').hide();
		Ext.getCmp('mnuQueryResultSaveAs').hide();
		Ext.getCmp('mnuQueryResultSaveAsXLS').hide();
		Ext.getCmp('mnuQueryResultSaveAsCSV').hide();
		Ext.getCmp('mnuQueryResultPrint').hide();
	});
	jQuery('body').on('cmdMainWindowUpdate', function() {
		if (jQuery('#desinventarRegionId').val() != '')
		{
			Ext.getCmp('westm').collapse();
		}
		jQuery('.contentBlock').hide();
		jQuery('#divLoading').show();
		setTimeout(function()
		{
			jQuery('body').trigger('cmdMainMenuUpdate');
			if (jQuery('#desinventarRegionId').val() != '')
			{
				jQuery('body').trigger('cmdMainQueryUpdate');
			}
			jQuery('.contentBlock').hide();
			jQuery('#divLoading').hide();
			doViewportShow();
		}, 2000);
	});
} //doCreateMainMenu()

function doMainMenuUpdate()
{
	jQuery('#divMainMenu span.item').each(function() {
		var id = jQuery(this).attr('id').replace('msg','mnu');
		var w = Ext.getCmp(id);
		if (w != undefined)
		{
			w.disable();
		}
	});

	// Menu items that are always enabled
	jQuery('#divMainMenu span.clsMenuAlwaysOn').each(function() {
		Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
	});
	Ext.getCmp('mnuUser').setText(jQuery('span#msgUser').text());
	
	// Enable menu items when a User is logged in
	if (jQuery('#desinventarUserId').val() == '')
	{
		Ext.getCmp('mnuDatacard').hide();
		jQuery('#divMainMenu span.clsMenuWithoutUser').each(function() {
			Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
		});
		Ext.getCmp('mnuUserLogin').show();
		Ext.getCmp('mnuUserChangeLogin').hide();
	}
	else
	{
		Ext.getCmp('mnuDatacard').show();
		jQuery('#divMainMenu span.clsMenuWithUser').each(function() {
			Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
		});
		Ext.getCmp('mnuUserLogin').hide();
		Ext.getCmp('mnuUserChangeLogin').show();
		Ext.getCmp('mnuUser').setText(jQuery('span#msgUser').text() + ' : ' + jQuery('#desinventarUserId').val());
	}

	// Configure which options are visible using RoleValue
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());

	Ext.getCmp('mnuUserAccountManagement').hide();
	if (UserRoleValue >= 5)
	{
		Ext.getCmp('mnuUserAccountManagement').show();
		Ext.getCmp('mnuUserAccountManagement').enable();
	}

	Ext.getCmp('mnuFileReplace').hide();
	Ext.getCmp('mnuDatacardSetup').hide();

	Ext.getCmp('mnuDatacardView').show();
	Ext.getCmp('mnuDatacardEdit').hide();
	
	// Show some menu items when a Region is Selected
	if (jQuery('#desinventarRegionId').val() == '')
	{
	}
	else
	{
		Ext.getCmp('mnuRegionLabel').setText('[' + jQuery('#desinventarRegionLabel').val() + ']');

		if (UserRoleValue > 0)
		{
			jQuery('#divMainMenu span.clsMenuWithRegion').each(function() {
				Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
			});
			Ext.getCmp('mnuDatacardView').show();
			Ext.getCmp('mnuDatacardView').enable();
			Ext.getCmp('mnuDatacardEdit').hide();
		}
		if (UserRoleValue >= 2) 
		{
			// Edit datacards instead of only view them
			Ext.getCmp('mnuDatacardView').hide();
			Ext.getCmp('mnuDatacardEdit').show();
			Ext.getCmp('mnuDatacardEdit').enable();
			// Enable other functions
			Ext.getCmp('mnuFileDownload').enable();

			if (UserRoleValue >= 4)
			{
				Ext.getCmp('mnuFileReplace').show();
				Ext.getCmp('mnuFileReplace').enable();
				Ext.getCmp('mnuDatacardSetup').show();
				Ext.getCmp('mnuDatacardSetup').enable();
			}
		}		
	}
	jQuery('body').trigger('cmdMainMenuResultButtonsDisable');
} //doMainMenuUpdate()

function doDialogsCreate()
{
	var w;
	// Query Open Window
	w = new Ext.Window({id:'wndQueryOpen',
		el:'qry-win', layout:'fit', width:300, height:200,
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
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
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'dbl', autoScroll: true })
	});
	w = new Ext.Window({id:'wndDialog',
		el:'dlg-win', layout:'fit', x:350, y:200, width:300, height:150, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
	});
	// Datacard View/Edit Window
	w = new Ext.Window({id:'wndDatacard',
		el:'divDatacardWindow', layout:'fit', 
		width:960, height:638, //x: 65, y: 0, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'divDatacardContent', autoScroll: true })
	});
	w.on('hide',function() {
		jQuery('#divDatacardWindow').hide();
		showtip('');					
	});

	w = new Ext.Window({id:'wndViewDataParams', 
		el:'divViewDataParamsWindow', layout:'fit',
		width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'divViewDataParamsContent', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewDataButtonClear').text(),
				handler: function()
				{
					$('CD').reset();
				} //handler
			},
			{
				text: jQuery('#msgViewDataButtonSend').text(),
				handler: function()
				{
					if (sendList("result"))
					{
						$('DCRes').value = "D";
						jQuery('body').trigger('cmdQueryResultsButtonShow');
						Ext.getCmp('wndViewDataParams').hide();
					}
					else
					{
						console.debug('Error while executing function ViewData');
					}
				} //handler
			},
			{
				text: jQuery('#msgViewDataButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewDataParams').hide();
				} //handler
			}
		] //button
	});

	w = new Ext.Window({id:'wndViewMapParams',
		el:'map-win',  layout:'fit',  width:650, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'map-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewMapButtonClear').text(),
				handler: function()
				{
					$('CM').reset();
				}
			},
			{
				text: jQuery('#msgViewMapButtonSend').text(),
				handler: function()
				{
					if (sendMap("result"))
					{
						$('DCRes').value = "M";
						Ext.getCmp('wndViewMapParams').hide();
						jQuery('body').trigger('cmdQueryResultsButtonShow');
					}
					else
					{
						console.debug('Error while executing function ViewMap');
					}
				}
			},
			{
				text: jQuery('#msgViewMapButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewMapParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewGraphParams',
		el:'divGraphParameters',  layout:'fit',  width:750, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'grp-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewGraphButtonClear').text(),
				handler: function()
				{
					$('CG').reset();
					jQuery('#prmGraphTypeHistogram').change();
				}
			},
			{
				text: jQuery('#msgViewGraphButtonSend').text(),
				handler: function()
				{
					sendGraphic('result');
					$('DCRes').value = "G";
					Ext.getCmp('wndViewGraphParams').hide();
					jQuery('body').trigger('cmdQueryResultsButtonShow');
				}
			},
			{
				text: jQuery('#msgViewGraphButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewGraphParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewStdParams',
		el:'std-win',  layout:'fit',  width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'std-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewStdButtonClear').text(),
				handler: function()
				{
					$('CS').reset();
				}
			},
			{
				text: jQuery('#msgViewStdButtonSend').text(),
				handler: function()
				{
					if (sendStatistic("result"))
					{
						$('DCRes').value = "S";
						Ext.getCmp('wndViewStdParams').hide();
						jQuery('body').trigger('cmdQueryResultsButtonShow');
					}
					else
					{
						console.debug('Error while executing ViewStd function');
					}
				} //handler
			},
			{
				text: jQuery('#msgViewStdButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewStdParams').hide();
				}
			}
		]
	});

} //doDialogsCreate()

function doGetRegionInfo(RegionId)
{
	jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
		  cmd         : 'cmdDatabaseGetInfo', 
		  RegionId    : RegionId,
		  LangIsoCode : jQuery('#desinventarLang').val()
		},
		function(data)
		{
			if (parseInt(data.Status)>0)
			{
				var i = data.RegionInfo;
				jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/?cmd=cmdDatabaseGetLogo&RegionId=' + RegionId + '" alt="" />');
				jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel);
				jQuery('#divRegionInfo #txtRegionPeriod').text(i.PeriodBeginDate + ' - ' + i.PeriodEndDate);
				jQuery('#divRegionInfo #txtRegionNumberOfRecords').text(i.NumberOfRecords);
				jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate);

				jQuery('#divRegionInfo #divInfoGeneral').hide();
				if (i.InfoGeneral != '')
				{
					jQuery('#divRegionInfo #divInfoGeneral #Text').html(i.InfoGeneral);
					jQuery('#divRegionInfo #divInfoGeneral').show();
				}
				jQuery('#divRegionInfo #divInfoCredits').hide();
				if (i.InfoCredits != '')
				{
					jQuery('#divRegionInfo #divInfoCredits #Text').html(i.InfoCredits);
					jQuery('#divRegionInfo #divInfoCredits').show();
				}
				jQuery('#divRegionInfo #divInfoSources').hide();
				if (i.InfoSources != '')
				{
					jQuery('#divRegionInfo #divInfoSources #Text').html(i.InfoSources);
					jQuery('#divRegionInfo #divInfoSources').show();
				}
				jQuery('#divRegionInfo #divInfoSynopsis').hide();
				if (i.InfoSynopsis != '')
				{
					jQuery('#divRegionInfo #divInfoSynopsis #Text').html(i.InfoSynopsis);
					jQuery('#divRegionInfo #divInfoSynopsis').show();
				}
			}
		},
		'json'
	);
} //doGetRegionInfo()

function doUpdateDatabaseListByUser()
{
	jQuery(".contentBlock").hide();
	jQuery("#divRegionList").show();
	// Hide everything at start...
	jQuery('.databaseTitle').hide();
	jQuery('.databaseList').hide();

	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			cmd: 'cmdSearchDB',
			searchDBQuery: '', 
			searchDBCountry : 0
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				if (parseInt(data.NoOfDatabases) > 0)
				{
					jQuery('#divDatabaseFindList').show();
					jQuery('#divDatabaseFindError').hide();
					RegionByRole = new Array(5);
					RegionByRole['ADMINREGION'] = new Array();
					RegionByRole['SUPERVISOR'] = new Array();
					RegionByRole['USER'] = new Array();
					RegionByRole['OBSERVER'] = new Array();
					RegionByRole['NONE'] = new Array();

					$RoleList = new Array(5);
					var iCount = 0;
					jQuery('.databaseList').empty();
					jQuery.each(data.RegionList, function(RegionId, value) {
						jQuery('#divRegionList #title_' + value.Role).show();
						jQuery('#divRegionList #list_' + value.Role).show().append('<a href="' + jQuery('#desinventarURL').val() + '/' + RegionId + '" id="' + RegionId + '" class="databaseLink">' + value.RegionLabel + '</a><br />');
						iCount++;
					});
					
					jQuery('.databaseLink').addClass("alt").unbind('click').click(function() {
						RegionId = jQuery(this).attr('id');
						if (jQuery('#desinventarPortalType').val() != '')
						{
							displayRegionInfo(RegionId);
						}
						else
						{
							window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
						}
						return false;
					}); //bind
				}
				else
				{
					jQuery('#divDatabaseFindList').hide();
					jQuery('#divDatabaseFindError').show();
				}
			} //if
		},
		'json' //function
	);
} //doUpdateDatabaseListByUser()
