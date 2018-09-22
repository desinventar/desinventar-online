const config = require('config')
const request = require('supertest')(config.test.portal.url)

describe('Bundle API Tests', () => {
  it('should load javascript bundle file', async () => {
    jest.setTimeout(10000)
    const response = await request.get('/scripts/bundle.js').expect(200)
    expect(response.header['content-type']).toBe('text/javascript')
  })
})
