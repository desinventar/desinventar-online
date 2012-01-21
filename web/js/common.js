/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyCommon()
{
	// Initialize tooltip for elements with title attribute
	jQuery('[title]').tooltip();

	jQuery('body').on('cmdWindowReload', function() {
		// Destroy viewport, the loading... message should stay.
		doViewportDestroy();
		// Reload document window
		window.location.reload(false);
	});
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

// jQuery Snippets Code

// http://css-tricks.com/snippets/jquery/serialize-form-to-json/
// Serialize Form to JSON
jQuery.fn.serializeObject = function()
{
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function() {
		console.log(this.name);
		if (o[this.name])
		{
			console.log('    debug 1');
			if (!o[this.name].push)
			{
				console.log('     debug 1A');
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		}
		else
		{
			console.log('     debug 2');
			o[this.name] = this.value || '';
		}
	});
	return o;
};

jQuery.fn.highlight = function() {
	jQuery(this).attr('old-bg-color', jQuery(this).css('background-color'));
	return jQuery(this).css('background-color', '#ffff66');
}

jQuery.fn.unhighlight = function() {
	if (jQuery(this).attr('old-bg-color') != '') {
		jQuery(this).css('background-color', jQuery(this).attr('old-bg-color'));
	}
}

jQuery.fn.disable = function() {
	jQuery(this).attr('disabled',true);
	jQuery(this).attr('readonly',true);
	jQuery(this).addClass('disabled');
	return this;
}

jQuery.fn.enable = function() {
	jQuery(this).removeAttr('disabled');
	jQuery(this).removeAttr('readonly');
	jQuery(this).removeClass('disabled');
	return this;
}

// Older Compatibility Functions

function checkForm(prmForm, prmFieldList, errmsg)
{
	var bReturn = true;
	jQuery.each(prmFieldList, function(index, value) {
		var selector = '#' + prmForm + ' #' + value;
		if (jQuery(selector).val().length < 1)
		{
			jQuery(selector).highlight();
			bReturn = false;
		} //if
	}); //.each
	return bReturn;
} //function

function doViewportDestroy()
{
	var viewport = Ext.getCmp('viewport');
	if (viewport != undefined)
	{
		viewport.destroy();
		jQuery('#loading').show();
		jQuery('#loading-mask').show();
	}
} //doViewportDestroy

function showtip(prmText)  //prmText, prmColor
{
	if (prmText != undefined)
	{
		var sColor = '#ffffff';
		if (arguments.length > 1)
		{
			sColor = arguments[1];
		}
		jQuery('#txtHelpArea').val(prmText).css('background-color', sColor);
	}
}
