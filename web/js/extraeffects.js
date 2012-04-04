/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyExtraEffects() {
	jQuery('.msgEEFieldStatus').hide();

	jQuery('#btnEEFieldAdd').unbind('click').click(function() {
		setExtraEff('','','','','','','');
		jQuery('#EEFieldCmd').val('cmdEEFieldInsert');
		return false;
	});	

	jQuery('#btnEEFieldReset').unbind('click').click(function() {
		jQuery('#extraeffaddsect').hide();
		uploadMsg('');
		return false;
	});
	
	jQuery('#frmEEFieldEdit').unbind('submit').submit(function() {
		var params = jQuery(this).serialize();
		var bContinue = true;
		if (bContinue) {
			if (jQuery('#EEFieldLabel').val() == '') {
				jQuery('#EEFieldLabel').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			if (jQuery('#EEFieldDesc').val() == '') {
				jQuery('#EEFieldDesc').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			if (jQuery('#EEFieldType').val() == '') {
				jQuery('#EEFieldType').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			jQuery.post(jQuery('#desinventarURL').val() + '/extraeffects.php',
				params,
				function(data) {
					jQuery('.msgEEFieldStatus').hide();
					if (data.Status == 'OK') {
						jQuery('#msgEEFieldStatusOk').show();
						updateList('lst_eef', jQuery('#desinventarURL').val() + '/extraeffects.php', 'cmd=cmdEEFieldList+&RegionId='+ jQuery('#desinventarRegionId').val());
						jQuery('#extraeffaddsect').hide();
					} else {
						jQuery('#msgEEFieldStatusError').show();
					}
				},
				'json'
			);
		}
		return false;
	});
} //function

function setExtraEff (id, label, def, type, size, active, public) {
	// clear highlighted fields...
	jQuery.each(jQuery('.clsValidateField'), function() {
		jQuery(this).unhighlight();
	});

	//jQuery('#EEFieldDesc').unhighlight();
	// hide status msg
	jQuery('.msgEEFieldStatus').hide();

	// Show form
	jQuery('#extraeffaddsect').hide();
	
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
