/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseConfig()
{
	jQuery('#frmCauseEdit').unbind('submit').submit(function() {
		var a = new Array('aCauseName','aCauseDesc');
		var validForm = checkForm('frmCauseEdit', a, '');
		if (validForm == true)
		{
			jQuery(this).find('#RegionId').val(jQuery('#desinventarRegionId').val());
			var params = jQuery(this).serialize();
			jQuery.post(
				'causes.php',
				params,
				function(data)
				{
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#aCausePredefined').val();
					if (opt == "1")
					{
						updateList('lst_caupred', 'causes.php', 'r='+ reg +'&cmd=list&predef=1&t=' + new Date().getTime());
					}
					else
					{
						updateList('lst_cauuser', 'causes.php', 'r='+ reg +'&cmd=list&predef=0&t=' + new Date().getTime());
					}
					updateList('qcaulst', 'index.php', 'r='+ reg +'&cmd=caulst&t=' + new Date().getTime());
					jQuery('#causeaddsect').hide();
				}
			);
		}
		return false;
	});
} //function
