/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseCreate()
{
	doDatabaseCreateSetup();
} //onReadyDatabaseCreate()

function doDatabaseCreateSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndDatabaseCreate', 
		el: 'divDatabaseCreateWin', layout:'fit', 
		width:400, height:300, modal:false,
		closeAction:'hide', plain: false, animCollapse: true,
		items: new Ext.Panel({
			contentEl: 'divDatabaseCreateContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});

	// Cancel Button - Hide Window and do nothing
	jQuery('#btnDatabaseCreateCancel').click(function() {
		Ext.getCmp('wndDatabaseCreate').hide();
	});

	// Send Button - Validate data and send command to backend
	jQuery('#btnDatabaseCreateSend').click(function() {
		console.log('btnDatabaseCreateSend');
	}).hide();

	// async Populate CountryIso - LanguageList fields
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGetLocaleList'
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#fldDatabaseEdit_LangIsoCode').empty();
				jQuery.each(data.LanguageList, function(key, value) {
					jQuery('#fldDatabaseEdit_LangIsoCode').append(jQuery('<option>', { value : key }).text(value));
				});
				jQuery('#fldDatabaseEdit_CountryIso').empty();
				jQuery.each(data.CountryList, function(key, value) {
					jQuery('#fldDatabaseEdit_CountryIso').append(jQuery('<option>', { value: key }).text(value));
				});
				jQuery('#btnDatabaseCreateSend').show();
			}
		},
		'json'
	);			
} //doDatabaseCreateSetup()

function doDatabaseCreateShow()
{
	// Clear fields in form
	jQuery('#frmDatabaseEdit :input').each(function() {
		jQuery(this).val('');
	});
	Ext.getCmp('wndDatabaseCreate').show();
} //doDatabaseCreateShow()
