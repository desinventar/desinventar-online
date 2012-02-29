/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('div.QueryDesign div.EffectLossesValue input').click(function() {
		enadisEff(jQuery(this).attr('id'), jQuery(this).prop('checked'));
	});
} //onReadyQueryDesign()
