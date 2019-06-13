const request = require('./helpers/httpClient').requestWithCookies

describe('Maps API Tests', () => {
  it('Attempt to download a non-existent KML file', async () => {
    try {
      await request.get('/maps/kml/non-existent-map-id')
    } catch (err) {
      expect(err.status).toEqual(404)
      expect(err.response.headers['content-type']).toBe(
        'application/json;charset=utf-8'
      )
    }
  })
})
