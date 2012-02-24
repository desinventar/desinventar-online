/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDBConfigCauses()
{
	// Hide first two columns (CauseId,CausePredefined)
	jQuery('td:nth-child(1)','#tblCauseListUser,#tblCauseListPredef').hide();
	jQuery('td:nth-child(2)','#tblCauseListUser,#tblCauseListPredef').hide();

	jQuery('#tblCauseListUser,#tblCauseListPredef').on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});

	jQuery('#tblCauseListUser,#tblCauseListPredef').on('click', 'tr', function(event) {
		jQuery('#causeaddsect').show();
		jQuery('#frmCauseEdit #Id').val(jQuery('#Id',this).text());
		jQuery('#frmCauseEdit #Name').val(jQuery('#Name',this).text());
		jQuery('#frmCauseEdit #Desc').val(jQuery('#Desc',this).text());
		jQuery('#frmCauseEdit #Active').attr('checked', jQuery('#Active',this).is(':checked'));
		jQuery('#frmCauseEdit #Predefined').val(jQuery('#Predefined',this).text());
		jQuery('#frmCauseEdit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmCauseEdit #cmd').val('cmdCauseUpdate');
		jQuery('#btnCauseEditAdd').hide();
		if (parseInt(jQuery('#frmCauseEdit #Predefined').val()) > 0)
		{
			jQuery('#frmCauseEdit #Desc').attr('readonly', true);
		}
	});

	jQuery('#btnCauseEditAdd').unbind('click').click(function() {
		jQuery('#causeaddsect').show();
		jQuery(this).hide();
		jQuery('#frmCauseEdit #Id').val('');
		jQuery('#frmCauseEdit #Name').val('');
		jQuery('#frmCauseEdit #Desc').val('');
		jQuery('#frmCauseEdit #Active').attr('checked', true);
		jQuery('#frmCauseEdit #Predefined').val(0);
		jQuery('#frmCauseEdit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmCauseEdit #cmd').val('cmdCauseInsert');
	});

	jQuery('#btnCauseEditCancel').unbind('click').click(function() {
		jQuery('#causeaddsect').hide();
		jQuery('#btnCauseEditAdd').show();
	});
	jQuery('#btnCauseEditSend').unbind('click').click(function() {
		jQuery('#frmCauseEdit').trigger('submit');
	});

	jQuery('#frmCauseEdit').unbind('submit').submit(function()
	{
		var a = new Array('Name','Desc');
		var validForm = checkForm('frmCauseEdit', a, ''); 
		if (validForm == true)
		{
			var params = jQuery(this).serialize();
			jQuery.post(jQuery('#desinventarURL').val() + '/causes.php',
				params, 
				function(data)
				{
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#frmCauseEdit #Predefined').val();
					if (opt == "1")
					{
						updateList('lst_caupred', jQuery('#desinventarURL').val() + '/causes.php', 'r=' + reg + '&cmd=list&predef=1&t=' + new Date().getTime());
					}
					else
					{
						updateList('lst_cauuser', jQuery('#desinventarURL').val() + '/causes.php', 'r=' + reg + '&cmd=list&predef=0&t=' + new Date().getTime());
					}
					updateList('qcaulst', jQuery('#desinventarURL').val() + '/', 'r='+ reg +'&cmd=caulst&t=' + new Date().getTime());
					jQuery('#frmCauseEdit #Desc').removeAttr('readonly');
					jQuery('#causeaddsect').hide();
					jQuery('#btnCauseEditAdd').show();
				}
			);
		}
		return false;
	});
	
}
	