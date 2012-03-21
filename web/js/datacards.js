/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatacards()
{
	jQuery('#divDatacardWindow').hide();

	jQuery('div.Datacard').on('cmdInitialize', function() {
		doDatacardInitialize();
		return false;
	});

	// Limit length of text area fields using a maxlength attribute...	
	jQuery('#DICard textarea').keyup(function() {
		var maxlength = parseInt(jQuery(this).attr('maxlength'));
		if (! isNaN(maxlength) ) {
			var value     = jQuery(this).val();
			if (value.length > maxlength) {
				jQuery(this).val(value.substr(0,maxlength));
			}
		}
	});

	jQuery('#DICard').unbind('submit').submit(function() {
		jQuery('#DatacardCommand').val(jQuery('#_CMD').val());
		jQuery('#RecordAuthor').val(jQuery('#desinventarUserId').val());
		displayDatacardStatusMsg('');
		var params = jQuery(this).serializeObject();
		jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
			jQuery.extend(params, { RegionId : jQuery('#desinventarRegionId').val() }),
			function(data)
			{
				if (data.Status == 'OK')
				{
					jQuery('#DisasterId').val(data.DisasterId);
					jQuery('#RecordSerial').text(data.RecordSerial);
					jQuery('#RecordPublished').text(data.RecordPublished);
					jQuery('#RecordReady').text(data.RecordReady);
					switch (data.StatusCode)
					{
						case 'INSERTOK':
							displayDatacardStatusMsg('msgDatacardInsertOk');
							jQuery('#cardsRecordSource').val('');
							jQuery('#cardsRecordCount').val(data.RecordCount);
							jQuery('#cardsRecordNumber').val(data.RecordCount);
							jQuery('#divRecordStat').show();
						break;
						case 'UPDATEOK':
							displayDatacardStatusMsg('msgDatacardUpdateOk');
							jQuery('#divRecordStat').show();
						break;
					} //switch
					DisableEnableForm($('DICard'), true);
					changeOptions('btnDatacardSave');
					if (parseInt(jQuery('#cardsRecordNumber').val()) > 0)
					{
						jQuery('#RecordNumber').text(jQuery('#cardsRecordNumber').val());
						jQuery('#RecordCount').text(jQuery('#cardsRecordCount').val());
						jQuery('#divRecordNavigationInfo').show();
					}
				}
				else
				{
					switch(data.ErrorCode)
					{
						case -10:
						case -52:
							displayDatacardStatusMsg('msgDatacardNetworkError');
						break;
						case -54:
							displayDatacardStatusMsg('msgDatacardDuplicatedSerial');
						break;
						case -61:
							displayDatacardStatusMsg('msgDatacardWithoutEffects');
						break;
						case -62:
							displayDatacardStatusMsg('msgDatacardOutsideOfPeriod');
						break;
						default:
							jQuery('#msgDatacardCustom').text(data.StatusMsg);
							displayDatacardStatusMsg('msgDatacardCustom');
						break;
					}
				}
				showtip('','#ffffff');
			},
			'json'
		);
		return false;
	});
	
	// Process combobox/input fields in effects
	jQuery('.clsEffectNumeric').jec({
		maxLength: 15,
		focusONewOption: true,
		acceptedKeys : [48,49,50,51,52,53,54,55,56,57,58]
	}).blur(function() {
		if (jQuery(this).val() == '')
		{
			jQuery(this).val(0);
		}
	});

	// Enable/Disable related EffectSector fields based on value of other fields...	
	jQuery('.clsEffectDouble').blur(function(event) {
		var altField = jQuery(this).attr('altField');
		var value    = parseInt(jQuery(this).val());
		var field    = jQuery('#DICard #' + altField);
		if (value > 0)
		{
			field.attr('oldValue', field.val());
			field.val(-1);
		}
		else
		{
			if (value == 0)
			{
				if (field.attr('oldValue') == '')
				{
					field.attr('oldValue', 0);
				}
				field.val(field.attr('oldValue'));
				
			}
		}
	});

	// Enable loading of geographic levels when editing...
	jQuery('#divDatacard .tblGeography').on('change', '.GeoLevelSelect', function() {
		var GeographyLevel = parseInt(jQuery(this).data('GeographyLevel'));
		var NextGeographyLevel = GeographyLevel + 1;
		var myGeographyId = jQuery(this).val();
		var GeographyParentId = myGeographyId.substr(0, myGeographyId.length - 5);
		var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;

		// Clear values of following sublevels
		for(var i = NextGeographyLevel; i < GeoLevelCount; i++)
		{
			var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
			mySelect.empty();
			mySelect.append(jQuery('<option>', { value : '' }).text(''));
			mySelect.disable();
		}

		if (jQuery(this).val() == '')
		{
			var PrevGeographyLevel = GeographyLevel - 1;
			myGeographyId = '';
			if (PrevGeographyLevel >= 0)
			{
				myGeographyId = jQuery('#divDatacard .tblGeography #GeoLevel' + PrevGeographyLevel).val();
				jQuery('#divDatacard #GeographyId').val(myGeographyId);
			}
		}
		else
		{
			jQuery('#divDatacard #GeographyId').val(myGeographyId);
			if (NextGeographyLevel < GeoLevelCount)
			{
				updateGeoLevelSelect(jQuery(this).val(), true);
			}
		}
		jQuery(this).focus();
	});	

	// Validation of DisasterBeginTime and Suggest Serial for New Datacards
	jQuery('#DisasterBeginTime0').blur(function() {
		cmd = jQuery('#_CMD').val();
		if (cmd == 'insertDICard')
		{
			doDatacardSuggestSerial();
		}
	});

	jQuery('#DisasterBeginTime1').blur(function() {
		if (jQuery(this).val() == '' ||
		    parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 12 )
		{
			jQuery(this).val('');
			jQuery('#DisasterBeginTime2').val('');
		}
	});

	jQuery('#DisasterBeginTime2').blur(function() {
		if (parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 31 )
		{
			jQuery(this).val('');
		}
	});

	// Button for suggesting serial of datacard or restoring initial Serial when editing...
	jQuery('#linkDatacardSuggestSerial').unbind('click').click(function() {
		if (jQuery('#DICard #Status').val() == 'NEW')
		{
			doDatacardSuggestSerial();
		}
		if (jQuery('#DICard #Status').val() == 'EDIT')
		{
			jQuery('#DisasterSerial').val(jQuery('#PrevDisasterSerial').val());
		}
	});

	// Apply some validation for several types of input fields
	jQuery('.inputInteger').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'integer:' + jQuery(this).attr('MaxLength'));
	});

	jQuery('.inputDouble').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'double:' + jQuery(this).attr('MaxLength'));
	}).blur(function() {
		/*
		var answer = validateInputDouble(jQuery(this).val());
		if (answer > 0)
		{
			jQuery(this).unhighlight();
		}
		else
		{
			jQuery(this).highlight();
			jQuery(this).focus();
		}
		*/
		return false;
	});

	jQuery('.inputText').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'text:');
	});

	jQuery('.inputAlphaNumber').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'alphanumber:');
	});
	
	// Datacard New/Edit/Save Commands
	jQuery('#btnDatacardNew').click(function() {
		doDatacardClear();
		doDatacardNew();
		jQuery('#txtDatacardFind').val('');
		jQuery('#GeographyId').val('');
		jQuery('#DisasterId').val(Math.uuid());
		return false;
	});
	
	jQuery('#btnDatacardEdit').click(function() {
		jQuery('#txtDatacardFind').val('');
		doDatacardEdit();
		return false;
	});
	
	jQuery('#btnDatacardSave').click(function() {
		doDatacardSave();
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
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoFirst();
		return false;
	});

	jQuery('#btnDatacardGotoLast').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoLast();
		return false;
	});
	
	jQuery('#btnDatacardGotoPrev').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoPrev();
		return false;
	});
	
	jQuery('#btnDatacardGotoNext').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoNext();
		return false;
	});

	// Datatacard Find 
	jQuery('#txtDatacardFind').keydown(function(event) {
		if(event.keyCode == 13)
		{
			doDatacardFind();
		}
	});	
	
	jQuery('#btnDatacardFind').click(function() {
		doDatacardFind();
	});

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

	jQuery('#divDatacard .EventId').on('mouseenter', 'option', function() {
		showtip(jQuery(this).data('tooltip'), 'lightblue');
	});
	jQuery('#divDatacard .EventId').mouseleave(function() {
		showtip('', '#fff');
	});

	jQuery('#divDatacard .CauseId').on('mouseenter', 'option', function() {
		showtip(jQuery(this).data('tooltip'), '#ffffc0');
	});
	jQuery('#divDatacard .CauseId').mouseleave(function() {
		showtip('', '#fff');
	});

	// Dependency between fields
	jQuery('#DICard').on('blur','#EffectRoads', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorTransport').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectFarmingAndForest', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorAgricultural').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectLiveStock', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorAgricultural').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectEducationCenters', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorEducation').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectMedicalCenters', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorHealth').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectOtherLosses', function() {
		if (jQuery.trim(jQuery(this).val()) != '')
		{
			jQuery('#DICard #SectorOther').val(-1);
		}
	});

	// Attach events to main body
	jQuery('body').on('cmdDatacardShow', function() {
		doDatacardShow();
	});

	jQuery('body').on('cmdDatacardGoto', function(event, prmDisasterId, prmRecordNumber, prmRecordCount) {
		setDICardFromId(jQuery('#desinventarRegionId').val(), prmDisasterId, prmRecordNumber, prmRecordCount);
	});
	//Initialize components
	jQuery('#divDatacard .tblGeography tr:first').hide();
	jQuery('div.Datacard table.EffectList').on('focus','select.value', function(event) {
		showtip(jQuery(this).data('helptext'), '#f1bd41');
	}).on('focus','input.value', function(event) {
		showtip(jQuery(this).data('helptext'), '#f1bd41');
	});
} //onReadyDatacards()

