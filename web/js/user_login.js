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
			desinventarURL = jQuery('#desinventarURL').val();
			if (desinventarURL == undefined) {
				desinventarURL = '';
			}
			jQuery.post(desinventarURL + 'user.php',
				{'cmd'        : 'login',
			     'UserId'     : UserId,
			     'UserPasswd' : hex_md5(UserPasswd)
			    },
			    function(data) {
					if (data == 'OK') {
						updateUserLoginMsg("#msgUserLoggedIn");
						// After login, clear passwd field
						jQuery("#txtUserPasswd").val('');

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
			if (data == 'OK') {
				Answer = 1;
				updateUserLoginMsg("#msgUserLoggedOut");
				// After login, clear passwd field
				jQuery('#txtUserId').val('');
				jQuery('#txtUserPasswd').val('');
				
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
		jQuery("#divUserLoginMsg").show();
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
};

