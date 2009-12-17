
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
		jQuery.getJSON('user.php' + '?cmd=getUserInfo&UserId=' + UserId, function(data) {
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
		jQuery("#txtUserEditCmd").val('insert');
		jQuery("#divUserEdit").show();
	});

	// Cancel Edit, hide form
	jQuery("#btnUserEditCancel").click(function() {
		jQuery("#divUserEdit").hide();
	});

	// Submit - Finish edit, validate form and send data...
	jQuery("#btnUserEditSubmit").click(function() {
		var bReturn = validateUserEditForm();
		if (bReturn) {
			// Remove the readonly attribute, this way the data is send to processing
			jQuery("#txtUserId").removeAttr('readonly');
			jQuery.post('user.php', jQuery("#frmUserEdit").serializeObject(), function() {
				jQuery("#divUserList").load('user.php' + '?cmd=list', function() {
					onReadyUserAdmin();
				});
			});
		}
		return false;
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
}

function clearUserEditForm() {
	jQuery("#txtUserId").val('');
	jQuery("#selCountryIso").val('');
	jQuery("#txtUserEMail").val('');
	jQuery("#txtUserFullName").val('');
	jQuery("#txtUserCity").val('');
	jQuery("#chkUserActive").attr('checked', '');
};

