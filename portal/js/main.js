function onReadyPortal() {
	var desinventarURL  = jQuery('#desinventarURL').val();
	var desinventarLang = jQuery('#desinventarLang').val();
	jQuery("#btnMainWindow").click(function() {
		window.open(desinventarURL + '?cmd=main&lang=' + desinventarLang,'_blank', 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
	});
	jQuery("#version").load(desinventarURL, { cmd: 'getversion' });
	// At start, display the map 
	showMap();
};

function updateDatabaseList(CountryIsoCode,searchByCountry) {
	jQuery("#pagemap").hide();
	jQuery("#pageinfo").hide();
	jQuery("#regionlist").show();
	var desinventarURL = jQuery('#desinventarURL').val();
	jQuery.get(desinventarURL, 
		{cmd: 'getCountryName', CountryIso : CountryIsoCode },
		function(data) { 
			jQuery("#pagetitle").html('<h3>' + data + '</h3>');
		}
	);
	jQuery.getJSON(desinventarURL,
		{ cmd: 'searchdb', searchdbquery: CountryIsoCode, searchbycountry : searchByCountry },
		function(data) {
			var iCount = 0;
			var RegionId = '';
			var jList = jQuery("#pagecontent");
			jQuery("#pagecontent").empty();
			jQuery.each(data, function(key, value) {
				iCount++;
				RegionId = key;
				jList.append(jQuery('<a href="javascript:void(null)" id="' + key + '">' + value + '</a><br />'));
				jQuery('#' + key).addClass("alt").unbind('click').click(function() {
					displayRegionInfo(key);
				}); //bind
			}); // each
			if (iCount == 1) {
				displayRegionInfo(RegionId);
			}
		} //function
	);
};

function showRegionInfo(RegionId) {
	jQuery("#regionlist" ).hide();
	displayRegionInfo(RegionId);
};

function displayRegionInfo(RegionId) {
	var desinventarURL = jQuery('#desinventarURL').val();
	var desinventarLang = jQuery('#desinventarLang').val();
	jQuery("#pagemap"    ).hide();
	jQuery("#pageinfo"   ).show();
	jQuery("#regionlogo" ).attr('src', desinventarURL + '?cmd=getRegionLogo&RegionId=' + RegionId);
	jQuery("#regionbasicinfo" ).load(desinventarURL, { cmd:'getRegionBasicInfo', RegionId : RegionId });
	jQuery("#regiontechinfo"  ).load(desinventarURL, { cmd:'getRegionTechInfo', RegionId : RegionId });
	jQuery("#regionlink").unbind('click').click(function() {
		window.open(desinventarURL + '?r=' + RegionId + '&lang=' + desinventarLang,'_blank', 
			'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
	});
};

function showMap() {
	jQuery("#pagemap").show();
	jQuery("#regionlist").hide();
	jQuery("#pageinfo").hide();
};

// personalization List menu..
function displayList(elem) {
	lst = 7;
	for (i=1; i <= lst; i++) {
		if (i == elem)
			jQuery("#sect"+ i).show();
		else
			jQuery("#sect"+ i).hide();
	}
}; //function
