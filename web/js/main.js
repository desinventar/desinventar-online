function onReadyMain() {
	jQuery('#DC').submit(function() {
		var myURL = jQuery(this).attr('action');
		alert(myURL);
		jQuery.post(myURL,
			jQuery(this).serialize(),
			function(data) {
				jQuery('#divGridView').html('');
				jQuery('#divGridView').html(data);
				onReadyData();
				onReadyGraphic();
				onReadyThematicMap();
				onReadyStatistic();
			}
		);
		return false;
	});
}
