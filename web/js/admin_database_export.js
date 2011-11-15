/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseExport()
{
	jQuery('clsAdminDatabaseExport').hide();
	jQuery('#txtAdminDatabaseExportRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	jQuery('#divAdminDatabaseExportStart').click(function() {
		jQuery('.clsAdminDatabaseExport').hide();
		jQuery('#divAdminDatabaseExportProgress').show();
		jQuery.post(jQuery('#desinventarURL').val(),
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
					// Hide Ext.Window
					Ext.getCmp('wndDatabaseExport').hide();

					// Open the backup file for download
					window.location = data.URL;
				}
				else
				{
					jQuery('#divAdminDatabaseExportError').show();
				}
			},
			'json'
		);
	});
} //onReadyAdminDatabaseExport

function doAdminDatabaseExportAction()
{
	jQuery('.clsAdminDatabaseExport').hide();
	jQuery('#divAdminDatabaseExportProgress').show();
	jQuery.post(jQuery('#desinventarURL').val(),
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
				// Hide Ext.Window
				Ext.getCmp('wndDatabaseExport').hide();

				// Open the backup file for download
				window.location = data.URL;
			}
			else
			{
				jQuery('#divAdminDatabaseExportError').show();
			}
		},
		'json'
	);
} //doAdminDatabaseExportAction

function doAdminDatabaseExportSetup(prmRegionId)
{
	jQuery('#txtAdminAdminDatabaseExport_RegionId').text(prmRegionId);
} //doAdminDatabaseExportSetup
