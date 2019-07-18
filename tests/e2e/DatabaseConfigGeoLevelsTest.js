import { Selector } from 'testcafe'
import user from './helpers/userHelper'
import { urlWithDatabase } from './helpers/config'

fixture('Database Config Geography Levels').page(urlWithDatabase)

test('Edit Geography Level Info', async t => {
  await user.login(t)
  // Open Database Config
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardSetup')
    .expect(Selector('div#DBConfig_tabs', { visibilityCheck: true }).visible)
    .ok()

  // Open Geography Levels Tab
  await t
    .click('a[data-id="DBConfig_GeoLevels"]')
    .expect(
      Selector('div.region-geolevels-edit', { visibilityCheck: true }).visible
    )
    .ok()

  // Edit geography level
  await t.click(await Selector('#tbodyGeolevels_List').nth(0))
  const geolevelName = Selector('input.GeoLevelName')
  await t
    .expect(await geolevelName.value)
    .eql('Provincia')
    .click('#divGeolevels_Edit .btnSave')
})
