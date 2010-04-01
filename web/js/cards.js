
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

// Display Geography in form and search; k=geoid, l=0, desc='', opc=''
function setgeo(k, l, desc, opc) {
	var RegionId = jQuery('#prmRegionId').text();
	if (opc == "search") {
		var fld = '_GeographyId';
		var lev = '_lev'+ l;
		var op = '&opc='+ opc;
	} else {
		var fld = 'GeographyId';
		var lev = 'lev'+ l;
		var op = '';
	}
	if (k.length >= 5) {
		$(fld).value = k;
		updateList(lev, 'cards.php', 'cmd=list' + '&GeographyId='+ k + op + '&r=' + RegionId);
	} else if (k == '') {
		showtip(desc, '#d4baf6');
		val = $(fld).value;
		$(fld).value = val.substr(0, val.length - 5);
		$(lev).innerHTML = '';
	}
}

function DisableEnableForm(xForm, disab) {
	objElems = xForm.elements;
	var myname = "";
	var mysty = "";
	if (disab)
		col = "#eee";
	else
		col = "#fff";
	for (i=0; i < objElems.length; i++) {
		myname = objElems[i].name + "";
		if (myname.substring(0,1) != "_") {
			objElems[i].disabled = disab;
			objElems[i].style.backgroundColor = col;
		}
	}
}

function disenabutton(butid, disab) {
	if (disab) {
		butid.disable();
		Element.removeClassName(butid, 'bb');
		Element.addClassName(butid, 'disabled');
	} else {
		butid.enable();
		Element.addClassName(butid, 'bb');
		Element.removeClassName(butid, 'disabled');
	}
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
