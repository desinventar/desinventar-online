/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
function onReadyStatParams()
{
	jQuery('#fldStatParam_FirstLev').change(function() {
		console.log('fldStatParam_FirstLev.change()');
		setTotalize('fldStatParam_FirstLev', '_S+Secondlev');
		setTotalize('_S+Secondlev', '_S+Thirdlev');
	});
}