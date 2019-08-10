const config = require('config')
const supertest = require('supertest')

module.exports = exports = {
  request: supertest(config.test.api.url),
  requestWithCookies: supertest.agent(config.test.api.url)
}
