// http://css-tricks.com/snippets/jquery/serialize-form-to-json/
// Serialize Form to JSON
jQuery.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
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
	return this;
}

jQuery.fn.enable = function() {
	jQuery(this).removeAttr('disabled');
	return this;
}
