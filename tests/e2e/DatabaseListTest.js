import { Selector } from 'testcafe'
import { url } from './helpers/config'

fixture('Database List').page(url)

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
