function onReadyPortal() {
	var desinventarURL  = jQuery('#fldDesinventarURL').val();
	var desinventarLang = jQuery('#fldDesinventarLang').val();
	
	// Main button to open a default desinventar
	jQuery("#btnMainWindow").click(function() {
		window.open(desinventarURL + '?cmd=main&lang=' + desinventarLang,'_blank', 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
		return false;
	});
	
	// Update version number on screen
	jQuery('#version').text(jQuery('#fldDesinventarVersion').val());
	
	// Handle clicks on mainpage map
	jQuery('area').click(function() {
		var country = jQuery(this).attr('alt');
		if (country != '') {
			updateDatabaseList(country, 1);
		}
		// Prevent default action
		return false;
	});
	
	// Handle clicks on RegionGroup Items
	jQuery('.RegionGroup').click(function() {
		var group = jQuery(this).attr('alt');
		if (group != '') {
			displayList(group);
		}
		return false;
	});

	// Handle clicks on countries that return a list of regions
	jQuery('.RegionList').click(function() {
		var country = jQuery(this).attr('alt');
		if (country != '') {
			updateDatabaseList(country, 1);
		}
		// Prevent default action
		return false;
	});

	// Handle clicks on countries that directly shows a Region
	jQuery('.RegionItem').click(function() {
		var RegionId = jQuery(this).attr('alt');
		if (RegionId != '') {
			showRegionInfo(RegionId);
		}
		// Prevent default action
		return false;
	});

	jQuery('body').bind('UserUpdateInfo', function() {
		UserName = jQuery('#fldDesinventarUserFullName').val();
		UserId   = jQuery('#fldDesinventarUserId').val();
		if (UserId != '') {
			jQuery('#txtUserFullName').text(UserName);
			jQuery('#txtUserId').text('(' + UserId + ')');
		} else {
			jQuery('#txtUserFullName').text('');
			jQuery('#txtUserId').text('');
		}
	});
		
	jQuery('body').bind('UserLoggedIn', function() {
		jQuery('.lstUserMenu').show();
		jQuery('#lstUserLogin').hide();
		jQuery('#divUserIsLoggedIn').show();
		jQuery('#divUserIsLoggedOut').hide();
		updateDatabaseListByUser();
		jQuery('body').trigger('UserUpdateInfo');
	});

	jQuery('body').bind('UserLoggedOut', function() {
		// Update User Menu
		jQuery('.lstUserMenu').show();
		jQuery('#lstUserLogout').hide();
		jQuery('#divUserIsLoggedIn').hide();
		jQuery('#divUserIsLoggedOut').show();
		jQuery('#frmUserLogin').hide();
		jQuery('body').trigger('UserUpdateInfo');
	});
	
	// Handle clicks on right menu (Home/LangSelect)
	jQuery('.MenuItem').click(function() {
		var MenuItem = jQuery(this).attr('id');
		if (MenuItem == 'mnuShowMap') {
			showMap();
		}
		// Prevent default action
		return false;
	});
	
	jQuery('#linkShowUserLogin').click(function() {
		jQuery('#frmUserLogin').show();
		return false;
	});

	jQuery('#linkUserRegionList').click(function() {
		updateDatabaseListByUser();
		return false;
	});
	
	jQuery('#linkUserLogout').click(function() {
		doUserLogout();
		return false;
	});

	// Top Menu/Language Select
	jQuery('#MainMenu').clickMenu();
	// Remove the black border from the menu
	jQuery('div.cmDiv').css('border','0px solid black');

	// At start, display the map 
	showMap();
	
	if (jQuery('#fldDesinventarUserId').val() != '') {
		jQuery('body').trigger('UserLoggedIn');
	} else {
		jQuery('body').trigger('UserLoggedOut');
	}		
};

function updateDatabaseList(CountryIsoCode,searchByCountry) {
	jQuery(".portalcontent").hide();
	jQuery("#regionlist").show();
	var desinventarURL = jQuery('#fldDesinventarURL').val();
	jQuery.get(desinventarURL, 
		{cmd: 'getCountryName', CountryIso : CountryIsoCode },
		function(data) { 
			jQuery("#pagetitle").html('<h3>' + data + '</h3>');
		}
	);
	jQuery.post(desinventarURL,
		{ cmd: 'searchdb', searchdbquery: CountryIsoCode, searchbycountry : searchByCountry },
		function(data) {
			var iCount = 0;
			var RegionId = '';
			var jList = jQuery("#pagecontent");
			jQuery("#pagecontent").empty();
			jQuery.each(data, function(key, value) {
				iCount++;
				RegionId = key;
				jList.append(jQuery('<a href="#" id="' + key + '">' + value.RegionLabel + ' (' + value.Role + ')' + '</a><br />'));
				jQuery('#' + key).addClass("alt").unbind('click').click(function() {
					displayRegionInfo(key);
					return false;
				}); //bind
			}); // each
			if (iCount == 1) {
				displayRegionInfo(RegionId);
			}
		}, //function
		'json'
	);
};

function updateDatabaseListByUser() {
	jQuery(".portalcontent").hide();
	jQuery("#regionlist").show();
	var desinventarURL = jQuery('#fldDesinventarURL').val();
	jQuery("#pagetitle").html('<h3></h3>');
	jQuery.post(desinventarURL,
		{ cmd: 'searchdb', 
		  searchdbquery: '', 
		  searchbycountry : 0},
		function(data) {
			var iCount = 0;
			var RegionId = '';
			var jList = jQuery("#pagecontent");
			jQuery("#pagecontent").empty();
			jQuery.each(data, function(key, value) {
				iCount++;
				RegionId = key;
				jList.append(jQuery('<a href="#" id="' + key + '">' + value.RegionLabel + ' (' + value.Role + ')' + '</a><br />'));
				jQuery('#' + key).addClass("alt").unbind('click').click(function() {
					displayRegionInfo(key);
					return false;
				}); //bind
			}); // each
			if (iCount == 1) {
				displayRegionInfo(RegionId);
			}
		},
		'json' //function
	);
};

function showRegionInfo(RegionId) {
	jQuery(".portalcontent" ).hide();
	displayRegionInfo(RegionId);
};

function displayRegionInfo(RegionId) {
	var desinventarURL = jQuery('#fldDesinventarURL').val();
	var desinventarLang = jQuery('#fldDesinventarLang').val();
	jQuery(".portalcontent").hide();
	jQuery("#pageinfo"     ).show();
	jQuery("#regionlogo"   ).attr('src', desinventarURL + '?cmd=getRegionLogo&RegionId=' + RegionId);
	jQuery("#regionbasicinfo" ).load(desinventarURL, { cmd:'getRegionBasicInfo', RegionId : RegionId });
	jQuery("#regiontechinfo"  ).load(desinventarURL, { cmd:'getRegionTechInfo', RegionId : RegionId });
	jQuery("#regionlink").unbind('click').click(function() {
		window.open(desinventarURL + '?r=' + RegionId + '&lang=' + desinventarLang,'_blank', 
			'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
		return false;
	});
};

function showMap() {
	jQuery('.portalcontent').hide();
	jQuery('#pagemap').show();
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