function doDatacardInitialize()
{
	// Load EffectPeople List (ef1)
	var effect_list = jQuery('div.Datacard table.EffectListPeople');
	effect_list.find('tr:gt(0)').remove();
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		var clone = jQuery('tr:last', effect_list).clone().show();
		jQuery('span.label'  , clone).text(label);
		jQuery('span.label'  , clone).attr('title', jQuery('span.tooltip', this).text());
		jQuery('select.value', clone).attr('id', field);
		jQuery('select.value', clone).attr('name', field);
		jQuery('select.value', clone).data('helptext', jQuery('span.helptext', this).text());
		effect_list.append(clone);
	});

	// Load EffectSector List (sec)
	var effect_list = jQuery('div.Datacard table.EffectListSector');
	effect_list.find('tr:gt(0)').remove();
	jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		var clone = jQuery('tr:last', effect_list).clone().show();
		jQuery('span.label'  , clone).text(label);
		jQuery('span.label'  , clone).attr('title', jQuery('span.tooltip', this).text());
		jQuery('select.value', clone).attr('id', field);
		jQuery('select.value', clone).attr('name', field);
		jQuery('select.value', clone).data('helptext', jQuery('span.helptext', this).text());
		effect_list.append(clone);
	});

	// Load EffectLosses List (ef2)
	var effect_list = jQuery('div.Datacard table.EffectListLosses1');
	effect_list.find('tr:gt(0)').remove();
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		var clone = jQuery('tr:last', effect_list).clone().show();
		jQuery('span.label'  , clone).text(label);
		jQuery('span.label'  , clone).attr('title', jQuery('span.tooltip', this).text());
		jQuery('input.value', clone).attr('id', field);
		jQuery('input.value', clone).attr('name', field);
		jQuery('input.value', clone).data('helptext', jQuery('span.helptext', this).text());
		effect_list.append(clone);
	});
	// EffectLosses2 List (ef3)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
		var fieldname = jQuery(this).data('field');
		var field = jQuery('div.Datacard table.EffectListLosses2 tr.' + fieldname);
		jQuery('span.label' , field).text(jQuery('span.label', this).text());
		jQuery('input.value', field).data('helptext', jQuery('span.helptext',this).text());
	});	
	// EffectOther List (ef4)
	jQuery('div.desinventarInfo div.EffectList div.EffectOther').each(function() {
		var fieldname = jQuery(this).data('field');
		var field = jQuery('div.Datacard table.EffectListOther tr.' + fieldname);
		jQuery('span.label' , field).text(jQuery('span.label', this).text());
		jQuery('span.label' , field).attr('title', jQuery('span.tooltip', this).text());
		jQuery('input.value', field).data('helptext', jQuery('span.helptext',this).text());
	});
	
	// Additional Effect List (EEFieldList);	
	var effect_list = jQuery('div.Datacard table.EffectListAdditional');
	effect_list.find('div.EffectAdditional:gt(0)').remove();
	var column = 0;
	var max_column = 3;
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		var field = key;
		var label = value[0];
		var tooltip = value[1];
		var type = value[2];

		var clone = jQuery('div.EffectAdditional:last', effect_list).clone().show();
		jQuery('span.label' , clone).text(label);
		jQuery('span.label' , clone).attr('title', tooltip);
		jQuery('input.value',clone).hide();
		var className='inputText';
		switch(type)
		{
			case 'INTEGER':
				className='inputInteger';
			break;
			case 'CURRENCY':
			case 'DOUBLE':
				className='inputDouble';
			break;
			default:
				className='inputText';
			break;			
		}
		jQuery('input', clone).attr('id','').attr('name','');
		jQuery('input.' + className, clone).show();
		jQuery('input.' + className, clone).attr('id', field);
		jQuery('input.' + className, clone).attr('name', field);
		jQuery('input.' + className, clone).data('helptext', tooltip);
		column = (column + 1) % max_column;
		jQuery('tr:last td:eq(' + column + ')',effect_list).append(clone);
	});
} //doDatacardInitialize()

