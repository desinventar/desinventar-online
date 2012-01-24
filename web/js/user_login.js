/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
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
						doUserLoginUpdateMsg('#msgUserLoggedIn');
						jQuery('#fldUserId').val('');
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
	//Initialization code
	doUserLoginCreate();
} //onReadyUserLogin()

function doUserLoginCreate()
{
	// User Login Window
	try
	{
		var w = new Ext.Window({id:'wndUserLogin',
			el:'divUserLoginWindow', layout:'fit', x:300, y:100, width:500, height:300, 
			closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
			items: new Ext.Panel({ contentEl: 'divUserLoginContent', autoScroll: true })
		});
	}
	catch (e)
	{
	}
} //doUserLoginCreate()

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
	jQuery('div.UserLogin .status').hide();
	if (msgId != '')
	{
		// Show specified message(s)
		jQuery('div.UserLogin ' + msgId).show();
	}
	return(true);
} //doUserLoginUpdateMsg()

function doUserLoginShow()
{
	doUserLoginUpdateMsg();
	jQuery('#fldUserId').val('');
	jQuery('#fldUserPasswd').val('');
	Ext.getCmp('wndUserLogin').show();
} //doUserLoginShow()
