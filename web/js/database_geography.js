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
				cmd : 'cmdGeographyGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				jQuery.each(data.GeolevelsList, function(key, value) {
					var clonedCell = jQuery('div.GeographyListHeader table tr td:last').clone().show();
					jQuery('span.title', clonedCell).text(value.GeoLevelName);
					jQuery('select', clonedCell).data('GeoLevelId', key);
					jQuery('div.GeographyListHeader table tr').append(clonedCell);
				});
				jQuery('div.Geography select.GeographyListHeader').change();
			},
			'json'
		);
	});
}
