/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeography()
{
	jQuery('div.GeographyListHeader').on('change','select', function() {
		var geography_id = jQuery(this).val();
		populate_geography_list(geography_id);
	});

	jQuery('table.GeographyList').on('dblclick','tr', function() {
		var geography_id = jQuery('.GeographyId', this).text();
			
	});

	jQuery('body').on('cmdGeographyLoad', function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				if (parseInt(data.Status) > 0)
				{
					jQuery.each(data.GeolevelsList, function(key, value) {
						var clonedCell = jQuery('div.GeographyListHeader table tr td:last').clone().show();
						jQuery('span.title', clonedCell).text(value.GeoLevelName);
						jQuery('select', clonedCell).data('GeoLevelId', key);
						jQuery('div.GeographyListHeader table tr').append(clonedCell);
						var select = jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + value.GeoLevelId + '")').disable();
					});
					if (data.GeolevelsList.length > 0)
					{
						populate_geography_list('');
					}
				}
			},
			'json'
		);
	});
}

function populate_geography_list(prmGeographyId)
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGeographyGetList',
			RegionId : jQuery('#desinventarRegionId').val(),
			GeographyId : prmGeographyId
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				var select = jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + data.GeoLevelId + '")');
				select.empty();
				select.append(jQuery('<option>', { value : '' }).text('--'));
				jQuery('table.GeographyList tbody tr').remove();
				jQuery.each(data.GeographyList, function(key, value) {
					select.append(jQuery('<option>', { value : key }).text(value.GeographyName));
					var clonedRow = jQuery('table.GeographyList thead tr:first').clone();
					jQuery('.GeographyId'    ,clonedRow).html(value.GeographyId);
					jQuery('.GeographyLevel' ,clonedRow).html(value.GeographyLevel);
					jQuery('.GeographyCode'  ,clonedRow).html(value.GeographyCode);
					jQuery('.GeographyName'  ,clonedRow).html(value.GeographyName);
					jQuery('.GeographyActive',clonedRow).html(value.GeographyActive);
					jQuery('.GeographyStatus',clonedRow).html(jQuery('select.GeographyStatusText option[value="' + value.GeographyActive + '"]').text());
					jQuery('table.GeographyList tbody').append(clonedRow);
				});
				if (parseInt(data.GeographyListCount) > 0)
				{
					select.enable();
				}
				//jQuery('table.GeographyList td.GeographyLevel').hide();
				//jQuery('table.GeographyList td.GeographyActive').hide();
				jQuery('table.GeographyList tr').removeClass('under');
				jQuery('table.GeographyList tr:even').addClass('under');
			}
		},
		'json'
	);
}
