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
		jQuery('#fldDatabaseEvents_EventId').val(jQuery('.EventId',this).text());
		jQuery('#fldDatabaseEvents_EventName').val(jQuery('.EventName',this).text());
		jQuery('#fldDatabaseEvents_EventDesc').val(jQuery('.EventDesc',this).prop('title'));
		jQuery('#fldDatabaseEvents_EventActive').prop('checked', jQuery('.EventActive :input',this).is(':checked'));
		jQuery('#fldDatabaseEvents_EventPredefined').val(jQuery('.EventPredefined',this).text());

		jQuery('#btnDatabaseEvents_Add').hide();
		// In Predefined Events cannot edit Description
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
		if (parseInt(jQuery('#fldDatabaseEvents_EventPredefined').val()) > 0)
		{
			jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', true);
		}
		jQuery('#divDatabaseEvents_Edit').show();
	});

	jQuery('#btnDatabaseEvents_Add').click(function() {
		jQuery('#divDatabaseEvents_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseEvents_EventId').val('');
		jQuery('#fldDatabaseEvents_EventName').val('');
		jQuery('#fldDatabaseEvents_EventDesc').val('');
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
		jQuery('#fldDatabaseEvents_EventActive').prop('checked', true);
		jQuery('#fldDatabaseEvents_EventPredefined').val(0);
	});

	jQuery('#btnDatabaseEvents_Save').click(function() {
		jQuery('#frmDatabaseEvents_Edit').trigger('submit');
	});

	jQuery('#btnDatabaseEvents_Cancel').click(function() {
		jQuery('#divDatabaseEvents_Edit').hide();
		jQuery('#btnDatabaseEvents_Add').show();
	});

	jQuery('#frmDatabaseEvents_Edit').submit(function() {
		jQuery('.clsDatabaseEventsStatus').hide();
		var a = new Array('Name','Desc');
		var validForm = checkForm('frmDatabaseEvents_Edit', a, ''); 
		if (validForm == true)
		{
			var params = jQuery(this).serialize();
			jQuery.post(jQuery('#desinventarURL').val() + '/events.php',
				params, 
				function(data)
				{
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#frmDatabaseEvents_Edit #Predefined').val();
					if (opt == "1")
					{
						updateList('lst_evepred', jQuery('#desinventarURL').val() + '/events.php', 'r=' + reg + '&cmd=list&predef=1&t=' + new Date().getTime());
					}
					else
					{
						updateList('lst_eveuser', jQuery('#desinventarURL').val() + '/events.php', 'r=' + reg + '&cmd=list&predef=0&t=' + new Date().getTime());
					}
					updateList('qevelst', jQuery('#desinventarURL').val() + '/', 'r='+ reg +'&cmd=evelst&t=' + new Date().getTime());
					jQuery('#frmDatabaseEvents_Edit #Desc').removeAttr('readonly');
					jQuery('#eventaddsect').hide();
					jQuery('#btnEventEditAdd').show();
					jQuery('#msgDatabaseEvents_Updated').show();
					setTimeout(function() {
						jQuery('.clsDatabaseEvents').hide();
					}, 2500);
				}
			);
		}
		return false;
	});
} //onReadyDatabaseEvents()

function doDatabaseEventsPopulateLists()
{
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
		},
		'json'
	);
} //doDatabaseEventsPopulateLists()

function doDatabaseEventsPopulateList(tbodyId, EventList)
{
	jQuery('#' + tbodyId).find('tr:gt(0)').remove();
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
