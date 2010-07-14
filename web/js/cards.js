function onReadyDatacards() {
	jQuery('#divDatacardWindow').hide();
	jQuery('#divDatacardWindow').bind('displayForm', function() {
		jQuery(this).show();
		doDatacardClear();
	});
	
	jQuery('#DICard').submit(function() {
		jQuery('#DatacardCommand').val(jQuery('#_CMD').val());
		jQuery('#RecordAuthor').val(jQuery('#desinventarUserId').val());
		jQuery('#RegionId').val(jQuery('#desinventarRegionId').val());
		var params = 'RegionId=' + jQuery('#RegionId').val() + '&' + jQuery(this).serialize();
		jQuery.post('cards.php',
			params,
			function(data) {
				jQuery('#DisasterId').val(data.DisasterId);
				jQuery('#RecordSerial').text(data.RecordSerial);
				jQuery('#RecordPublished').text(data.RecordPublished);
				jQuery('#RecordReady').text(data.RecordReady);
				switch (data.Status) {
					case 'INSERTOK':
						displayDatacardStatusMsg('msgDatacardInsertOk');
						jQuery('#divRecordStat').show();
					break;
					case 'UPDATEOK':
						displayDatacardStatusMsg('msgDatacardUpdateOk');
						jQuery('#divRecordStat').show();
					break;
				}
				DisableEnableForm($('DICard'), true);
				changeOptions('btnDatacardSave');
				// clear Help text area
				showtip('','#ffffff');
			},
			'json'
		);
		return false;
	});
	
	// Process combobox/input fields in effects
	jQuery('.clsEffectNumeric').keypress(function(event) {
		edit(event);
	});
	jQuery('.clsEffectNumeric').blur(function(event) {
		this.editing=false;
		if(parseInt(this.value) == 0) { 
			this.value = '0';
		}
	});

	// Enable/Disable related EffectSector fields based on value of other fields...	
	jQuery('.clsEffectDouble').blur(function(event) {
		altField = jQuery(this).attr('altField');
		if(parseInt(jQuery(this).val()) > 0) {
			jQuery('#' + altField).val('-1');
		}
		if(parseInt(jQuery(this).val()) == 0) {
			jQuery('#' + altField).val('0');
		}	
	});
	
	// Geography Levels/Items Functions...
	jQuery('.GeoLevelSelect').bind('loadGeographyItems', function(event) {
		var mySelect = jQuery(this);
		mySelect.trigger('clearGeographyItems');
		var LevelId = parseInt(mySelect.attr('level'));
		var myValue = jQuery('#GeographyItemId' + LevelId).text();
		if (event.ReadOnly) {
			if (LevelId > 0) {
				mySelect.append('<option value="' + jQuery('#GeographyItemId' + LevelId).text() +'">' + jQuery('#GeographyItemValue' + LevelId).text() + '</option>');
			}
			mySelect.val(myValue);
		} else {
			if (LevelId > 0) {
				PrevLevel = parseInt(LevelId) - 1;
				GeographyParentId = jQuery('#GeographyItemId' + PrevLevel).text();
				jQuery.get('cards.php',
					{'cmd'               : 'getGeographyItemsByLevel',
					 'GeographyLevelId'  : LevelId,
					 'GeographyParentId' : GeographyParentId,
					 'RegionId'          : jQuery('#desinventarRegionId').val()
					},
					function(data) {
						jQuery(data).each(function(key,value) {
							mySelect.append('<option value="' + value['GeographyId'] + '">' + value['GeographyName'] + '</option>');
						});
						mySelect.val(myValue);
					},
					'json'
				);
			} //if
		}
	});
	
	// Clear Geography Items from a Select Box
	jQuery('.GeoLevelSelect').bind('clearGeographyItems', function(event) {
		var mySelect = jQuery(this);
		var LevelId = parseInt(mySelect.attr('level'));
		if (LevelId > 0) {
			mySelect.find('option').remove();
			mySelect.append('<option value=""></option>');
		}
		//mySelect.val('');
	});
	
	// Enable loading of geographic levels when editing...
	jQuery('.GeoLevelSelect').change(function() {
		var LevelId = parseInt(jQuery(this).attr('level'));
		var NextLevelId = parseInt(LevelId) + 1;
		var MyGeoLevelId = jQuery(this).val();
		var GeoLevelCount = jQuery('.GeoLevelSelect').size();
		
		// Update Select Boxes for Next Level
		jQuery('#GeographyItemId' + LevelId).text(MyGeoLevelId);
		jQuery('#GeographyItemValue' + LevelId).text(jQuery(this).find('option[value=' + MyGeoLevelId + ']').text());

		// Clear values of following sublevels
		for(var i = NextLevelId; i < GeoLevelCount; i++) {
			jQuery('#GeoLevel' + i).trigger('clearGeographyItems');
			jQuery('#GeographyItemId' + i).text('');
			jQuery('#GeographyItemValue' + i).text('');
		}
		
		// Preload Items for Next Level only
		jQuery('#GeoLevel' + NextLevelId).trigger({type : 'loadGeographyItems', ReadOnly: false});
		
		// Update value of GeographyId
		var myGeographyId = '';
		for(var i = 0; i < GeoLevelCount; i++) {
			myValue = jQuery('#GeographyItemId' + i).text();
			if (myValue != '') {
				myGeographyId = myValue;
			}
		}
		jQuery('#GeographyId').val(myGeographyId);
	});	

	// Hide StatusMessages
	displayDatacardStatusMsg('');
	jQuery('#divDatacardStatusMsg').show();
	// Hide window's parameters
	jQuery('#divDatacardParameter').hide();
	
	DisableEnableForm($('DICard'), true);
	changeOptions();
	UserRole = jQuery('#desinventarUserRole').val();
	if (UserRole != '') {
		displayDatacardStatusMsg('msgDatacardStartNew');
	}

	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionActive, 60);

	// Validation of DisasterBeginTime and Suggest Serial for New Datacards
	jQuery('#DisasterBeginTime0').blur(function() {
		cmd = jQuery('#_CMD').val();
		if (cmd == 'insertDICard') {
			doDatacardSuggestSerial();
		}
	});

	jQuery('#DisasterBeginTime1').blur(function() {
		if (jQuery(this).val() == '' ||
		    parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 12 ) {
				jQuery(this).val('');
				jQuery('#DisasterBeginTime2').val('');
		}
	});

	jQuery('#DisasterBeginTime2').blur(function() {
		if (parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 31 ) {
				jQuery(this).val('');
		}
	});

	// Button for suggesting serial of datacard
	jQuery('#linkDatacardSuggestSerial').click(function() {
		if (jQuery('#DisasterSerial').attr('disabled') == false) {
			doDatacardSuggestSerial();
		}
	});

	// Apply some validation for several types of input fields
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
	
	
	// Datacard New/Edit/Save Commands
	jQuery('#btnDatacardNew').click(function() {
		doDatacardNew();
		jQuery('#GeoLevel0').trigger('clearGeographyItems');
		jQuery('#GeographyId').val('');
		jQuery('#DisasterId').val('');
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

	jQuery('#btnDatacardCancel').click(function() {
		doDatacardCancel();
		return false;
	});
	
	jQuery('#btnDatacardPrint').click(function() {
		window.print();
		return false;
	});

	// Datacard Navigation Functions
	jQuery('#btnDatacardGotoFirst').click(function() {
		jQuery('#divRecordStat').hide();
		doDatacardGotoFirst();
		return false;
	});

	jQuery('#btnDatacardGotoLast').click(function() {
		jQuery('#divRecordStat').hide();
		doDatacardGotoLast();
		return false;
	});
	
	jQuery('#btnDatacardGotoPrev').click(function() {
		jQuery('#divRecordStat').hide();
		doDatacardGotoPrev();
		return false;
	});
	
	jQuery('#btnDatacardGotoNext').click(function() {
		jQuery('#divRecordStat').hide();
		doDatacardGotoNext();
		return false;
	});

	// Datatacard Find function 
	jQuery('#txtDatacardFind').keydown(function(event) {
		if(event.keyCode == 13) {
			doDatacardFind();
		}
	});	
	
	jQuery('#btnDatacardFind').click(function() {
		doDatacardFind();
	});

	// Start with Basic Effects show
	jQuery('#divDatacardEffectsBasic').show();
	
	// Switch between Basic and Additional Effects
	jQuery('#linkDatacardShowEffectsBasic').click(function() {
		jQuery('#divDatacardEffectsBasic').show();
		jQuery('#divDatacardEffectsAdditional').hide();
		return false;
	});
	
	jQuery('#linkDatacardShowEffectsAditional').click(function() {
		jQuery('#divDatacardEffectsBasic').hide();
		jQuery('#divDatacardEffectsAdditional').show();
		return false;
	});

	//Show Command Buttons only for Role>=USER
	jQuery('.DatacardCmdButton').hide;
	jQuery('#btnDatacardPrint').show();
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
		jQuery('.DatacardCmdButton').show();
	}
} //onReadyDatacards()

