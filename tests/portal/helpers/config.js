const myConfig = {
  url: process.env.TEST_PORTAL_URL,
  username: process.env.TEST_PORTAL_USERNAME,
  passwd: process.env.TEST_PORTAL_PASSWD
}

export default myConfig
export const url = myConfig.url
