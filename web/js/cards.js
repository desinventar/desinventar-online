function onReadyDatacards() {
	// Hide StatusMessages
	displayDatacardStatusMsg('');
	jQuery('#divDatacardStatusMsg').show();
	// Hide window's parameters
	jQuery('#divDatacardParameter').hide();
	
	DisableEnableForm($('DICard'), true);
	changeOptions();
	UserRole = jQuery('#prmUserRole').val();
	if (UserRole != '') {
		displayDatacardStatusMsg('msgDatacardStartNew');
	}

	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionActive, 60);

	jQuery('#DisasterBeginTime0').blur(function() {
		cmd = jQuery('#_CMD').val();
		if (cmd == 'insertDICard') {
			doDatacardSuggestSerial();
		}
	});

	jQuery('#linkDatacardSuggestSerial').click(function() {
		if (jQuery('#DisasterSerial').attr('disabled') == false) {
			doDatacardSuggestSerial();
		}
	});

	jQuery('.inputInteger').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'integer:' + jQuery(this).attr('MaxLength'));
	});

	jQuery('.inputDouble').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'double:' + jQuery(this).attr('MaxLength'));
	});

	jQuery('.inputText').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'text:');
	});

	jQuery('.inputAlphaNumber').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'alphanumber:');
	});
	
	jQuery('#DisasterBeginTime1').blur(function() {
		if (parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 12 ) {
				jQuery(this).val('');
		}
	});
	

	jQuery('#DisasterBeginTime2').blur(function() {
		if (parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 31 ) {
				jQuery(this).val('');
		}
	});
	
	jQuery('#btnDatacardNew').click(function() {
		doDatacardNew();
		return false;
	});
	
	jQuery('#btnDatacardEdit').click(function() {
		doDatacardEdit();
		return false;
	});
	
	jQuery('#btnDatacardSave').click(function() {
		doDatacardSave();
		return false;
	});
	
	jQuery('#btnDatacardClear').click(function() {
		doDatacardClear();
		return false;
	});
	
	jQuery('#btnDatacardPrint').click(function() {
		window.print();
		return false;
	});
	
	jQuery('#btnDatacardGotoFirst').click(function() {
		doDatacardGotoFirst();
		return false;
	});

	jQuery('#btnDatacardGotoLast').click(function() {
		doDatacardGotoLast();
		return false;
	});
	
	jQuery('#btnDatacardGotoPrev').click(function() {
		doDatacardGotoPrev();
		return false;
	});
	
	jQuery('#btnDatacardGotoNext').click(function() {
		doDatacardGotoNext();
		return false;
	});

	jQuery('#txtDatacardFind').keydown(function(event) {
		if(event.keyCode == 13) {
			doDatacardFind();
		}
	});	
	
	jQuery('#btnDatacardFind').click(function() {
		doDatacardFind();
	});
	
	jQuery('#linkDatacardShowEffectsBasic').click(function() {
		jQuery('#divDatacardEffectsBasic').show();
		jQuery('#divDatacardEffectsAditional').hide();
	});
	
	jQuery('#linkDatacardShowEffectsAditional').click(function() {
		jQuery('#divDatacardEffectsBasic').hide();
		jQuery('#divDatacardEffectsAditional').show();
	});
	
	jQuery('.DatacardCmdButton').hide;
	jQuery('#btnDatacardPrint').show();
	if (jQuery('#prmUserRoleValue').val() >= 2) {
		jQuery('.DatacardCmdButton').show();
	}
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
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoPrev'), true);
			disenabutton($('btnDatacardGotoNext'), true);
			disenabutton($('btnDatacardGotoLast'), true);
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardEdit":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardClear'), false);
			disenabutton($('btnDatacardCancel'), false);
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoPrev'), true);
			disenabutton($('btnDatacardGotoNext'), true);
			disenabutton($('btnDatacardGotoLast'), true);
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardSave":
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), false);
			disenabutton($('btnDatacardClear'), true);
			disenabutton($('btnDatacardCancel'), true);
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoPrev'), false);
			disenabutton($('btnDatacardGotoNext'), false);
			disenabutton($('btnDatacardGotoLast'), false);
			disenabutton($('btnDatacardFind'), false);
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
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoPrev'), false);
			disenabutton($('btnDatacardGotoNext'), false);
			disenabutton($('btnDatacardGotoLast'), false);
			disenabutton($('btnDatacardFind'), false);
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
				displayDatacardStatusMsg('');
				if (data.DisasterId != '') {
					valid = setDICardfromId(RegionId, data.DisasterId, '');
					UserRole = jQuery('#prmUserRole').val();
					canUpdateDatacard = getDatacardUpdatePerm(UserRole);
					if (canUpdateDatacard) {
						disenabutton($('btnDatacardEdit'), false);
					}
					if (myCmd == 'getDisasterIdFromSerial') {
						disenabutton($('btnDatacardGotoPrev'), false);
						disenabutton($('btnDatacardGotoNext'), false);
					}
					jQuery('#prmRecordNumber').val(data.RecordNumber);
				} else {
					displayDatacardStatusMsg('msgDatacardNotFound');
					bReturn = false;
				}
			} else {
				bReturn = false;
			}
		},
		'json'
	);
	$('dostat').innerHTML = "";
	return bReturn;
}

function doDatacardFind() {
	if(jQuery('#txtDatacardFind').val() !='') {
		requestDatacard('getDisasterIdFromSerial', jQuery('#txtDatacardFind').val());
	}
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

function doDatacardGotoFirst() {
	displayDatacardStatusMsg('');
	bUpdate = getDatacardUpdatePerm(jQuery('#prmUserRole').val());
	bFound = requestDatacard('getDisasterIdFirst', jQuery('#DisasterId').val());
	if (bUpdate) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoPrev'), true);
	disenabutton($('btnDatacardGotoNext'), false);
}

function doDatacardGotoLast() {
	displayDatacardStatusMsg('');
	bUpdate = getDatacardUpdatePerm(jQuery('#prmUserRole').val());
	bFound = requestDatacard('getDisasterIdLast', jQuery('#DisasterId').val());
	if (bUpdate) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoPrev'), false);
	disenabutton($('btnDatacardGotoNext'), true);
}

function doDatacardGotoPrev() {
	displayDatacardStatusMsg('');
	bUpdate = getDatacardUpdatePerm(jQuery('#prmUserRole').val());
	bFound = requestDatacard('getDisasterIdPrev', jQuery('#prmRecordNumber').val());
	if (bFound == false) {
		displayDatacardStatusMsg('msgDatacardNotFound');
	}
	if (bUpdate) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoNext'), false);
}

function doDatacardGotoNext() {
	displayDatacardStatusMsg('');
	bUpdate = getDatacardUpdatePerm(jQuery('#prmUserRole').val());
	bFound = requestDatacard('getDisasterIdNext', jQuery('#prmRecordNumber').val());
	if (bFound == false) {
		displayDatacardStatusMsg('msgDatacardNotFound');
	}
	if (bUpdate) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoPrev'), false);
}

function doKeepSessionActive() {
	updateList('dostat', 'cards.php', 'u=1');
}

function doDatacardSuggestSerial() {
	if (jQuery('#DisasterBeginTime0').val() != '') {
		requestDatacard('getNextSerial', jQuery('#DisasterBeginTime0').val());
	}
}
