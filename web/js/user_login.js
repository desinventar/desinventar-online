function onReadyUserLogin() {
	// hide all status messages on start
	updateUserLoginMsg('');
	
	// submit form validation and process..
	jQuery("#frmUserLogin").submit(function() {
		var UserId     = jQuery('#txtUserId').val();
		var UserPasswd = jQuery("#txtUserPasswd").val();
		if (UserId == '' || UserPasswd == '') {
			updateUserLoginMsg('#msgEmptyFields');
		} else {
			diURL = jQuery('#desinventarURL').val();
			if (diURL == undefined) {
				diURL = '';
			}
			jQuery.post(diURL + 'user.php',
				{'cmd'        : 'login',
			     'UserId'     : UserId,
			     'UserPasswd' : hex_md5(UserPasswd)
			    },
			    function(data) {
					if (data == 'OK') {
						updateUserLoginMsg("#msgUserLoggedIn");
						// After login, clear passwd field
						jQuery("#txtUserPasswd").val('');
						window.location.reload(false);
					} else {
						updateUserLoginMsg("#msgInvalidPasswd");
					}
				},
				'json'
			);
		}
		return(false);
	});
};

function updateUserLoginMsg(msgId) {
	// Hide all status Msgs (class="status")
	jQuery(".status").hide();
	if (msgId != '') {
		jQuery("#divUserLoginMsg").show();
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
};
