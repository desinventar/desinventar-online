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
			jQuery.post("user.php?cmd=updatepasswd", 
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
		// 2010-01-26 (jhcaiced) This reference to dblw needs to be fixed !!! 
		dblw.hide();
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
		jQuery.getJSON('user.php' + '?cmd=getUserInfo&UserId=' + UserId + '&t=' + new Date().getTime(), function(data) {
			jQuery("#txtUserId").attr('readonly','true');
			jQuery("#txtUserId").val(data.UserId);
			jQuery("#selCountryIso").val(data.CountryIso);
			jQuery("#txtUserEMail").val(data.UserEMail);
			jQuery("#txtUserFullName").val(data.UserFullName);
			jQuery("#txtUserCity").val(data.UserCity);
			jQuery("#chkUserActive").attr('checked', data.UserActive);
			jQuery("#txtUserEditCmd").val('update');
		});
		jQuery("#divUserEdit").show();
	});

	// Add new User...
	jQuery("#btnUserAdd").click(function() {
		clearUserEditForm();
		jQuery("#txtUserId").removeAttr('readonly');
		jQuery("#txtUserEditCmd").val('insert');
		jQuery("#divUserEdit").show();
	});

	// Cancel Edit, hide form
	jQuery("#btnUserEditCancel").click(function() {
		jQuery("#divUserEdit").hide();
	});

	// Submit - Finish edit, validate form and send data...
	jQuery("#btnUserEditSubmit").click(function() {
		// validate Form
		var bReturn = validateUserEditForm();
		if (bReturn) {
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
			jQuery.post('user.php', user, function(data) {
				eval('var myObj = ' + data);
				jQuery("#lblUserStatusMsg").text(myObj.Message);
				// Reload user list on success
				jQuery("#divUserList").load('user.php' + '?cmd=list', function(data) {
					onReadyUserAdmin();
				});
			});
		}
		return false;
	});
	
	jQuery("#txtUserId").keyup(function() {
		if (this.value != this.lastValue) {
			var t = this;
			if (this.timer) { clearTimeout(this.timer);
			}
			this.timer = setTimeout(function() {
				jQuery.ajax({
					url      : 'user.php',
					data     : 'cmd=chklogin&UserId=' + t.value,
					type     : 'post',
					success  : function(data) {
						jQuery("#lblUserStatusMsg").text(data);
					}
				});
			}, 400);
			this.lastValue = this.value;
		}
	});
};

function validateUserEditForm() {
	var bReturn = true;
	jQuery(".error").hide();
	var UserId = jQuery("#txtUserId").val();
	if (UserId == '') {
		jQuery("#txtUserId").after('<span class="error">Cannot be empty</span>');
		//bReturn = false;
	}
	/*
		action="javascript:var s=$('userpafrm').serialize(); sendData('','user.php', s, '');"
		onSubmit="javascript:var a=new Array('UserId', 'UserEMail', 'UserFullName'); return(checkForm(a, '{-#errmsgfrmregist#-}'));"> 
	*/
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

