function onReadyDatabaseList()
{
	jQuery('#divDatabaseFindList table.databaseList').on('mouseover','td.RegionDelete', function() {
		jQuery(this).parent().highlight();
	}).on('mouseout','td.RegionDelete', function() {
		jQuery(this).parent().unhighlight();
	});
} //onReadyDatabaseList