function updateGeoLevelSelect(prmGeographyId, prmWithChilds)
{
	var GeographyList = jQuery('body').data('GeographyList-' + prmGeographyId);
				
	if (GeographyList === undefined)
	{
		// Load GeographyList using POST
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				'cmd'         : 'cmdGeographyGetItemsById',
				'RegionId'    : jQuery('#desinventarRegionId').val(),
				'GeographyId' : prmGeographyId
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					jQuery.each(data.GeographyList, function(key, value) {
						// Store result for later use from cache
						var NextGeographyLevel = parseInt(key.length)/5;
						jQuery('body').data('GeographyList-' + key, value);
						doUpdateGeoLevelSelect(NextGeographyLevel, value);
					});
				}
			},
			'json'
		);
	}
	else
	{
		// Enable sublevels and reuse data from local cache
		var GeoLevelCount = prmGeographyId.length/5 + 1;
		for(var GeographyLevel = 1; GeographyLevel < GeoLevelCount; GeographyLevel++)
		{
			var GeographyParent = prmGeographyId.substr(0, GeographyLevel * 5);
			var myGeographyList = jQuery('body').data('GeographyList-' + GeographyParent);
			doUpdateGeoLevelSelect(GeographyLevel, myGeographyList);
		}
	}
}

function doUpdateGeoLevelSelect(prmGeographyLevel, prmGeographyList)
{
	var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + prmGeographyLevel);
	var myPrevValue = mySelect.val();
	mySelect.empty();
	mySelect.append(jQuery('<option>', { value : '' }).text(''));
	jQuery.each(prmGeographyList, function(index, value) {
		mySelect.append(jQuery('<option>', { value : value.GeographyId }).text(value.GeographyName));
	});
	mySelect.val(myPrevValue);
	if (myPrevValue != '')
	{
		myGeographyId = myPrevValue;
	}
	mySelect.enable();
} //doUpdateGeoLevelSelect()

