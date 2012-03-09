function onReadyDatabaseList()
{
	doDatabaseDeleteCreate();
	
	jQuery('#divDatabaseFindList table.databaseList').on('mouseover','td.RegionDelete', function() {
		jQuery(this).parent().highlight();
	}).on('mouseout','td.RegionDelete', function() {
		jQuery(this).parent().unhighlight();
	}).on('click','td.RegionDelete', function(event) {
		var RegionId = jQuery(this).parent().find('td.RegionId').text();
		var RegionLabel = jQuery(this).parent().find('span.RegionLabel').text();
		jQuery('#divDatabaseDeleteContent span.RegionId').text(RegionId);
		jQuery('#divDatabaseDeleteContent span.RegionLabel').text(RegionLabel);
		doDatabaseDeleteShow();
		event.preventDefault();
	});
	jQuery('#divDatabaseDeleteContent').on('click', 'a.buttonOk', function(event) {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseDelete',
				RegionId : jQuery('div.DatabaseDelete span.RegionId').text()
			},
			function(data)
			{
				jQuery('div.DatabaseDelete span.status').hide();
				if (parseInt(data.Status) > 0)
				{
					jQuery('div.DatabaseDelete input.HasDeleted').val(1);
					jQuery('div.DatabaseDelete span.StatusOk').show();
					jQuery('div.DatabaseDelete a.button').hide();
					jQuery('div.DatabaseDelete a.buttonClose').show();
				}
				else
				{
					jQuery('div.DatabaseDelete input.HasDeleted').val(0);
					jQuery('div.DatabaseDelete span.StatusError').show();
					setTimeout(function() {
						jQuery('div.DatabaseDelete span.status').hide();
					}, 3000);
				}
			},
			'json'
		);
		event.preventDefault();
	});
	jQuery('div.DatabaseDelete').on('click', 'a.buttonCancel', function(event) {
		jQuery('div.DatabaseDelete input.HasDeleted').val(0);
		Ext.getCmp('wndDatabaseDelete').hide();
		event.preventDefault();
	});
	jQuery('div.DatabaseDelete').on('click', 'a.buttonClose', function(event) {
		Ext.getCmp('wndDatabaseDelete').hide();
		event.preventDefault();
	});
} //onReadyDatabaseList

function doDatabaseDeleteCreate()
{
	var w = new Ext.Window({id:'wndDatabaseDelete', 
		el: 'divDatabaseDeleteWin', layout:'fit', 
		width:450, height:200, modal:false, constrainHeader: true,
		plain: false, animCollapse: false,
		closeAction: 'hide',
		items: new Ext.Panel({
			contentEl: 'divDatabaseDeleteContent',
			autoScroll: true
		})
	});
	w.on('hide', function() {
		var HasDeleted = parseInt(jQuery('div.DatabaseDelete input.HasDeleted').val());
		if (HasDeleted > 0)
		{
			doUpdateDatabaseListByUser();
		}
	});
} // doDatabaseUploadCreate()

function doDatabaseDeleteShow()
{
	// Initialization
	jQuery('div.DatabaseDelete span.status').hide();
	jQuery('div.DatabaseDelete a.button').show();
	jQuery('div.DatabaseDelete a.buttonClose').hide();
	jQuery('div.DatabaseDelete input.HasDeleted').val(0);
	//Show
	Ext.getCmp('wndDatabaseDelete').show();
} // doDatabaseUploadAction

