function onReadyDatabaseBackup() {
	jQuery('#divDBBackupProgress').hide();
	jQuery('#divDBBackupResults').hide();
	jQuery('#txtDBBackupRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	jQuery('#btnDBBackupDoBackup').click(function() {
		jQuery('#divDBBackupProgress').show();
		jQuery.post(jQuery('#desinventarURL').val(),
			{cmd      : 'doDatabaseBackup',
			 RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				if (data != null) {
				jQuery('#linkDBBackupDownload').attr('href', data.FileName);
				}
				jQuery('#divDBBackupProgress').hide();
				jQuery('#divDBBackupResults').show();
			},
			'json'
		);
	});
}
