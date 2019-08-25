import { Selector } from 'testcafe'
import user from './helpers/userHelper'
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

  // Add a new additional effect record
  const name = `Test-${Date.now()}`
  const description = `Description for ${name}`
  const typeSelect = Selector('#EEFieldType')
  const typeOption = typeSelect.find('option')
  await t
    .click('#btnEEFieldAdd')
    .expect(Selector('#extraeffaddsect', { visibilityCheck: true }).visible)
    .ok()
    .typeText('#EEFieldLabel', name, { replace: true })
    .typeText('#EEFieldDesc', description, { replace: true })
    .click(typeSelect)
    .click(typeOption.filter('[value="INTEGER"]'))
    .click('#EEFieldActive')
    .click('#btnSave')
    .expect(Selector('#msgEEFieldStatusOk', { visibilityCheck: true }).visible)
    .ok()

  // Load the new record from the list
  await t
    .click(Selector('tr').filter(`[data-name="${name}"]`))
    .expect(Selector('#extraeffaddsect', { visibilityCheck: true }).visible)
    .ok()
    .expect(Selector('#EEFieldLabel').value)
    .eql(name)
    .expect(Selector('#EEFieldDesc').value)
    .eql(description)
    .expect(Selector('#EEFieldType').value)
    .eql('INTEGER')
    .expect(Selector('#EEFieldType').hasAttribute('disabled'))
    .ok()
    .expect(Selector('#EEFieldActive').checked)
    .eql(true)
    .expect(Selector('#EEFieldPublic').checked)
    .eql(false)
})
