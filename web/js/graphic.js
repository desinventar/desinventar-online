function onReadyGraphic() {
	// 2010-02-21 (jhcaiced) This jQuery calls ensures that the Period and Stat
	// parameters are not empty at the same time.
	jQuery('#graphParamPeriod').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#graphParamStat').val('');
		} else {
			jQuery('#graphParamStat').val('MONTH');
		}
	});
	jQuery('#graphParamStat').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#graphParamPeriod').val('');
		} else {
			jQuery('#graphParamPeriod').val('YEAR');
		}
	});
}