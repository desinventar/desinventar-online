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
};

function updateUserChangePasswdMsg(msgId) {
	// Hide all status Msgs (class="status")
	jQuery(".status").hide();
	if (msgId != '') {
		jQuery("#divUserChangePasswdMsg").show();
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
};

function onReadyUserAdmin() {
	// Start with Edit form hidden
	jQuery("#divUserEdit").hide();
	// Create table stripes
	jQuery("#tblUserList tr:odd").addClass("normal");
	jQuery("#tblUserList tr:even").addClass("under");
	// Change background color of row under mouse
	jQuery("#tblUserList tr").mouseover(function() {
		jQuery(this).addClass('highlight');
	});
	jQuery("#tblUserList tr").mouseout(function() {
		jQuery(this).removeClass('highlight');
	});
	jQuery("#txtUserId").removeAttr('readonly');
	
	// When selecting a row, start editing data...
	jQuery("#tblUserList tr").click(function() {
		var UserId = jQuery(this).children("td:first").html();
		jQuery.getJSON(jQuery('#desinventarURL').val() + '/user.php' + '?cmd=getUserInfo&UserId=' + UserId, function(data) {
			jQuery("#txtUserId").attr('readonly','true');
			jQuery("#txtUserId").val(data.UserId);
			jQuery("#selCountryIso").val(data.CountryIso);
			jQuery("#txtUserEMail").val(data.UserEMail);
			jQuery("#txtUserFullName").val(data.UserFullName);
			jQuery("#txtUserCity").val(data.UserCity);
			jQuery("#chkUserActive").attr('checked', data.UserActive);
			jQuery("#txtUserEditCmd").val('update');
		});
		UserEditFormUpdateStatus('');
		jQuery("#divUserEdit").show();
	});

	// Add new User...
	jQuery("#btnUserAdd").click(function() {
		clearUserEditForm();
		jQuery("#txtUserId").removeAttr('readonly');
		jQuery("#txtUserEditCmd").val('insert');
		UserEditFormUpdateStatus('');
		jQuery("#divUserEdit").show();
	});

	// Cancel Edit, hide form
	jQuery("#btnUserEditCancel").unbind('click').click(function() {
		jQuery("#divUserEdit").hide();
	});

	// Submit - Finish edit, validate form and send data...
	jQuery('#frmUserEdit').unbind('submit').submit(function() {
		UserEditFormUpdateStatus('');
		// validate Form
		var bReturn = validateUserEditForm();
		if (bReturn > 0) {
			// Remove the readonly attribute, this way the data is sent to processing
			jQuery("#txtUserId").removeAttr('readonly');
			// Create an object with the information to send
			var user = jQuery("#frmUserEdit").serializeObject();
			// Checkboxes not selected are not passed by default to server, so we need
			// to checkout and set a value here.
			if (! jQuery("#chkUserActive").attr('checked')) {
				user['User[UserActive]'] = 'off';
			}
			// Send AJAX request to update information
			jQuery.post(jQuery('#desinventarURL').val() + '/user.php', 
				user, 
				function(data) {
					if (data.Status > 0) {
						// Reload user list on success
						jQuery("#divUserList").load(jQuery('#desinventarURL').val() + '/user.php' + '?cmd=list', function(data) {
							onReadyUserAdmin();
						});
					}
					UserEditFormUpdateStatus(data.Status);
				},
				'json'
			);
		}
		return false;
	}); //submit
};

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
};

function clearUserEditForm() {
	jQuery("#txtUserId").val('');
	jQuery("#selCountryIso").val('');
	jQuery("#txtUserEMail").val('');
	jQuery("#txtUserFullName").val('');
	jQuery("#txtUserCity").val('');
	jQuery("#chkUserActive").attr('checked', '');
};

