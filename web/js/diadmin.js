/* global Ext */

var mod = ''
var reg = ''

function uploadMsg(msg) {
  if (mod != '') {
    var mydiv = $(mod + 'statusmsg')
    mydiv.innerHTML = msg
  }
}

function updateList(divSelector, url, pars, callback) {
  jQuery('#' + divSelector).load(url, pars, function(response, status, xhr) {
    // Hide first two columns (EventId,EventPredefined)
    jQuery('td:nth-child(1)', '#tblEventListUser,#tblEventListPredef').hide()
    jQuery('td:nth-child(2)', '#tblEventListUser,#tblEventListPredef').hide()
    // Hide first two columns (CauseId,CausePredefined)
    jQuery('td:nth-child(1)', '#tblCauseListUser,#tblCauseListPredef').hide()
    jQuery('td:nth-child(2)', '#tblCauseListUser,#tblCauseListPredef').hide()
  })
}

function getGeoItems(regionId, geoid, l, lev, src) {
  let ele
  let div
  if (src == 'DATA') {
    div = window.parent.frames['dif'].document.getElementById('lev' + l)
    ele = window.parent.frames['dif'].document.getElementById('geolev' + l)
  } else {
    div = $('lev' + l)
    ele = $('geolev' + l)
  }
  const geo = geoid.substr(0, (l + 1) * 5)
  for (var w = 0; w < ele.length; w++) {
    if (ele.options[w].value == geo) ele.selectedIndex = w
  }
  if (l < lev) {
    new Ajax.Updater(div, jQuery('#desinventarURL').val() + '/cards.php', {
      method: 'get',
      parameters:
        'r=' +
        regionId +
        '&cmd=list&GeographyId=' +
        geo +
        '&t=' +
        new Date().getTime(),
      onComplete: function() {
        getGeoItems(reg, geoid, l + 1, lev, src)
      }
    })
  }
}

function disab(field) {
  if (field != null) {
    field.disabled = true
    field.className = 'disabled'
  }
}

function enab(field) {
  if (field != null) {
    field.disabled = false
    field.className = ''
  }
}

function combineForms(dcf, ref) {
  var dc = $(dcf)
  var rf = $(ref).elements
  var ih = null
  for (var i = 0; i < rf.length; i++) {
    if (rf[i].disabled == false) {
      ih = document.createElement('input')
      ih.type = 'hidden'
      ih.value = rf[i].value
      ih.name = rf[i].name
      dc.appendChild(ih)
    }
  }
}

