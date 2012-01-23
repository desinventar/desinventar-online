function onReadyPortal() {
	// Run common functions in web/portal
	onReadyCommon();

	var desinventarURL  = jQuery('#desinventarURL').val();
	var desinventarLang = jQuery('#desinventarLang').val();
	
	// Main button to open a default desinventar
	jQuery("#btnMainWindow").click(function() {
		window.open(desinventarURL + '?cmd=main&lang=' + desinventarLang,'_blank', 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
		return false;
	});
	
	// Update version number on screen
	jQuery('#txtVersion').text(jQuery('#desinventarVersion').val());
	
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
			var expanded = jQuery(this).attr('expanded');
			if (expanded == 'yes') {
				jQuery('#sect' + group).hide();
				jQuery(this).attr('expanded','no');
			} else {
				jQuery('#sect' + group).show();
				jQuery(this).attr('expanded','yes');
			}
			//displayList(group);
		}
		return false;
	});

	// Expand some region groups at start
	jQuery('.RegionGroup[expand=yes]').trigger('click');

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
		UserName = jQuery('#desinventarUserFullName').val();
		UserId   = jQuery('#desinventarUserId').val();
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
		doUpdateDatabaseListByUser();
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
		showMap();
	});
	
	// Handle clicks on right menu (Home/LangSelect)
	jQuery('.MenuItem').click(function() {
		var MenuItem = jQuery(this).attr('id');
		if (MenuItem == 'mnuShowMap') {
			displayPortal('portal');
			showMap();
		}
		// Prevent default action
		return false;
	});
	
	jQuery('#linkShowUserLogin').click(function() {
		jQuery('#frmUserLogin').toggle();
		return false;
	});

	jQuery('#linkUserRegionList').click(function() {
		doUpdateDatabaseListByUser();
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
	
	displayPortal(jQuery('#desinventarPortalType').val());

	jQuery('#linkPortalGAR2011').click(function() {
		displayPortal('gar2011');
		return false;
	});
	
	if (jQuery('#desinventarUserId').val() != '') {
		jQuery('body').trigger('UserLoggedIn');
	} else {
		jQuery('body').trigger('UserLoggedOut');
	}
	
	jQuery(".regionlink").unbind('click').click(function() {
		var RegionId = jQuery('#desinventarRegionId').val();
		var LangIsoCode = jQuery('#desinventarLang').val();
		window.open(desinventarURL + '?r=' + RegionId + '&lang=' + desinventarLang,'_blank',
			'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');
		return false;
	});
}; //onReadyPortal()

function displayPortal(myPortal) 
{
	// Select which portal to display : main, gar2009, gar2011
	jQuery('.divBlock').hide();
	jQuery('#desinventarPortalType').val(myPortal);
	switch(myPortal)
	{
		case 'gar2009':
			jQuery('.divBlockGAR2009').show();
		break;
		case 'gar2011':
			jQuery('.divBlockGAR2011').show();
		break;
		default:
			jQuery('.divBlockSouthAmerica').show();
			// At start, display the map 
			showMap();
		break;
	}
}

function showRegionInfo(RegionId) {
	jQuery(".contentBlock" ).hide();
	displayRegionInfo(RegionId);
}

function displayRegionInfo(RegionId) {
	jQuery(".contentBlock").hide();
	jQuery("#pageinfo").hide();
	doGetRegionInfo(RegionId);
	jQuery('#desinventarRegionId').val(RegionId);
	jQuery('#pageinfo').show();
}

function showMap() {
	jQuery('.contentBlock').hide();
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
