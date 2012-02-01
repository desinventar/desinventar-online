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
} //onReadyStatParams()
