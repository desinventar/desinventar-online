const Knex = require('knex')
const fs = require('fs')
const path = require('path')
const getopt = require('node-getopt')
const expandHomeDir = require('expand-home-dir')

const parsedOptions = getopt
  .create([
    ['f', 'file=ARG', 'Filename to process'],
    ['m', 'mode=ARG', 'Migration mode to apply (core|base|region)'],
    ['d', 'dir=ARG', 'Directory mode, process all databases']
  ])
  .bindHelp()
  .parseSystem()
const { options } = parsedOptions
if (options.dir && options.dir !== '') {
  processDirectory(options.dir).then(() => {
    process.exit(0)
  })
} else {
  if (!options.file || typeof options.file === 'undefined') {
    process.stderr.write(`file is a required parameter\n`)
    process.exit(1)
  }
  if (!options.mode || typeof options.mode === 'undefined') {
    process.stderr.write(`mode is a required parameter\n`)
    process.exit(1)
  }
  processDatabase(options.mode, options.file).then(() => {
    process.exit(0)
  })
}

function processDatabase(mode, fileName) {
  return new Promise(async resolve => {
    const knexConfig = require(`./knexfile-${mode}`)
    const env = process.env.NODE_ENV || 'development'
    knexConfig[env].connection.filename = path.resolve(expandHomeDir(fileName))
    const knex = Knex(knexConfig[env])
    const migrations = await knex.migrate.latest()
    process.stdout.write(`${fileName}: migrations applied.\n`)
    if (migrations[1].length > 0) {
      process.stdout.write(`${migrations[1].join(',')}\n`)
    }
    resolve(migrations)
  })
}

function processDirectory(dir) {
  return new Promise(async resolve => {
    const items = fs.readdirSync(dir)
    await Promise.all(
      items.map(async item => {
        const stats = fs.lstatSync(`${dir}/${item}`)
        if (!stats.isDirectory()) {
          return
        }
        const fileName = `${dir}/${item}/desinventar.db`
        if (!fs.existsSync(fileName)) {
          return
        }
        return processDatabase('region', fileName)
      })
    )
    resolve(true)
  })
}
