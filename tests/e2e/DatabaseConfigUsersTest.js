import { Selector } from 'testcafe'
import user from './helpers/user'
import { urlWithDatabase } from './helpers/config'

fixture('Database Config Users').page(urlWithDatabase)

test('Database Open', async t => {
  await user.login(t)
  // Open Database Config
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardSetup')
    .expect(Selector('div#DBConfig_tabs', { visibilityCheck: true }).visible)
    .ok()

  // Open Config Users tab
  await t
    .click('a[data-id="DBConfig_Users"]')
    .expect(Selector('div.clsDatabaseUsers', { visibilityCheck: true }).visible)
    .ok()

  // Choose Category=Official and Save
  await t
    .click('select.RegionOrder')
    .click(Selector('option.category-official'))
    .click('#frmDiffusion a.button.btnSave')

  await t
    // Switch to Info Tab
    .click('a[data-id="DBConfig_Info"]')
    // Switch back to Users tab and verify the changes made
    .click('a[data-id="DBConfig_Users"]')
    .expect(Selector('select.RegionOrder').selectedIndex)
    .eql(1)
})
