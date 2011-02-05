/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('#queryBeginYear').blur(function() {
		validateBeginYear();	
	});
	jQuery('#queryEndYear').blur(function() {
		validateEndYear();
	});
}

function validateQueryDefinition()
{
	var iReturn = 1;
	return iReturn;
};

function validateBeginYear()
{
	var prmQueryMinYear = jQuery("#prmQueryMinYear").val();
	var MinYear = jQuery("#queryBeginYear").val();
	if (parseInt(MinYear) != MinYear-0 )
	{
		jQuery("#queryBeginYear").val(prmQueryMinYear);
	}
}

function validateEndYear()
{
	var prmQueryMaxYear = jQuery("#prmQueryMaxYear").val();
	var MaxYear = jQuery("#queryEndYear").val();
	if (parseInt(MaxYear) != MaxYear-0 )
	{
		jQuery("#queryEndYear").val(prmQueryMaxYear);
	}
}