function doDatacardShow()
{
	//if (jQuery('#divDatacard').is(':hidden'))
	{
		//GeoLevel
		jQuery('#divDatacard .tblGeography tr:gt(0)').remove();
		jQuery('#divDatacard .tblGeography tr:first').hide();
		var GeolevelsList = jQuery('body').data('GeolevelsList');
		if (GeolevelsList == undefined)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdDatabaseLoadData',
					RegionId : jQuery('#desinventarRegionId').val()
				},
				function(data)
				{
					jQuery('body').data('GeolevelsList', data.GeolevelsList);
					jQuery('body').data('EventList', data.EventList);
					jQuery('body').data('CauseList', data.CauseList);
					jQuery('body').data('RecordCount', data.RecordCount);
					var dataItems = jQuery('body').data();
					jQuery.each(dataItems, function(index, value) {
						if (index.substr(0,13) === 'GeographyList')
						{
							jQuery('body').removeData(index);
						}
					});
					jQuery('body').data('GeographyList', data.GeographyList);
					doDatacardUpdateDisplay();
				},
				'json'
			);
		}
		else
		{
			doDatacardUpdateDisplay();
		}
	}
	var UserRoleValue = jQuery('#desinventarUserRoleValue').val();
	if (UserRoleValue <= 2)
	{
		jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').disable();
		jQuery('#DICard select.RecordStatus option[value="DELETED"]').disable();
	}
	else
	{
		jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').enable();
		jQuery('#DICard select.RecordStatus option[value="DELETED"]').enable();
	}
} //doDatacardShow()

function doDatacardUpdateDisplay()
{	
	var GeolevelsList = jQuery('body').data('GeolevelsList');
	if (GeolevelsList != undefined)
	{
		jQuery.each(GeolevelsList, function(index, value) {
			var clonedRow = jQuery('#divDatacard .tblGeography tr:last').clone().show();
			jQuery('.GeoLevelId', clonedRow).text(index);
			jQuery('.GeoLevelName', clonedRow).text(value.GeoLevelName);
			jQuery('select', clonedRow).attr('id', 'GeoLevel' + index).attr('level', index).data('GeographyLevel', index);
			jQuery('.tblGeography').append(clonedRow);
		});
	}

	var FirstRow = jQuery('#divDatacard .tblGeography select:data("GeographyLevel=0")');
	FirstRow.empty();
	FirstRow.append(jQuery('<option>', { value : '' }).text(''));
	GeographyList = jQuery('body').data('GeographyList');
	if (GeographyList != undefined)
	{
		jQuery.each(GeographyList, function(index, value) {
			FirstRow.append(jQuery('<option>', { value : index }).text(value.GeographyName));
		});
	}

	jQuery('#divDatacard .EventId').empty();
	jQuery('#divDatacard .EventId').append(jQuery('<option>', { value : '' }).text(''));
	EventList = jQuery('body').data('EventList');
	if (EventList != undefined)
	{
		jQuery.each(EventList, function(index, value) {
			jQuery('#divDatacard .EventId').append(jQuery('<option>', { value : index }).text(value.EventName).data('tooltip',value.EventDesc));
		});
	}

	jQuery('#divDatacard .CauseId').empty();
	jQuery('#divDatacard .CauseId').append(jQuery('<option>', { value : '' }).text(''));
	CauseList = jQuery('body').data('CauseList');
	if (CauseList != undefined)
	{
		jQuery.each(CauseList, function(index, value) {
			jQuery('#divDatacard .CauseId').append(jQuery('<option>', { value : index }).text(value.CauseName).data('tooltip',value.CauseDesc));
		});
	}

	jQuery('#divDatacard #cardsRecordNumber').val(0);
	jQuery('#divDatacard #cardsRecordCount').val(jQuery('body').data('RecordCount'));

	// Initialize controls in form when it is displayed
	// Reset buttons
	doDatacardClear();
	// Hide StatusMessages
	displayDatacardStatusMsg('');
	jQuery('#divDatacardStatusMsg').show();
	// Hide window's parameters
	jQuery('#divDatacardParameter').hide();
	jQuery('#divRecordNavigationInfo').hide();
	
	DisableEnableForm($('DICard'), true);
	changeOptions();

	// Start with Basic Effects show
	jQuery('#linkDatacardShowEffectsBasic').trigger('click');
	
	//Show Command Buttons only for Role>=USER
	jQuery('.DatacardCmdButton').hide();
	jQuery('#btnDatacardPrint').show();
	if (parseInt(jQuery('#desinventarUserRoleValue').val()) >= 2)
	{
		jQuery('.DatacardCmdButton').show();
		displayDatacardStatusMsg('msgDatacardStartNew');
	}
	doDatacardNavButtonsEnable();

	var w = Ext.getCmp('wndDatacard');
	if (w != undefined)
	{
		w.show();
	}
} //doDatacardUpdateDisplay();

