function onReadyAdminUsers()
{
	jQuery('body').on('mouseover', '#divAdminUsers #tblUserList tr', function() {
		jQuery(this).addClass('highlight');
	}).on('mouseout', '#divAdminUsers #tblUserList tr', function() {
		jQuery(this).removeClass('highlight');
	}).on('click', '#divAdminUsers #tblUserList tr', function() {
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
	jQuery('body').on('click', '#divAdminUsers #btnUserAdd', function() {
		clearUserEditForm();
		jQuery("#txtUserId").removeAttr('readonly');
		jQuery("#txtUserEditCmd").val('insert');
		UserEditFormUpdateStatus('');
		jQuery("#divUserEdit").show();
	});

	// Cancel Edit, hide form
	jQuery('body').on('click', '#divAdminUsers #btnUserEditCancel', function() {
		jQuery("#divUserEdit").hide();
	});

	// Cancel Edit, hide form
	jQuery('body').on('click', '#divAdminUsers #btnUserEditSubmit', function() {
		jQuery('#divAdminUsers #frmUserEdit').trigger('submit');
	});

	// Submit - Finish edit, validate form and send data...
	jQuery('body').on('submit', '#divAdminUsers #frmUserEdit', function() {
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
						});
					}
					UserEditFormUpdateStatus(data.Status);
				},
				'json'
			);
		}
		return false;
	}); //submit
	doAdminUsersReset();
} //onReadyAdminUsers()

function doAdminUsersReset()
{
	// Start with Edit form hidden
	jQuery("#divUserEdit").hide();
	// Create table stripes
	jQuery("#tblUserList tr:odd").addClass("normal");
	jQuery("#tblUserList tr:even").addClass("under");
	jQuery("#txtUserId").removeAttr('readonly');
}

function onReadyUserAdmin() {
} //onReadyUserAdmin()