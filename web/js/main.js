/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyMain()
{
	onReadyDatabaseList();
	onReadyDatabaseUpload();
	onReadyDatabaseCreate();
	onReadyDatabaseUsers();
	onReadyQueryDesign();
	onReadyGeography();
	onReadyGeolevels();
	onReadyDatabaseEvents();
	onReadyDatabaseCauses();
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

	jQuery('#DBConfig_Causes').on('show', function() {
		jQuery('body').trigger('cmdDatabaseCausesShow');
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

	jQuery(window).bind('hashchange', function(e) {
		var url = jQuery.param.fragment();
		var options = url.split('/');
		switch(options[0])
		{
			case '':
				//Nothing to do
				jQuery('#desinventarRegionId').val('');
				jQuery('body').trigger('cmdViewportShow');
			break;
			default:
				var RegionId = options[0];
				jQuery('#desinventarRegionId').val(RegionId);
				jQuery('body').trigger('cmdDatabaseLoadData');
			break;
		}
	});
	jQuery(window).trigger('hashchange');
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
				// Initialize data-* components for body
				jQuery('body').data('RegionId', data.RegionId);
				jQuery('body').data('params', data.params);
				jQuery('body').data('GeolevelsList', data.GeolevelsList);
				jQuery('body').data('EventList', data.EventList);
				jQuery('body').data('CauseList', data.CauseList);
				jQuery('body').data('EEFieldList', data.EEFieldList);
				jQuery('body').data('RecordCount', data.RecordCount);

				//Compatibility with old methods
				jQuery('#desinventarUserId').val(data.params.UserId);
				jQuery('#desinventarUserFullName').val(data.params.UserFullName);
				jQuery('#desinventarUserRole').val(data.params.UserRole);
				jQuery('#desinventarUserRoleValue').val(data.params.UserRoleValue);
				
				var dataItems = jQuery('body').data();
				jQuery.each(dataItems, function(index, value) {
					if (index.substr(0,13) === 'GeographyList')
					{
						jQuery('body').removeData(index);
					}
				});
				jQuery('body').data('GeographyList', data.GeographyList);
				// Info
				jQuery('#desinventarLang').val(data.params.LangIsoCode);
				jQuery('#desinventarRegionId').val(data.params.RegionId);
				jQuery('#desinventarRegionLabel').val(data.params.RegionLabel);
				jQuery('#desinventarNumberOfRecords').val(data.RecordCount);
				
				// Trigger event on mainblock components to update them
				jQuery('.mainblock').trigger('cmdInitialize');
				// Trigger ViewportShow
				jQuery('body').trigger('cmdViewportShow');
				/*
				console.log('loadData');
				console.log(data.query_design);
				var query_design = jQuery.parseXML(data.query_design);
				jQuery(query_design).find('geography_id').each(function() {
					var geography_id = jQuery(this).text();
					jQuery('div.QueryDesign div.GeographyList li.item input:checkbox[value="' + geography_id + '"]').trigger('click');
					console.log(geography_id);
				});
				console.log('endLoadData');
				*/
			},
			'json'
		);
	}
} //doDatabaseLoadData()
