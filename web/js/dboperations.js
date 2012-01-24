function doUpdateDatabaseListByUser()
{
	jQuery(".contentBlock").hide();
	jQuery("#divRegionList").show();
	// Hide everything at start...
	jQuery('.databaseTitle').hide();
	jQuery('.databaseList').hide();

	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			cmd: 'cmdSearchDB',
			searchDBQuery: '', 
			searchDBCountry : 0
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				if (parseInt(data.NoOfDatabases) > 0)
				{
					jQuery('#divDatabaseFindList').show();
					jQuery('#divDatabaseFindError').hide();
					RegionByRole = new Array(5);
					RegionByRole['ADMINREGION'] = new Array();
					RegionByRole['SUPERVISOR'] = new Array();
					RegionByRole['USER'] = new Array();
					RegionByRole['OBSERVER'] = new Array();
					RegionByRole['NONE'] = new Array();

					$RoleList = new Array(5);
					var iCount = 0;
					jQuery('.databaseList').empty();
					jQuery.each(data.RegionList, function(RegionId, value) {
						jQuery('#divRegionList #title_' + value.Role).show();
						jQuery('#divRegionList #list_' + value.Role).show().append('<a href="' + jQuery('#desinventarURL').val() + '/' + RegionId + '" id="' + RegionId + '" class="databaseLink">' + value.RegionLabel + '</a><br />');
						iCount++;
					});
					
					jQuery('.databaseLink').addClass("alt").unbind('click').click(function() {
						RegionId = jQuery(this).attr('id');
						if (jQuery('#desinventarPortalType').val() != '')
						{
							displayRegionInfo(RegionId);
						}
						else
						{
							window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
						}
						return false;
					}); //bind
				}
				else
				{
					jQuery('#divDatabaseFindList').hide();
					jQuery('#divDatabaseFindError').show();
				}
			} //if
		},
		'json' //function
	);
} //doUpdateDatabaseListByUser()

function doGetRegionInfo(RegionId)
{
	jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(jQuery(
		'#desinventarURL').val() + '/',
		{
		  cmd         : 'cmdDatabaseGetInfo', 
		  RegionId    : RegionId,
		  LangIsoCode : jQuery('#desinventarLang').val()
		},
		function(data)
		{
			if (parseInt(data.Status)>0)
			{
				var i = data.RegionInfo;
				jQuery('#divRegionInfo').show();
				jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/?cmd=cmdDatabaseGetLogo&RegionId=' + RegionId + '" alt="" />');
				jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel);
				jQuery('#divRegionInfo #txtRegionPeriod').text(i.PeriodBeginDate + ' - ' + i.PeriodEndDate);
				jQuery('#divRegionInfo #txtRegionNumberOfRecords').text(i.NumberOfRecords);
				jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate);

				jQuery('#divRegionInfo #divInfoGeneral').hide();
				if (i.InfoGeneral != '')
				{
					jQuery('#divRegionInfo #divInfoGeneral #Text').html(i.InfoGeneral);
					jQuery('#divRegionInfo #divInfoGeneral').show();
				}
				jQuery('#divRegionInfo #divInfoCredits').hide();
				if (i.InfoCredits != '')
				{
					jQuery('#divRegionInfo #divInfoCredits #Text').html(i.InfoCredits);
					jQuery('#divRegionInfo #divInfoCredits').show();
				}
				jQuery('#divRegionInfo #divInfoSources').hide();
				if (i.InfoSources != '')
				{
					jQuery('#divRegionInfo #divInfoSources #Text').html(i.InfoSources);
					jQuery('#divRegionInfo #divInfoSources').show();
				}
				jQuery('#divRegionInfo #divInfoSynopsis').hide();
				if (i.InfoSynopsis != '')
				{
					jQuery('#divRegionInfo #divInfoSynopsis #Text').html(i.InfoSynopsis);
					jQuery('#divRegionInfo #divInfoSynopsis').show();
				}
			}
		},
		'json'
	);
} //doGetRegionInfo()

function updateDatabaseList(CountryIsoCode,searchByCountry) {
	jQuery(".contentBlock").hide();
	// Hide everything at start...
	jQuery('.databaseTitle').hide();
	jQuery('.databaseList').hide();
	jQuery("#divRegionList").hide();
	jQuery.get(
		jQuery('#desinventarURL').val() + '/', 
		{
			cmd: 'getCountryName',
			CountryIso : CountryIsoCode
		},
		function(data)
		{ 
			jQuery("#divRegionList #title_COUNTRY").html('<h3>' + data.CountryName + '</h3>');
			jQuery("#divRegionList").show();
		},
		'jsonp'
	);
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd: 'cmdSearchDB', 
			searchDBQuery: CountryIsoCode, 
			searchDBCountry : 1
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				var iCount = 0;
				var RegionId = '';

				// Hide everything at start...
				jQuery('.databaseTitle').hide();
				jQuery('.databaseList').hide();

				var jList = jQuery("#divRegionList #list_COUNTRY");
				var myRegionId = '';
				jList.empty();
				jQuery.each(data.RegionList, function(RegionId, value) {
					iCount++;
					jList.append('<a href="#" id="' + RegionId + '" class="databaseLink">' + value.RegionLabel + '</a><br />');
					myRegionId = RegionId;
				}); // each
				if (iCount == 1) {	
					// If only one region is in list, show directly info instead of list
					displayRegionInfo(myRegionId);
				} else {
					jQuery('#divRegionList #title_COUNTRY').show();
					jQuery('#divRegionList #list_COUNTRY').show();
					jQuery('.databaseLink').addClass("alt").unbind('click').click(function() {
						RegionId = jQuery(this).attr('id');
						displayRegionInfo(RegionId);
						return false;
					}); //bind
				}
			}
		}, //function
		'json'
	);
};
