function onReadyAdminUsers()
{
	doAdminUsersSetup();
} //onReadyAdminUsers()


function doAdminUsersSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndAdminUsers', 
		el: 'divAdminUsersWin', layout:'fit', 
		width:600, height:300, modal:false, constrainHeader: true,
		closeAction:'hide', plain: false, animCollapse: true,
		items: new Ext.Panel({
			contentEl: 'divAdminUsersContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});
} //doAdminUsersSetup()

function doAdminUsersShow()
{
	Ext.getCmp('wndAdminUsers').show();
} //doAdminUsersShow()
