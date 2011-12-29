/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseGeolevels()
{
	// Avoid duplicate GeoLevelName
	//onBlur="updateList('levstatusmsg', jQuery('#desinventarURL').val() + '/geolevel.php', 'r={-$reg-}&cmd=chkname&GeoLevelId='+ $('GeoLevelId').value +'&GeoLevelName='+ $('GeoLevelName').value);"

	jQuery('#tbodyDatabaseGeolevels').delegate('tr', 'click', function(e) {
		//onClick="setLevGeo('{-$key-}','{-$item[0]-}','{-$item[1]-}','','{-$item[2][0][1]-}','{-$item[2][0][2]-}',
		//'{-$item[2][0][3]-}','lev'); $('cmd').value='update';"
	});
	jQuery('#btnDatabaseGeolevels_Add').click(function() {
		// onclick="setLevGeo('','','','','','','','lev'); $('cmd').value='insert';"
	});

	// Attach events to main page
	jQuery('body').on('cmdDatabaseGeolevelsShow', function() {
		doDatabaseGeolevelsPopulateList();		
	});
} //onReadyDatabaseGeolevels()

function doDatabaseGeolevelsPopulateList()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd      : 'cmdDatabaseGeolevelsGetList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#tbodyDatabaseGeolevels_List').find('tr:gt(0)').remove();
				jQuery.each(data.GeolevelList, function(index, value) {
					var clonedRow = jQuery('#tbodyDatabaseGeolevels_List tr:last').clone().show();
					jQuery('.GeoLevelId', clonedRow).html(index);
					jQuery('.GeoLevelName', clonedRow).html(value.GeoLevelName);
					jQuery('.GeoLevelDesc', clonedRow).html(value.GeoLevelDesc);
					jQuery('#tbodyDatabaseGeolevels_List').append(clonedRow);
				});
				jQuery('#tblDatabaseGeolevels_List .GeoLevelId').hide();
				jQuery('#tbodyDatabaseGeolevels_List tr:even').addClass('under');
			}
		},
		'json'
	);
}

