/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyMain()
{
	onReadyDatabaseUpload();
	onReadyDatabaseCreate();
	onReadyDatabaseUsers();
	onReadyGeography();
	onReadyGeolevels();
	onReadyDatabaseEvents();
	onReadyAdminUsers();
	onReadyUserPermAdmin();
	onReadyCommon();
	onReadyPrototype();
	onReadyUserLogin();
	onReadyUserAccount();
	onReadyDatacards();
	onReadyAdminDatabase();
	onReadyExtraEffects();
	onReadyQueryResults();
	onReadyData();
	onReadyGraphic();
	onReadyThematicMap();	
	onReadyStatParams();

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
							jQuery('body').trigger('cmdViewDataUpdate');
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

	jQuery('#DBConfig_Geolevels').on('show', function() {
		jQuery('body').trigger('cmdGeolevelsShow');
	});
	jQuery('#DBConfig_Geography').on('show', function() {
		jQuery('body').trigger('cmdGeographyShow');
	});

	jQuery('#DBConfig_Events').on('show', function() {
		jQuery('body').trigger('cmdDatabaseEventsShow');
	});

	jQuery('#DBConfig_Users').on('show', function() {
		jQuery('body').trigger('cmdDatabaseUsersShow');
	});
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		var me = jQuery(jQuery(this).attr('href'));
		showtip(me.find('.helptext').text());
		var cmd = jQuery(this).attr('cmd');
		if (cmd == '')
		{
			jQuery(me).trigger('show');
		}
		else
		{
			me.find('.content').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(
				jQuery(this).data('url'),
				{
					cmd      : cmd,
					RegionId : jQuery('#desinventarRegionId').val(),
					lang     : jQuery('#desinventarLang').val()
				},
				function(data)
				{
					me.find('.content').html(data);
					switch(cmd)
					{
						case 'cmdDBInfoCause':
							onReadyDBConfigCauses();
						break;
						default:
							onReadyDatabaseConfig();
							onReadyExtraEffects();
						break;
					}
				}
			);
		}
		return false;
	});
	jQuery('body').on('cmdDatabaseLoadData', function() {
		doDatabaseLoadData();
	});
	jQuery('body').trigger('cmdDatabaseLoadData');
} //onReadyMain()

function doDatabaseLoadData()
{
	if (jQuery('#desinventarRegionId').val() != '')
	{
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseLoadData',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data)
			{
				jQuery('body').data('GeolevelsList', data.GeolevelsList);
				jQuery('body').data('EventList', data.EventList);
				jQuery('body').data('CauseList', data.CauseList);
				jQuery('body').data('RecordCount', data.RecordCount);
				var dataItems = jQuery('body').data();
				jQuery.each(dataItems, function(index, value) {
					if (index.substr(0,13) === 'GeographyList')
					{
						jQuery('body').removeData(index);
					}
				});
				jQuery('body').data('GeographyList', data.GeographyList);
			},
			'json'
		);
	}
} //doDatabaseLoadData()
