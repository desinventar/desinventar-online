const config = require('config')
const superagent = require('superagent')
const use = require('superagent-use')
const prefix = require('superagent-prefix')

const request = use(superagent)
request.use(prefix(config.test.api.url))

const requestWithCookies = use(superagent.agent())
requestWithCookies.use(prefix(config.test.api.url))

module.exports = exports = {
  request,
  requestWithCookies
}
