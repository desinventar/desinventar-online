function onReadyUserLogin() {
	// hide all status messages on start
	updateUserLoginMsg('');
	
	// submit form validation and process..
	jQuery("#frmUserLogin").submit(function() {
		var UserId     = jQuery('#fldUserId').val();
		var UserPasswd = jQuery("#fldUserPasswd").val();
		if (UserId == '' || UserPasswd == '') {
			updateUserLoginMsg('#msgEmptyFields');
		} else {
			var desinventarURL = jQuery('#desinventarURL').val();
			if (desinventarURL == undefined) {
				desinventarURL = '';
			}
			jQuery.post(desinventarURL + 'user.php',
				{'cmd'        : 'login',
			     'UserId'     : UserId,
			     'UserPasswd' : hex_md5(UserPasswd)
			    },
			    function(data) {
					if (data.Status == 'OK') {
						updateUserLoginMsg("#msgUserLoggedIn");
						// After login, clear passwd field
						jQuery("#fldUserPasswd").val('');

						// Update UserInfo Fields...
						jQuery('#desinventarUserId').val(data.UserId);
						jQuery('#desinventarUserFullName').val(data.UserFullName);

						// Trigger Event and Update User Menu etc.
						jQuery('body').trigger('UserLoggedIn');
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

function doUserLogout() {
	var Answer = 0;
	desinventarURL = jQuery('#desinventarURL').val();
	if (desinventarURL == undefined) {
		desinventarURL = '';
	}
	jQuery.post(desinventarURL + 'user.php',
		{'cmd'        : 'logout'
		},
		function(data) {
			if (data.Status == 'OK') {
				Answer = 1;
				updateUserLoginMsg("#msgUserLoggedOut");
				// After login, clear passwd field
				jQuery('#fldUserId').val('');
				jQuery('#fldUserPasswd').val('');
				
				// Update UserInfo Fields...
				jQuery('#desinventarUserId').val('');
				jQuery('#desinventarUserFullName').val('');
				
				// Trigger Event, used to update menu or reload page...
				jQuery('body').trigger('UserLoggedOut');
			} else {
				updateUserLoginMsg("#msgInvalidLogout");
				Answer = 0;
			}
		},
		'json'
	);
	return Answer;
}
function updateUserLoginMsg(msgId) {
	// Hide all status Msgs (class="status")
	jQuery(".status").hide();
	if (msgId != '') {
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
};

