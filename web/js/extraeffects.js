function onReadyExtraEffects() {
	jQuery('.msgEEFieldStatus').hide();
	
	jQuery('#eeffrm').unbind('submit').submit(function() {
		//onSubmit="javascript: var a=new Array('EEFieldLabel','EEFieldDesc', 'EEFieldType'); return(checkForm('eeffrm',a, '{-#errmsgfrmeef#-}'));"	
		//action="javascript: var s=$('eeffrm').serialize(); sendData('{-$reg-}', 'extraeffects.php', s, '');"
		var params = jQuery(this).serialize();
		jQuery.post('extraeffects.php',
			params,
			function(data) {
				jQuery('.msgEEFieldStatus').hide();
				if (data.Status == 'OK') {
					jQuery('#msgEEFieldStatusOk').show();
					updateList('lst_eef', 'extraeffects.php', 'cmd=cmdEEFieldList+&RegionId='+ jQuery('#desinventarRegionId').val());
				} else {
					jQuery('#msgEEFieldStatusError').show();
				}
			},
			'json'
		);
		return false;
	});
} //function

function setExtraEff (id, label, def, type, size, active, public) {
	jQuery('.msgEEFieldStatus').hide();
	mod = "extraeff";
	$(mod + 'addsect').style.display = 'block';
	$('EEFieldId').value = id;
	$('EEFieldLabel').value = label;
	$('EEFieldDesc').value = def;
	$('EEFieldType').value = type;
	$('EEFieldSize').value = size;
	if (active == "1")
		$('EEFieldActive').checked = true;
	else
		$('EEFieldActive').checked = false;
	if (public == "1")
		$('EEFieldPublic').checked = true;
	else
		$('EEFieldPublic').checked = false;
} //function
