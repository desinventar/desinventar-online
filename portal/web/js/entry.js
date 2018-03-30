const main = require('./main.js')
const ready = require('document-ready-promise')
ready().then(function() {
  main.init()
})
