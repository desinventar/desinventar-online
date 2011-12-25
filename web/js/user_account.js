function onReadyUserAccount()
{
	jQuery('.clsUserAccountChangePasswdStatus').hide();
	doUserAccountCreate();
	jQuery('#btnUserAccountChangePasswdSubmit').click(function() {
		var bReturn = doUserAccountChangePasswdValidate();
		if (bReturn > 0)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd : 'cmdUserAccountChangePasswd',
					OldPasswd : hex_md5(jQuery('#fldUserAccountPasswd').val()),
					NewPasswd : hex_md5(jQuery('#fldUserAccountPasswd2').val())
				},
				function(data)
				{
					jQuery('.clsUserAccountChangePasswdStatus').hide();
					if (parseInt(data.Status) > 0)
					{
						jQuery('#msgUserAccountPasswdUpdated').show();
						setTimeout(function() {
							Ext.getCmp('wndUserAccount').hide();
						}, 2000);
					}					
					else
					{
						jQuery('#msgUserAccountInvalidPasswd').show();
					}
				},
				'json'				
			);
		}
	});
	jQuery('#btnUserAccountChangePasswdCancel').click(function() {
		Ext.getCmp('wndUserAccount').hide();
	});
}

function doUserAccountCreate()
{
	var w = new Ext.Window({id:'wndUserAccount',
		el:'divUserAccountWin', layout:'fit', x:200, y:100, width:400, height:170, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'divUserAccountContent', autoScroll: true })
	});
}

function doUserAccountShow()
{
	jQuery('#fldUserAccountPasswd').val('');
	jQuery('#fldUserAccountPasswd2').val('');
	jQuery('#fldUserAccountPasswd3').val('');
	Ext.getCmp('wndUserAccount').show();
}

function doUserAccountChangePasswdValidate()
{
	var bReturn = 1;
	jQuery('.clsUserAccountChangePasswdStatus').hide();
	if ( (jQuery('#fldUserAccountPasswd').val() == '') ||
	     (jQuery('#fldUserAccountPasswd2').val() == '') ||
	     (jQuery('#fldUserAccountPasswd3').val() == '') )
	{
		jQuery('#msgUserAccountEmptyFields').show();
		bReturn = -1;
	}
	if (bReturn > 0)
	{
		if (jQuery('#fldUserAccountPasswd2').val() != jQuery('#fldUserAccountPasswd3').val() )
		{
			jQuery('#msgUserAccountPasswdDoNotMatch').show();
			bReturn = -1;
		}	
	}
	setTimeout(function() {
		jQuery('.clsUserAccountChangePasswdStatus').hide();
	}, 2500);
	return bReturn;		
};

