const express = require('express')
const commonRouter = require('./routes/common')

const app = express()
const port = 3000
app.get('/', (req, res) => res.send('Hello World!'))
app.use('/common', commonRouter)

app.listen(port, () => process.stdout.write(`Listening on port ${port}\n`))
