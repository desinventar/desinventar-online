import { Selector } from 'testcafe'
import { urlWithDatabase } from './helpers/config'

fixture('View Graph').page(urlWithDatabase)

test('View Graph', async t => {
  const paramsWindow = Selector('#divGraphParameters', {
    visibilityCheck: true
  })
  await t
    .click(Selector('#btnViewGraph'))
    .expect(paramsWindow.visible)
    .ok()
    .click(Selector('#divGraphParameters button').withText('Generate'))
    .expect(paramsWindow.exists)
    .eql(true)
    .expect(Selector('#dcr', { visibilityCheck: true }).visible)
    .eql(true)
    .expect(Selector('#viewGraphRecordCount').textContent)
    .eql('109')
    .expect(Selector('#viewGraphImg').visible)
    .eql(true)
})
