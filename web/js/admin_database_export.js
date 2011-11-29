/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseExport()
{
	//jQuery('.clsAdminDatabaseExport').hide();	
	//jQuery('#txtAdminDatabaseExportRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	doAdminDatabaseExportCreate();
} //onReadyAdminDatabaseExport

function doAdminDatabaseExportCreate()
{
	// Database Export
	var w = new Ext.Window({id:'wndDatabaseExport', 
		el: 'divDatabaseExportWin', layout:'fit', 
		width:400, height:200, modal:false,
		closeAction:'hide', plain: false, animCollapse: false,
		items: new Ext.Panel({
			contentEl: 'divDatabaseExportContent',
			autoScroll: true
		}),
		buttons: [
			/*{
				id: 'btnAdminDatabaseExportSend',
				text: jQuery('#msgAdminDatabaseExportButtonSend').text(),
				handler: function()
				{
					doAdminDatabaseExportAction();
				} //handler
			},*/
			{
				text: jQuery('#msgAdminDatabaseExportButtonClose').text(),
				handler: function()
				{
					jQuery('#fldAdminDatabaseExportSave').val(0);
					jQuery('#imgAdminDatabaseExportWait').attr('src','');
					Ext.getCmp('wndDatabaseExport').hide();
				} //handler
			}
		] //button
	});
	jQuery('#fldAdminDatabaseExportSave').val(1);
} // doAdminDatabaseExportCreate()

function doAdminDatabaseExportAction()
{
	jQuery('.clsAdminDatabaseExport').hide();
	Ext.getCmp('wndDatabaseExport').show();
	jQuery('.clsAdminDatabaseExport').hide();
	jQuery('#divAdminDatabaseExportProgress').show();
	
	jQuery('#imgAdminDatabaseExportWait').attr('src', jQuery('#fldAdminDatabaseExportImage').val());
	jQuery('#imgAdminDatabaseExportWait').show();
	
	jQuery('#fldAdminDatabaseExportSave').val(1);
	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			cmd      : 'cmdAdminDatabaseExport',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			jQuery('.clsAdminDatabaseExport').hide();
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divAdminDatabaseExportResults').show();
				jQuery('#imgAdminDatabaseExportWait').attr('src','').hide();
				// Hide Ext.Window
				Ext.getCmp('wndDatabaseExport').hide();
				if (parseInt(jQuery('#fldAdminDatabaseExportSave').val()) > 0)
				{
					// Open the backup file for download
					window.location = data.URL;
				}
			}
			else
			{
				jQuery('#divAdminDatabaseExportError').show();
			}
		},
		'json'
	);
} // doAdminDatabaseExportAction
