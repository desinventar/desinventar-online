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
					jQuery('.databaseList').each(function() {
						jQuery('table tr:gt(0)', this).remove();
					});
					jQuery.each(data.RegionList, function(RegionId, value) {
						jQuery('#divRegionList #title_' + value.Role).show();
						jQuery('#divRegionList #list_' + value.Role).show();
						var list = jQuery('#divRegionList #list_' + value.Role + ' table').show();
						var item = jQuery('tr:last', list).clone().show();
						jQuery('td.RegionId', item).text(RegionId);
						jQuery('td span.RegionLabel', item).text(value.RegionLabel);
						jQuery('td a.RegionLink', item).attr('href', jQuery('#desinventarURL').val() + '/' + RegionId + '/');
						list.append(item);
						iCount++;
					});
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
