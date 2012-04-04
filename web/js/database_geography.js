/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeography()
{
	jQuery('div.Geography').on('change','select.ListHeader', function() {
		var geography_id = jQuery(this).val();
		var geolevel_id = jQuery(this).data('GeoLevelId');
		load_geography_list(geography_id, geolevel_id);
	});

	jQuery('div.Geography table.List tbody').on('dblclick','tr', function() {
		/*
		var geography_id = jQuery('.GeographyId', this).text();
		jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + jQuery('.GeographyLevel', this).text() + '")').val(geography_id).change();
		*/
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
		jQuery('.GeographyActiveCheckbox',form).prop('checked', parseInt(jQuery('.GeographyActive',this).text()) > 0).change();
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
	});

	jQuery('div.Geography form.Edit input.GeographyActiveCheckbox').change(function() {
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
		jQuery('div.Geography form.Edit input.GeographyId').val('');
		
		jQuery('div.Geography form.Edit input.GeographyActiveCheckbox').change();
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
		return false;
	});

	jQuery('div.Geography a.Export').click(function() {
		var form = jQuery('div.Geography form.Export');
		jQuery('div.Geography form.Export').submit();
		return false;
	});

	jQuery('div.Geography a.Cancel').click(function() {
		jQuery('div.Geography form.Edit').each(function() {
			this.reset();
		});
		jQuery('div.Geography div.Add').show();
		jQuery('div.Geography div.Edit').hide();
		return false;
	});

	jQuery('div.Geography a.Save').click(function() {
		jQuery('div.Geography form.Edit').submit();
		return false;
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
					jQuery('div.Geography div.Status span').hide();
					if (parseInt(data.Status) > 0)
					{
						populate_geography_list(data.GeographyList,data.GeographyListCount);
						jQuery('div.Geography div.Status span.Ok').show();
						setTimeout(function() {
							jQuery('div.Geography div.Status span').hide();
							jQuery('div.Geography div.Add').show();
							jQuery('div.Geography div.Edit').hide();
						}, 2000);
					}
					switch(parseInt(data.Status))
					{
						case 1:
						break;
						case -44:
							jQuery('div.Geography div.Status span.DuplicatedCode').show();
						break;
						case -48:
							jQuery('div.Geography div.Status span.WithDatacards').show();
						break;
						default:
							jQuery('div.Geography div.Status span.Error').show();
						break;
					}
					setTimeout(function() {
						jQuery('div.Geography div.Status span').hide();
					}, 4000);
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
					jQuery('div.Geography table.ListHeader tr td:not(:first)').remove();
					jQuery.each(data.GeolevelsList, function(key, value) {
						var clonedCell = jQuery('div.Geography table.ListHeader tr td:last').clone().show();
						jQuery(clonedCell).data('GeoLevelId', key);
						jQuery('span.title', clonedCell).text(parseInt(key + 1) + ' - ' + value.GeoLevelName);
						jQuery('select', clonedCell).data('GeoLevelId', key);
						jQuery('div.Geography table.ListHeader tr').append(clonedCell);
						jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + value.GeoLevelId + '")').disable();
					});
					jQuery('div.Geography input.GeoLevelCount').val(data.GeolevelsList.length);
					if (data.GeolevelsList.length > 0)
					{
						jQuery('div.Geography select.ListHeader:first').change();
					}
				}
			},
			'json'
		);
	});

	// Initialize
	jQuery('div.Geography div.Add').show();
	jQuery('div.Geography div.Edit').hide();
	jQuery('div.Geography div.Status span').hide();

	// Initialize labels for csv geography export	
	var labels = '';
	var count = 0;
	jQuery('div.Geography table.List thead td').each(function() {
		if (count > 0)
		{
			labels = labels + ',';
		}
		labels = labels + '"' + jQuery(this).text().trim() + '"';
		count++;
	});
	jQuery('div.Geography form.Export input.Labels').val(labels);
} //onReadyGeography()

function populate_geography_list(prmGeographyList,prmGeographyListCount)
{
	var prmGeoLevelId = parseInt(jQuery('div.Geography input.GeoLevelId').val());
	var prmParentId = jQuery('div.Geography input.ParentId').val();
	var geolevel_count = parseInt(jQuery('div.Geography input.GeoLevelCount').val()) - 1;
	jQuery('div.Geography select.ListHeader').each(function() {
		if (parseInt(jQuery(this).data('GeoLevelId')) > prmGeoLevelId)
		{
			jQuery(this).val(jQuery('option:first', this).val());
			jQuery(this).disable();
		}
	});
	var select = jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + prmGeoLevelId + '")');
	select.empty();
	select.append(jQuery('<option>', { value : prmParentId }).text(jQuery('div.Geography span.All').text()));
	jQuery('div.Geography table.List tbody tr').remove();
	jQuery.each(prmGeographyList, function(key, value) {
		if (prmGeoLevelId < geolevel_count)
		{
			select.append(jQuery('<option>', { value : key }).text(value.GeographyName));
		}
		var clonedRow = jQuery('div.Geography table.List thead tr:first').clone();
		jQuery('.GeographyId'    ,clonedRow).html(value.GeographyId);
		jQuery('.GeographyLevel' ,clonedRow).html(value.GeographyLevel);
		jQuery('.GeographyCode'  ,clonedRow).html(value.GeographyCode);
		jQuery('.GeographyName'  ,clonedRow).html(value.GeographyName);
		jQuery('.GeographyActive',clonedRow).html(value.GeographyActive);
		jQuery('.GeographyStatus',clonedRow).html(jQuery('select.GeographyStatusText option[value="' + value.GeographyActive + '"]').text());
		jQuery('div.Geography table.List tbody').append(clonedRow);
	});
	if (jQuery('option',select).size() > 1)
	{
		select.enable();
	}
	jQuery('div.Geography table.List td.GeographyLevel').hide();
	jQuery('div.Geography table.List td.GeographyActive').hide();
	jQuery('div.Geography table.List tr').removeClass('under');
	jQuery('div.Geography table.List tr:even').addClass('under');
} //populate_geography_list

function load_geography_list(prmGeographyId, prmGeoLevelId)
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
} //load_geography_list()
