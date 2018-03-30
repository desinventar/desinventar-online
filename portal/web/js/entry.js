// portal - entry.js
const ready = require('document-ready-promise')

import main from './main'
import user from './user_login'
ready().then(function() {
  main.init()
  user.onReadyUserLogin()
  // user.onReadyPortal()
})
