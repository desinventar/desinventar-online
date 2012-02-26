/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeography()
{
	jQuery('div.Geography select.GeographyListHeader').change(function() {
		console.log('select change : ' + jQuery(this).data('GeoLevelId'));
	});

	jQuery('body').on('cmdGeographyLoad', function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				jQuery.each(data.GeolevelsList, function(key, value) {
					var clonedCell = jQuery('div.GeographyListHeader table tr td:last').clone().show();
					jQuery('span.title', clonedCell).text(value.GeoLevelName);
					jQuery('select', clonedCell).data('GeoLevelId', key);
					jQuery('div.GeographyListHeader table tr').append(clonedCell);
				});
				if (data.GeolevelsList.length > 0)
				{
					populate_geography_list(0);
				}
			},
			'json'
		);
	});
}

function populate_geography_list(prmGeoLevelId)
{
	var geolevel_id = parseInt(prmGeoLevelId);
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGeographyGetList',
			RegionId : jQuery('#desinventarRegionId').val(),
			GeoLevelId : geolevel_id
		},
		function(data)
		{
			var select = jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + geolevel_id + '")');
			select.empty();
			select.append(jQuery('<option>', { value : '' }).text('--'));
			jQuery('table.GeographyList tbody tr').remove();
			jQuery.each(data.GeographyList, function(key, value) {
				select.append(jQuery('<option>', { value : key }).text(value[1]));
				var clonedRow = jQuery('table.GeographyList thead tr:first').clone();
				jQuery('.GeographyId', clonedRow).html(key);
				jQuery('.GeographyCode',clonedRow).html(value[0]);
				jQuery('.GeographyName',clonedRow).html(value[1]);
				jQuery('.GeographyActive',clonedRow).html(value[2]);
				jQuery('.GeographyStatus',clonedRow).html(value[2]);
				jQuery('table.GeographyList tbody').append(clonedRow);
			});
			jQuery('table.GeographyList tr:even').addClass('under');
		},
		'json'
	);
}
