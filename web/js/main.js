function onReadyMain() {
	onReadyCommon();
	onReadyThematicMap();

	jQuery('#DC').submit(function() {
		var myURL = jQuery(this).attr('action');
		var myCmd = jQuery('#prmQueryCommand').val();
		if ( (myCmd == 'cmdGridSave') ||
		     (myCmd == 'cmdGraphSave') ||
		     (myCmd == 'cmdMapSave') || 
		     (myCmd == 'cmdStatSave') ||
		     (myCmd == 'cmdQuerySave')) {
			return true;
		} else {
			jQuery('#divDatabaseInfo').hide();
			jQuery('#dcr').show();
			jQuery('#dcr').html('<img src="loading.gif">');
			jQuery.post(myURL,
				jQuery(this).serialize(),
				function(data) {
					jQuery('#dcr').html(data);
					onReadyData();
					onReadyThematicMap();
					onReadyStatistic();
				}
			);
			return false;
		}
	});

	jQuery('.contentBlock').hide();
	if (jQuery('#desinventarRegionId').val() != '') {
		// Load Database Info and Show
		jQuery('#divDatabaseInfo').load('index.php?cmd=getRegionFullInfo&r=' + jQuery('#desinventarRegionId').val());
		jQuery('#divDatabaseInfo').show();
		jQuery('#dcr').hide();
		jQuery('#divQueryResults').show();
	} else {
		// Show database list
		jQuery("#divDatabaseList").show();
	}
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		var me = jQuery(jQuery(this).attr('href'));
		me.html('<img src="loading.gif" />');
		//jQuery(jQuery(this).attr('href')).load(jQuery(this).attr('data'), {r : jQuery('#desinventarRegionId').val() });
		jQuery.post(
			jQuery(this).attr('data'),
			{r : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				me.html(data);
				onReadyDatabaseConfig();
			}
		);
	});
	jQuery('.classDBConfig_tabs:first').click();
}
