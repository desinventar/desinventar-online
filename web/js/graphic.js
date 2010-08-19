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
	
	jQuery('#graphParamTypeHistogram').change(function() {
		var grp = jQuery(this).val();
		// Histogram Type
		disab($('_G+K_pie'));
		$('_G+Kind').value = "BAR";
		enab($('graphParamPeriod'));
		$('graphParamPeriod').value = 'YEAR';
		enab($('graphParamStat'));
		enab($('_G+Scale'));
		if (grp.substr(19, 1) == "|") {
			disabAxis2();
			disab($('_G+M_accu'));
			enab($('_G+M_over'));
		} else {
			enabAxis2();
			enab($('_G+M_accu'));
			disab($('_G+M_over'));
		}
		disab($('_G+D_perc'));

		jQuery('#graphParamTypeComparative').val('');
		$('_G+Type').value = grp;
		// For other graphics different from Temporal Histogram, the second variable should be disabled
		if (grp != 'D.DisasterBeginTime') {
			jQuery('#graphParamField2').removeAttr('disabled');
			jQuery('#graphParamField2').val('');
			jQuery('#graphParamField2').attr('disabled',true);
		}
	});
	
	jQuery('#graphParamTypeComparative').change(function() {
		var grp = jQuery(this).val();
		// Comparatives
		disabAxis2();
		enab($('_G+K_pie'));
		$('_G+Kind').value = "PIE";
		$('graphParamPeriod').value = "";
		jQuery('#graphParamField2').val('');
		disab($('graphParamPeriod'));
		$('graphParamStat').value = "";
		disab($('graphParamStat'));
		disab($('_G+Scale'));
		disab($('_G+M_accu'));
		disab($('_G+M_over'));
		enab($('_G+D_perc'));
		jQuery('#graphParamTypeHistogram').val('');
		$('_G+Type').value = grp;
	});
} // onReadyGraphic()
