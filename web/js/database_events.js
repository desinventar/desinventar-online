/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseEvents()
{
	//Attach main events
	jQuery('body').on('cmdDatabaseEventsShow', function() {
		doDatabaseEventsPopulateLists();
	});

	jQuery('.clsDatabaseEventsStatus').hide();

	jQuery('#tbodyDatabaseEvents_EventListCustom,#tbodyDatabaseEvents_EventListDefault').delegate('tr','click', function(event) {
		if (! jQuery('#divDatabaseEvents_Edit').is(':visible'))
		{
			jQuery('#fldDatabaseEvents_EventId').val(jQuery('.EventId',this).text());
			jQuery('#fldDatabaseEvents_EventName').val(jQuery('.EventName',this).text());
			jQuery('#fldDatabaseEvents_EventDesc').val(jQuery('.EventDesc',this).prop('title'));
			jQuery('#fldDatabaseEvents_EventActiveCheckbox').prop('checked', jQuery('.EventActive :input',this).is(':checked')).change();
			jQuery('#fldDatabaseEvents_EventPredefined').val(jQuery('.EventPredefined',this).text());

			jQuery('#btnDatabaseEvents_Add').hide();
			// In Predefined Events cannot edit Description
			jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
			if (parseInt(jQuery('#fldDatabaseEvents_EventPredefined').val()) > 0)
			{
				jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', true);
			}
			jQuery('#divDatabaseEvents_Edit').show();
		}
	});

	jQuery('#btnDatabaseEvents_Add').click(function() {
		jQuery('#divDatabaseEvents_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseEvents_EventId').val('');
		jQuery('#fldDatabaseEvents_EventName').val('');
		jQuery('#fldDatabaseEvents_EventDesc').val('');
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
		jQuery('#fldDatabaseEvents_EventActiveCheckbox').prop('checked', true).change();
		jQuery('#fldDatabaseEvents_EventPredefined').val(0);
	});

	jQuery('#btnDatabaseEvents_Save').click(function() {
		jQuery('#frmDatabaseEvents_Edit').trigger('submit');
	});

	jQuery('#btnDatabaseEvents_Cancel').click(function() {
		jQuery('#divDatabaseEvents_Edit').hide();
		jQuery('#btnDatabaseEvents_Add').show();
	});

	jQuery('#fldDatabaseEvents_EventActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#fldDatabaseEvents_EventActive').val(v);
	});

	jQuery('#frmDatabaseEvents_Edit').submit(function() {
		var bContinue = true;
		if (bContinue && jQuery.trim(jQuery('#fldDatabaseEvents_EventName').val()) == '')
		{
			jQuery('#fldDatabaseEvents_EventName').highlight();
			jQuery('#msgDatabaseEvents_ErrorEmtpyFields').show();
			setTimeout(function () {
				jQuery('#fldDatabaseEvents_EventName').unhighlight();
				jQuery('.clsDatabaseEventsStatus').hide();
			}, 2500);
			bContinue = false;
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdDatabaseEventsUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					Event    : jQuery('#frmDatabaseEvents_Edit').serializeObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divDatabaseEvents_Edit').hide();
						jQuery('#msgDatabaseEvents_UpdateOk').show();
						doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListCustom' , data.EventListCustom);
						doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListDefault', data.EventListDefault);
					}
					else
					{
						switch(data.Status)
						{
							case -15:
								jQuery('#msgDatabaseEvents_ErrorCannotDelete').show();
							break;
							default:
								jQuery('#msgDatabaseEvents_UpdateError').show();
							break;
						}
					}					
					setTimeout(function () {
						jQuery('.clsDatabaseEventsStatus').hide();
					}, 2500);
				},
				'json'
			);
		}		
		return false;
	});
} //onReadyDatabaseEvents()

function doDatabaseEventsPopulateLists()
{
	jQuery('body').trigger('cmdMainWaitingShow');
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdDatabaseEventsGetList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListCustom' , data.EventListCustom);
				doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListDefault', data.EventListDefault);
			}
			jQuery('body').trigger('cmdMainWaitingHide');
		},
		'json'
	);
} //doDatabaseEventsPopulateLists()

function doDatabaseEventsPopulateList(tbodyId, EventList)
{
	jQuery('#' + tbodyId).find('tr:gt(0)').remove();
	jQuery('#' + tbodyId).find('tr').removeClass('under');
	jQuery.each(EventList, function(index, value) {
		var clonedRow = jQuery('#tbodyDatabaseEvents_EventListCustom tr:last').clone().show();
		jQuery('.EventId', clonedRow).html(index);
		jQuery('.EventPredefined',clonedRow).html(value.EventPredefined);
		jQuery('.EventName', clonedRow).html(value.EventName);
		jQuery('.EventDesc', clonedRow).html(value.EventDesc.substring(0,150));
		jQuery('.EventDesc', clonedRow).prop('title', value.EventDesc);
		jQuery('.EventActive :input', clonedRow).prop('checked', value.EventActive>0);
		jQuery('#' + tbodyId).append(clonedRow);
	});
	jQuery('#' + tbodyId + ' .EventId').hide();
	jQuery('#' + tbodyId + ' .EventPredefined').hide();
	jQuery('#' + tbodyId + ' tr:even').addClass('under');
} //doDatabaseEventsPopulateList()
