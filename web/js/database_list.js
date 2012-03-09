function onReadyDatabaseList()
{
	jQuery('#divDatabaseFindList table.databaseList').on('mouseover','td.RegionDelete', function() {
		jQuery(this).parent().highlight();
	}).on('mouseout','td.RegionDelete', function() {
		jQuery(this).parent().unhighlight();
	}).on('click','td.RegionDelete', function(event) {
		var RegionId = jQuery(this).parent().find('td.RegionId').text();
		var RegionLabel = jQuery(this).parent().find('span.RegionLabel').text();
		alert('Delete Database' + RegionLabel + '(' + RegionId +')');
		event.preventDefault();
	});
} //onReadyDatabaseList

