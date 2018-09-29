import { Selector } from 'testcafe'
import user from './helpers/user'
import { url } from './helpers/config'

fixture('Admin Users Test').page(url)

test('Admin Users', async t => {
  await user.login(t)
  await t
    .expect(Selector('div#divRegionList', { visibilityCheck: true }).visible)
    .ok()
    .click('#mnuUser')
    .click('#mnuUserAccountManagement')
    .expect(
      Selector('#divAdminUsersContent', { visibilityCheck: true }).visible
    )
    .ok()

  const userActive = Selector('#chkUserActive')
  await t
    .click('span.UserId[data-id="none"]')
    .expect(Selector('input#txtUserId', { visibilityCheck: true }).visible)
    .ok()
    .expect(userActive.checked)
    .ok()
    .click(userActive)
    .click('#btnUserEditSubmit')
    .click('span.UserId[data-id="none"]')
    .expect(userActive.checked)
    .notOk()
    .click(userActive)
    .click('#btnUserEditSubmit')
    .click('span.UserId[data-id="none"]')
    .expect(userActive.checked)
    .ok()
})
