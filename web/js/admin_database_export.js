/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseExport()
{
	jQuery('.DBBackup').hide();
	jQuery('#txtDBBackupRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	/*
	jQuery('#divDBBackupParameters').show();
	jQuery('#btnDBBackupDoBackup').click(function() {
		jQuery('.DBBackup').hide();
		jQuery('#divDBBackupProgress').show();
		jQuery.post(jQuery('#desinventarURL').val(),
			{cmd      : 'doDatabaseBackup',
			 RegionId : jQuery('#txtAdminDatabaseExport_RegionId').text()
			},
			function(data) {
				var bOk = true;
				if (data == null) {
					bOk = false;
				} else {
					if (data.Status != 'OK') {
						$bOk = false;
					}
				}				
				if (bOk) {
					jQuery('#linkDBBackupDownload').attr('href', data.BackupURL);
					jQuery('#btnDBBackupDownload').attr('href', data.BackupURL);
					jQuery('.DBBackup').hide();
					jQuery('#divDBBackupResults').show();
					jQuery('#divDBBackupParameters').show();		
				} else {
					jQuery('.DBBackup').hide();
					jQuery('#divDBBackupErrors').show();
					jQuery('#divDBBackupParameters').show();		
				}
			},
			'json'
		);
	});
	
	jQuery('#btnDBBackupDownload').click(function() {
		var url = jQuery(this).attr('href');
		window.open(url,'','');
	});
	
	jQuery('#divDatabaseExport').bind('DBBackupRestart', function() {
		jQuery('.DBBackup').hide();
		jQuery('#divDBBackupParameters').show();		
	});
	*/
}

function doAdminDatabaseExportAction()
{
	jQuery('.DBBackup').hide();
	jQuery('#divDBBackupProgress').show();
	jQuery.post(jQuery('#desinventarURL').val(),
		{
			cmd      : 'cmdDatabaseExport',
			RegionId : jQuery('#txtAdminDatabaseExport_RegionId').text()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#linkDBBackupDownload').attr('href', data.URL);
				jQuery('#btnDBBackupDownload').attr('href', data.URL);
				jQuery('.DBBackup').hide();
				jQuery('#divDBBackupResults').show();
				jQuery('#divDBBackupParameters').show();		
				//window.location = data.URL;
			}
			else
			{
				jQuery('.DBBackup').hide();
				jQuery('#divDBBackupErrors').show();
				jQuery('#divDBBackupParameters').show();		
			}
		},
		'json'
	);
} //doAdminDatabaseExportAction

function doAdminDatabaseExportSetup(prmRegionId)
{
	console.log('DatabaseExportSetup : ' + prmRegionId);
	jQuery('#txtAdminDatabaseExport_RegionId').text(prmRegionId);
} //doAdminDatabaseExportSetup
