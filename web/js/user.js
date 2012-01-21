/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyUserChangePasswd(windowId) {
	// hide all status messages on start
	updateUserChangePasswdMsg('');
	
	// submit form validation and process..
	jQuery("#frmUserChangePasswd").submit(function() {
		var UserPasswd = jQuery("#txtUserPasswd").val();
		var UserPasswd2 = jQuery("#txtUserPasswd2").val();
		var UserPasswd3 = jQuery("#txtUserPasswd3").val();
		if (UserPasswd == '' || UserPasswd2 == '' || UserPasswd3 == '') {
			updateUserChangePasswdMsg('#msgEmptyFields');
		} else if (UserPasswd2 != UserPasswd3) {
			updateUserChangePasswdMsg('#msgPasswdDoNotMatch');
		} else {
			jQuery.post(jQuery('#desinventarURL').val() + '/user.php?cmd=updatepasswd', 
			    {'UserPasswd'  : hex_md5(UserPasswd),
			     'UserPasswd2' : hex_md5(UserPasswd2)
			    },
			    function(data) {
					if (data == 'OK') {
						updateUserChangePasswdMsg("#msgPasswdUpdated");
						// After update, clear first field
						jQuery("#txtUserPasswd").val('');
					} else {
						updateUserChangePasswdMsg("#msgInvalidPasswd");
					}
				}
			);
		}
		return(false);
	});
	jQuery("#btnUserEditCancel").click(function() {
		updateUserChangePasswdMsg('');
		if (windowId != '') {
			jQuery(windowId).hide();
		}
		Ext.getCmp('wndDatabaseList').hide();
		return(false);
	});
}

function updateUserChangePasswdMsg(msgId) {
	// Hide all status Msgs (class="status")
	jQuery(".status").hide();
	if (msgId != '') {
		jQuery("#divUserChangePasswdMsg").show();
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
}

function UserEditFormUpdateStatus(value) {
	jQuery('.UserEditFormStatus').hide();
	MsgId = '';
	switch(value) {
		case  1:
			MsgId = 'UserEditFormStatusOk';
		break;
		case -1:
			MsgId = 'UserEditFormStatusError';
		break;
		case -100:
			MsgId = 'UserEditFormStatusDuplicateId';
		break;
		case -101:
			MsgId = 'UserEditFormStatusEmptyId';
		break;
	}
	if (MsgId != '') {
		jQuery('#' + MsgId).show();
	}
}

function validateUserEditForm() {
	var bReturn = 1;
	jQuery('#txtUserId').unhighlight();
	if (jQuery('#txtUserId').val() == '') {
		bReturn = -101;
		jQuery("#txtUserId").highlight();
	}
	jQuery('#txtUserFullName').unhighlight();
	if (jQuery('#txtUserFullName').val() == '') {
		bReturn = -101;
		jQuery("#txtUserFullName").highlight();
	}
	jQuery('#txtUserEMail').unhighlight();
	if (jQuery('#txtUserEMail').val() == '') {
		bReturn = -101;
		jQuery("#txtUserEMail").highlight();
	}
	UserEditFormUpdateStatus(bReturn);
	return bReturn;		
}

function clearUserEditForm() {
	jQuery("#txtUserId").val('');
	jQuery("#selCountryIso").val('');
	jQuery("#txtUserEMail").val('');
	jQuery("#txtUserFullName").val('');
	jQuery("#txtUserCity").val('');
	jQuery("#chkUserActive").attr('checked', '');
}
