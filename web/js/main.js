function onReadyMain() {
	jQuery('#DC').submit(function() {
		var myURL = jQuery(this).attr('action');
		jQuery.post(myURL,
			jQuery(this).serialize(),
			function(data) {
				jQuery('#dcr').html('');
				jQuery('#dcr').html(data);
				onReadyData();
				onReadyGraphic();
				onReadyThematicMap();
				onReadyStatistic();
			}
		);
		return false;
	});
}
