const common = require('./common')

const getVersion = async () => {
  const info = await common.readJson('../../../package.json')
  return {
    major_version: info.version.split('.')[0],
    version: info.version || 'unknown',
    release_date: info.desinventar.releaseDate || ''
  }
}

module.exports = exports = {
  getVersion
}
