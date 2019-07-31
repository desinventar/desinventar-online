const md5 = require('md5')
const request = require('./helpers/httpClient').requestWithCookies

describe('Admin API Tests', () => {
  test('attempt user login with wrong credentials', async () => {
    let res

    res = await request.get('/session/info')
    expect(res.body.data.isUserLoggedIn).toEqual(false)

    res = await request
      .post('/session/login')
      .send({ username: 'root', password: md5('anything') })
    expect(res.status).toEqual(200)
    expect(res.body.data).toEqual(false)

    res = await request.get('/session/info')
    expect(res.body.data.isUserLoggedIn).toEqual(false)
  })

  test('login with correct credentials', async () => {
    let res

    res = await request.get('/session/info')
    expect(res.body.data.isUserLoggedIn).toEqual(false)

    res = await request
      .post('/session/login')
      .send({ username: 'root', password: md5('desinventar') })
    expect(res.status).toEqual(200)
    expect(res.body.data).toEqual(true)

    res = await request.get('/session/info')
    expect(res.body.data.isUserLoggedIn).toEqual(true)

    res = await request.post('/session/logout')
    expect(res.body.data).toEqual(true)

    res = await request.get('/session/info')
    expect(res.body.data.isUserLoggedIn).toEqual(false)
  })
})
