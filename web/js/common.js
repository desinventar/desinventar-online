/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyCommon()
{
	// Initialize tooltip for elements with title attribute
	jQuery('[title]').tooltip();
} //onReadyCommon()

function onReadyPrototype()
{
	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 180);
} //onReadyPrototype()

function doKeepSessionAwake()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdSessionAwake'
		},
		function(data)
		{
		},
		'json'
	);
} //doKeepSessionAwake()
