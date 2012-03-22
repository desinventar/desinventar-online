/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyStatParams()
{
	jQuery('#fldStatParam_FirstLev').change(function() {
		setTotalize('fldStatParam_FirstLev', 'fldStatParam_SecondLev');
		jQuery('#fldStatParam_ThirdLev').empty();
	});
	
	jQuery('#fldStatParam_SecondLev').change(function() {
		setTotalize('fldStatParam_SecondLev', 'fldStatParam_ThirdLev');
	});

	jQuery('body').on('cmdViewStdParams', function() {
		Ext.getCmp('wndViewStdParams').show();
		jQuery('#fldStatParam_FirstLev').trigger('change');
	});

	jQuery('div.ViewStatParams').on('cmdInitialize', function(event) {
		doViewStatParamsInitialize();
	});
} //onReadyStatParams()

function doViewStatParamsInitialize()
{
	var statlevel_list = jQuery('div.ViewStatParams select.StatlevelFirst');
	statlevel_list.find('option').remove();
	jQuery.each(jQuery('body').data('GeolevelsList'), function(key, value) {
		statlevel_list.append(jQuery('<option>', { value: value.GeoLevelId + '|D.GeographyId' }).text(value.GeoLevelName));
	});
	statlevel_list.append(jQuery('<option>', { value : '|D.EventId'}).text(jQuery('#ViewStatParamsLabelEvent').text()));
	statlevel_list.append(jQuery('<option>', { value : 'YEAR|D.DisasterBeginTime'}).text(jQuery('#ViewStatParamsLabelYear').text()));
	statlevel_list.append(jQuery('<option>', { value : 'MONTH|D.DisasterBeginTime'}).text(jQuery('#ViewStatParamsLabelMonth').text()));
	statlevel_list.append(jQuery('<option>', { value : '|D.CauseId'}).text(jQuery('#ViewStatParamsLabelCause').text()));
	statlevel_list.val(jQuery('option:first', statlevel_list).val());

	var field_list = jQuery('div.ViewStatParams select.FieldsAvailable');
	field_list.find('option').remove();
	// EffectPeople (ef1)
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(jQuery('#StatLabelAuxHave').text() + ' ' + label));
	});	
	// EffectLosses1 List (ef2)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});
	// EffectLosses2 List (ef3)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});	
	// EffectSector (sec)
	jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(jQuery('#StatLabelAuxAffect').text() + ' ' + label));
	});
	field_list.append(jQuery('<option>', { value: '', disabled:'disabled'}).text('---'));
	// EEFieldList
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		var field = key;
		var label = value[0];
		var type  = value[2];
		if ( (type == 'INTEGER') || (type == 'DOUBLE'))
		{
			field_list.append(jQuery('<option>', { value: 'E.' + field + '|>|-1' }).text(label));
		}
	});
	field_list.append(jQuery('<option>', { value : 'D.EventDuration|S|-1'}).text(jQuery('#StatLabelEventDuration').text()));

	var field_list = jQuery('div.ViewStatParams select.FieldsShow');
	field_list.find('option').remove();
	// EffectPeople (ef1)
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + 'Q|>|-1' }).text(label));
	});	
	
} //doViewStatParamsInitialize()
