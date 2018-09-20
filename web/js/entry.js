import main from './main'
import ready from 'document-ready-promise'

ready().then(function() {
  main.init()
})
