/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
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
			var params = jQuery(this).serialize();
			jQuery.post('geography.php',
				params,
				function(data)
				{
					jQuery('.clsDBConfigGeographyStatus').hide();
					if (parseInt(data.Status) > 0)
					{
						jQuery('#msgDBConfigGeographyUpdate').show();
						jQuery('#geoaddsect').hide();
						var RegionId = jQuery('#desinventarRegionId').val();
						updateList('lst_ageo', 'geography.php', 'RegionId=' + RegionId +'&cmd=list&GeographyId=');
						updateList('qgeolst', 'index.php', 'RegionId='+ RegionId +'&cmd=geolst');
					}
					else
					{
						jQuery('#msgDBConfigGeographyError').text('Cannot save geography, error code : ' + data.Status).show();
					}
					//mod='geo'; sendData('{-$reg-}','geography.php', s, '');"
				},
				'json'
			);
		}
		return false;
	});
}
