const config = require('config')
const supertest = require('supertest')
const md5 = require('md5')

const requestWithCookies = supertest.agent(config.test.api.url)

requestWithCookies.userLogin = async function(username, password) {
  await this.post('/session/login').send({
    username: username,
    password: md5(password)
  })
  return this
}

requestWithCookies.userLogout = async function() {
  await this.post('/session/logout')
  return this
}

module.exports = exports = {
  request: supertest(config.test.api.url),
  requestWithCookies
}
