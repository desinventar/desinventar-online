import { Selector } from 'testcafe'
import user from './helpers/userHelper'
import { urlWithDatabase } from './helpers/config'

fixture('Datacard Edit Page').page(urlWithDatabase)

test('Datacard Edit Dialog', async t => {
  await user.login(t)

  // Open Datacard Edit window
  await t
    .click('#mnuDatacard')
    .click('#mnuDatacardEdit')
    .expect(Selector('#divDatacardWindow', { visibilityCheck: true }).visible)
    .ok()

  // Create a new datacard
  const msgDatacardFill = Selector('#msgDatacardFill')
  const state = Selector('#DICard #Status')
  const now = new Date()
  const year = now.getUTCFullYear()
  await t
    .click('#btnDatacardNew')
    .expect(state.value)
    .eql('NEW')
    .expect(msgDatacardFill.visible)
    .ok()
    .typeText('#DisasterBeginTime0', `${year}`)
    .pressKey('tab')

  // Verify the suggested serial value
  const serial = await Selector('#DisasterSerial').value
  await t.expect(RegExp(`^${year}-[0-9]{5}`).test(serial)).eql(true)

  // Attempt to type characters into a numeric field
  const roads = Selector('#EffectRoads')
  await t
    .typeText(roads, 'a5f6b7h.o3', { replace: true })
    .expect(roads.value)
    .eql('567.3')

  // Set required values
  const geographySelect = Selector('select#GeoLevel0')
  const eventIdSelect = Selector('select#EventId')
  const causeIdSelect = Selector('select#CauseId')
  await t
    .click(geographySelect)
    .click(geographySelect.find('option').nth(1))
    .click(eventIdSelect)
    .click(eventIdSelect.find('option').nth(1))
    .click(causeIdSelect)
    .click(causeIdSelect.find('option').nth(1))

  // Save new card
  const msgDatacardInsertOk = Selector('#msgDatacardInsertOk')
  await t
    .expect(msgDatacardInsertOk.exists)
    .ok()
    .expect(msgDatacardInsertOk.visible)
    .notOk()
    .click('#btnDatacardSave')
    .expect(msgDatacardInsertOk.visible)
    .ok()

  // Edit Datacard
  const msgDatacardUpdateOk = Selector('#msgDatacardUpdateOk')
  await t
    .click('#btnDatacardEdit')
    .expect(msgDatacardFill.visible)
    .ok()
    .expect(state.value)
    .eql('EDIT')
    .click('#btnDatacardSave')
    .expect(msgDatacardUpdateOk.visible)
    .ok()
    .expect(state.value)
    .eql('VIEW')
})
