const config = require('config')
const request = require('supertest')(config.test.api.url)

describe('Maps API Tests', () => {
  it('Attempt to download a non-existent KML file', async () => {
    const response = await request
      .get('/maps/kml/non-existent-map-id')
      .expect(404)
    expect(response.header['content-type']).toBe(
      'application/json;charset=utf-8'
    )
  })
})
