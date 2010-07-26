function onReadyDatabaseAdmin() {
	// Table Stripes...
	jQuery('#tblDatabaseList tr:odd').addClass('normal');
	jQuery('#tblDatabaseList tr:even').addClass('under');
	
	jQuery('#tblDatabaseList #RegionId').hide();
	jQuery('#tblDatabaseList #LangIsoCode').hide();
	
	// Highlight row on mouseOver
	jQuery('#tblDatabaseList tr:not(:first)').hover(
		function() {
			jQuery(this).addClass('highlight');
		},
		function() {
			jQuery(this).removeClass('highlight');
		}
	).click(function() {
		uploadMsg(''); 
		mod='regionpa';
		jQuery('#regionpafrm #cmd').val('update');
		setRegionPA(jQuery(this).find('#RegionId').html(),
		            jQuery(this).find('#CountryIso').html(),
		            jQuery(this).find('#RegionLabel').html(),
		            jQuery(this).find('#LangIsoCode').html(),
		            jQuery(this).find('#RegionUserAdmin').html(),
		            jQuery(this).find('#RegionActive input').attr('checked'),
		            jQuery(this).find('#RegionPublic input').attr('checked'));
	});
}
