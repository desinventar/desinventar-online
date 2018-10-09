import { Selector } from 'testcafe'
import user from './helpers/user'
import { urlWithDatabase } from './helpers/config'

fixture('Database Config Additional Fields').page(urlWithDatabase)

test('Edit Database Additional Fields', async t => {
  await user.login(t)
  // Open Database Config
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardSetup')
    .expect(Selector('div#DBConfig_tabs', { visibilityCheck: true }).visible)
    .ok()

  // Open Additional Effects Tab
  await t
    .click('a[data-id="DBConfig_AdditionalEffects"]')
    .expect(
      Selector('table.database-admin-eefield-list', { visibilityCheck: true })
        .visible
    )
    .ok()
})
