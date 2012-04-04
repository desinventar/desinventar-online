/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyUserAccount()
{
	jQuery('form.UserChangePasswd .status').hide();

	jQuery('form.UserChangePasswd .btnSubmit').click(function() {
		jQuery('form.UserChangePasswd').trigger('submit');
		return false;
	});
	
	jQuery('form.UserChangePasswd .btnCancel').click(function() {
		jQuery('body').trigger('cmdUserAccountHide');
		return false;
	});

	jQuery('form.UserChangePasswd').submit(function() {
		var form = jQuery(this);
		var UserPasswd = jQuery('.UserPasswd', form).val();
		var UserPasswd2 = jQuery('.UserPasswd2', form).val();
		var UserPasswd3 = jQuery('.UserPasswd3', form).val();
		var bContinue = true;
		jQuery('.status', form).hide();
		if (UserPasswd == '' || UserPasswd2 == '' || UserPasswd3 == '')
		{
			jQuery('#msgEmptyFields', form).show();
			bContinue = false;
		}
		
		if (bContinue && (UserPasswd2 != UserPasswd3) )
		{
			jQuery('#msgPasswdDoNotMatch', form).show();
			bContinue = false;
		}
		
		if (bContinue)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd         : 'cmdUserPasswdUpdate',
					UserId      : jQuery('#desinventarUserId').val(),
					UserPasswd  : hex_md5(UserPasswd),
					UserPasswd2 : hex_md5(UserPasswd2)
				},
			    function(data)
			    {
			    	jQuery('.status', form).hide();
			    	if (parseInt(data.Status) > 0)
			    	{
						doUserAccountReset();
						jQuery('#msgPasswdUpdated', form).show();
					}
					else
					{
						jQuery('#msgInvalidPasswd', form).show();
					}
					setTimeout(function() {
						jQuery('.status',form).hide();
						if (parseInt(data.Status) > 0)
						{
							jQuery('body').trigger('cmdUserAccountHide');
						}
					}, 2500);
				},
				'json'
			);
		}
		else
		{
			setTimeout(function() {
				jQuery('.status',form).hide();
			}, 2500);
		}
		return false;
	});

	jQuery('body').on('cmdUserAccountShow', function() {
		doUserAccountReset();
		var w = Ext.getCmp('wndUserAccount');
		if (w != undefined)
		{
			w.show();
		}
	});

	jQuery('body').on('cmdUserAccountHide', function() {
		var w = Ext.getCmp('wndUserAccount');
		if (w != undefined)
		{
			w.hide();
		}
	});

	doUserAccountReset();
	doUserAccountCreate();
}

function doUserAccountReset()
{
	var form = jQuery('form.UserChangePasswd');
	jQuery('.status', form).hide();
	jQuery('.UserPasswd', form).val('');
	jQuery('.UserPasswd2', form).val('');
	jQuery('.UserPasswd3', form).val('');
}

function doUserAccountCreate()
{
	jQuery('#divUserAccountWindow').each(function() {
		var w = new Ext.Window({id:'wndUserAccount',
			el:'divUserAccountWindow', layout:'fit', x:200, y:100, width:400, height:200, 
			closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
			items: new Ext.Panel({ contentEl: 'divUserAccountContent', autoScroll: true })
		});
	});
}
