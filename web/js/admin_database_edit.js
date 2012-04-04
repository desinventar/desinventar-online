/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyAdminDatabaseEdit()
{
	// Populate CountryList/LanguageList in frmRegionEdit form
	jQuery('#desinventarCountryList option').clone().appendTo('#frmRegionEdit_CountryIso');
	jQuery('#desinventarLanguageList option').clone().appendTo('#frmRegionEdit_LangIsoCode');

	jQuery('#frmRegionEdit_CountryIso').change(function() {
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdDatabaseCreate')
		{
			jQuery.post(jQuery('#desinventarURL').val() + '/',
				{cmd        : 'cmdRegionBuildRegionId',
				 CountryIso : jQuery(this).val()
				},
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#frmRegionEdit_RegionId').val(data.RegionId);
					}
				},
				'json'
			);
		}
	});
	
	jQuery('#frmRegionEdit #lblRegionId').dblclick(function() {
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdDatabaseCreate')
		{
			jQuery('#frmRegionEdit_RegionId').removeAttr('disabled').focus();
		}
		return false;
	});
	
	jQuery('#frmRegionEdit').submit(function() {
		// Validate Fields
		var bContinue = true;
		/*
		var a=new Array('CountryIso','RegionLabel','LangIsoCode','RegionUserAdmin');
		var bContinue = checkForm('frmRegionEdit',a, '{-#errmsgfrm#-}');
		*/
		var RegionStatus = jQuery('#frmRegionEdit_RegionStatus');
		RegionStatus.val(0);
		if (jQuery('#frmRegionEdit_RegionActive').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 1);
		}
		if (jQuery('#frmRegionEdit_RegionPublic').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 2);
		}
		jQuery('#frmRegionEdit_RegionId').removeAttr('disabled');
		var params = jQuery(this).serializeObject();
		jQuery('#frmRegionEdit_RegionId').attr('disabled','disabled');
		if (bContinue) {
			jQuery('#frmRegionEdit :input').unhighlight();
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				params, 
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#divDatabaseEditResult').html(data.Status + ' ' + data.RegionId);
						// (jhcaiced) Change this for a method to update only the 
						// affected row intead of loading all list again
						updateList('dbl', jQuery('#desinventarURL').val() + '/region.php', 'cmd=adminreg');
					}
				},
				'json'
			);
		}
		return false;
	});

	jQuery('#frmRegionEdit_Cancel').click(function() {
		jQuery(this).trigger('evAdminDatabaseCancel');
		return false;
	});
} //onReadyAdminDatabaseEdit()

function doAdminDatabaseGetInfo(RegionId)
{
	// Load Information about database...
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{cmd      : 'cmdAdminDatabaseGetInfo',
		 RegionId : RegionId
		},
		function(data)
		{
			jQuery('#frmRegionEdit_Cmd').val('cmdDatabaseUpdate');
			jQuery('#frmRegionEdit_CountryIso').val(data.Region.CountryIso);
			jQuery('#frmRegionEdit_RegionId').val(data.Region.RegionId);
			jQuery('#frmRegionEdit_RegionLabel').val(data.Region.RegionLabel);
			jQuery('#frmRegionEdit_LangIsoCode').val(data.Region.LangIsoCode);
			jQuery('#frmRegionEdit_RegionStatus').val(data.Region.RegionStatus);
			
			// RegionActive/RegionPublic are set based on RegionStatus value
			jQuery('#frmRegionEdit_RegionActive').removeAttr('checked');
			if (parseInt(data.Region.RegionStatus) & 1)
			{
				jQuery('#frmRegionEdit_RegionActive').attr('checked','checked');
			}
			jQuery('#frmRegionEdit_RegionPublic').removeAttr('checked');
			if (parseInt(data.Region.RegionStatus) & 2)
			{
				jQuery('#frmRegionEdit_RegionPublic').attr('checked','checked');
			}
		},
		'json'
	);
} //doAdminDatabaseGetInfo()
