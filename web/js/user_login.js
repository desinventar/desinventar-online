/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyUserLogin()
{
	// hide all status messages on start
	doUserLoginUpdateMsg('');

	jQuery('body').on('cmdUserLoginShow', function() {
		doUserLoginShow();
	});

	jQuery('body').on('cmdUserLogout', function() {
		doUserLogout();
	});

	jQuery('body').on('UserLoggedIn',function() {
		jQuery('body').trigger('cmdWindowReload');
	});

	jQuery('body').on('UserLoggedOut',function() {
		jQuery('body').trigger('cmdWindowReload');
	});
	
	
	// submit form validation and process..
	jQuery("#btnUserLoginSend").click(function() {
		jQuery('#frmUserLogin').trigger('submit');
		return false;
	});

	jQuery('#fldUserPasswd').keypress(function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13)
		{
			jQuery('#btnUserLoginSend').trigger('click');
		}
	});

	jQuery('#frmUserLogin').submit(function() {
		var UserId     = jQuery('#fldUserId').val();
		var UserPasswd = jQuery("#fldUserPasswd").val();

		doUserLoginUpdateMsg('');

		if (UserId == '' || UserPasswd == '')
		{
			doUserLoginUpdateMsg('#msgEmptyFields');
		}
		else
		{
			jQuery.post(jQuery('#desinventarURL').val() + '/',
				{
					'cmd'        : 'cmdUserLogin',
					'RegionId'   : jQuery('#desinventarRegionId').val(),
					'UserId'     : UserId,
					'UserPasswd' : hex_md5(UserPasswd)
			    },
				function(data)
				{
					if (parseInt(data.Status) > 0)
					{
						doUserLoginUpdateMsg("#msgUserLoggedIn");
						// After login, clear passwd field
						jQuery("#fldUserPasswd").val('');

						// Update UserInfo Fields...
						doUserUpdateInfo(data.User);

						Ext.getCmp('wndUserLogin').hide();
						// Trigger Event and Update User Menu etc.
						jQuery('body').trigger('cmdMainWindowUpdate');
					}
					else
					{
						doUserLoginUpdateMsg("#msgInvalidPasswd");
					}
				},
				'json'
			);
		}
		return false;
	});
} //onReadyUserLogin()

function doUserUpdateInfo(User)
{
	jQuery('#desinventarUserId').val(User.Id);
	jQuery('#desinventarUserFullName').val(User.FullName);
	jQuery('#desinventarUserRole').val(User.Role);
	jQuery('#desinventarUserRoleValue').val(User.RoleValue);
}

function doUserLogout()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			'cmd'        : 'cmdUserLogout',
			'RegionId'   : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doUserLoginUpdateMsg("#msgUserLoggedOut");
				// After login, clear passwd field
				jQuery('#fldUserId').val('');
				jQuery('#fldUserPasswd').val('');
				doUserUpdateInfo(data.User);
				// Trigger Event, used to update menu or reload page...
				jQuery('body').trigger('cmdMainWindowUpdate');
			}
			else
			{
				doUserLoginUpdateMsg("#msgInvalidLogout");
			}
		},
		'json'
	);
} //doUserLogout()

function doUserLoginUpdateMsg(msgId)
{
	// Hide all status Msgs (class="status")
	jQuery(".status").hide();
	if (msgId != '')
	{
		// Show specified message(s)
		jQuery(msgId).show();
	}
	return(true);
} //doUserLoginUpdateMsg()

function doUserLoginShow()
{
	doUserLoginUpdateMsg();
	Ext.getCmp('wndUserLogin').show();
} //doUserLoginShow()
