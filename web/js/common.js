/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyCommon() {
	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 60);
}

function doKeepSessionAwake() {
	jQuery.post(jQuery('#desinventarURL').val() + '/index.php',
		{cmd : 'cmdSessionAwake'},
		function(data) {
		},
		'json'
	);
}

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
