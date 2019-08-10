const request = require('./helpers/httpClient').requestWithCookies

describe('Admin API Tests', () => {
  it('Requesting an admin url without auth should fail', async () => {
    const res = await request.get('/admin/my-region-id/')
    expect(res.status).toEqual(404)
    expect(res.body.message).toEqual('Access denied')
  })
})
