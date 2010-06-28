function onReadyMain() {
	jQuery('#DC').submit(function() {
		var myURL = jQuery(this).attr('action');
		var myCmd = jQuery('#prmCommand').val();
		if ( (myCmd == 'cmdMapSave') || 
		     (myCmd == 'cmdQuerySave')) {
			return true;
		} else {
			jQuery.post(myURL,
				jQuery(this).serialize(),
				function(data) {
					jQuery('#dcr').html('');
					jQuery('#dcr').html(data);
					onReadyData();
					onReadyGraphic();
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
		jQuery('#dcr').load('index.php?cmd=getRegionFullInfo&r=' + jQuery('#desinventarRegionId').val());
		jQuery('#divQueryResults').show();
	} else {
		// Show database list
		jQuery("#divDatabaseList").show();
	}
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		jQuery(jQuery(this).attr('href')).html('<img src="loading.gif" />');
		jQuery(jQuery(this).attr('href')).load(jQuery(this).attr('data'), {r : jQuery('#desinventarRegionId').val() });
	});
	jQuery('.classDBConfig_tabs').first().click();
}
