/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyGeolevels()
{
	jQuery('#tbodyGeolevels_List').delegate('tr', 'click', function(e) {
		if (! jQuery('#divGeolevels_Edit').is(':visible'))
		{
			jQuery('#fldGeolevels_GeoLevelId').val(jQuery('.GeoLevelId',this).text());
			jQuery('#fldGeolevels_GeoLevelName').val(jQuery('.GeoLevelName',this).text());
			jQuery('#fldGeolevels_GeoLevelDesc').val(jQuery('.GeoLevelDesc',this).prop('title'));
			jQuery('#txtGeolevels_GeoLevelActive').hide();
			jQuery('#fldGeolevels_GeoLevelActiveCheckbox').prop('checked', jQuery('.GeoLevelActive :input',this).is(':checked')).change().hide();
			jQuery('#btnGeolevels_Add').hide();
			jQuery('#divGeolevels_Edit').show();
		}
	});
	jQuery('#btnGeolevels_Add').click(function() {
		jQuery('#divGeolevels_Edit').show();
		jQuery(this).hide();
		jQuery('#fldGeolevels_GeoLevelId').val('-1');
		jQuery('#fldGeolevels_GeoLevelName').val('');
		jQuery('#fldGeolevels_GeoLevelDesc').val('');
		jQuery('#txtGeolevels_GeoLevelActive').hide();
		jQuery('#fldGeolevels_GeoLevelActiveCheckbox').prop('checked', true).change().hide();
	});

	jQuery('#btnGeolevels_Save').click(function() {
		jQuery('#frmGeolevels_Edit').trigger('submit');
	});

	jQuery('#btnGeolevels_Cancel').click(function() {
		jQuery('#divGeolevels_Edit').hide();
		jQuery('#btnGeolevels_Add').show();
	});

	jQuery('#fldGeolevels_GeoLevelActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#fldGeolevels_GeoLevelActive').val(v);
	});

	jQuery('#frmGeolevels_Edit').submit(function() {
		var bContinue = true;
		if (bContinue && jQuery.trim(jQuery('#fldGeolevels_GeoLevelName').val()) == '')
		{
			jQuery('#fldGeolevels_GeoLevelName').highlight();
			jQuery('#msgGeolevels_ErrorEmtpyFields').show();
			setTimeout(function () {
				jQuery('#fldGeolevels_GeoLevelName').unhighlight();
				jQuery('.clsGeolevelsStatus').hide();
			}, 2500);
			bContinue = false;
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdGeolevelsUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					GeoLevel : jQuery('#frmGeolevels_Edit').serializeObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divGeolevels_Edit').hide();
						jQuery('#btnGeolevels_Add').show();
						jQuery('#msgGeolevels_UpdateOk').show();
						doGeolevelsPopulateList(data.GeolevelsList);
					}
					else
					{
						jQuery('#msgGeolevels_UpdateError').show();
					}					
					setTimeout(function () {
						jQuery('.clsGeolevelsStatus').hide();
					}, 2500);
				},
				'json'
			);
		}		
		return false;
	});

	// Attach events to main page
	jQuery('body').on('cmdGeolevelsShow', function() {
		jQuery('body').trigger('cmdMainWaitingShow');
		jQuery('.clsGeolevelsStatus').hide();
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data)
			{
				jQuery('body').trigger('cmdMainWaitingHide');
				if (parseInt(data.Status) > 0)
				{
					doGeolevelsPopulateList(data.GeolevelsList);
				}
			},
			'json'
		);
	});
} //onReadyGeolevels()

function doGeolevelsPopulateList(GeolevelsList)
{
	jQuery('#divGeolevels_Edit').hide();
	jQuery('#tbodyGeolevels_List').find('tr:gt(0)').remove();
	jQuery.each(GeolevelsList, function(index, value) {
		var clonedRow = jQuery('#tbodyGeolevels_List tr:last').clone().show();
		jQuery('.GeoLevelId', clonedRow).html(index);
		jQuery('.GeoLevelName', clonedRow).html(value.GeoLevelName);
		jQuery('.GeoLevelDesc', clonedRow).html(value.GeoLevelDesc.substring(0,150));
		jQuery('.GeoLevelDesc', clonedRow).prop('title', value.GeoLevelDesc);
		jQuery('.GeoLevelActive :input', clonedRow).prop('checked', value.GeoLevelActive>0);
		jQuery('#tbodyGeolevels_List').append(clonedRow);
	});
	jQuery('#tblGeolevels_List .GeoLevelId').hide();
	jQuery('#tblGeolevels_List .GeoLevelActive').hide();
	jQuery('#tbodyGeolevels_List tr:even').addClass('under');
} //doGeolevelsPopulateList()

