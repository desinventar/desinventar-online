function onReadyImport() {
  jQuery('div.divImport').hide()
  jQuery('div#divImportSelectFile').show()
}

function sendForm() {
  var fr = document.getElementById('iframe2')
  var im = document.getElementById('divDatacardsImport')
  fr.src = jQuery('#desinventarURL').val() + '/images/loading.gif'
  im.submit()
}

function enadisField(lnow, lnext, val) {
  var sour = document.getElementById(lnow)
  if (val) sour.disabled = false
  else {
    sour.disabled = true
    fillColumn(lnow, lnext, false)
    for (var i = sour.length - 1; i >= 0; i--) {
      sour.remove(i)
    }
  }
}

function fillColumn(lnow, lnext, exclude) {
  var sour = document.getElementById(lnow)
  var dest = document.getElementById(lnext)
  var test
  // clean dest list
  for (var i = dest.length - 1; i >= 0; i--) {
    dest.remove(i)
  }
  for (var i = 0; i < sour.length; i++) {
    test = true
    if (exclude) {
      test = !sour[i].selected
    }
    if (test) {
      var opt = document.createElement('option')
      opt.value = sour[i].value
      opt.text = sour[i].text
      var pto = dest.options[i]
      try {
        dest.add(opt, pto)
      } catch (ex) {
        dest.add(opt, i)
      }
    }
  }
}
