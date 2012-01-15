/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyQueryResults()
{
	jQuery('#queryBeginYear').blur(function() {
		validateBeginYear();	
	});
	jQuery('#queryEndYear').blur(function() {
		validateEndYear();
	});

	jQuery('body').on('cmdMainQueryUpdate', function() {
		// 2011-02-05 (jhcaiced) Configure RecordStatus field
		if ( (jQuery('#desinventarUserId').val() != '') &&
		     (jQuery('#desinventarUserRoleValue').val() > 1) )
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED','READY']);
			jQuery('#divQueryRecordStatus').show();
		}
		else
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED']);
			jQuery('#divQueryRecordStatus').hide();
		}
	});
	
	jQuery('#btnViewData').click(function() {
		jQuery('body').trigger('cmdViewDataParams');
	});
	jQuery('#btnViewMap').click(function() {
		jQuery('body').trigger('cmdViewMapParams');
	});
	jQuery('#btnViewGraph').click(function() {
		jQuery('body').trigger('cmdViewGraphParams');
	});
	jQuery('#btnViewStd').click(function() {
		jQuery('body').trigger('cmdViewStdParams');
	});

	jQuery('body').on('cmdQueryResultsButtonHide', function() {
		console.log('cmdQueryResultsButtonHide');
		jQuery('#btnResultSave').hide();
		jQuery('#btnResultPrint').hide();
	});

	// Initialize code
	jQuery('body').trigger('cmdMainQueryUpdate');
} //onReadyQueryResults()

function validateQueryDefinition()
{
	var iReturn = 1;
	return iReturn;
} //validateQueryDefinition()

function validateBeginYear()
{
	var prmQueryMinYear = jQuery("#prmQueryMinYear").val();
	var MinYear = jQuery("#queryBeginYear").val();
	if (parseInt(MinYear) != MinYear-0 )
	{
		jQuery("#queryBeginYear").val(prmQueryMinYear);
	}
} //validateBeginYear()

function validateEndYear()
{
	var prmQueryMaxYear = jQuery("#prmQueryMaxYear").val();
	var MaxYear = jQuery("#queryEndYear").val();
	if (parseInt(MaxYear) != MaxYear-0 )
	{
		jQuery("#queryEndYear").val(prmQueryMaxYear);
	}
} //validateEndYear()
