import { Selector } from 'testcafe'
import config from 'config'
import { url } from './helpers/config'

fixture('User Login Page').page(url)

test('User Login', async t => {
  await t
    .expect(Selector('div#divRegionList').exists)
    .ok()
    .expect(Selector('div#divRegionList', { visibilityCheck: true }).visible)
    .ok()
    .click('#mnuUser')
    .click('#mnuUserLogin')
    .expect(
      Selector('div#divUserLoginContent', { visibilityCheck: true }).visible
    )
    .ok()
    .click('form#frmUserLogin a.button.Send') // Login without entering data
    .expect(
      Selector('div.UserLogin span.msgEmptyFields', { visibilityCheck: true })
        .visible
    )
    .ok()
    .typeText('form#frmUserLogin input#fldUserId', config.test.web.username)
    .typeText('form#frmUserLogin input#fldUserPasswd', 'wrongpasswd')
    .click('form#frmUserLogin a.button.Send')
    .expect(
      Selector('div.UserLogin span.msgInvalidPasswd', { visibilityCheck: true })
        .visible
    )
    .ok()
    .typeText('form#frmUserLogin input#fldUserId', config.test.web.username, {
      replace: true
    })
    .typeText('form#frmUserLogin input#fldUserPasswd', config.test.web.passwd, {
      replace: true
    })
    .click('form#frmUserLogin a.button.Send')
    .expect(Selector('table#mnuUser button[type="button"]').textContent)
    .eql('User : root')
})
