/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabase()
{
	// Populate CountryList/LanguageList in frmRegionEdit form
	jQuery('#desinventarCountryList option').clone().appendTo('#frmRegionEdit_CountryIso');
	jQuery('#desinventarLanguageList option').clone().appendTo('#frmRegionEdit_LangIsoCode');
	
	// Highlight row on mouseOver
	jQuery('#tblDatabaseList tr').live({
		mouseenter: function() {
			jQuery(this).addClass('highlight');
		},
		mouseleave: function() {
			jQuery(this).removeClass('highlight');
		},
		click:  function() {
			jQuery('#divAdminDatabaseList').hide();
			jQuery('#divAdminDatabaseUpdate .RegionLabel').text(jQuery('.RegionLabel',this).html());
			jQuery('#divAdminDatabaseUpdate .RegionId').text(jQuery('.RegionId',this).html());
			jQuery('#divAdminDatabaseUpdate').show();
			jQuery('.clsAdminDatabaseButton').show();
			jQuery('#btnAdminDatabaseNew').hide();
			
		}
	});

	jQuery('#btnAdminDatabaseEdit').click(function() {
		var RegionId = jQuery('#divAdminDatabaseUpdate .RegionId').text();
		// Load Information about database...
		jQuery.post(
			jQuery('#desinventarURL').val(),
			{cmd      : 'cmdAdminDatabaseGetInfo',
			 RegionId : RegionId
			},
			function(data)
			{
				jQuery('.clsAdminDatabase').hide();
				jQuery('#divAdminDatabaseEdit').show();
				jQuery('#frmRegionEdit_Cmd').val('cmdRegionUpdate');
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
	});

	jQuery('#btnAdminDatabaseExport').click(function() {
		jQuery('.clsAdminDatabase').hide();
		jQuery('#divAdminDatabaseExport').show();
	});

	jQuery('#btnAdminDatabaseImport').click(function() {
		jQuery('.clsAdminDatabase').hide();
		jQuery('#divAdminDatabaseImport').show();
	});

	// Add New Region
	jQuery('#btnAdminDatabaseNew').live('click', function() {
		jQuery('#regionpaaddsect').show();
		setRegionPA('','', '', '', '', true,false);
		jQuery('#frmRegionEdit_Cmd').val('cmdRegionCreate');
	}).hide();
	jQuery('.clsAdminDatabaseButton').hide();

	// Select Database from List
	jQuery('#btnAdminDatabaseSelect').live('click', function() {
		jQuery('#divAdminDatabaseUpdate').hide();
		jQuery('#divAdminDatabaseList').show();
	});

	jQuery('#frmRegionEdit_CountryIso').change(function() {
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdRegionCreate')
		{
			jQuery.post('index.php',
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
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdRegionCreate')
		{
			jQuery('#frmRegionEdit_RegionId').removeAttr('disabled').focus();
		}
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
				jQuery('#desinventarURL').val(),
				params, 
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
	jQuery('#frmRegionEdit_Cancel').click(function() {
		jQuery('#divAdminDatabaseEdit').hide();
	});
} //onReadyAdminDatabase()

function doAdminDatabaseUpdateList()
{
	jQuery.post(
		jQuery('#desinventarURL').val(),
		{
			cmd:'cmdAdminDatabaseGetList'
		},
		function(data)
		{
			jQuery.each(data.RegionList, function(index, value) {
				var clonedRow = jQuery('#tbodyDatabaseList tr:last').clone().show();
				jQuery('.CountryIso'     , clonedRow).html(value.CountryIso);
				jQuery('.RegionLabel'    , clonedRow).html(value.RegionLabel);
				jQuery('.RegionAdminUserId', clonedRow).text(value.RegionAdminUserId);
				jQuery('.RegionAdminUserFullName', clonedRow).text(value.RegionAdminUserFullName).show();
				jQuery('.RegionActive'   , clonedRow).attr('checked', value.RegionActive).attr('disabled',true);
				jQuery('.RegionPublic'   , clonedRow).attr('checked', value.RegionPublic).attr('disabled',true);
				jQuery('.RegionId'       , clonedRow).html(value.RegionId).hide();
				jQuery('.LangIsoCode'    , clonedRow).html(value.LangIsoCode).hide();
				jQuery('#tbodyDatabaseList').append(clonedRow);
			});
			// Table Stripes...
			jQuery('#tblDatabaseList tr:odd').removeClass('normal').addClass('normal');
			jQuery('#tblDatabaseList tr:even').removeClass('normal').addClass('under');
			
			jQuery('#tblDatabaseList #RegionId').hide();
			jQuery('#tblDatabaseList #LangIsoCode').hide();
			if (jQuery('#desinventarUserId').val() == 'root') 
			{
				jQuery('#btnAdminDatabaseNew').show();
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
	jQuery('#frmRegionEdit #RegionId').val(prmRegionId);
	jQuery('#frmRegionEdit #CountryIso').val(prmCountryIso);
	jQuery('#frmRegionEdit #RegionLabel').val(prmRegionLabel);
	jQuery('#frmRegionEdit #LangIsoCode').val(prmLangIsoCode);
	jQuery('#frmRegionEdit #RegionUserAdmin').val(prmUserId_AdminRegion);
	jQuery('#frmRegionEdit #RegionActive').attr('checked', prmRegionActive);
	jQuery('#frmRegionEdit #RegionPublic').attr('checked', prmRegionPublic);
	// RegionId is readonly by default
	jQuery('#frmRegionEdit #RegionId').attr('disabled','disabled');
}