function displayDatacardStatusMsg(msgId)
{
	// First hide all items
	jQuery('.datacardStatusMsg').hide();
	// Show a specific message
	if (msgId != '')
	{
		jQuery('#' + msgId).show();
	}
}

var mod = "di";

function DisableEnableForm(xForm, disab)
{
	if (xForm != null)
	{
		objElems = xForm.elements;
		var myname = '';
		var mysty = '';
		if (disab)
		{
			col = '#eee';
		}
		else
		{
			col = '#fff';
		}
		for (i=0; i < objElems.length; i++)
		{
			myname = objElems[i].name + "";
			if (myname.substring(0,1) != "_")
			{
				objElems[i].disabled = disab;
				objElems[i].style.backgroundColor = col;
			}
		}
	}
}

function disenabutton(butid, disab)
{
	if (disab)
	{
		if (butid != null)
		{
			butid.disable();
		}
		Element.removeClassName(butid, 'bb');
		Element.addClassName(butid, 'disabled');
	}
	else
	{
		if (butid != null)
		{
			butid.enable();
		}
		Element.addClassName(butid, 'bb');
		Element.removeClassName(butid, 'disabled');
	}
}

function doDatacardNavButtonsDisable()
{
	disenabutton($('btnDatacardGotoFirst'), true);
	disenabutton($('btnDatacardGotoPrev'), true);
	disenabutton($('btnDatacardGotoNext'), true);
	disenabutton($('btnDatacardGotoLast'), true);
}

function doDatacardNavButtonsEnable()
{
	var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
	var RecordCount  = parseInt(jQuery('#cardsRecordCount').val());
	if (RecordNumber > 0)
	{
		if (RecordNumber > 1)
		{
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoPrev'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoPrev'), true);
		}
		if (RecordNumber < RecordCount)
		{
			disenabutton($('btnDatacardGotoLast'), false);
			disenabutton($('btnDatacardGotoNext'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoLast'), true);
			disenabutton($('btnDatacardGotoNext'), true);
		}
	}
	else
	{
		disenabutton($('btnDatacardGotoPrev'), true);
		disenabutton($('btnDatacardGotoNext'), true);
		if (RecordCount > 0)
		{
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoLast'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoLast'), true);
		}
	}
}

function changeOptions(but)
{
	switch (but)
	{
		case "btnDatacardNew":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), false);
			doDatacardNavButtonsDisable();
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardEdit":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), false);
			doDatacardNavButtonsDisable();
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardSave":
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), false);
			disenabutton($('btnDatacardCancel'), true);
			doDatacardNavButtonsEnable();
			disenabutton($('btnDatacardFind'), false);
		break;
		case "btnDatacardCancel":
			if ($('DisasterId').value == '')
			{
				disenabutton($('btnDatacardEdit'), true);
			}
			else
			{
				disenabutton($('btnDatacardEdit'), false);
			}
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardCancel'), true);
			disenabutton($('btnDatacardNew'), false);
			doDatacardNavButtonsEnable();
			disenabutton($('btnDatacardFind'), false);
		break;
		default:
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), true);
		break;
	}
}

function requestDatacard(myCmd, myValue)
{
	var bReturn = true;
	var RegionId=jQuery('#desinventarRegionId').val();
	jQuery('#dostat').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif' + '" alt="" />');
	jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
		{
			cmd:myCmd,
			value:myValue,
			r:RegionId
		},
		function(data)
		{
			jQuery('#dostat').html('');
			if (myCmd == 'getNextSerial')
			{
				if (data.DisasterSerial.length >= 5)
				{
					jQuery('#DisasterSerial').val(myValue + '-' + data.DisasterSerial);
				}
			}
			else if (data.Status == 'OK')
			{
				displayDatacardStatusMsg('');
				if (data.DisasterId != '')
				{
					jQuery('#cardsRecordSource').val('');
					valid = setDICardFromId(RegionId, data.DisasterId, data.RecordNumber, data.RecordCount);
					if (jQuery('#desinventarUserRoleValue').val() >= 2)
					{
						disenabutton($('btnDatacardEdit'), false);
					}
					if (myCmd == 'getDisasterIdFromSerial')
					{
						displayDatacardStatusMsg('msgDatacardFound');
					}
				}
				else
				{
					displayDatacardStatusMsg('msgDatacardNotFound');
					bReturn = false;
				}
			}
			else
			{
				bReturn = false;
			}
		},
		'json'
	);
	jQuery('#dostat').html('');
	return bReturn;
}


