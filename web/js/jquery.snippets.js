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
