import { Selector } from 'testcafe'
import config, { url } from './helpers/config'
fixture('Portal Page').page(url)

test('Main Page', async t => {
  await t
    .expect(Selector('div#pagemap').exists)
    .ok()
    .expect(Selector('div#pagemap', { visibilityCheck: true }).visible)
    .ok()
})

test('User Login/Logout', async t => {
  // Validate initial fields visibility
  await t
    .expect(Selector('#linkShowUserLogin').visible)
    .ok()
    .expect(Selector('#linkUserLogout').visible)
    .eql(false)

  // Login
  await t
    .click('#linkShowUserLogin')
    .expect(Selector('#fldUserId', { visibilityCheck: true }).visible)
    .ok()
    .expect(Selector('#fldUserPasswd', { visibilityCheck: true }).visible)
    .ok()
    .typeText('#fldUserId', config.username, {
      replace: true
    })
    .typeText('#fldUserPasswd', config.passwd, {
      replace: true
    })
    .click('input[value="Login"]')
    .expect(Selector('#linkUserLogout').visible)
    .ok()
  // Logout
  await t
    .click('#linkUserLogout')
    .expect(Selector('#linkUserLogout').visible)
    .eql(false)
})
