const supertest = require('supertest')
const md5 = require('md5')

const testUrl = process.env.TEST_API_URL || 'http://localhost:8080'
const requestWithCookies = supertest.agent(testUrl)

async function userLogin(username, password) {
  await this.post('/session/login').send({
    username: username,
    password: md5(password)
  })
  return this
}

async function userLogout() {
  await this.post('/session/logout')
  return this
}

module.exports = exports = {
  request: supertest(testUrl),
  requestWithCookies: Object.assign(requestWithCookies, {
    userLogin,
    userLogout
  })
}
