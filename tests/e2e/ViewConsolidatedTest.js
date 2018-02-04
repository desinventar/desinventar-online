const config = require('config')

import { Selector } from 'testcafe'

const url = config.test.url + '/#' + config.test.database + '/'
fixture('View Consolidated').page(url)

test('View Consolidated', async t => {
  const paramsWindow = Selector('#std-win', {
    visibilityCheck: true
  })
  await t
    .click(Selector('#btnViewStd'))
    .expect(paramsWindow.visible)
    .ok()
    .click(Selector('#std-win button').withText('Generate'))
    .expect(paramsWindow.exists)
    .eql(true)
    .expect(Selector('#dcr', { visibilityCheck: true }).visible)
    .eql(true)
    .expect(Selector('#viewConsolidatedGroupCount').textContent)
    .eql('3')
    .expect(Selector('#viewConsolidatedRecordCount').textContent)
    .eql('109')
    .expect(Selector('#StatCurPage').value)
    .eql('1')
    .expect(Selector('#StatCurPageCount').textContent)
    .eql('1')
})
