const config = require('config')
const request = require('supertest')(config.test.api.url)

describe('Basic API Tests', () => {
  it('should open root', async () => {
    jest.setTimeout(10000)
    const response = await request.get('/').expect(200)
    expect(response.header['content-type']).toBe('text/html; charset=UTF-8')
  })

  it('should return api version', async () => {
    const response = await request
      .get('/common/version')
      .expect(200)
      .expect('Content-Type', 'application/json;charset=utf-8')
    expect(response.body.data.major_version).toBe('10')
  })

  it('should check error response', async () => {
    await request.get('/non-existent-endpoint').expect(404)
  })

  it('can change the session language', async () => {
    let response = await request.get('/session/info').expect(200)
    expect(response.body.data.language.startsWith('en')).toBe(true)
    let cookies = response.headers['set-cookie']
    let call = request.post('/session/change-language')
    response = await call.set('Cookie', cookies).send({
      language: 'es'
    })
    call = request.get('/session/info')
    response = await call.set('Cookie', cookies)
    expect(response.body.data.language.startsWith('es')).toBe(true)
  })
})
