const config = require('config')
const request = require('supertest')(config.test.api.url)

describe('Basic API Tests', () => {
  it('should open root', async () => {
    const response = await request.get('/').expect(200)
    expect(response.header['content-type']).toBe(
      'application/json;charset=utf-8'
    )
    expect(response.text).toMatchSnapshot()
  })

  it('should return api version', async () => {
    const response = await request
      .get('/common/version')
      .expect(200)
      .expect('Content-Type', 'application/json;charset=utf-8')
    expect(response.body.data.major_version).toBe('10')
  })

  it('should check error response', async () => {
    await request.get('non-existent-endpoint').expect(302)
  })
})
