function onReadyDatabaseList()
{
	console.log('onReadyDatabaseList');
	jQuery('#divDatabaseRegionList table.databaseList').on('mouseover','td.RegionDelete', function() {
		jQuery(this).parent().highlight();
	}).on('mouseout','td.RegionDelete', function() {
		jQuery(this).parent().unhighlight();
	});
} //onReadyDatabaseList

