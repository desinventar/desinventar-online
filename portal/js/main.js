/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/

import common from './common'
import user from './user_login'

const me = {}
me.init = () => {
  jQuery('#btnMainWindow').attr('href', jQuery('#desinventarURL').val() + '/')

  // Main button to open a default desinventar
  jQuery('#btnMainWindow').click(function() {
    jQuery(this).attr('href', jQuery('#desinventarURL').val() + '/')
    return true
  })

  // Handle clicks on mainpage map
  jQuery('area').click(function() {
    var country = jQuery(this).attr('alt')
    if (country != '') {
      common.updateDatabaseList(country, 1)
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
      common.updateDatabaseList(country, 1)
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
    const UserName = jQuery('#desinventarUserFullName').val()
    const UserId = jQuery('#desinventarUserId').val()
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
    common.updateDatabaseListByUser()
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
      displayPortal(jQuery('#desinventarPortalType').val())
      showMap()
    }
    // Prevent default action
    return false
  })

  jQuery('span.change-language').on('click', event => {
    jQuery.post(
      '/change-language',
      {
        language: jQuery(event.target).data('language')
      },
      function() {
        window.location.reload(false)
      },
      'json'
    )
    return false
  })

  jQuery('#linkShowUserLogin').click(function() {
    jQuery('#frmUserLogin').toggle()
    return false
  })

  jQuery('#linkUserRegionList').click(function() {
    common.updateDatabaseListByUser()
    return false
  })

  jQuery('#linkUserLogout').click(function() {
    user.doUserLogout()
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

  jQuery('.regionlink').on('click', function() {
    var RegionId = jQuery('#desinventarRegionId').val()
    window.open(jQuery('#desinventarURL').val() + '/#' + RegionId)
    return false
  })

  //Initialization code
  jQuery('body').trigger('cmdUserGetInfo')
}

function displayPortal(myPortal) {
  // Select which portal to display : main, gar2009, gar2011
  jQuery('.divBlock').hide()
  //jQuery('#desinventarPortalType').val(myPortal)
  switch (myPortal) {
    case 'gar2009':
    case 'gar-isdr-2009':
      jQuery('.divBlockGAR2009').show()
      break
    case 'gar-isdr-2011':
    case 'gar2011':
      jQuery('.divBlockGAR2011').show()
      break
    case 'covid-19-col':
      common.doGetRegionInfo('COL-2020320184328')
      jQuery('.divCovid-19-Col').show()
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
  common.displayRegionInfo(RegionId)
}

function showMap() {
  jQuery('.contentBlock').hide()
  jQuery('#pagemap').show()
}

// personalization List menu..
me.displayList = elem => {
  let lst = 7
  for (var i = 1; i <= lst; i++) {
    if (i == elem) jQuery('#sect' + i).show()
    else jQuery('#sect' + i).hide()
  }
}

export default me
