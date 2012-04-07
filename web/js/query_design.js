/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('div.QueryDesign').on('mouseover','.withHelpOver',function() {
		showtip(jQuery(this).data('help'));
	}).on('focus','.withHelpFocus',function() {
		showtip(jQuery(this).data('help'));
	});

	jQuery('div.QueryDesign div.GeographyList').on('click', 'li.item input:checkbox', function(event) {
		jQuery(this).trigger('GeographyUpdate');
	}).on('click','li.item span.label', function(event) {
		jQuery(this).parent().find('input:checkbox').trigger('click');
		jQuery(this).trigger('GeographyUpdate');
	}).on('GeographyUpdate', 'li.item', function(event) {
		var GeoLevelCount = jQuery('div.QueryDesign div.GeolevelsHeader table tr td').size() - 2;
		var GeographyLevel = jQuery(this).data('GeographyLevel');
		if (GeographyLevel < GeoLevelCount)
		{
			var GeographyId = jQuery(this).data('GeographyId');
			var item = jQuery(this);
			jQuery('ul.list li', item).remove();
			var isChecked = jQuery('input:checkbox',this).prop('checked');
			
			if (isChecked)
			{
				GeographyList = jQuery('body').data('GeographyList-' + GeographyId);
				if (GeographyList == undefined) 
				{
					jQuery.post(
						jQuery('#desinventarURL').val() + '/',
						{
							cmd         : 'cmdGeographyGetItemsById',
							RegionId    : jQuery('#desinventarRegionId').val(),
							GeographyId : GeographyId
						},
						function(data)
						{
							if (parseInt(data.Status) > 0)
							{
								jQuery.each(data.GeographyList, function(key, value) {
									jQuery('body').data('GeographyList' + key, value);
								});
								jQuery.each(data.GeographyList[GeographyId], function(key, value) {
									var clone = jQuery('div.QueryDesign div.GeographyList ul.mainlist li.item:first').clone().show();
									clone.data('GeographyId', key);
									clone.data('GeographyLevel', GeographyLevel + 1);
									jQuery('input:checkbox',clone).attr('value', key);
									jQuery('span.label',clone).text(value.GeographyName);
									jQuery('ul.list:first',item).append(clone);
								});
							}
						},
						'json'
					);
				}
				else
				{
					jQuery.each(GeographyList, function(key, value) {
						var clone = jQuery('div.QueryDesign div.GeographyList ul.mainlist li.item:first').clone().show();
						clone.data('GeographyId', key);
						clone.data('GeographyLevel', GeographyLevel + 1);
						jQuery('input:checkbox',clone).attr('value', key);
						jQuery('span.label',clone).text(value.GeographyName);
						jQuery('ul.list:first',item).append(clone);
					});
				}
			}
		}
		event.stopPropagation();
	});

	jQuery('div.QueryDesign table.EffectList').on('click', 'input:checkbox', function(event) {
		jQuery(this).trigger('EffectUpdate');
	}).on('click','span.label', function(event) {
		var checkbox = jQuery(this).parent().find('input:checkbox');
		checkbox.prop('checked', !checkbox.prop('checked'));
		jQuery(this).trigger('EffectUpdate');
	}).on('change','select.operator', function(event) {
		var value = jQuery(this).val();
		jQuery(this).trigger('HideValues');
		if ((value == '>=') || (value == '<=') || (value == '=') || (value == '-3') )
		{
			jQuery(this).trigger('ShowFirstValue');
			if (value == '-3')
			{
				jQuery(this).trigger('ShowLastValue');
			}
		}
	}).on('EffectUpdate', 'td div', function(event) {
		if (jQuery('input:checkbox',this).prop('checked'))
		{
			jQuery('span.options', this).show();
			jQuery('select.operator',this).enable().change();
		}
		else
		{
			jQuery('span.options',this).hide();
			jQuery('select.operator',this).disable().change();
		}		
	}).on('HideValues', 'td div', function(event) {
		jQuery('span.firstvalue', this).hide();
		jQuery('span.firstvalue input',this).disable();
		jQuery('span.lastvalue', this).hide();
		jQuery('span.lastvalue input',this).disable();
	}).on('ShowFirstValue', 'td div', function(event) {
		jQuery('span.firstvalue', this).show();
		jQuery('span.firstvalue input',this).enable();
	}).on('ShowLastValue', 'td div', function(event) {
		jQuery('span.lastvalue', this).show();
		jQuery('span.lastvalue input', this).enable();
	});

	jQuery('div.QueryDesign').on('click', 'table.QueryCustom div.field', function(event) {
		setAdvQuery(jQuery(this).data('field'), jQuery(this).data('type'));
	});

	jQuery('body').on('cmdMainQueryUpdate', function() {
		// 2011-02-05 (jhcaiced) Configure RecordStatus field
		if ( (jQuery('#desinventarUserId').val() != '') &&
		     (jQuery('#desinventarUserRoleValue').val() > 1) )
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED','READY']);
			jQuery('#divQueryRecordStatus').show();
		}
		else
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED']);
			jQuery('#divQueryRecordStatus').hide();
		}
	});
	
	jQuery('div.QueryDesign').on('cmdInitialize', function() {
		var params = jQuery('body').data('params');

		jQuery('div.QueryDesign input:text,textarea').val('');

		// Initialize fields
		jQuery('input.RegionId', this).val(jQuery('body').data('RegionId'));
		jQuery('input.MinYear' , this).val(params.MinYear);
		jQuery('input.MaxYear' , this).val(params.MaxYear);
		jQuery('input.queryBeginYear', this).val(params.MinYear);
		jQuery('input.queryEndYear', this).val(params.MaxYear);

		// Load Geolevels List
		var geolevel_list = jQuery('body').data('GeolevelsList');
		jQuery('div.QueryDesign div.GeolevelsHeader table tr td:gt(0)').remove();
		jQuery.each(geolevel_list, function(key, value) {
			var clone = jQuery('div.QueryDesign div.GeolevelsHeader table tr td:last').clone().show();
			jQuery('span',clone).text(value.GeoLevelName);
			jQuery('span',clone).data('help', value.GeoLevelDesc);
			jQuery('div.QueryDesign div.GeolevelsHeader table tr').append(clone);
		});
		// Load Geography List
		var geography_list = jQuery('div.QueryDesign div.GeographyList ul.mainlist');
		geography_list.find('li:gt(0)').remove();
		geography_list.find('li').hide();
		jQuery.each(jQuery('body').data('GeographyList'), function(key, value) {
			var item = geography_list.find('li:last').clone().show();
			jQuery('input:checkbox', item).attr('value', key);
			jQuery('span.label', item).html(value.GeographyName);
			jQuery(item).data('GeographyId', key);
			jQuery(item).data('GeographyLevel', 0);
			geography_list.append(item);
		});
		// Load Event List
		jQuery('div.QueryDesign select.Event').empty();
		jQuery.each(jQuery('body').data('EventList'), function(key, value) {
			if (parseInt(value.EventPredefined) > 0)
			{
				var option = jQuery('<option>', { value : value.EventId }).text(value.EventName);
				option.data('help', value.EventDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Event').append(option);
			}
		});
		var option = jQuery('<option>', { value : '' }).text('---');
		option.attr('disabled','disabled');
		jQuery('div.QueryDesign select.Event').append(option);
		jQuery.each(jQuery('body').data('EventList'), function(key, value) {
			if (parseInt(value.EventPredefined) < 1)
			{
				var option = jQuery('<option>', { value : key }).text(value.EventName);
				option.data('help', value.EventDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Event').append(option);
			}
		});		
		// Load Cause List
		jQuery('div.QueryDesign select.Cause').empty();
		jQuery.each(jQuery('body').data('CauseList'), function(key, value) {
			if (parseInt(value.CausePredefined) > 0)
			{
				var option = jQuery('<option>', { value : key }).text(value.CauseName);
				option.data('help', value.CauseDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Cause').append(option);
			}
		});
		var option = jQuery('<option>', { value : '' }).text('---');
		option.attr('disabled','disabled');
		jQuery('div.QueryDesign select.Cause').append(option);
		jQuery.each(jQuery('body').data('CauseList'), function(key, value) {
			if (parseInt(value.CausePredefined) < 1)
			{
				var option = jQuery('<option>', { value : value.CauseId }).text(value.CauseName);
				option.data('help', value.CauseDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Cause').append(option);
			}
		});
		// Load EffectPeople List (ef1)
		var effect_list = jQuery('div.QueryDesign table.EffectPeopleList');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.firstvalue input', clone).attr('name', 'D_' + field + '[1]');
			jQuery('span.lastvalue input', clone).attr('name', 'D_' + field + '[2]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectPeople',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});

		// Load EffectSector List (sec)
		var effect_list = jQuery('div.QueryDesign table.EffectSectorList');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectSector',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});

		// Load EffectLosses2 List (ef3)
		var effect_list = jQuery('div.QueryDesign table.EffectListLosses2');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.firstvalue input', clone).attr('name', 'D_' + field + '[1]');
			jQuery('span.lastvalue input', clone).attr('name', 'D_' + field + '[2]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectLosses2',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});
		// Load EffectAdditional List (EEFieldList)
		var effect_list = jQuery('div.QueryDesign table.EffectAdditionalList');
		effect_list.find('tr:gt(0)').remove();
		jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
			var field = key;
			var type = value[2];
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('div.Effect', clone).hide();
			switch(type)
			{
				case 'INTEGER':
				case 'DOUBLE':
				case 'CURRENCY':
					jQuery('select.operator'      , clone).attr('name', 'EEFieldQuery[' + field + '][0]').disable();
					jQuery('span.firstvalue input', clone).attr('name', 'EEFieldQuery[' + field + '][1]');
					jQuery('span.lastvalue input' , clone).attr('name', 'EEFieldQuery[' + field + '][2]').disable();
					jQuery('div.EffectNumeric', clone).show();
				break;
				case 'STRING':
				case 'TEXT':
				case 'DATE':
					jQuery('input.text', clone).attr('name', 'EEFieldQuery[' + field + '][Text]');
					jQuery('div.EffectText', clone).show();
				break;
			}
			jQuery('input.type', clone).attr('name', 'EEFieldQuery[' + field + '][Type]');
			jQuery('input.type', clone).attr('value', type);
			jQuery('span.label', clone).text(value[0]);
			jQuery('div.EffectAdditional',clone).data('field', field);
			effect_list.append(clone);
		});
		// Load QueryCustom field list
		var field_list = jQuery('div.QueryDesign table.QueryCustom div.list');
		field_list.find('div:gt(0)').remove();
		jQuery('div.QueryDesign table.QueryCustom div.defaultlist span').each(function() {
			var field = jQuery(this).data('field');
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', field);
			jQuery(clone).data('type', jQuery(this).data('type'));
			jQuery('input', clone).attr('value', jQuery(this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'number');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'boolean');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'number');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectOther').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'text');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
			var field = key;
			var type = value[2];
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', field);
			jQuery(clone).data('type', 'text');
			jQuery('input', clone).attr('value', value[0]);
			field_list.append(clone);
		});
		jQuery('body').trigger('cmdMainQueryUpdate');
		jQuery('div.QueryDesign dt.QueryDatacard').trigger('mousedown');
	});
} //onReadyQueryDesign()
