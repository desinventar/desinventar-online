/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseExport()
{
	jQuery('clsDatabaseExport').hide();
	jQuery('#txtDatabaseExportRegionLabel').text(jQuery('#desinventarRegionLabel').val());
}

function doAdminDatabaseExportAction()
{
	var iAnswer = 0;
	jQuery('clsDatabaseExport').hide();
	jQuery('#divDatabaseExportProgress').show();
	jQuery.post(jQuery('#desinventarURL').val(),
		{
			cmd      : 'cmdDatabaseExport',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			jQuery('clsAdminDatabaseExport').hide();
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divAdminDatabaseExportResults').show();
				iAnswer = 1;
				// Open the backup file for download
				window.location = data.URL;
			}
			else
			{
				jQuery('#divAdminDatabaseExportError').show();
				iAnswer = -1;
			}
		},
		'json'
	);
} //doAdminDatabaseExportAction

function doAdminDatabaseExportSetup(prmRegionId)
{
	jQuery('#txtAdminDatabaseExport_RegionId').text(prmRegionId);
} //doAdminDatabaseExportSetup
