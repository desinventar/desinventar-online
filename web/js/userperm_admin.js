/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyUserPermAdmin()
{
	doUserPermAdminSetup();
} //onReadyUserPermAdmin()

function doUserPermAdminSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndUserPermAdmin', 
		el: 'divUserPermAdminWin', layout:'fit', 
		width:400, height:250, modal:false,
		closeAction:'hide', plain: false, animCollapse: true,
		items: new Ext.Panel({
			contentEl: 'divUserPermAdminContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});

	// Cancel Button - Hide Window and do nothing
	jQuery('#btnUserPermAdminCancel').click(function() {
		Ext.getCmp('wndUserPermAdmin').hide();
		return false;
	});

	// Send Button - Validate data and send command to backend
	jQuery('#btnUserPermAdminSend').click(function() {
		iReturn = doUserPermAdminValidate();
		if (iReturn > 0)
		{
		}
		else
		{
			jQuery('#txtUserPermAdminFormError').show();
			setTimeout(function() {
				jQuery('.clsUserPermAdminStatus').hide();
			}, 2500);
		}
		return false;
	});

	// Hide Send button until the combobox has been populated
	jQuery('#btnUserPermAdminSend').hide();
} //doUserPermAdminSetup()

function doUserPermAdminShow()
{
	jQuery('.clsUserPermAdminStatus').hide();

	// If first time, populate data lists
	var iCount = jQuery('#fldUserPermAdmin_UserId option').length;
	if (iCount < 2)
	{
		doUserPermAdminPopulateLists();
	} 
	Ext.getCmp('wndUserPermAdmin').show();
	jQuery('#fldUserPermAdmin_UserId').focus();
} //doUserPermAdminShow()

function doUserPermAdminValidate()
{
	var iReturn = 1;
	if ( (iReturn > 0) && (jQuery('#fldUserPermAdmin_UserId').val() == '') )
	{
		iReturn = -1;
	}
	return iReturn;
}

function doUserPermAdminPopulateLists()
{
	// async UserList field
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGetUserPermList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery.each(data.UserList, function(key, value) {
					jQuery('#fldUserPermAdmin_UserId').append(
						jQuery('<option>', { value : key }).text(value)
					);
				});
				jQuery('#fldUserPermAdmin_UserId').val(data.UserAdmin.UserId);
				var UserAdmin = data.UserAdmin.UserFullName;
				if (data.UserAdmin.UserEMail != '')
				{	
					UserAdmin = UserAdmin + '<' + data.UserAdmin.UserEMail + '>';
				}
				jQuery('#txtUserPermAdminCurrent').text(UserAdmin);
				jQuery('#btnUserPermAdminSend').show();
			}
		},
		'json'
	);
} //doUserPermAdminPopulateLists()

