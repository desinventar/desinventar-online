import config from 'config'

const myConfig = {
  url: config.test.web.url,
  urlWithDatabase: config.test.web.url + '/#' + config.test.web.database + '/'
}

export default myConfig
export const url = myConfig.url
export const urlWithDatabase = myConfig.urlWithDatabase
