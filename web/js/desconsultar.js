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

	// 2011-02-05 (jhcaiced) Configure RecordStatus field
	if (jQuery('#desinventarUserRoleValue').val() > 0)
	{
		jQuery('#fldQueryRecordStatus').val(['PUBLISHED','READY']);
		jQuery('#divQueryRecordStatus').show();
	}
	else
	{
		jQuery('#fldQueryRecordStatus').val('PUBLISHED');
		jQuery('#divQueryRecordStatus').hide();
	}
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
