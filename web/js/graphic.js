/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

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
		if (jQuery(this).val() != '')
		{
			enab($('prmGraphScale1'));
			enab($('prmGraphData1'));
			enab($('prmGraphMode1'));
		}
		else
		{
			disab($('prmGraphScale1'));
			disab($('prmGraphData1'));
			disab($('prmGraphMode1'));
		}
	});
	
	jQuery('#prmGraphTypeHistogram').change(function() {
		jQuery('#prmGraphType').val('HISTOGRAM');
		var grp = parseInt(jQuery(this).val());
		// Histogram Type
		disab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('BAR');
		enab($('prmGraphPeriod'));
		$('prmGraphPeriod').value = 'YEAR';
		enab($('prmGraphStat'));
		jQuery('#prmGraphScale0').enable();
		if (grp > 0)
		{
			disabAxis2();
			jQuery('#prmGraphMode0').val('NORMAL');
			disab($('prmGraphModeCummulative0'));
			enab($('prmGraphModeStacked0'));
		}
		else
		{
			enabAxis2();
			jQuery('#prmGraphMode0').val('NORMAL');
			enab($('prmGraphModeCummulative0'));
			disab($('prmGraphModeStacked0'));
		}
		disab($('_G+D_perc'));
		if (jQuery('#prmGraphTypeComparative').val() != '') {
			jQuery('#prmGraphTypeComparative').val('');
		}
		jQuery('#prmGraphSubType').val(grp);
	});
	
	jQuery('#prmGraphTypeComparative').change(function() {
		jQuery('#prmGraphType').val('COMPARATIVE');
		var grp = parseInt(jQuery(this).val());
		// Comparatives
		disabAxis2();
		enab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('PIE');
		$('prmGraphPeriod').value = "";
		disab($('prmGraphPeriod'));
		$('prmGraphStat').value = "";
		disab($('prmGraphStat'));
		jQuery('#prmGraphScale0').disable();
		jQuery('#prmGraphMode0').val('NORMAL');
		disab($('prmGraphModeCummulative0'));
		disab($('prmGraphModeStacked0'));
		enab($('_G+D_perc'));
		if (jQuery('#prmGraphTypeHistogram').val() != '') {
			jQuery('#prmGraphTypeHistogram').val('');
		}
		jQuery('#prmGraphSubType').val(grp);
	});

	jQuery('#prmGraphKind').change(function() {
		comp = parseInt(jQuery('#prmGraphTypeComparative').val());
		var kind = jQuery(this).val();
		if (comp > 0)
		{
			if (kind != 'PIE')
			{
				disab($('_G+D_perc'));
				if (jQuery('#prmGraphData0').val() == 'PERCENT')
				{	
					jQuery('#prmGraphData0').val('NONE');
				}
			}
			else
			{
				enab($('_G+D_perc'));
			}
		}
		if ( (kind == 'BAR' || kind == 'LINE' || kind == 'PIE') && (comp < 200) )
		{
			 enabAxis2();
			 enab($('prmGraphModeCummulative0'));
			 disab($('prmGraphModeStacked0'));
			 jQuery('#prmGraphScale0').enable();
		}
		else
		{
			disabAxis2();
			disab($('prmGraphModeCummulative0'));
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

function disabAxis2()
{
	jQuery('#divVerticalAxis2').hide();
	jQuery('#prmGraphField1').val('');
} //disabAxis2()

function enabAxis2()
{
	jQuery('#divVerticalAxis2').show();
	//jQuery('#prmGraphField1').val('');
}


