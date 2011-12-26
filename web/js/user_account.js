function onReadyUserAccount()
{
	doUserAccountCreate();
}

function doUserAccountCreate()
{
	var w = new Ext.Window({id:'wndUserAccount',
		el:'divUserAccountWin', layout:'fit', x:200, y:100, width:400, height:200, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'divUserAccountContent', autoScroll: true })
	});
}

function doUserAccountShow()
{
	Ext.getCmp('wndUserAccount').show();
}
