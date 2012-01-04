/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseGeolevels()
{
	// Avoid duplicate GeoLevelName
	//onBlur="updateList('levstatusmsg', jQuery('#desinventarURL').val() + '/geolevel.php', 'r={-$reg-}&cmd=chkname&GeoLevelId='+ $('GeoLevelId').value +'&GeoLevelName='+ $('GeoLevelName').value);"

	jQuery('#tbodyDatabaseGeolevels_List').delegate('tr', 'click', function(e) {
		if (! jQuery('#divDatabaseGeolevels_Edit').is(':visible'))
		{
			jQuery('#fldDatabaseGeolevels_GeoLevelId').val(jQuery('.GeoLevelId',this).text());
			jQuery('#fldDatabaseGeolevels_GeoLevelName').val(jQuery('.GeoLevelName',this).text());
			jQuery('#fldDatabaseGeolevels_GeoLevelDesc').val(jQuery('.GeoLevelDesc',this).prop('title'));
			jQuery('#fldDatabaseGeolevels_GeoLevelActiveCheckbox').prop('checked', jQuery('.GeoLevelActive :input',this).is(':checked')).change();
			jQuery('#btnDatabaseGeolevels_Add').hide();
			jQuery('#divDatabaseGeolevels_Edit').show();
		}
	});
	jQuery('#btnDatabaseGeolevels_Add').click(function() {
		jQuery('#divDatabaseGeolevels_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseGeolevels_GeoLevelId').val('');
		jQuery('#fldDatabaseGeolevels_GeoLevelName').val('');
		jQuery('#fldDatabaseGeolevels_GeoLevelDesc').val('');
		jQuery('#fldDatabaseGeolevels_GeoLevelActiveCheckbox').prop('checked', true).change();
	});

	jQuery('#btnDatabaseGeolevels_Save').click(function() {
		jQuery('#frmDatabaseGeolevels_Edit').trigger('submit');
	});

	jQuery('#btnDatabaseGeolevels_Cancel').click(function() {
		jQuery('#divDatabaseGeolevels_Edit').hide();
		jQuery('#btnDatabaseGeolevels_Add').show();
	});

	jQuery('#fldDatabaseGeolevels_GeoLevelActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#fldDatabaseGeolevels_GeoLevelActive').val(v);
	});

	jQuery('#frmDatabaseGeolevels_Edit').submit(function() {
		return false;
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
					jQuery('.GeoLevelDesc', clonedRow).html(value.GeoLevelDesc.substring(0,150));
					jQuery('.GeoLevelDesc', clonedRow).prop('title', value.GeoLevelDesc);
					jQuery('.GeoLevelActive :input', clonedRow).prop('checked', value.GeoLevelActive>0);
					jQuery('#tbodyDatabaseGeolevels_List').append(clonedRow);
				});
				jQuery('#tblDatabaseGeolevels_List .GeoLevelId').hide();
				jQuery('#tbodyDatabaseGeolevels_List tr:even').addClass('under');
			}
		},
		'json'
	);
}

