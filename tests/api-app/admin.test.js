const request = require('./helpers/httpClient').requestWithCookies
const config = require('./helpers/config')

describe('Admin API Tests', () => {
  it('Requesting an admin url without auth should fail', async () => {
    const res = await request.get('/admin/my-region-id/')
    expect(res.status).toEqual(404)
    expect(res.body.message).toEqual('Access denied')
  })

  test('Request an admin page after login should work', async () => {
    await request.userLogin(config.ADMIN_USERNAME, config.ADMIN_PASSWORD)
    const res = await request.get('/admin/TEST/')
    expect(res.status).toEqual(200)
    await request.userLogout()
  })
})
