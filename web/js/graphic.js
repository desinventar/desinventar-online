function onReadyGraphic() {
	// 2010-02-21 (jhcaiced) This jQuery calls ensures that the Period and Stat
	// parameters are not empty at the same time.
	jQuery('#prmGraphPeriod').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#prmGraphStat').val('');
		} else {
			jQuery('#prmGraphStat').val('MONTH');
		}
	});
	jQuery('#prmGraphStat').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#prmGraphPeriod').val('');
		} else {
			jQuery('#prmGraphPeriod').val('YEAR');
		}
	});
	
	jQuery('#prmGraphTypeHistogram').change(function() {
		jQuery('#prmGraphType').val('HISTOGRAM');
		var grp = jQuery(this).val();
		// Histogram Type
		disab($('_G+K_pie'));
		$('_G+Kind').value = "BAR";
		enab($('prmGraphPeriod'));
		$('prmGraphPeriod').value = 'YEAR';
		enab($('prmGraphStat'));
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
		if (jQuery('#prmGraphTypeComparative').val() != '') {
			jQuery('#prmGraphTypeComparative').val('');
		}
		$('_G+Type').value = grp;
	});
	
	jQuery('#prmGraphTypeComparative').change(function() {
		jQuery('#prmGraphType').val('COMPARATIVE');
		var grp = jQuery(this).val();
		// Comparatives
		disabAxis2();
		enab($('_G+K_pie'));
		$('_G+Kind').value = "PIE";
		$('prmGraphPeriod').value = "";
		//jQuery('#prmGraphField2').disable();
		disab($('prmGraphPeriod'));
		$('prmGraphStat').value = "";
		disab($('prmGraphStat'));
		disab($('_G+Scale'));
		disab($('_G+M_accu'));
		disab($('_G+M_over'));
		enab($('_G+D_perc'));
		if (jQuery('#prmGraphTypeHistogram').val() != '') {
			jQuery('#prmGraphTypeHistogram').val('');
		}
		$('_G+Type').value = grp;
	});

	// Initialize Controls on Load
	jQuery('#prmGraphTypeHistogram').trigger('change');
} // onReadyGraphic()

function disabAxis2() {
	jQuery('#divVerticalAxis2').hide();
	jQuery('#prmGraphField2').val('');
	disab($('_G+Scale2'));
	disab($('_G+Data2'));
	disab($('_G+Mode2'));
}

function enabAxis2() {
	jQuery('#divVerticalAxis2').show();
	jQuery('#prmGraphField2').val('');
	enab($('_G+Scale2'));
	enab($('_G+Data2'));
	enab($('_G+Mode2'));
}

function grpSelectbyKind() {
	comp = $('_G+TypeC').value;
	if ($('_G+Kind').value == "BAR" || $('_G+Kind').value == "LINE" || ($('_G+Kind').value != "PIE" &&
	   (comp == "D.EventId" || comp == "D.CauseId" || comp.substr(0,13) == "D.GeographyId"))) {
		 enabAxis2();
		 enab($('_G+M_accu'));
		 disab($('_G+M_over'));
		 enab($('_G+Scale'));
	} else {
		disabAxis2();
		disab($('_G+M_accu'));
		disab($('_G+Scale'));
	}
} //function

