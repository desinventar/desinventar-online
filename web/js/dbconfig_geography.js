/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDBConfigGeography()
{
	jQuery('.clsDBConfigGeographyStatus').hide();

	jQuery('#frmDBConfigGeographyEdit').unbind('submit').submit(function() {
		var bContinue = true;
		var a = new Array('aGeographyCode','aGeographyName');
		bContinue = checkForm('frmDBConfigGeographyEdit', a, 'Required fields are missing');
		if (bContinue)
		{
			jQuery('#frmDBConfigGeographyEdit #RegionId').val(jQuery('#desinventarRegionId').val());
			jQuery('#frmDBConfigGeographyEdit .GeographyActive').val(0);
			if (jQuery('#frmDBConfigGeographyEdit .GeographyActiveCheckbox').is(':checked'))
			{
				jQuery('#frmDBConfigGeographyEdit .GeographyActive').val(1);
			}
			var params = jQuery(this).serialize();
			jQuery.post(jQuery('#desinventarURL').val() + '/geography.php',
				params,
				function(data)
				{
					jQuery('.clsDBConfigGeographyStatus').hide();
					if (parseInt(data.Status) > 0)
					{
						jQuery('#msgDBConfigGeographyUpdate').show();
						jQuery('#geoaddsect').hide();
						var RegionId = jQuery('#desinventarRegionId').val();
						updateList('lst_ageo', jQuery('#desinventarURL').val() + '/geography.php', 'RegionId=' + RegionId +'&cmd=list&GeographyId=');
						updateList('qgeolst', jQuery('#desinventarURL').val() + '/', 'RegionId='+ RegionId +'&cmd=geolst');
					}
					else
					{
						jQuery('#msgDBConfigGeographyError').text('Cannot save geography, error code : ' + data.Status).show();
					}
				},
				'json'
			);
		}
		return false;
	});
}
