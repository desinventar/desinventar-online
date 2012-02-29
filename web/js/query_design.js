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
		/*
		enadisEff(jQuery(this).attr('id'), jQuery(this).prop('checked'));
		function enadisEff(id, chk)
		{
			if (chk)
			{
				$('o'+ id).style.display = 'inline';
				enab($(id +'[0]'));
				enab($(id +'[1]'));
				enab($(id +'[2]'));
			}
			else
			{
				$('o'+ id).style.display = 'none';
				disab($(id +'[0]'));
				disab($(id +'[1]'));
				disab($(id +'[2]'));
			}
		}
		*/
	}).focus(function() {
		showtip(jQuery(this).data('help'));
	});
} //onReadyQueryDesign()
