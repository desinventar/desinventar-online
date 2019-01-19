const express = require('express')
const version = require('../../src/common/version')

const router = express.Router()

router.get('/version', async (req, res) => {
  res.json({ version: await version.getVersion() })
})

module.exports = exports = router
