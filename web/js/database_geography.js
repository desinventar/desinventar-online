/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeography()
{
	jQuery('div.GeographyListHeader').on('change','select', function() {
		var geography_id = jQuery(this).val();
		load_geography_list(geography_id);
	});

	jQuery('table.GeographyList').on('dblclick','tr', function() {
		var geography_id = jQuery('.GeographyId', this).text();
		jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + jQuery('.GeographyLevel', this).text() + '")').val(geography_id).change();
	}).on('mouseover', 'tr', function(event) {
		jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	}).on('click', 'tr', function(event) {
		var form = jQuery('div.Geography form.Edit');
		jQuery('.GeographyId'    , form).val(jQuery('.GeographyId'    , this).text());
		jQuery('.GeographyCode'  , form).val(jQuery('.GeographyCode'  , this).text());
		jQuery('.GeographyName'  , form).val(jQuery('.GeographyName'  , this).text());
		jQuery('.GeographyActive', form).val(jQuery('.GeographyActive', this).text());
		jQuery('.GeographyActiveCheckbox',form).prop('checked', parseInt(jQuery('.GeographyActive',this).text()) > 0);
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
	});

	jQuery('div.Geography form.Edit input.GeographytActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('div.Geography form.Edit input.GeographyActive').val(v);
	});

	jQuery('div.Geography a.Add').click(function() {
		jQuery('div.Geography form.Edit').each(function() {
			this.reset();
		});
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
	});

	jQuery('div.Geography a.Cancel').click(function() {
		jQuery('div.Geography form.Edit').each(function() {
			this.reset();
		});
		jQuery('div.Geography div.Add').show();
		jQuery('div.Geography div.Edit').hide();
	});

	jQuery('div.Geography a.Save').click(function() {
		jQuery('div.Geography form.Edit').submit();
	});

	jQuery('div.Geography form.Edit').submit(function() {
		var bContinue = 1;
		var w = '';
		if (bContinue > 0)
		{
			w = jQuery('div.Geography form.Edit input.GeographyCode');
			if (w.val() == '')
			{
				w.highlight();			
			}
		}
		if (bContinue > 0)
		{
			w = jQuery('div.Geography form.Edit input.GeographyName');
			if (w.val() == '')
			{
				w.highlight();
			}
		}
		if (bContinue > 0)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd       : 'cmdGeographyUpdate',
					RegionId  : jQuery('#desinventarRegionId').val(),
					Geography : jQuery('div.Geography form.Edit').toObject(),
					ParentId  : jQuery('div.Geography input.ParentId').val()
				},
				function(data)
				{
					if (parseInt(data.Status) > 0)
					{
						populate_geography_list(data.GeographyList,data.GeographyListCount);
						jQuery('div.Geography div.Add').show();
						jQuery('div.Geography div.Edit').hide();
					}
				},
				'json'
			);
		}
		return false;
	});

	jQuery('body').on('cmdGeographyShow', function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				if (parseInt(data.Status) > 0)
				{
					jQuery('div.GeographyListHeader table tr td:not(:first)').remove();
					jQuery.each(data.GeolevelsList, function(key, value) {
						if (key < parseInt(data.GeolevelsList.length - 1))
						{
							var clonedCell = jQuery('div.GeographyListHeader table tr td:last').clone().show();
							jQuery('span.title', clonedCell).text(value.GeoLevelName);
							jQuery('select', clonedCell).data('GeoLevelId', key);
							jQuery('div.GeographyListHeader table tr').append(clonedCell);
							var select = jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + value.GeoLevelId + '")').disable();
						}
					});
					if (data.GeolevelsList.length > 0)
					{
						load_geography_list('');
					}
				}
			},
			'json'
		);
	});

	// Initialize
	jQuery('div.Geography div.Add').show();
	jQuery('div.Geography div.Edit').hide();
} //onReadyGeography()

function populate_geography_list(prmGeographyList,prmGeographyListCount)
{
	var prmGeoLevelId = jQuery('div.Geography input.GeoLevelId').val();
	var prmParentId = jQuery('div.Geography input.ParentId').val();
	jQuery('select.GeographyListHeader').each(function() {
		if (parseInt(jQuery(this).data('GeoLevelId')) > prmGeoLevelId)
		{
			jQuery(this).val(jQuery('option:first', this).val());
			jQuery(this).disable();
		}
	});
	var select = jQuery('div.Geography select.GeographyListHeader:data("GeoLevelId=' + prmGeoLevelId + '")');
	select.empty();
	select.append(jQuery('<option>', { value : prmParentId }).text('--'));
	jQuery('table.GeographyList tbody tr').remove();
	jQuery.each(prmGeographyList, function(key, value) {
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
	if (parseInt(prmGeographyListCount) > 0)
	{
		select.enable();
	}
	jQuery('table.GeographyList td.GeographyLevel').hide();
	jQuery('table.GeographyList td.GeographyActive').hide();
	jQuery('table.GeographyList tr').removeClass('under');
	jQuery('table.GeographyList tr:even').addClass('under');
}

function load_geography_list(prmGeographyId)
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
				jQuery('div.Geography input.ParentId').val(prmGeographyId);
				jQuery('div.Geography input.GeoLevelId').val(data.GeoLevelId);
				populate_geography_list(data.GeographyList,data.GeographyListCount);
				jQuery('div.Geography div.Add').show();
				jQuery('div.Geography div.Edit').hide();
			}
		},
		'json'
	);
}
