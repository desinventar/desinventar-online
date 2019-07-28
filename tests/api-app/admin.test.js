const request = require('./helpers/httpClient').requestWithCookies

describe('Admin API Tests', () => {
  it('Requesting an admin url without auth should fail', async () => {
    try {
      await request.get('/admin/my-region-id/')
    } catch (err) {
      expect(err.status).toEqual(404)
      expect(err.response.headers['content-type']).toBe(
        'application/json;charset=utf-8'
      )
      expect(err.response.body.message).toEqual('Access denied')
    }
  })
})
