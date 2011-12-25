/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAboutDialog()
{
	doAboutDialogCreate();
} //onReadyAboutDialog()

function doAboutDialogCreate()
{
	var w = new Ext.Window({id:'wndAboutDialog',
		el:'divAboutDialogWin', layout:'fit', x:350, y:200, width:300, height:150, 
		closeAction:'hide', plain: true, animCollapse: false,
		items: new Ext.Panel({ contentEl: 'divAboutDialogContent', autoScroll: true })
	});
} //doAboutDialogCreate()