function doDatacardFind()
{
	if(jQuery('#txtDatacardFind').val() !='')
	{
		requestDatacard('getDisasterIdFromSerial', jQuery('#txtDatacardFind').val());
	}
}

function doDatacardClear()
{
	jQuery('#DisasterId').val();
	$('DICard').reset();
	jQuery('#_CMD').val('insertDICard');
	jQuery('#cardsRecordNumber').val(0);
	jQuery('#DICard .clsEffectNumeric').each(function() {
		jQuery(this).jecValue('');
		jQuery(this).val(0);
	});
	jQuery('#DICard .inputDouble').each(function() {
		jQuery(this).val(0);
	});
	jQuery('#DICard .inputInteger').each(function() {
		jQuery(this).val(0);
	});
	jQuery('#DICard #DisasterBeginTime0').val('');
	jQuery('#DICard #DisasterBeginTime1').val('');
	jQuery('#DICard #DisasterBeginTime2').val('');
	jQuery('#DICard #EventDuration').val(0);
}

function doDatacardNew()
{

	DisableEnableForm($('DICard'), false);
	jQuery('#DisasterBeginTime0').focus();
	displayDatacardStatusMsg('msgDatacardFill');
	changeOptions('btnDatacardNew');
	jQuery('#divRecordNavigationInfo').hide();
	jQuery('#DICard #Status').val('NEW');

	// Clear values of following sublevels
	var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
	for(var i = 1; i < GeoLevelCount; i++)
	{
		var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
		mySelect.empty();
		mySelect.append(jQuery('<option>', { value : '' }).text(''));
		mySelect.disable();
	}
}

function doDatacardEdit()
{
	displayDatacardStatusMsg('');
	var RegionId = jQuery('#desinventarRegionId').val();
	jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
		{
			'cmd'        : 'chklocked',
			'r'          : jQuery('#desinventarRegionId').val(),
			'DisasterId' : jQuery('#DisasterId').val()
		},
		function(data)
		{
			if (data.DatacardStatus == 'RESERVED')
			{
				DisableEnableForm($('DICard'), false);
				jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
				jQuery('#DisasterBeginTime0').focus();
				jQuery('#_CMD').val('updateDICard');
				displayDatacardStatusMsg('msgDatacardFill');
				changeOptions('btnDatacardEdit');

				// Clear values of following sublevels
				var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
				for(var i = 1; i < GeoLevelCount; i++)
				{
					var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
					mySelect.disable();
				}
				updateGeoLevelSelect(jQuery('#DICard #GeographyId').val(), true);
				jQuery('#DICard #Status').val('EDIT');
			}
			else
			{
				displayDatacardStatusMsg('msgDatacardIsLocked');
			}
		},
		'json'
	);
} //doDatacardEdit()

