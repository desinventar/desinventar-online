const config = require('config')

import { Selector } from 'testcafe'

const url = config.test.url + '/#' + config.test.database + '/'
fixture('Database Query').page(url)

test('Database Open', async t => {
  await t
    .expect(Selector('table#mnuRegionLabel button').textContent)
    .eql('[Bolivia - Gran Chaco]', 'Region label is not set')
    // Default values for query begin/query end
    .expect(Selector('#queryBeginYear').value)
    .eql('1971')
    .expect(Selector('#queryEndYear').value)
    .eql('2007')
    // Number of states/provinces in the main level
    .expect(Selector('#tree-geotree li').count)
    .eql(10)
    // Number of Events in list
    .expect(Selector('#qevelst option').count)
    .eql(38)
    // Number of Causes in list
    .expect(Selector('#qcaulst option').count)
    .eql(37)
})
