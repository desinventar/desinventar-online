
function onReadyUserAdmin() {
	// Create table stripes
	jQuery("#tblUserList tr:odd").addClass("normal");
	jQuery("#tblUserList tr:even").addClass("under");
	// Change background color of row under mouse
	jQuery("#tblUserList tr").mouseover(function() {
		jQuery(this).addClass('highlight');
	});
	jQuery("#tblUserList tr").mouseout(function() {
		jQuery(this).removeClass('highlight');
	});
	// When selecting a row, start editing data...
	jQuery("#tblUserList tr").click(function() {
		jQuery("#divUserEdit").show();
	});
	// Add new User...
	jQuery("#btnUserAdd").click(function() {
		jQuery("#divUserEdit").show();
		//onclick="setUserPA('','','','','','','1'); $('cmd').value='insert'; $('UserPasswd').disabled=true;"
	});
	// Finish edit, validate form and send data...
	jQuery("#btnUserEditSubmit").click(function() {
		alert('submit');
	});
	jQuery("#btnUserEditCancel").click(function() {
		jQuery("#divUserEdit").hide();
		//onClick="$('userpaaddsect').style.display='none'; uploadMsg('');"
	});
};

function clearUserEditForm() {
};

