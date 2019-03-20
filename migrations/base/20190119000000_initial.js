const db = require('../helpers/DatabaseHelper')

exports.up = async function(knex) {
  await db.createTable(knex, 'Cause', table => {
    table.string('CauseId', 50)
    table.string('LangIsoCode', 3)
    table.string('RegionId', 50)
    table.string('CauseName', 50)
    table.text('CauseDesc')
    table.integer('CauseActive')
    table.integer('CausePredefined')
    table.string('CauseRGBColor', 10)
    table.text('CauseKeyWords')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'Country', table => {
    table.string('CountryIso', 3)
    table.string('CountryIsoName', 100)
    table.string('CountryName', 100)
    table.double('CountryMinX')
    table.double('CountryMinY')
    table.double('CountryMaxX')
    table.double('CountryMaxY')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'CountryName', table => {
    table.string('CountryIso', 3)
    table.string('LangIsoCode', 3)
    table.string('CountryName', 100)
  })

  await db.createTable(knex, 'Dictionary', table => {
    table.integer('DictLabelId')
    table.string('LangIsoCode', 3)
    table.string('DictTranslation', 30)
    table.string('DictTechHelp', 50)
    table.text('DictBasDesc')
    table.text('DictFullDesc')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'Event', table => {
    table.string('EventId', 50)
    table.string('LangIsoCode', 3)
    table.string('RegionId', 50)
    table.string('EventName', 50)
    table.text('EventDesc')
    table.integer('EventActive')
    table.integer('EventPredefined')
    table.string('EventRGBColor', 10)
    table.text('EventKeyWords')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'Info', table => {
    table.string('InfoKey', 50)
    table.string('InfoValue', 1024)
    table.string('InfoAuxValue', 1024)
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'LabelGroup', table => {
    table.integer('DictLabelId')
    table.string('LGName', 50)
    table.string('LabelName', 30)
    table.string('id', 50)
    table.integer('LGOrder')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'Language', table => {
    table.string('LangIsoCode', 3)
    table.string('LangIsoName', 50)
    table.string('LangLocalName', 50)
    table.integer('LangStatus')
    table.datetime('RecordCreation')
    table.datetime('RecordSync')
    table.datetime('RecordUpdate')
  })

  await db.createTable(knex, 'Sync', table => {
    table.string('SyncId', 50)
    table.string('RegionId', 50)
    table.string('SyncTable', 100)
    table.datetime('SyncUpload')
    table.datetime('SyncDownload')
    table.string('SyncURL', 1024)
    table.string('SyncSpec', 1024)
  })
}

exports.down = async function(knex) {
  await db.dropTable(knex, 'Cause')
  await db.dropTable(knex, 'Country')
  await db.dropTable(knex, 'CountryName')
  await db.dropTable(knex, 'Dictionary')
  await db.dropTable(knex, 'Event')
  await db.dropTable(knex, 'Info')
  await db.dropTable(knex, 'LabelGroup')
  await db.dropTable(knex, 'Language')
  await db.dropTable(knex, 'Sync')
}
