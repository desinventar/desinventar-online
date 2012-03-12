/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('div.QueryDesign div.EffectLossesValue input').click(function() {
		parent = jQuery(this).parent();
		if (jQuery(this).prop('checked'))
		{
			jQuery('span.options', parent).show();
			jQuery('select.operator',parent).enable().change();
			jQuery('span.minvalue',parent).enable();
			jQuery('span.maxvalue',parent).enable();
		}
		else
		{
			jQuery('span.options', parent).hide();
			jQuery('select.operator',parent).disable().change();
			jQuery('span.minvalue',parent).disable();
			jQuery('span.maxvalue',parent).disable();
		}
	}).focus(function() {
		showtip(jQuery(this).data('help'));
	});

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

	jQuery('div.QueryDesign div.EffectPeopleList table.EffectPeopleList').on('click', 'input:checkbox', function(event) {
		jQuery(this).trigger('EffectUpdate');
	}).on('click','span.label', function(event) {
		var checkbox = jQuery(this).parent().find('input:checkbox');
		checkbox.prop('checked', !checkbox.prop('checked'));
		jQuery(this).trigger('EffectUpdate');
	}).on('change','select.operator', function(event) {
		var value = jQuery(this).val();
		jQuery('span.firstvalue', jQuery(this).parent()).hide();
		jQuery('span.lastvalue', jQuery(this).parent()).hide();
		if ((value == '>=') || (value == '<=') || (value == '=') || (value == '-3') )
		{
			jQuery('span.firstvalue', jQuery(this).parent()).show();
			if (value == '-3')
			{
				jQuery('span.lastvalue', jQuery(this).parent()).show();
			}
		}
	}).on('EffectUpdate', 'td div', function(event) {
		if (jQuery('input:checkbox',this).prop('checked'))
		{
			jQuery('span.options', this).show();
		}
		else
		{
			jQuery('span.options',this).hide();
		}		
	});
	
	jQuery('div.QueryDesign').on('cmdUpdate', function() {
		var params = jQuery('body').data('params');
		jQuery('input.RegionId', this).val(jQuery('body').data('RegionId'));
		jQuery('input.MinYear' , this).val(params.MinYear);
		jQuery('input.MaxYear' , this).val(params.MaxYear);
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
				var option = jQuery('<option>', { value : value.EventId }).text(value.EventName);
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
				var option = jQuery('<option>', { value : value.CauseId }).text(value.CauseName);
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
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectPeople',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});
	});
} //onReadyQueryDesign()
