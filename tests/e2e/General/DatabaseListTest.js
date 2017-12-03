const config = require('config')
import { Selector } from 'testcafe'

fixture('Database List').page(config.test.url)

test('Database List', async t => {
  await t
    .expect(Selector('div#divRegionList').exists)
    .ok()
    .expect(Selector('div#divRegionList', { visibilityCheck: true }).visible)
    .ok()
    .click('#mnuFile')
    .click('#mnuFileOpen')
    .expect(Selector('div#divRegionList', { visibilityCheck: true }).visible)
    .ok()
})
