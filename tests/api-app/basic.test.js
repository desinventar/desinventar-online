const request = require('./helpers/httpClient').requestWithCookies

describe('Basic API Tests', () => {
  it('should open root', async () => {
    jest.setTimeout(10000)
    const response = await request.get('/')
    expect(response.status).toEqual(200)
    expect(response.header['content-type']).toBe('text/html; charset=UTF-8')
  })

  it('should return api version', async () => {
    const response = await request.get('/common/version')
    expect(response.status).toEqual(200)
    expect(response.header['content-type']).toEqual(
      'application/json;charset=utf-8'
    )
    expect(response.body.data.major_version).toBe('10')
  })

  it('should check error response', async () => {
    try {
      await request.get('/non-existent-endpoint')
    } catch (err) {
      expect(err.status).toEqual(404)
    }
  })

  it('can change the session language', async () => {
    let response = await request.get('/session/info')
    expect(response.status).toEqual(200)
    expect(response.body.data.language.startsWith('en')).toBe(true)

    await request.post('/session/change-language').send({
      language: 'es'
    })

    response = await request.get('/session/info')
    expect(response.body.data.language.startsWith('es')).toBe(true)

    response = await request.post('/session/change-language').send({
      language: 'non-existent'
    })
    expect(response.body.errors[0].message).toBe('Invalid Language Code')
    response = await request.get('/session/info')
    expect(response.body.data.language.startsWith('es')).toBe(true)
  })
})
