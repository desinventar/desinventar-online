import { Selector } from 'testcafe'
import user from './helpers/userHelper'
import { urlWithDatabase } from './helpers/config'

fixture('Database Config Technical Info').page(urlWithDatabase)

test('Edit Database Technical Info', async t => {
  await user.login(t)
  // Open Database Config
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardSetup')
    .expect(Selector('div#DBConfig_tabs', { visibilityCheck: true }).visible)
    .ok()

  // Open Technical Info Tab
  await t
    .click('a[data-id="DBConfig_Info"]')
    .expect(Selector('div.region-info-edit', { visibilityCheck: true }).visible)
    .ok()

  // Confirm that labels are being rendered correctly
  const databaseLabel = await Selector('div.label[data-id="RegionLabel"]')
    .textContent
  await t.expect(databaseLabel.trim()).eql('Database')

  const label = Selector('#RegionLabel')
  const databaseName = await label.value
  // Change database name to something else
  await t
    .typeText(label, 'Test', { replace: true })
    .click('input.save')
    .click('a[data-id="DBConfig_Info"]')
    .expect(label.value)
    .eql('Test')
  // Restore previous database name
  await t
    .typeText(label, databaseName, { replace: true })
    .click('input.save')
    .expect(label.value)
    .eql(databaseName)
})
