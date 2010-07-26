function onReadyDatabaseAdmin() {
	// Table Stripes...
	jQuery('#tblDatabaseList tr:odd').removeClass('normal').addClass('normal');
	jQuery('#tblDatabaseList tr:even').removeClass('normal').addClass('under');
	
	jQuery('#tblDatabaseList #RegionId').hide();
	jQuery('#tblDatabaseList #LangIsoCode').hide();

	// Highlight row on mouseOver
	jQuery('#tblDatabaseList tr:not(:first)').unbind('hover').hover(
		function() {
			jQuery(this).addClass('highlight');
		},
		function() {
			jQuery(this).removeClass('highlight');
		}
	).unbind('click').click(function() {
		uploadMsg(''); 
		jQuery('#frmDatabaseEdit #cmd').val('cmdRegionUpdate');
		setRegionPA(jQuery(this).find('#RegionId').html(),
		            jQuery(this).find('#CountryIso').html(),
		            jQuery(this).find('#RegionLabel').html(),
		            jQuery(this).find('#LangIsoCode').html(),
		            jQuery(this).find('#RegionUserAdmin').html(),
		            jQuery(this).find('#RegionActive input').attr('checked'),
		            jQuery(this).find('#RegionPublic input').attr('checked')
		);
	});

	/*
	method="GET" action="javascript: var s=$('regionpafrm').serialize(); 
		mod='regionpa'; sendData('','region.php', s, '');" onSubmit="javascript: 
		var a=new Array('CountryIso','RegionLabel','LangIsoCode','RegionUserAdmin'); return(checkForm(a, '{-#errmsgfrm#-}'));"
	*/
	jQuery('#frmDatabaseEdit').unbind('submit').submit(function() {
		jQuery.post('index.php',
			{cmd        : 'cmdRegionUpdate',
			 RegionInfo :jQuery(this).serializeObject()
			},
			function(data) {
			},
			'json'
		);
		return false;
	});
}

function setRegionPA(prmRegionId, prmCountryIso, prmRegionLabel, 
					 prmLangIsoCode, prmUserId_AdminRegion, 
					 prmRegionActive, prmRegionPublic) {
	mod = "regionpa";
	$(mod + 'addsect').style.display = 'block';
	jQuery('#frmDatabaseEdit #RegionId').val(prmRegionId);
	jQuery('#frmDatabaseEdit #CountryIso').val(prmCountryIso);
	jQuery('#frmDatabaseEdit #RegionLabel').val(prmRegionLabel);
	jQuery('#frmDatabaseEdit #LangIsoCode').val(prmLangIsoCode);
	jQuery('#frmDatabaseEdit #RegionUserAdmin').val(prmUserId_AdminRegion);
	jQuery('#frmDatabaseEdit #RegionActive').attr('checked', prmRegionActive);
	jQuery('#frmDatabaseEdit #RegionPublic').attr('checked', prmRegionPublic);
}