function doDatacardSave()
{
	var bContinue = 1;
	var cmd = jQuery('#_CMD').val();
	var DisasterSerial = jQuery('#DisasterSerial').val();
	var PrevDisasterSerial = jQuery('#PrevDisasterSerial').val();
	var Status = jQuery('#DICard #Status').val();

	if (bContinue > 0)
	{
		var error_count = 0;
		var answer = 1;
		jQuery('#DICard .inputDouble').each(function() {
			answer = validateInputDouble(jQuery(this).val());
			if (answer > 0)
			{
				if (jQuery(this).attr('old-bg-color') != '') {
					jQuery(this).unhighlight();
				}
			}
			else
			{
				jQuery(this).highlight();
				error_count++;
			}
		});
		if (error_count > 0)
		{
			bContinue = 0;
			displayDatacardStatusMsg('msgDatacardInvalidNumber');
		}
	}	

	if (bContinue > 0)
	{
		// Validate Record Status
		if (jQuery('#DICard #RecordStatus').val() == '')
		{
			displayDatacardStatusMsg('msgDatacardWithoutStatus');
			jQuery('#DICard #RecordStatus').highlight().focus();
			bContinue = 0;
		}
	}
	
	if (bContinue > 0)
	{
		if (jQuery('#DICard #RecordStatus').val() == 'PUBLISHED')
		{
			jQuery('#DICard #DisasterSource').unhighlight();
			jQuery('#DICard #RecordStatus').unhighlight();
			var DisasterSource = jQuery('#DICard #DisasterSource').val();
			DisasterSource = jQuery.trim(DisasterSource);
			if (DisasterSource == '')
			{
				displayDatacardStatusMsg('msgDatacardWithoutSource');
				jQuery('#DICard #DisasterSource').highlight().focus();
				jQuery('#DICard #RecordStatus').highlight();
				bContinue = 0;
			}
		}
	}
	
	if (bContinue > 0)
	{
		// Validate Record Status
		if ( (jQuery('#DICard #RecordStatus').val() == 'PUBLISHED') ||
		     (jQuery('#DICard #RecordStatus').val() == 'DELETED'  ) )
		{
			if (jQuery('#desinventarUserRoleValue').val() <= 2)
			{
				displayDatacardStatusMsg('msgDatacardInvalidStatus');
				jQuery('#DICard #RecordStatus').highlight().focus();
				bContinue = 0;
			}
		}
	}
	
	if ( (bContinue > 0) && (jQuery('#GeographyId').val() == '') )
	{
		displayDatacardStatusMsg('msgDatacardInvalidGeography');
		jQuery('.GeoLevelSelect').highlight();
		jQuery('#GeoLevel0').focus();
		bContinue = 0;
	}

	jQuery('#DICard #EventId').unhighlight();
	if ( (bContinue > 0) && (jQuery('#DICard #EventId').val() == '') )
	{
		jQuery('#DICard #EventId').highlight().focus();
		bContinue = 0;
	}

	jQuery('#DICard #CauseId').unhighlight();
	if ( (bContinue > 0) && (jQuery('#DICard #CauseId').val() == '') )
	{
		jQuery('#DICard #CauseId').highlight().focus();
		bContinue = 0;
	}

	// Use AJAX to save datacard
	if (bContinue > 0)
	{
		if (jQuery('#DICard #Status').val() == 'SAVING')
		{
			// Do Nothing.. already saving datacard...
		}
		else
		{
			jQuery('#DICard #Status').val('SAVING');
			jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
				{
					'cmd'            : 'existDisasterSerial',
					'RegionId'       : jQuery('#desinventarRegionId').val(),
					'DisasterSerial' : DisasterSerial
				},
				function(data)
				{
					bContinue = 1;
					if ( (cmd == 'insertDICard') && (data.DisasterSerial != '') )
					{
						// Serial of new datacard already exists...
						bContinue = 0;
					}
					if (cmd == 'updateDICard')
					{
						if ( (DisasterSerial != PrevDisasterSerial) && (data.DisasterSerial != '') )
						{
							// Edited Serial exists in database...
							bContinue = 0;
						}
					}
					if (bContinue < 1)
					{
						displayDatacardStatusMsg('msgDatacardDuplicatedSerial');
						jQuery('#DICard #Status').val(Status);
						jQuery('#DICard #DisasterSerial').highlight().focus();
					}
					if (bContinue > 0)
					{
						//'DisasterSource', 
						var fl = new Array('DisasterSerial', 'DisasterBeginTime0', 
											'GeoLevel0', 'EventId', 'CauseId');
						if (checkForm('DICard', fl, jQuery('#msgDatacardFieldsError').text()))
						{
							jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
							jQuery('#DICard').submit();
						}
						else
						{
							displayDatacardStatusMsg('msgDatacardFieldsError');
						}
						jQuery('#DICard #Status').val('VIEW');
					}
				},
				'json'
			);
		}
	}
} //doDatacardSave()

function doDatacardCancel()
{
	if (jQuery('#DICard #Status').val() == 'EDIT')
	{
		jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
			{
				'cmd'        : 'chkrelease',
				'r'          : jQuery('#desinventarRegionId').val(),
				'DisasterId' : jQuery('#DisasterId').val()
			},
			function(data)
			{
				DisableEnableForm($('DICard'), true);
				changeOptions('btnDatacardCancel');
				// clear Help text area
				showtip('','#ffffff');

				valid = setDICardFromId(jQuery('#desinventarRegionId').val(), jQuery('#DisasterId').val(), jQuery('#cardsRecordNumber').val(), jQuery('#cardsRecordCount').val());
				if (jQuery('#desinventarUserRoleValue').val() >= 2)
				{
					disenabutton($('btnDatacardEdit'), false);
				}
				displayDatacardStatusMsg('');
				doDatacardNavButtonsEnable();
				jQuery('#DICard #Status').val('VIEW');
			},
			'json'
		);
	}
	else
	{
		$('DICard').reset();
		DisableEnableForm($('DICard'), true);
		changeOptions('btnDatacardCancel');
		// clear Help text area
		showtip('','#ffffff');
		displayDatacardStatusMsg('msgDatacardStartNew');
		doDatacardNavButtonsEnable();
		jQuery('#DICard #Status').val('');
	}
} //doDatacardCancel()

