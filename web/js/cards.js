
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
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardClear'), false);
			disenabutton($('btnDatacardCancel'), false);
			disenabutton($('first'), true);
			disenabutton($('prev'), true);
			disenabutton($('next'), true);
			disenabutton($('last'), true);
			disenabutton($('cardfnd'), true);
		break;
		case "btnDatacardEdit":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), false);
			disenabutton($('first'), true);
			disenabutton($('prev'), true);
			disenabutton($('next'), true);
			disenabutton($('last'), true);
			disenabutton($('cardfnd'), true);
		break;
		case "btnDatacardSave":
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), false);
			disenabutton($('btnDatacardClear'), true);
			disenabutton($('btnDatacardCancel'), true);
			disenabutton($('first'), false);
			disenabutton($('prev'), false);
			disenabutton($('next'), false);
			disenabutton($('last'), false);
			disenabutton($('cardfnd'), false);
		break;
		case "btnDatacardCancel":
			if ($('DisasterId').value == "")
				disenabutton($('btnDatacardEdit'), true);
			else
				disenabutton($('btnDatacardEdit'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardClear'), true);
			disenabutton($('btnDatacardCancel'), true);
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('first'), false);
			disenabutton($('prev'), false);
			disenabutton($('next'), false);
			disenabutton($('last'), false);
			disenabutton($('cardfnd'), false);
		break;
		default:
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardClear'), true);
			disenabutton($('btnDatacardCancel'), true);
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
			if (myCmd == 'getNextSerial') {
				if (data.DisasterSerial.length >= 5) {
					$('DisasterSerial').value = myValue + '-' + data.DisasterSerial;
				}
			} else if (data.Status == 'OK') {
				// check valid DisasterId
				valid = setDICardfromId(RegionId, data.DisasterId, '');
				UserRole = jQuery('#prmUserRole').val();
				canUpdateDatacard = getDatacardUpdatePerm(UserRole);
				if (canUpdateDatacard) {
					disenabutton($('btnDatacardEdit'), false);
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

function doDatacardEdit() {
	displayDatacardStatusMsg('');
	RegionId = jQuery('#prmRegionId').val();
	jQuery.post('cards.php',
		{'cmd'       : 'chklocked',
		 'r'         : jQuery('#prmRegionId').val(),
		'DisasterId' : jQuery('#DisasterId').val()
		},
		function(data) {
			if (data.DatacardStatus == 'RESERVED') {
				DisableEnableForm($('DICard'), false);
				jQuery('#DisasterBeginTime0').focus();
				jQuery('#_CMD').val('updateDICard');
				displayDatacardStatusMsg('msgDatacardFill');
				changeOptions('btnDatacardEdit');
			} else {
				displayDatacardStatusMsg('msgDatacardIsLocked');
			}
		},
		'json'
	);
}

function doDatacardSave() {
	var bContinue = true;
	var cmd = jQuery('#_CMD').val();
	var DisasterSerial = jQuery('#DisasterSerial').val();
	var PrevDisasterSerial = jQuery('#PrevDisasterSerial').val();
	jQuery.post('cards.php',
		{'cmd'            : 'existDisasterSerial',
		 'RegionId'       : jQuery('#prmRegionId').val(),
		 'DisasterSerial' : DisasterSerial
		},
		function(data) {
			bContinue = true;
			if ( (cmd == 'insertDICard') && (data.DisasterSerial != '') ) {
				// Serial of new datacard already exists...
				//alert('Disaster Serial already exists...');
				bContinue = false;
			}
			if (cmd == 'updateDICard') {
				if ( (DisasterSerial != PrevDisasterSerial) && (data.DisasterSerial != '') ) {
					// Edited Serial exists in database...
					//alert('Disaster Serial is duplicated...');
					bContinue = false;
				}
			}
			if (bContinue == false) {
				displayDatacardStatusMsg('msgDuplicatedDisasterSerial');
			}
			if (bContinue) {
				var fl = new Array('DisasterSerial', 'DisasterBeginTime0', 'DisasterSource', 
									'geolev0', 'EventId', 'CauseId', 'RecordStatus');
				if (checkForm(fl, jQuery('#msgDatacardFieldsError').text())) {
					displayDatacardStatusMsg('');
					uploadMsg('');
					$('DICard').submit();
					DisableEnableForm($('DICard'), true);
					changeOptions('btnDatacardSave');
					// clear Help text area
					showtip('','#ffffff');
				} else {
					displayDatacardStatusMsg('msgDatacardFieldsError');
				}
			}
		},'json'
	);
}

function doDatacardClear() {
	$('DICard').reset();
	$('lev0').innerHTML='';
	displayDatacardStatusMsg('');
	uploadMsg('');
	jQuery('#DisasterBeginTime0').focus();
}

function doDatacardCancel() {
	jQuery.post('cards.php',
		{'cmd'        : 'chkrelease',
		 'r'          : jQuery('#prmRegionId').val(),
		 'DisasterId' : jQuery('#DisasterId').val()
		},
		function(data) {
			DisableEnableForm($('DICard'), true);
			changeOptions('btnDatacardCancel');
			// clear Help text area
			showtip('','#ffffff');
			displayDatacardStatusMsg('msgDatacardStartNew');
		},
		'json'
	);
}
