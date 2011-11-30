/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseCreate()
{
	doDatabaseCreateSetup();
}

function doDatabaseCreateSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndDatabaseCreate', 
		el: 'divDatabaseCreateWin', layout:'fit', 
		width:400, height:300, modal:false,
		closeAction:'hide', plain: false, animCollapse: true,
		items: new Ext.Panel({
			contentEl: 'divDatabaseCreateContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});
} //doDatabaseCreateSetup()
