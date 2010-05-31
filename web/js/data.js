function onReadyData() {
	jQuery('#pp').keydown(function(event) {
		if(event.keyCode == 13) {
			displayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
	
	jQuery('#btnGridGotoFirstPage').click(function() {
		displayPage(1);
	});
	jQuery('#btnGridGotoPrevPage').click(function() {
		displayPage('prev');
	});
	jQuery('#btnGridGotoNextPage').click(function() {
		displayPage('next');
	});
	jQuery('#btnGridGotoLastPage').click(function() {
		displayPage(jQuery('#prmNumberOfPages').val());
	});
}
