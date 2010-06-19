function onReadyDatabaseBackup() {
	jQuery('#divDBBackupProgress').hide();
	jQuery('#divDBBackupResults').hide();
	jQuery('#txtDBBackupRegionLabel').text(jQuery('#desinventarRegionLabel').val());
	jQuery('#btnDBBackupDoBackup').click(function() {
		jQuery('#divDBBackupProgress').show();
		jQuery.post(jQuery('#desinventarURL').val(),
			{cmd : 'getversion'
			},
			function(data) {
				jQuery('#divDBBackupProgress').hide();
				jQuery('#linkDBBackupDownload').attr('href','http://www.google.com');
				jQuery('#divDBBackupResults').show();
			}
		);
	});
}
