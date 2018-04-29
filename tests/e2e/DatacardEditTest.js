import { Selector } from 'testcafe'
import user from './helpers/user.js'
import { urlWithDatabase } from './helpers/config'

fixture('Datacard Edit Page').page(urlWithDatabase)

test('User Login', async t => {
  await user.login(t)

  // Open Datacard Edit window
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardEdit')
    .expect(Selector('#divDatacardWindow', { visibilityCheck: true }).visible)
    .ok()

  // Create a new datacard
  const now = new Date()
  const year = now.getUTCFullYear()
  await t
    .click('#btnDatacardNew')
    .typeText('#DisasterBeginTime0', `${year}`)
    .pressKey('tab')
  // Verify the suggested serial value
  const serial = await Selector('#DisasterSerial').value
  await t.expect(RegExp(`^${year}-[0-9]{5}`).test(serial)).eql(true)
})
