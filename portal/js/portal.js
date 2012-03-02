/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function doGetRegionInfo(RegionId)
{
	jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/loading.gif" />');
	jQuery.post(jQuery('#desinventarURL').val(),
		{
		  cmd         : 'cmdGetRegionInfo', 
		  RegionId    : RegionId,
		  LangIsoCode : jQuery('#desinventarLang').val()
		},
		function(data)
		{
			if (parseInt(data.Status)>0)
			{
				var i = data.RegionInfo;
				jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '?cmd=getRegionLogo&RegionId=' + RegionId + '" alt="" />');
				jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel);
				jQuery('#divRegionInfo #txtRegionPeriod').text(i.PeriodBeginDate + ' - ' + i.PeriodEndDate);
				jQuery('#divRegionInfo #txtRegionNumDatacards').text(i.NumDatacards);
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
}

function updateDatabaseList(CountryIsoCode,searchByCountry) {
	jQuery(".contentBlock").hide();
	// Hide everything at start...
	jQuery('.databaseTitle').hide();
	jQuery('.databaseList').hide();
	jQuery("#divRegionList").hide();
	var desinventarURL = jQuery('#desinventarURL').val();
	jQuery.get(desinventarURL, 
		{cmd: 'getCountryName', CountryIso : CountryIsoCode },
		function(data) { 
			jQuery("#divRegionList #title_COUNTRY").html('<h3>' + data.CountryName + '</h3>');
			jQuery("#divRegionList").show();
		},
		'jsonp'
	);
	jQuery.post(desinventarURL,
		{cmd: 'cmdSearchDB', 
		 searchDBQuery: CountryIsoCode, 
		 searchDBCountry : 1
		},
		function(data) {
			if (data.Status == 'OK') {
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

