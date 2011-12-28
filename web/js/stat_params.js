/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
function onReadyStatParams()
{
	jQuery('#fldStatParam_FirstLev').change(function() {
		console.log('fldStatParam_FirstLev.change()');
		setTotalize('fldStatParam_FirstLev', 'fldStatParam_SecondLev');
		jQuery('#fldStatParam_ThirdLev').empty();
	});
	
	jQuery('#fldStatParam_SecondLev').change(function() {
		setTotalize('fldStatParam_SecondLev', 'fldStatParam_ThirdLev');
	});
}