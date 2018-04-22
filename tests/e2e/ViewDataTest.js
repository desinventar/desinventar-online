import { Selector } from 'testcafe'
import { urlWithDatabase } from './helpers/config'

fixture('View Data').page(urlWithDatabase)

test('View Data', async t => {
  const paramsWindow = Selector('#divViewDataParamsWindow', {
    visibilityCheck: true
  })
  await t
    .click(Selector('#btnViewData'))
    .expect(paramsWindow.visible)
    .ok()
    .click(Selector('#divViewDataParamsWindow button').withText('Generate'))
    .expect(paramsWindow.exists)
    .eql(true)
    .expect(Selector('#dcr', { visibilityCheck: true }).visible)
    .eql(true)
    .expect(Selector('#viewDataRecordCount').textContent)
    .eql('109')
    .expect(Selector('#DataCurPagePrev').value)
    .eql('1')
    .expect(Selector('#DataCurPageCount').textContent)
    .eql('2')
})
