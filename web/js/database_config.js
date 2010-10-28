function onReadyDatabaseConfig() {
	jQuery('#frmEventEdit').unbind('submit').submit(function() {
		var a = new Array('EventName','aEventDesc');
		var validForm = checkForm('frmEventEdit', a, ''); 
		if (validForm) {
			jQuery('#frmEventEdit #RegionId').val(jQuery('#desinventarRegionId').val());
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

