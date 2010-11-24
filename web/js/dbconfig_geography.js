function onReadyDBConfigGeography() {

	jQuery('#frmDBConfigGeographyEdit').unbind('submit').submit(function() {
		var bContinue = true;
		var a = new Array('aGeographyCode','aGeographyName');
		bContinue = checkForm('frmDBConfigGeographyEdit', a, 'Required fields are missing');
		if (bContinue) {
			jQuery('#frmDBConfigGeographyEdit #RegionId').val(jQuery('#desinventarRegionId').val());
			var params = jQuery(this).serialize();
			jQuery.post('geography.php',
				params,
				function(data) {
					//mod='geo'; sendData('{-$reg-}','geography.php', s, '');"
				}
			);
		}
		return false;
	});
}
