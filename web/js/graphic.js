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

	jQuery('#prmGraphField1').change(function() {
		enab($('prmGraphScale1'));
		enab($('prmGraphData1'));
		enab($('prmGraphMode1'));
	});
	
	jQuery('#prmGraphTypeHistogram').change(function() {
		jQuery('#prmGraphType').val('HISTOGRAM');
		var grp = jQuery(this).val();
		// Histogram Type
		disab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('BAR');
		enab($('prmGraphPeriod'));
		$('prmGraphPeriod').value = 'YEAR';
		enab($('prmGraphStat'));
		jQuery('#prmGraphScale0').enable();
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
		jQuery('#prmGraphVar').val(grp);
	});
	
	jQuery('#prmGraphTypeComparative').change(function() {
		jQuery('#prmGraphType').val('COMPARATIVE');
		var grp = jQuery(this).val();
		// Comparatives
		disabAxis2();
		enab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('PIE');
		$('prmGraphPeriod').value = "";
		disab($('prmGraphPeriod'));
		$('prmGraphStat').value = "";
		disab($('prmGraphStat'));
		jQuery('#prmGraphScale0').disable();
		disab($('_G+M_accu'));
		disab($('_G+M_over'));
		enab($('_G+D_perc'));
		if (jQuery('#prmGraphTypeHistogram').val() != '') {
			jQuery('#prmGraphTypeHistogram').val('');
		}
		jQuery('#prmGraphVar').val(grp);
	});

	jQuery('#prmGraphKind').change(function() {
		comp = $('prmGraphTypeComparative').value;
		var kind = jQuery(this).val();
		if ( (kind == 'BAR' || kind == 'LINE' || kind == 'PIE') &&
		     (comp == 'D.EventId' || comp == 'D.CauseId' || comp.substr(0,13) == 'D.GeographyId') 
		   ) {
			 enabAxis2();
			 enab($('_G+M_accu'));
			 disab($('_G+M_over'));
			 jQuery('#prmGraphScale0').enable();
		} else {
			disabAxis2();
			disab($('_G+M_accu'));
			jQuery('#prmGraphScale0').disable();
		}
	});
	
	jQuery('[help_tip]').mouseover(function() {
		showtip(jQuery(this).attr('help_tip'));
	});

	// Initialize Controls on Load
	jQuery('#prmGraphTypeComparative').val('').trigger('change');
	jQuery('#prmGraphTypeHistogram').val('D.DisasterBeginTime').trigger('change');
} // onReadyGraphic()

function disabAxis2() {
	jQuery('#divVerticalAxis2').hide();
	jQuery('#prmGraphField1').val('').disable();
}

function enabAxis2() {
	jQuery('#divVerticalAxis2').show();
	jQuery('#prmGraphField1').val('').enable();
}