function displayDatacardStatusMsg(msgId) {
	// First hide all items
	jQuery('.datacardStatusMsg').hide();
	// Show a specific message
	if (msgId != '') {
		jQuery('#' + msgId).show();
	}
}

var mod = "di";

function showtip(tip, clr) {
	try {
	var d = parent.document.getElementById('_DIDesc');
	d.style.backgroundColor = clr;
	d.value = tip;
	} catch(err) { };
}

// Display Geography in form and search; k=geoid, l=0, desc='', opc=''
function setgeo(k, l, desc, opc) {
	var RegionId = jQuery('#desinventarRegionId').val();
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
	if (xForm != null) {
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

function requestDatacard(myCmd, myValue) {
	var bReturn = true;
	var RegionId=jQuery('#desinventarRegionId').val();

	jQuery('#dostat').html(waiting);

	jQuery.post('cards.php',
		{cmd:myCmd,value:myValue,r:RegionId},
		function(data) {
			if (myCmd == 'getNextSerial') {
				if (data.DisasterSerial.length >= 5) {
					jQuery('#DisasterSerial').val(myValue + '-' + data.DisasterSerial);
				}
			} else if (data.Status == 'OK') {
				displayDatacardStatusMsg('');
				if (data.DisasterId != '') {
					jQuery('#cardsRecordNumber').val(data.RecordNumber);
					jQuery('#cardsRecordCount').val(data.RecordCount);
					jQuery('#cardsRecordSource').val('');
					valid = setDICardFromId(RegionId, data.DisasterId);
					
					if (jQuery('#desinventarUserRoleValue').val() >= 2) {
						disenabutton($('btnDatacardEdit'), false);
					}
					if (myCmd == 'getDisasterIdFromSerial') {
						disenabutton($('btnDatacardGotoPrev'), false);
						disenabutton($('btnDatacardGotoNext'), false);
						displayDatacardStatusMsg('msgDatacardFound');
					}
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
	jQuery('#dostat').html('');
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
	jQuery('#DisasterId').val();
	$('DICard').reset();
	jQuery('#_CMD').val('insertDICard');
	jQuery('#DisasterBeginTime0').val('');
	jQuery('#DisasterBeginTime1').val('');
	jQuery('#DisasterBeginTime2').val('');
	displayDatacardStatusMsg('msgDatacardFill');
	changeOptions('btnDatacardNew');
	jQuery('#divRecordNavigationInfo').hide();
}

function doDatacardEdit() {
	displayDatacardStatusMsg('');
	RegionId = jQuery('#desinventarRegionId').val();
	jQuery.post('cards.php',
		{'cmd'       : 'chklocked',
		 'r'         : jQuery('#desinventarRegionId').val(),
		'DisasterId' : jQuery('#DisasterId').val()
		},
		function(data) {
			if (data.DatacardStatus == 'RESERVED') {
				DisableEnableForm($('DICard'), false);
				jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
				jQuery('#DisasterBeginTime0').focus();
				jQuery('#_CMD').val('updateDICard');
				displayDatacardStatusMsg('msgDatacardFill');
				changeOptions('btnDatacardEdit');
				jQuery('.GeoLevelSelect').trigger({type : 'loadGeographyItems', ReadOnly : false});
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

	if (bContinue) {
		// Validate Record Status
		if (jQuery('#RecordStatus').val() == '') {
			displayDatacardStatusMsg('msgDatacardInvalidStatus');
			jQuery('#RecordStatus').highlight().focus();
			bContinue = false;
		}
	}
	
	if (bContinue) {
		// Validate Record Status
		if ( (jQuery('#RecordStatus').val() == 'PUBLISHED') ||
		     (jQuery('#RecordStatus').val() == 'TRASH') ||
		     (jQuery('#RecordStatus').val() == 'DELETED') ) {
			if (jQuery('#desinventarUserRoleValue').val() <= 2) {
				displayDatacardStatusMsg('msgDatacardInvalidStatus');
				jQuery('#RecordStatus').highlight().focus();
				bContinue = false;
			}
		}
	}
	
	if (bContinue) {
		// Validate GeographyId
		if (jQuery('#GeographyId').val() == '') {
			displayDatacardStatusMsg('msgDatacardInvalidGeography');
			jQuery('.GeoLevelSelect').highlight();
			jQuery('#GeoLevel0').focus();
			bContinue = false;
		}
	}
	
	// Use AJAX to save datacard
	if (bContinue) {
		jQuery.post('cards.php',
			{'cmd'            : 'existDisasterSerial',
			 'RegionId'       : jQuery('#desinventarRegionId').val(),
			 'DisasterSerial' : DisasterSerial
			},
			function(data) {
				bContinue = true;
				if ( (cmd == 'insertDICard') && (data.DisasterSerial != '') ) {
					// Serial of new datacard already exists...
					bContinue = false;
				}
				if (cmd == 'updateDICard') {
					if ( (DisasterSerial != PrevDisasterSerial) && (data.DisasterSerial != '') ) {
						// Edited Serial exists in database...
						bContinue = false;
					}
				}
				if (bContinue == false) {
					displayDatacardStatusMsg('msgDatacardDuplicatedSerial');
				}
				if (bContinue) {
					var fl = new Array('DisasterSerial', 'DisasterBeginTime0', 'DisasterSource', 
										'geolev0', 'EventId', 'CauseId');
					if (checkForm(fl, jQuery('#msgDatacardFieldsError').text())) {
						jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
						jQuery('#DICard').submit();
					} else {
						displayDatacardStatusMsg('msgDatacardFieldsError');
					}
				}
			},'json'
		);
	}
}

function doDatacardClear() {
	$('DICard').reset();
	jQuery('#lev0').html();
	displayDatacardStatusMsg('');
	jQuery('#divRecordNavigationInfo').hide();
	disenabutton($('btnDatacardGotoPrev'), true);
	disenabutton($('btnDatacardGotoNext'), true);
	jQuery('#DisasterBeginTime0').focus();
}

function doDatacardCancel() {
	if (jQuery('#DisasterId').val() != '') {
		jQuery.post('cards.php',
			{'cmd'        : 'chkrelease',
			 'r'          : jQuery('#desinventarRegionId').val(),
			 'DisasterId' : jQuery('#DisasterId').val()
			},
			function(data) {
				DisableEnableForm($('DICard'), true);
				changeOptions('btnDatacardCancel');
				// clear Help text area
				showtip('','#ffffff');
				if (jQuery('#DisasterId').val() != '') {
					valid = setDICardFromId(jQuery('#desinventarRegionId').val(), jQuery('#DisasterId').val());
					
					if (jQuery('#desinventarUserRoleValue').val() >= 2) {
						disenabutton($('btnDatacardEdit'), false);
					}
					disenabutton($('btnDatacardGotoPrev'), false);
					disenabutton($('btnDatacardGotoNext'), false);
					displayDatacardStatusMsg('');
					//jQuery('#cardsRecordNumber').val(data.RecordNumber);
				}
			},
			'json'
		);
	} else {
		doDatacardClear();
		DisableEnableForm($('DICard'), true);
		changeOptions('btnDatacardCancel');
		// clear Help text area
		showtip('','#ffffff');
		displayDatacardStatusMsg('msgDatacardStartNew');
	}
}

function doDatacardGotoFirst() {
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data') {
		var RecordNumber = 1;
		var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
		jQuery('#cardsRecordNumber').val(RecordNumber);
		valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId);
	} else {
		bFound = requestDatacard('getDisasterIdFirst', jQuery('#DisasterId').val());
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoPrev'), true);
	disenabutton($('btnDatacardGotoNext'), false);
}

function doDatacardGotoLast() {
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data') {
		var RecordNumber = jQuery('#cardsRecordCount').val();
		var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
		jQuery('#cardsRecordNumber').val(RecordNumber);
		valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId);
	} else {
		bFound = requestDatacard('getDisasterIdLast', jQuery('#DisasterId').val());
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoPrev'), false);
	disenabutton($('btnDatacardGotoNext'), true);
}


function doDatacardGotoPrev() {
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data') {
		var RecordNumber = jQuery('#cardsRecordNumber').val();
		if (RecordNumber > 1) {
			RecordNumber--;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			jQuery('#cardsRecordNumber').val(RecordNumber);
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId);
		}
	} else {
		bFound = requestDatacard('getDisasterIdPrev', jQuery('#cardsRecordNumber').val());
		if (bFound == false) {
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
		disenabutton($('btnDatacardEdit'), false);
	}
	disenabutton($('btnDatacardGotoNext'), false);
}

function doDatacardGotoNext() {
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data') {
		var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
		if (RecordNumber < jQuery('#cardsRecordCount').val() ) {
			RecordNumber = RecordNumber + 1;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			jQuery('#cardsRecordNumber').val(RecordNumber);
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId);
		}
	} else {
		bFound = requestDatacard('getDisasterIdNext', jQuery('#cardsRecordNumber').val());
		if (bFound == false) {
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
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


// SET DATACARD FORM
function setElementValue(formElement, value) {
	switch(formElement.type) {
		case 'undefined': return;
		case 'radio': formElement.checked = value; break;
		case 'checkbox': formElement.checked = value; break;
		case 'select-one': 
			var unk = true;
			for (var w=0; w < formElement.length; w++) {
				if (formElement.options[w].value == value) {
					formElement.selectedIndex = w ;
					unk = false;
				}
			}
			if (unk)
				formElement[3]=new Option(value, value, false, true);
		break;
		case 'select-multiple':
			for(var x=0; x < formElement.length; x++)
				formElement[x].selected = value[x];
		break;
		default: 
			formElement.value = value; 
		break;
	}
}

function setDICardFromId(prmRegionId, prmDisasterId) {
	jQuery.post('cards.php',
		{'cmd' : 'getDatacard',
		 'RegionId' : prmRegionId,
		 'DisasterId' : prmDisasterId
		},
		function(data) {
			setDICard(prmRegionId, data);
			jQuery('#divRecordNavigationInfo').hide();
			if (jQuery('#cardsRecordNumber').val() > 0) {
				jQuery('#divRecordNavigationInfo').show();
				jQuery('#RecordNumber').text(jQuery('#cardsRecordNumber').val());
				jQuery('#RecordCount').text(jQuery('#cardsRecordCount').val());
			}
			return true;
		},
		'json'
	);
	return false;
}


function setDICard(prmRegionId, arr) {
	var diform = null;
	var myForm = null;
	diform = $('DICard');
	myForm = jQuery(diform);
	
	var objElems = diform.elements; // DICard is DesInventar form..
	for (i=0; i < objElems.length; i++) {
		if ( (objElems[i].id == 'GeoLevel0') ||
			 (objElems[i].id == 'GeoLevel1') ||
			 (objElems[i].id == 'GeoLevel2') ) {
		} else {
			varName = jQuery(myForm).find('#' + objElems[i].id).attr('name');
			setElementValue(objElems[i], arr[varName]);
		}
	}
	
	jQuery(myForm).find('#PrevDisasterSerial').val(jQuery(myForm).find('#DisasterSerial').val());

	//Set GeographyItem info into hidden fields
	jQuery(myForm).find('.GeographyItemInfo').text('');
	jQuery(arr['GeographyItems']).each(function(key, value) {
		jQuery(myForm).find('#GeographyItemId' + key).text(value['GeographyId']);
		jQuery(myForm).find('#GeographyItemValue' + key).text(value['GeographyName']);
	});
	
	// Load Select Boxes with Geography Info
	jQuery(myForm).find('.GeoLevelSelect').trigger({type: 'loadGeographyItems', ReadOnly: true});
	
	// Enable Edit Button according to Role
	if (jQuery('#desinventarUserRoleValue').val() >= 2) {
		disenabutton($('btnDatacardEdit'), false);
	}
}

