const fs = require('fs')

async function readJson(path) {
  const data = await fs.readFileSync(require.resolve(path))
  return JSON.parse(data)
}

module.exports = {
  readJson
}
