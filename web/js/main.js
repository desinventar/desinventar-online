/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
function onReadyMain()
{
	onReadyCommon();
	onReadyQueryDesign();
	onReadyThematicMap();	
	
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
			jQuery('#divRegionInfo').hide();
			jQuery('#dcr').show();
			jQuery('#dcr').html('<img src="loading.gif">');
			jQuery.post(myURL,
				jQuery(this).serialize(),
				function(data)
				{
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

	jQuery('.contentBlock').hide();
	if (jQuery('#desinventarRegionId').val() != '')
	{
		// Load Database Info and Show
		doGetRegionInfo(jQuery('#desinventarRegionId').val());
		jQuery('#divRegionInfo').show();
		jQuery('#dcr').hide();
		jQuery('#divQueryResults').show();
	}
	else
	{
		// Show database list
		updateDatabaseListByUser();
	}
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		var me = jQuery(jQuery(this).attr('href'));
		me.html('<img src="loading.gif" />');
		jQuery.post(
			jQuery(this).attr('data'),
			{RegionId : jQuery('#desinventarRegionId').val(),
			 lang     : jQuery('#desinventarLang').val(),
			 cmd      : jQuery(this).attr('cmd')
			},
			function(data)
			{
				me.html(data);
				onReadyDatabaseConfig();
				onReadyExtraEffects();
				onReadyDBConfigGeography();
			}
		);
	});
	jQuery('.classDBConfig_tabs:first').click();
}
