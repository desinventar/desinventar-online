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
	// Hide first two columns (EventId,EventPredefined)
	jQuery('td:nth-child(1)','#tblEventListUser,#tblEventListPredef').hide();
	jQuery('td:nth-child(2)','#tblEventListUser,#tblEventListPredef').hide();
	
	jQuery('#tblEventListUser,#tblEventListPredef').delegate('tr','mouseover', function(event) {
		jQuery(this).addClass('highlight');
	}).delegate('tr', 'mouseout', function(event) {
		jQuery(this).removeClass('highlight');
	});

	jQuery('#tblEventListUser,#tblEventListPredef').delegate('tr','click', function(event) {
		jQuery('#eventaddsect').show();
		jQuery('#frmDatabaseEvents_Edit #Id').val(jQuery('#Id',this).text());
		jQuery('#frmDatabaseEvents_Edit #Name').val(jQuery('#Name',this).text());
		jQuery('#frmDatabaseEvents_Edit #Desc').val(jQuery('#Desc',this).text());
		jQuery('#frmDatabaseEvents_Edit #Active').attr('checked', jQuery('#Active',this).is(':checked'));
		jQuery('#frmDatabaseEvents_Edit #Predefined').val(jQuery('#Predefined',this).text());
		jQuery('#frmDatabaseEvents_Edit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmDatabaseEvents_Edit #cmd').val('cmdEventUpdate');
		jQuery('#btnDatabaseEvents_Add').hide();
		// In Predefined Events cannot edit Description
		if (parseInt(jQuery('#frmDatabaseEvents_Edit #Predefined').val()) > 0)
		{
			jQuery('#frmDatabaseEvents_Edit #Desc').attr('readonly', true);
		}
	});

	jQuery('#btnDatabaseEvents_Add').click(function() {
		jQuery('#divDatabaseEvents_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseEvents_EventId').val('');
		jQuery('#fldDatabaseEvents_EventName').val('');
		jQuery('#fldDatabaseEvents_EventDesc').val('');
		jQuery('#fldDatabaseEvents_EventActive').prop('checked', true);
		jQuery('#fldDatabaseEvents_EventPredefined').val(0);
	});

	jQuery('#btnEventEditSend').unbind('click').click(function() {
		jQuery('#frmDatabaseEvents_Edit').trigger('submit');
	});

	jQuery('#btnEventEditCancel').unbind('click').click(function() {
		jQuery('#eventaddsect').hide();
		jQuery('#btnEventEditAdd').show();
	});

	jQuery('#frmDatabaseEvents_Edit').unbind('submit').submit(function()
	{
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
		jQuery('.EventName', clonedRow).html(value.EventName);
		jQuery('.EventDesc', clonedRow).html(value.EventDesc.substring(0,150));
		jQuery('.EventActive :input', clonedRow).prop('checked', value.EventActive>0);
		jQuery('#' + tbodyId).append(clonedRow);
	});
	jQuery('#' + tbodyId + ' .EventId').hide();
	jQuery('#' + tbodyId + ' .EventPredefined').hide();
	jQuery('#' + tbodyId + ' tr:even').addClass('under');
} //doDatabaseEventsPopulateList()