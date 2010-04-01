
function onDatacardReady() {
}

var mod = "di";

function hidediv(myDiv) {
	$(myDiv).style.visibility = 'hidden';
}

function showdiv(myDiv) {
	$(myDiv).style.visibility = 'visible';
}

function showtip(tip, clr) {
	try {
	var d = parent.document.getElementById('_DIDesc');
	d.style.backgroundColor = clr;
	d.value = tip;
	} catch(err) { };
}

function getDatacardUpdatePerm(UserRole) {
	var bAnswer = false;
	if ( (UserRole == 'USER') || 
	     (UserRole == 'SUPERVISOR') ||
	     (UserRole == 'ADMINREGION') ) {
	     bAnswer = true;
	}
	return bAnswer;
}

function requestDCard(cmd, value) {
	var bReturn = true;
	var RegionId=jQuery('#prmRegionId').text();
	var lsAjax = new Ajax.Request('cards.php', {
		method: 'get', parameters:  'cmd='+ cmd +'&value='+ value + '&r=' + RegionId,
		onLoading: function(request) {
			$('dostat').innerHTML = waiting;
		},
		onSuccess: function(request) {
			var res = request.responseText;
			if (res.length >= 5 && cmd == "getNextSerial") {
				// check valid DisasterSerial
					$('DisasterSerial').value = value +'-'+ res;
			} else if (res.length >= 36 && (cmd == "getPrevDId" || cmd == "getNextDId" || cmd == "getIdfromSerial")) {
				// check valid DisasterId
				valid = setDICardfromId(RegionId, res, '');
				UserRole = jQuery('#prmUserRole').text();
				canUpdateDatacard = getDatacardUpdatePerm(UserRole);
				if (canUpdateDatacard) {
					disenabutton($('cardupd'), false);
				}
				if (cmd == "getIdfromSerial") {
					disenabutton($('prev'), false);
					disenabutton($('next'), false);
				}
			} else {
				bReturn = false;
			}
			$('dostat').innerHTML = "";
		}
	} );
	return bReturn;
}
