/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
function onReadyMain()
{
	onReadyDatabaseUpload();
	onReadyDatabaseCreate();
	onReadyDatabaseUsers();
	onReadyDatabaseEvents();
	onReadyAdminUsers();
	onReadyUserPermAdmin();
	onReadyCommon();
	onReadyPrototype();
	onReadyUserLogin();
	onReadyDatacards();
	onReadyAdminDatabase();
	onReadyExtraEffects();
	onReadyQueryResults();
	onReadyData();
	onReadyGraphic();
	onReadyThematicMap();	
	onReadyStatParams();

	jQuery('body').on('UserLoggedIn',function() {
		doWindowReload();
	});

	jQuery('body').on('UserLoggedOut',function() {
		doWindowReload();
	});
	
	jQuery('#frmMainQuery').submit(function() {
		var myURL = jQuery(this).attr('action');
		var myCmd = jQuery('#prmQueryCommand').val();
		if ( (myCmd == 'cmdGridSave') ||
		     (myCmd == 'cmdGraphSave') ||
		     (myCmd == 'cmdMapSave') || 
		     (myCmd == 'cmdStatSave') ||
		     (myCmd == 'cmdQuerySave'))
		{
			return true;
		}
		else
		{
			//jQuery('body').trigger('cmdMainWaitingShow');
			jQuery('#divRegionInfo').hide();
			jQuery('#dcr').show();
			jQuery('#dcr').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(myURL,
				jQuery(this).serialize(),
				function(data)
				{
					//jQuery('body').trigger('cmdMainWaitingHide');
					jQuery('#dcr').html(data);
					switch(myCmd)
					{
						case 'cmdGridShow':
							onReadyData();
						break;
						case 'cmdMapShow':
							createThematicMap();
						break;
						case 'cmdGraphShow':
						break;
						case 'cmdStatShow':
							onReadyStatistic();
						break;
						default:
						break;
					} //switch
				}
			);
			return false;
		}
	});

	jQuery('#DBConfig_DatabaseEvents').on('show', function() {
		jQuery('body').trigger('cmdDatabaseEventsShow');
	});

	jQuery('#DBConfig_DatabaseUsers').on('show', function() {
		jQuery('body').trigger('cmdDatabaseUsersShow');
	});
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		var me = jQuery(jQuery(this).attr('href'));
		var cmd = jQuery(this).attr('cmd');
		if (cmd == '')
		{
			jQuery(me).trigger('show');
		}
		else
		{
			me.html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(
				jQuery(this).attr('data'),
				{
					cmd      : cmd,
					RegionId : jQuery('#desinventarRegionId').val(),
					lang     : jQuery('#desinventarLang').val()
				},
				function(data)
				{
					me.html(data);
					switch(cmd)
					{
						case 'cmdDBInfoCause':
							onReadyDBConfigCauses();
						break;
						default:
							onReadyDatabaseConfig();
							onReadyExtraEffects();
							onReadyDBConfigGeography();
						break;
					}
				}
			);
		}
		return false;
	});
} //onReadyMain()

function doViewportDestroy()
{
	var viewport = Ext.getCmp('viewport');
	if (viewport != undefined)
	{
		viewport.destroy();
		jQuery('#loading').show();
		jQuery('#loading-mask').show();
	}
} //doViewportDestroy

function doWindowReload()
{
	// Destroy viewport, the loading... message should stay.
	doViewportDestroy();
	// Reload document window
	window.location.reload(false);
} //doWindowReload
