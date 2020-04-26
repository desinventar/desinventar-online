const fs = require('fs')
const path = require('path')
const winston = require('winston')

const outputDir = path.resolve(`${__dirname}/../../web/conf`)
const localeDir = path.resolve(`${__dirname}`)

const languages = [
  {
    confFile: `eng.conf`,
    jsonFile: `locale/en.json`
  },
  {
    confFile: `spa.conf`,
    jsonFile: `locale/es_419.json`
  },
  {
    confFile: `fre.conf`,
    jsonFile: `locale/fr.json`
  },
  {
    confFile: `por.conf`,
    jsonFile: `locale/pt.json`
  }
]

const logger = winston.loggers.add('language', {
  transports: [
    new winston.transports.Console({
      level: 'debug'
    })
  ]
})

function sortByGroup(keys) {
  const stringsByGroup = {}
  Object.keys(keys).forEach(key => {
    const keyParts = key.split('.')
    const groupKey = keyParts[0]
    const stringKey = keyParts[1]
    if (!stringsByGroup[groupKey]) {
      stringsByGroup[groupKey] = {}
    }
    stringsByGroup[groupKey][stringKey] = keys[key]['translation']
  })
  return stringsByGroup
}

logger.info(`outputDir: ${outputDir}`)
languages.forEach(language => {
  const confFile = `${outputDir}/${language.confFile}`
  logger.info(`${language.jsonFile} => ${language.confFile}`)
  const fileName = `${localeDir}/${language.jsonFile}`
  const keys = require(fileName)

  const stringsByGroup = sortByGroup(keys)

  const file = fs.createWriteStream(confFile)
  Object.keys(stringsByGroup).forEach(groupKey => {
    file.write(`[${groupKey}]\n`)
    Object.keys(stringsByGroup[groupKey]).forEach(stringKey => {
      file.write(`${stringKey}=${stringsByGroup[groupKey][stringKey]}\n`)
    })
    file.write(`\n`)
  })
  file.end()
})
