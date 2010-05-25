
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
	var RegionId = jQuery('#prmRegionId').val();
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
		if (butid != null) {
			butid.disable();
		}
		Element.removeClassName(butid, 'bb');
		Element.addClassName(butid, 'disabled');
	} else {
		if (butid != null) {
			butid.enable();
		}
		Element.addClassName(butid, 'bb');
		Element.removeClassName(butid, 'disabled');
	}
}

function changeOptions(but) {
	switch (but) {
		case "btnDatacardNew":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('cardsav'), false);
			disenabutton($('cardupd'), true);
			disenabutton($('cardcln'), false);
			disenabutton($('cardcan'), false);
			disenabutton($('first'), true);
			disenabutton($('prev'), true);
			disenabutton($('next'), true);
			disenabutton($('last'), true);
			disenabutton($('cardfnd'), true);
		break;
		case "cardupd":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('cardsav'), false);
			disenabutton($('cardupd'), true);
			disenabutton($('cardcan'), false);
			disenabutton($('first'), true);
			disenabutton($('prev'), true);
			disenabutton($('next'), true);
			disenabutton($('last'), true);
			disenabutton($('cardfnd'), true);
		break;
		case "cardsav":
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('cardsav'), true);
			disenabutton($('cardupd'), false);
			disenabutton($('cardcln'), true);
			disenabutton($('cardcan'), true);
			disenabutton($('first'), false);
			disenabutton($('prev'), false);
			disenabutton($('next'), false);
			disenabutton($('last'), false);
			disenabutton($('cardfnd'), false);
		break;
		case "cardcan":
			if ($('DisasterId').value == "")
				disenabutton($('cardupd'), true);
			else
				disenabutton($('cardupd'), false);
			disenabutton($('cardsav'), true);
			disenabutton($('cardcln'), true);
			disenabutton($('cardcan'), true);
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('first'), false);
			disenabutton($('prev'), false);
			disenabutton($('next'), false);
			disenabutton($('last'), false);
			disenabutton($('cardfnd'), false);
		break;
		default:
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('cardsav'), true);
			disenabutton($('cardupd'), true);
			disenabutton($('cardcln'), true);
			disenabutton($('cardcan'), true);
		break;
	}
}

function switchEff(section) {
	if (section == 'effext') {
		$('eeimg').src="images/di_efftag.png";
		$('efimg').src="images/di_eeftag.png";
		$('effbas').style.display='none';
		$('effext').style.display='block';
	}
	if (section == 'effbas') {
		$('efimg').src="images/di_efftag.png";
		$('eeimg').src="images/di_eeftag.png";
		$('effbas').style.display='block';
		$('effext').style.display='none';
	}
}

function displayDatacardStatusMsg(msgId) {
	// First hide all items
	jQuery('.datacardStatusMsg').hide();
	// Show a specific message
	if (msgId != '') {
		jQuery('#' + msgId).show();
	}
}

function requestDatacard(myCmd, myValue) {
	var bReturn = true;
	var RegionId=jQuery('#prmRegionId').val();

	$('dostat').innerHTML = waiting;

	jQuery.post('cards.php',
		{cmd:myCmd,value:myValue,r:RegionId},
		function(data) {
			if ( (myCmd == 'getNextSerial') && (data.DisasterSerial.length >= 5) ) {
				$('DisasterSerial').value = myValue + '-' + data.DisasterSerial;
			} else if (data.Status == 'OK') {
				// check valid DisasterId
				valid = setDICardfromId(RegionId, data.DisasterId, '');
				UserRole = jQuery('#prmUserRole').val();
				canUpdateDatacard = getDatacardUpdatePerm(UserRole);
				if (canUpdateDatacard) {
					disenabutton($('cardupd'), false);
				}
				if (myCmd == 'getDisasterIdFromSerial') {
					disenabutton($('prev'), false);
					disenabutton($('next'), false);
				}
				jQuery('#prmRecordNumber').val(data.RecordNumber);
			} else {
				bReturn = false;
			}
		},
		'json'
	);
	$('dostat').innerHTML = "";
	return bReturn;
}

function doDatacardNew() {
	alert('New');
	DisableEnableForm($('DICard'), false);
	jQuery('#DisasterBeginTime0').focus();
	$('DisasterId').value='';
	$('DICard').reset();
	$('_CMD').value = 'insertDICard';
	jQuery('#DisasterBeginTime0').val('');
	jQuery('#DisasterBeginTime1').val('');
	jQuery('#DisasterBeginTime2').val('');
	displayDatacardStatusMsg('msgDatacardFill');
	changeOptions('btnDatacardNew');
}
