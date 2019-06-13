const request = require('./helpers/httpClient').requestWithCookies

describe('Bundle API Tests', () => {
  it('should load javascript bundle file', async () => {
    jest.setTimeout(10000)
    const response = await request.get('/scripts/bundle.js')
    expect(response.status).toEqual(200)
    expect(response.header['content-type']).toBe('text/javascript')
  })
})
