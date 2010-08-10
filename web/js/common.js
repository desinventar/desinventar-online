function onReadyCommon() {
	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 10);
}

function doKeepSessionAwake() {
	jQuery.post(jQuery('#desinventarURL').val() + '/index.php',
		{cmd : 'cmdSessionAwake'},
		function(data) {
		},
		'json'
	);
}
