/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyCommon()
{
	// 2011-04-29 (jhcaiced) Fix for use of ExtJS in IE9 ?
	if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
	{
		Range.prototype.createContextualFragment = function(html)
		{
			var frag = document.createDocumentFragment(), div = document.createElement("div");
			frag.appendChild(div);
			div.outerHTML = html;
			return frag;
		};
	}

	// Initialize tooltip for elements with title attribute
	jQuery('[title]').tooltip();

	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 180);
}

function doKeepSessionAwake() {
	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{cmd : 'cmdSessionAwake'},
		function(data) {
		},
		'json'
	);
}

function doWindowReload()
{
	// Destroy viewport, the loading... message should stay.
	jQuery('body').trigger('WindowReload');

	// Reload document window
	window.location.reload(false);
} //doWindowReload
