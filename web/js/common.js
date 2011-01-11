/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyCommon() {
	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 60);
}

function doKeepSessionAwake() {
	jQuery.post(jQuery('#desinventarURL').val() + '/index.php',
		{cmd : 'cmdSessionAwake'},
		function(data) {
		},
		'json'
	);
}
