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
				if (data.Status == 'OK') {
					jQuery('#linkDBBackupDownload').attr('href', data.BackupURL);
					jQuery('.DBBackup').hide();
					jQuery('#divDBBackupResults').show();
				} else {
					jQuery('.DBBackup').hide();
					jQuery('#divDBBackupErrors').show();
				}
			},
			'json'
		);
	});
}
