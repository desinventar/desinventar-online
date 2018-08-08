import { Selector } from 'testcafe'
import user from './helpers/user.js'
import { urlWithDatabase } from './helpers/config'

fixture('Database Upload Page').page(urlWithDatabase)

test('Database Upload Test', async t => {
  await user.login(t)

  await t
    .navigateTo('/')
    .click('#mnuFile')
    .hover('#mnuFileUpload')
    .click('#mnuFileUploadCopy')
    .expect(
      Selector('#divDatabaseUploadWin', { visibilityCheck: true }).visible
    )
    .ok()
})
