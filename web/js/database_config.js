/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseConfig() {
	jQuery('#frmCauseEdit').unbind('submit').submit(function() {
		//onSubmit="javascript: var a=new Array('aCauseName','aCauseDesc'); return(checkForm('frmCauseEdit',a, '{-#errmsgfrm#-}'));"		return false;
		//action="javascript:var s=$('frmCauseEdit').serialize(); sendData('{-$reg-}', 'causes.php', s, $('aCausePreDefined').value);"
		var a = new Array('aCauseName','aCauseDesc');
		var validForm = checkForm('frmCauseEdit', a, '');
		if (validForm == true) {
			jQuery(this).find('#RegionId').val(jQuery('#desinventarRegionId').val());
			var params = jQuery(this).serialize();
			jQuery.post(
				'causes.php',
				params,
				function(data) {
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#aCausePreDefined').val();
					if (opt == "1") {
						updateList('lst_caupred', 'causes.php', 'r='+ reg +'&cmd=list&predef=1');
					} else {
						updateList('lst_cauuser', 'causes.php', 'r='+ reg +'&cmd=list&predef=0');
					}
					updateList('qcaulst', 'index.php', 'r='+ reg +'&cmd=caulst');
					jQuery('#causeaddsect').hide();
				}
			);
		}
		return false;
	});

	jQuery('#frmEventEdit').unbind('submit').submit(function() {
		var a = new Array('EventName','aEventDesc');
		var validForm = checkForm('frmEventEdit', a, ''); 
		if (validForm == true) {
			jQuery(this).find('#RegionId').val(jQuery('#desinventarRegionId').val());
			var params = jQuery(this).serialize();
			jQuery.post(
				'events.php',
				params, 
				function(data) {
					var reg = jQuery('#desinventarRegionId').val();
					var opt = jQuery('#aEventPreDefined').val();
					if (opt == "1") {
						updateList('lst_evepred', 'events.php', 'r='+ reg +'&cmd=list&predef=1');
					} else {
						updateList('lst_eveuser', 'events.php', 'r='+ reg +'&cmd=list&predef=0');
					}
					updateList('qevelst', 'index.php', 'r='+ reg +'&cmd=evelst');
					jQuery('#eventaddsect').hide();
				}
			);
		}
		return false;
	});
} //function

