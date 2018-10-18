import { Selector } from 'testcafe'
import { urlWithDatabase } from './helpers/config'

fixture('Query Design').page(urlWithDatabase)

test('New Query Action', async t => {
  const fieldYearStart = Selector('#queryBeginYear')
  const fieldYearEnd = Selector('#queryEndYear')
  const yearStart = await fieldYearStart.value
  const yearEnd = await fieldYearEnd.value
  const yearStartTest = '1900'
  const yearEndTest = '1910'
  await t
    .typeText(fieldYearStart, `${yearStartTest}`, { replace: true })
    .pressKey('backspace backspace backspace backspace')
    .typeText(fieldYearStart, `${yearStartTest}`, { replace: true })
    .typeText(fieldYearEnd, `${yearEndTest}`, { replace: true })
    .pressKey('backspace backspace backspace backspace')
    .typeText(fieldYearEnd, `${yearEndTest}`, { replace: true })
    .expect(fieldYearStart.value)
    .eql(yearStartTest)
    .expect(fieldYearEnd.value)
    .eql(yearEndTest)
    .click('#mnuQuery')
    .hover('#mnuQueryOption')
    .click('#mnuQueryOptionNew')
    .expect(fieldYearStart.value)
    .eql(yearStart)
    .expect(fieldYearEnd.value)
    .eql(yearEnd)
})
