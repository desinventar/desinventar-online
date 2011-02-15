/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDBConfigEvents()
{
	// Hide first Column (EventId)
	jQuery('#tblEventListUser td:nth-child(1)').hide();
	
	jQuery('#tblEventListUser').delegate('tr','mouseover', function(event) {
		jQuery(this).addClass('highlight');
	}).delegate('tr', 'mouseout', function(event) {
		jQuery(this).removeClass('highlight');
	});
	
	jQuery('#tblEventListUser').delegate('tr','click', function(event) {
		//onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
		//onClick="setEveCau('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','0','event'); 
		//	uploadMsg(''); $('cmd').value='update';">
		jQuery('#eventaddsect').show();
		jQuery('#frmEventEdit #Id').val(jQuery('#Id',this).text());
		jQuery('#frmEventEdit #Name').val(jQuery('#Name',this).text());
		jQuery('#frmEventEdit #Desc').val(jQuery('#Desc',this).text());
		jQuery('#frmEventEdit #Active').attr('checked', jQuery('#Active',this).is(':checked'));
		jQuery('#frmEventEdit #PreDefined').val(0);
		jQuery('#frmEventEdit #RegionId').val(jQuery('#desinventarRegionId').val());
		jQuery('#frmEventEdit #cmd').val('update');
	});

	jQuery('#frmEventEdit').unbind('submit').submit(function()
	{
		var a = new Array('Name','Desc');
		var validForm = checkForm('frmEventEdit', a, ''); 
		if (validForm == true)
		{
			var params = jQuery(this).serialize();
			jQuery.post(
				'events.php',
				params, 
				function(data)
				{
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#aEventPreDefined').val();
					if (opt == "1")
					{
						updateList('lst_evepred', 'events.php', 'r=' + reg + '&cmd=list&predef=1&t=' + new Date().getTime());
					}
					else
					{
						updateList('lst_eveuser', 'events.php', 'r=' + reg + '&cmd=list&predef=0&t=' + new Date().getTime());
					}
					updateList('qevelst', 'index.php', 'r='+ reg +'&cmd=evelst&t=' + new Date().getTime());
					jQuery('#eventaddsect').hide();
				}
			);
		}
		return false;
	});
}
