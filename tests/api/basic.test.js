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

    response = await request
      .post('/session/change-language')
      .set('Cookie', cookies)
      .send({
        language: 'es'
      })

    response = await request.get('/session/info').set('Cookie', cookies)
    expect(response.body.data.language.startsWith('es')).toBe(true)

    response = await request
      .post('/session/change-language')
      .set('Cookie', cookies)
      .send({
        language: 'non-existent'
      })
    expect(response.body.errors[0].message).toBe('Invalid Language Code')
    response = await request.get('/session/info').set('Cookie', cookies)
    expect(response.body.data.language.startsWith('es')).toBe(true)
  })
})
