import { Selector, ClientFunction } from 'testcafe'
import { urlWithDatabase } from './helpers/config'

fixture('View Map').page(urlWithDatabase)

test('View Map', async t => {
  const paramsWindow = Selector('#map-win', {
    visibilityCheck: true
  })

  const httpStatusForUrl = ClientFunction(url => {
    return new Promise(resolve => {
      const xhr = new XMLHttpRequest()
      xhr.open('GET', url)
      xhr.onload = function() {
        resolve(xhr.status)
      }
      xhr.send(null)
    })
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

  await t
    .expect(Selector('img.view-map-legend').visible)
    .eql(true)
    .expect(
      httpStatusForUrl(
        await Selector('img.view-map-legend').getAttribute('src')
      )
    )
    .eql(200)
})