function doDatacardGotoFirst()
{
	displayDatacardStatusMsg('');
	bFound = requestDatacard('getDisasterIdFirst', jQuery('#DisasterId').val());
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoFirst()

function doDatacardGotoLast()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordCount = parseInt(jQuery('#cardsRecordCount').val());
		var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordCount + ']').attr('DisasterId');
		valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordCount, RecordCount);
	}
	else
	{
		bFound = requestDatacard('getDisasterIdLast', jQuery('#DisasterId').val());
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoLast()

function doDatacardGotoPrev()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
		if (RecordNumber > 1)
		{
			RecordNumber--;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordNumber, jQuery('#cardsRecordCount').val());
		}
	}
	else
	{
		bFound = requestDatacard('getDisasterIdPrev', jQuery('#cardsRecordNumber').val());
		if (bFound == false)
		{
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoPrev()

function doDatacardGotoNext()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
		if (RecordNumber < parseInt(jQuery('#cardsRecordCount').val()) )
		{
			RecordNumber = RecordNumber + 1;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordNumber, jQuery('#cardsRecordCount').val());
		}
	}
	else
	{
		bFound = requestDatacard('getDisasterIdNext', jQuery('#cardsRecordNumber').val());
		if (bFound == false)
		{
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoNext()

function doDatacardSuggestSerial()
{
	if (jQuery('#DisasterBeginTime0').val() != '')
	{
		requestDatacard('getNextSerial', jQuery('#DisasterBeginTime0').val());
	}
} //doDatacardSuggestSerial()

// SET DATACARD FORM
function setElementValue(formElement, value)
{
	switch(formElement.type)
	{
		case 'undefined': return;
		case 'radio': formElement.checked = value; break;
		case 'checkbox': formElement.checked = value; break;
		case 'select-one': 
			var unk = true;
			for (var w=0; w < formElement.length; w++)
			{
				if (formElement.options[w].value == value)
				{
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
} //setElementValue()

function setDICardFromId(prmRegionId, prmDisasterId, prmRecordNumber, prmRecordCount)
{
	jQuery('#cardsRecordNumber').val(prmRecordNumber);
	jQuery('#cardsRecordCount').val(prmRecordCount);
	jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
		{
			'cmd'       : 'getDatacard',
			'RegionId'  : prmRegionId,
			'DisasterId': prmDisasterId
		},
		function(data)
		{
			jQuery('#DICard .clsEffectNumeric').each(function() {
				jQuery(this).jecValue(data[jQuery(this).attr('id')]);
			});
			setDICard(prmRegionId, data);
			jQuery('#divRecordNavigationInfo').hide();
			var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
			var RecordCount  = parseInt(jQuery('#cardsRecordCount').val());
			if (RecordNumber > 0)
			{
				jQuery('#divRecordNavigationInfo').show();
				jQuery('#RecordNumber').text(RecordNumber);
				jQuery('#RecordCount').text(RecordCount);
			}
			doDatacardNavButtonsEnable();
			jQuery('#DICard #Status').val('VIEW');
			return true;
		},
		'json'
	);
	return false;
} //setDICardFromId()

function setDICard(prmRegionId, arr)
{
	var diform = null;
	var myForm = null;
	diform = $('DICard');
	myForm = jQuery('div.Datacard');
	
	var objElems = diform.elements; // DICard is DesInventar form..
	for (i=0; i < objElems.length; i++)
	{
		if ( (objElems[i].id == 'GeoLevel0') ||
			 (objElems[i].id == 'GeoLevel1') ||
			 (objElems[i].id == 'GeoLevel2') )
		{
		}
		else
		{
			if (objElems[i].id != '')
			{
				varName = jQuery(myForm).find('#' + objElems[i].id).attr('name');
				setElementValue(objElems[i], arr[varName]);
			}
		}
	}

	jQuery('#PrevDisasterSerial', myForm).val(jQuery('#DisasterSerial', myForm).val());

	//Set GeographyItem info into hidden fields
	jQuery('#divDatacard .tblGeography select:gt(1)').empty().disable();
	jQuery(arr['GeographyItems']).each(function(key, value) {
		var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + key);
		if (parseInt(key) == 0)
		{
			mySelect.val(value.GeographyId);
		}
		else			
		{
			mySelect.append(jQuery('<option>', { value : value.GeographyId }).text(value.GeographyName));
		}
	});
	
	// Enable Edit Button according to Role
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //setDICard

function validateInputDouble(prmValue)
{
	var answer = 1;
	var value = prmValue;
	if (isNaN(value))
	{
		answer = 0;
	}
	if (value == '')
	{
		answer = 0;
	}
	if (answer > 0)
	{
		if (occurrences(value, ',') > 0)
		{
			answer = 0;
		}
	}
	if (answer > 0)
	{
		if (occurrences(value, '.') > 1)
		{
			answer = 0;
		}
	}
	return(answer);
}

function occurrences(string, substring)
{
	var n=0;
	var pos=0;
	while(true)
	{
		pos=string.indexOf(substring,pos);
		if (pos!=-1)
		{
			n++;
			pos+=substring.length;
		}
		else
		{
			break;
		}
	}
	return(n);
}
