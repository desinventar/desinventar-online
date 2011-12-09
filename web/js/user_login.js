/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyUserLogin() {
	// hide all status messages on start
	doUserLoginUpdateMsg('');
	
	// submit form validation and process..
	jQuery("#btnUserLoginSend").click(function() {
		jQuery('#frmUserLogin').trigger('submit');
		return false;
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
			jQuery.post(jQuery('#desinventarURL').val() + '/user.php',
				{
					'cmd'        : 'login',
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
						jQuery('#desinventarUserId').val(data.UserId);
						jQuery('#desinventarUserFullName').val(data.UserFullName);

						// Trigger Event and Update User Menu etc.
						jQuery('body').trigger('UserLoggedIn');
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

function doUserLogout()
{
	var Answer = 0;
	jQuery.post(jQuery('#desinventarURL').val() + '/user.php',
		{
			'cmd'        : 'logout'
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				Answer = 1;
				doUserLoginUpdateMsg("#msgUserLoggedOut");
				// After login, clear passwd field
				jQuery('#fldUserId').val('');
				jQuery('#fldUserPasswd').val('');
				
				// Update UserInfo Fields...
				jQuery('#desinventarUserId').val('');
				jQuery('#desinventarUserFullName').val('');
				
				// Trigger Event, used to update menu or reload page...
				jQuery('body').trigger('UserLoggedOut');
			}
			else
			{
				doUserLoginUpdateMsg("#msgInvalidLogout");
				Answer = 0;
			}
		},
		'json'
	);
	return Answer;
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
