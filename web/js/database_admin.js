/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseAdmin()
{
	// Populate CountryList/LanguageList
	jQuery('#desinventarCountryList').clone().attr('id','CountryIso').attr('name','CountryIso').appendTo('#frmDBImport #spanCountryIso').show();
	jQuery('#desinventarLanguageList').clone().attr('id','LangIsoCode').attr('name','LangIsoCode').appendTo('#frmDBImport #spanLangIsoCode').show();
	
	// Highlight row on mouseOver
	jQuery('#tblDatabaseList tr').live({
		mouseenter: function() {
			jQuery(this).addClass('highlight');
		},
		mouseleave: function() {
			jQuery(this).removeClass('highlight');
		},
		click:  function() {
			jQuery('.clsDatabaseAdminButton').show();
			jQuery('#btnDatabaseAdminNew').hide();
			/*
			jQuery('#frmDatabaseEdit :input').unhighlight();
			jQuery('#frmDatabaseEdit #cmd').val('cmdRegionUpdate');
			setRegionPA(jQuery(this).find('.CountryIso').html(),
						jQuery(this).find('.RegionLabel').html(),
						jQuery(this).find('.RegionUserAdmin').html(),
						jQuery(this).find('.RegionActive').attr('checked'),
						jQuery(this).find('.RegionPublic').attr('checked'),
						jQuery(this).find('.RegionId').html(),
						jQuery(this).find('.LangIsoCode').html(),
						jQuery(this).find('.RegionUserAdminName').html()
			);
			*/
		}
	});

	jQuery('#frmDatabaseEdit #CountryIso').unbind('change').change(function() {
		if (jQuery('#frmDatabaseEdit #cmd').val() == 'cmdRegionCreate') {
			jQuery.post('index.php',
				{cmd        : 'cmdRegionBuildRegionId',
				 CountryIso : jQuery(this).val()
				},
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#frmDatabaseEdit #RegionId').val(data.RegionId);					
					}
				},
				'json'
			);
		}
	});
	
	jQuery('#frmDatabaseEdit #lblRegionId').dblclick(function() {
		if (jQuery('#frmDatabaseEdit #cmd').val() == 'cmdRegionCreate') {
			jQuery('#frmDatabaseEdit #RegionId').removeAttr('disabled').focus();
		}
	});
	
	jQuery('#frmDatabaseEdit').unbind('submit').submit(function() {
		// Validate Fields
		var a=new Array('CountryIso','RegionLabel','LangIsoCode','RegionUserAdmin');
		var bContinue = checkForm('frmDatabaseEdit',a, '{-#errmsgfrm#-}');
		var s = jQuery(this).find('#RegionStatus');
		s.val(0);
		if (jQuery(this).find('#RegionActive').attr('checked')) {
			s.val(parseInt(s.val()) | 1);
		}
		if (jQuery(this).find('#RegionPublic').attr('checked')) {
			s.val(parseInt(s.val()) | 2);
		}
		jQuery('#frmDatabaseEdit #RegionId').removeAttr('disabled');
		var params = jQuery(this).serializeObject();
		jQuery('#frmDatabaseEdit #RegionId').attr('disabled','disabled');
		if (bContinue) {
			jQuery('#frmDatabaseEdit :input').unhighlight();
			jQuery.post('index.php',
				{cmd        : 'cmdRegionUpdate',
				 RegionInfo : params
				},
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#divDatabaseEditResult').html(data.Status + ' ' + data.RegionId);
						// (jhcaiced) Change this for a method to update only the 
						// affected row intead of loading all list again
						updateList('dbl', 'region.php', 'cmd=adminreg');
					}
				},
				'json'
			);
		}
		return false;
	});

	// Add New Region
	jQuery('#btnDatabaseAdminNew').live('click', function() {
		jQuery('#regionpaaddsect').show();
		setRegionPA('','', '', '', '', true,false);
		jQuery('#frmDatabaseEdit #cmd').val('cmdRegionCreate');
	}).hide();
	jQuery('.clsDatabaseAdminButton').hide();
} //onReadyDatabaseAdmin()

function doDatabaseAdminUpdateList()
{
	jQuery.post(
		jQuery('#desinventarURL').val(),
		{cmd:'cmdAdminGetDatabaseList'}, 
		function(data)
		{
			jQuery.each(data.RegionList, function(index, value) {
				var clonedRow = jQuery('#lst_regionpa tr:last').clone().show();
				jQuery('.CountryIso'     , clonedRow).html(value.CountryIso);
				jQuery('.RegionLabel'    , clonedRow).html(value.RegionLabel);
				jQuery('.RegionAdminUserId', clonedRow).text(value.RegionAdminUserId);
				jQuery('.RegionAdminUserFullName', clonedRow).text(value.RegionAdminUserFullName).show();
				jQuery('.RegionActive'   , clonedRow).attr('checked', value.RegionActive).attr('disabled',true);
				jQuery('.RegionPublic'   , clonedRow).attr('checked', value.RegionPublic).attr('disabled',true);
				jQuery('.RegionId'       , clonedRow).html(value.RegionId).hide();
				jQuery('.LangIsoCode'    , clonedRow).html(value.LangIsoCode).hide();
				jQuery('#lst_regionpa').append(clonedRow);
			});
			// Table Stripes...
			jQuery('#tblDatabaseList tr:odd').removeClass('normal').addClass('normal');
			jQuery('#tblDatabaseList tr:even').removeClass('normal').addClass('under');
			
			jQuery('#tblDatabaseList #RegionId').hide();
			jQuery('#tblDatabaseList #LangIsoCode').hide();
			if (jQuery('#desinventarUserId').val() == 'root') 
			{
				jQuery('#btnDatabaseAdminNew').show();
			}
		},
		'json'
	);
}

function setRegionPA(prmRegionId, prmCountryIso, prmRegionLabel, 
					 prmLangIsoCode, prmUserId_AdminRegion, 
					 prmRegionActive, prmRegionPublic) {
	mod = "regionpa";
	jQuery('#regionpaaddsect').show();
	jQuery('#frmDatabaseEdit #RegionId').val(prmRegionId);
	jQuery('#frmDatabaseEdit #CountryIso').val(prmCountryIso);
	jQuery('#frmDatabaseEdit #RegionLabel').val(prmRegionLabel);
	jQuery('#frmDatabaseEdit #LangIsoCode').val(prmLangIsoCode);
	jQuery('#frmDatabaseEdit #RegionUserAdmin').val(prmUserId_AdminRegion);
	jQuery('#frmDatabaseEdit #RegionActive').attr('checked', prmRegionActive);
	jQuery('#frmDatabaseEdit #RegionPublic').attr('checked', prmRegionPublic);
	// RegionId is readonly by default
	jQuery('#frmDatabaseEdit #RegionId').attr('disabled','disabled');
}
