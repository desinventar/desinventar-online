/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
function onReadyPortal() {
  var desinventarLang = jQuery('#desinventarLang').val()

  jQuery('#btnMainWindow').attr('href', jQuery('#desinventarURL').val() + '/')

  // Main button to open a default desinventar
  jQuery('#btnMainWindow').click(function() {
    jQuery(this).attr(
      'href',
      jQuery('#desinventarURL').val() + '/' + '?lang=' + desinventarLang
    )
    return true
  })

  // Update version number on screen
  jQuery('#txtVersion').text(jQuery('#desinventarVersion').val())

  // Handle clicks on mainpage map
  jQuery('area').click(function() {
    var country = jQuery(this).attr('alt')
    if (country != '') {
      updateDatabaseList(country, 1)
    }
    // Prevent default action
    return false
  })

  // Handle clicks on RegionGroup Items
  jQuery('.RegionGroup').click(function() {
    var group = jQuery(this).attr('alt')
    if (group != '') {
      var expanded = jQuery(this).attr('expanded')
      if (expanded == 'yes') {
        jQuery('#sect' + group).hide()
        jQuery(this).attr('expanded', 'no')
      } else {
        jQuery('#sect' + group).show()
        jQuery(this).attr('expanded', 'yes')
      }
      //displayList(group);
    }
    return false
  })

  // Expand some region groups at start
  jQuery('.RegionGroup[expand=yes]').trigger('click')

  // Handle clicks on countries that return a list of regions
  jQuery('.RegionList').click(function() {
    var country = jQuery(this).attr('alt')
    if (country != '') {
      updateDatabaseList(country, 1)
    }
    // Prevent default action
    return false
  })

  // Handle clicks on countries that directly shows a Region
  jQuery('.RegionItem').click(function() {
    var RegionId = jQuery(this).attr('alt')
    if (RegionId != '') {
      showRegionInfo(RegionId)
    }
    // Prevent default action
    return false
  })

  jQuery('body').bind('UserUpdateInfo', function() {
    UserName = jQuery('#desinventarUserFullName').val()
    UserId = jQuery('#desinventarUserId').val()
    if (UserId != '') {
      jQuery('#txtUserInfo').text(UserId + ' ' + UserName)
      jQuery('#txtUserFullName').text(UserName)
      jQuery('#txtUserId').text('(' + UserId + ')')
    } else {
      jQuery('#txtUserInfo').text('')
      jQuery('#txtUserFullName').text('')
      jQuery('#txtUserId').text('')
    }
  })

  jQuery('body').bind('UserLoggedIn', function() {
    jQuery('.lstUserMenu').show()
    jQuery('#lstUserLogin').hide()
    jQuery('#divUserIsLoggedIn').show()
    jQuery('#divUserIsLoggedOut').hide()
    updateDatabaseListByUser()
    jQuery('body').trigger('UserUpdateInfo')
  })

  jQuery('body').bind('UserLoggedOut', function() {
    // Update User Menu
    jQuery('.lstUserMenu').show()
    jQuery('#lstUserLogout').hide()
    jQuery('#divUserIsLoggedIn').hide()
    jQuery('#divUserIsLoggedOut').show()
    jQuery('#frmUserLogin').hide()
    jQuery('body').trigger('UserUpdateInfo')
    showMap()
  })

  // Handle clicks on right menu (Home/LangSelect)
  jQuery('.MenuItem').click(function() {
    var MenuItem = jQuery(this).attr('id')
    if (MenuItem == 'mnuShowMap') {
      displayPortal('portal')
      showMap()
    }
    // Prevent default action
    return false
  })

  jQuery('#linkShowUserLogin').click(function() {
    jQuery('#frmUserLogin').toggle()
    return false
  })

  jQuery('#linkUserRegionList').click(function() {
    updateDatabaseListByUser()
    return false
  })

  jQuery('#linkUserLogout').click(function() {
    doUserLogout()
    return false
  })

  // Top Menu/Language Select
  jQuery('#MainMenu').clickMenu()
  // Remove the black border from the menu
  jQuery('div.cmDiv').css('border', '0px solid black')

  displayPortal(jQuery('#desinventarPortalType').val())

  jQuery('#linkPortalGAR2011').click(function() {
    displayPortal('gar-isdr-2011')
    return false
  })

  if (jQuery('#desinventarUserId').val() != '') {
    jQuery('body').trigger('UserLoggedIn')
  } else {
    jQuery('body').trigger('UserLoggedOut')
  }

  jQuery('.regionlink').on('click', function(event) {
    var RegionId = jQuery('#desinventarRegionId').val()
    var LangIsoCode = jQuery('#desinventarLang').val()
    window.open(jQuery('#desinventarURL').val() + '/#' + RegionId)
    return false
  })

  jQuery.post(
    jQuery('#desinventarURL').val() + '/',
    {
      cmd: 'cmdGetVersion'
    },
    function(data) {
      if (parseInt(data.Status) > 0) {
        jQuery('#txtVersion').text(data.Version)
      }
    },
    'json'
  )
  //Initialization code
  jQuery('body').trigger('cmdUserGetInfo')
} //onReadyPortal()

function displayPortal(myPortal) {
  // Select which portal to display : main, gar2009, gar2011
  jQuery('.divBlock').hide()
  jQuery('#desinventarPortalType').val(myPortal)
  switch (myPortal) {
    case 'gar2009':
    case 'gar-isdr-2009':
      jQuery('.divBlockGAR2009').show()
      break
    case 'gar-isdr-2011':
    case 'gar2011':
      jQuery('.divBlockGAR2011').show()
      break
    default:
      jQuery('.divBlockSouthAmerica').show()
      // At start, display the map
      showMap()
      break
  }
}

function showRegionInfo(RegionId) {
  jQuery('.contentBlock').hide()
  jQuery('.contentRegionBlock').hide()
  displayRegionInfo(RegionId)
}

function displayRegionInfo(RegionId) {
  jQuery('.contentBlock').hide()
  jQuery('#pageinfo').hide()
  doGetRegionInfo(RegionId)
  jQuery('#desinventarRegionId').val(RegionId)
  jQuery('#regionBlock').show()
  jQuery('#pageinfo').show()
}

function showMap() {
  jQuery('.contentBlock').hide()
  jQuery('#pagemap').show()
}

// personalization List menu..
function displayList(elem) {
  lst = 7
  for (i = 1; i <= lst; i++) {
    if (i == elem) jQuery('#sect' + i).show()
    else jQuery('#sect' + i).hide()
  }
} //function
