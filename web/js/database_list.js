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
	jQuery('#divDatabaseDeleteContent').on('click', 'a.Ok', function(event) {
		event.preventDefault();
	});
	jQuery('#divDatabaseDeleteContent').on('click', 'a.Cancel', function(event) {
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
} // doDatabaseUploadCreate()

function doDatabaseDeleteShow()
{
	Ext.getCmp('wndDatabaseDelete').show();
} // doDatabaseUploadAction

