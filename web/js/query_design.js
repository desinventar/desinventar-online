/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('div.QueryDesign div.EffectLossesValue input').click(function() {
		parent = jQuery(this).parent();
		if (jQuery(this).prop('checked'))
		{
			jQuery('span.options', parent).show();
			jQuery('select.operator',parent).enable().change();
			jQuery('span.minvalue',parent).enable();
			jQuery('span.maxvalue',parent).enable();
		}
		else
		{
			jQuery('span.options', parent).hide();
			jQuery('select.operator',parent).disable().change();
			jQuery('span.minvalue',parent).disable();
			jQuery('span.maxvalue',parent).disable();
		}
	}).focus(function() {
		showtip(jQuery(this).data('help'));
	});
} //onReadyQueryDesign()
