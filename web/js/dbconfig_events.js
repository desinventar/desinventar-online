/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDBConfigEvents()
{
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
		jQuery('#frmEventEdit #Id').val(jQuery('#Id',this).text());
		jQuery('#frmEventEdit #Name').val(jQuery('#Name',this).text());
		jQuery('#frmEventEdit #Desc').val(jQuery('#Desc',this).text());
		jQuery('#frmEventEdit #Active').attr('checked', jQuery('#Active',this).is(':checked'));
		jQuery('#frmEventEdit #Predefined').val(jQuery('#Predefined',this).text());
		jQuery('#frmEventEdit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmEventEdit #cmd').val('cmdEventUpdate');
		jQuery('#btnEventEditAdd').hide();
		// In Predefined Events cannot edit Description
		if (parseInt(jQuery('#frmEventEdit #Predefined').val()) > 0)
		{
			jQuery('#frmEventEdit #Desc').attr('readonly', true);
		}
	});

	jQuery('#btnEventEditAdd').unbind('click').click(function() {
		jQuery('#eventaddsect').show();
		jQuery(this).hide();
		jQuery('#frmEventEdit #Id').val('');
		jQuery('#frmEventEdit #Name').val('');
		jQuery('#frmEventEdit #Desc').val('');
		jQuery('#frmEventEdit #Active').attr('checked', true);
		jQuery('#frmEventEdit #Predefined').val(0);
		jQuery('#frmEventEdit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmEventEdit #cmd').val('cmdEventInsert');
	});

	jQuery('#btnEventEditSend').unbind('click').click(function() {
		jQuery('#frmEventEdit').trigger('submit');
	});

	jQuery('#btnEventEditCancel').unbind('click').click(function() {
		jQuery('#eventaddsect').hide();
		jQuery('#btnEventEditAdd').show();
	});

	jQuery('#frmEventEdit').unbind('submit').submit(function()
	{
		var a = new Array('Name','Desc');
		var validForm = checkForm('frmEventEdit', a, ''); 
		if (validForm == true)
		{
			var params = jQuery(this).serialize();
			jQuery.post(jQuery('#desinventarURL').val() + '/events.php',
				params, 
				function(data)
				{
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#frmEventEdit #Predefined').val();
					if (opt == "1")
					{
						updateList('lst_evepred', jQuery('#desinventarURL').val() + '/events.php', 'r=' + reg + '&cmd=list&predef=1&t=' + new Date().getTime());
					}
					else
					{
						updateList('lst_eveuser', jQuery('#desinventarURL').val() + '/events.php', 'r=' + reg + '&cmd=list&predef=0&t=' + new Date().getTime());
					}
					updateList('qevelst', jQuery('#desinventarURL').val() + '/', 'r='+ reg +'&cmd=evelst&t=' + new Date().getTime());
					jQuery('#frmEventEdit #Desc').removeAttr('readonly');
					jQuery('#eventaddsect').hide();
					jQuery('#btnEventEditAdd').show();
				}
			);
		}
		return false;
	});
}
