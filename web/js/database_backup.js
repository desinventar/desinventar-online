function onReadyDatabaseBackup() {
	jQuery('.DBBackup').hide();
	jQuery('#txtDBBackupRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	jQuery('#divDBBackupParameters').show();
	jQuery('#btnDBBackupDoBackup').click(function() {
		jQuery('.DBBackup').hide();
		jQuery('#divDBBackupProgress').show();
		jQuery.post(jQuery('#desinventarURL').val(),
			{cmd      : 'doDatabaseBackup',
			 RegionId : jQuery('#desinventarRegionId').val()
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
	
	jQuery('#divDatabaseBackup').bind('DBBackupRestart', function() {
		jQuery('.DBBackup').hide();
		jQuery('#divDBBackupParameters').show();		
	});
}
