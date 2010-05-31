function onReadyMain() {
	jQuery('#DC').submit(function() {
		jQuery.post('data.php',
			jQuery(this).serialize(),
			function(data) {
				jQuery('#divGridView').html('');
				jQuery('#divGridView').html(data);
				onReadyData();
			}
		);
		return false;
	});
}
