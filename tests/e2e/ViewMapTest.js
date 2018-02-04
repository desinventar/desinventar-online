const config = require('config')

import { Selector } from 'testcafe'

const url = config.test.url + '/#' + config.test.database + '/'
fixture('View Map').page(url)

test('View Map', async t => {
  const paramsWindow = Selector('#map-win', {
    visibilityCheck: true
  })
  await t
    .click(Selector('#btnViewMap'))
    .expect(paramsWindow.visible)
    .ok()
    .click(Selector('#map-win button').withText('Generate'))
    .expect(paramsWindow.exists)
    .eql(true)
    .expect(Selector('#dcr', { visibilityCheck: true }).visible)
    .eql(true)
    .expect(Selector('#viewMapRecordCount').textContent)
    .eql('109')
    .expect(Selector('#defaultMapTitle').visible)
    .eql(true)
})
