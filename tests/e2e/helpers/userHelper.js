const config = require('config')
import { Selector } from 'testcafe'

const userHelper = {
  login: async t => {
    await t
      .click('#mnuUser')
      .click('#mnuUserLogin')
      .typeText('form#frmUserLogin input#fldUserId', config.test.web.username, {
        replace: true
      })
      .typeText(
        'form#frmUserLogin input#fldUserPasswd',
        config.test.web.passwd,
        {
          replace: true
        }
      )
      .click('form#frmUserLogin a.button.Send')
      .expect(Selector('table#mnuUser button[type="button"]').textContent)
  }
}

export default userHelper
