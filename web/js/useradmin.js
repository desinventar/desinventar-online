
function onReadyUserAdmin() {
	jQuery("#userListTable tr:odd").addClass("normal");
	jQuery("#userListTable tr:even").addClass("under");
	jQuery("#userListTable tr").mouseover(function() {
		jQuery(this).addClass('highlight');
	});
	jQuery("#userListTable tr").mouseout(function() {
		jQuery(this).removeClass('highlight');
	});
	jQuery("#userListTable tr").click(function() {
		//alert('click on row');
	});
};
