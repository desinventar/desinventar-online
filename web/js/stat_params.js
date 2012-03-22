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
} //doViewStatParamsInitialize()
