const fs = require('fs')

class DatabaseHelper {
  constructor(knex, tableName) {
    this.knex = knex
    this.tableName = tableName
  }

  static createTable(knex, tableName, createTableFunc) {
    return knex.schema.hasTable(tableName).then(exists => {
      if (!exists) {
        return knex.schema.createTable(tableName, table => {
          createTableFunc(table)
        })
      }
      return false
    })
  }

  static dropTable(knex, tableName) {
    return knex.schema.hasTable(tableName).then(exists => {
      if (exists) {
        return knex.schema.dropTable(tableName)
      }
    })
  }

  static async truncateTable(knex, tableName) {
    await knex.raw('SET FOREIGN_KEY_CHECKS = 0')
    await knex.raw(`TRUNCATE ${tableName}`)
    await knex.raw('SET FOREIGN_KEY_CHECKS = 1')
  }

  static async seedTableFromJsonFile(knex, tableName, fileName) {
    const json = JSON.parse(fs.readFileSync(fileName))
    await this.truncateTable(knex, tableName)
    return knex(tableName).insert(json)
  }

  static async createIndexIfNotExists(knex, tableName, indexName, columns) {
    const existIndex = await this.existIndex(knex, tableName, indexName)
    if (existIndex) {
      return false
    }
    return knex.schema.table(tableName, table => {
      table.index(columns, indexName)
    })
  }

  static async dropIndex(knex, tableName, indexName) {
    return knex.raw(`DROP INDEX ${indexName} ON ${tableName}`)
  }

  static async dropIndexIfExists(knex, tableName, indexName) {
    const existIndex = await this.existIndex(knex, tableName, indexName)
    if (!existIndex) {
      return false
    }
    return this.dropIndex(knex, tableName, indexName)
  }

  static async existIndex(knex, tableName, indexName) {
    const rawResponse = await knex.raw(`SHOW INDEX FROM ${tableName}`)
    const indexes = JSON.parse(JSON.stringify(rawResponse[0]))
    for (const indexStr of Object.entries(indexes)) {
      const index = JSON.parse(JSON.stringify(indexStr[1]))
      if (index.Key_name === indexName) {
        return true
      }
    }
    return false
  }

  static async createFullTextIndex(knex, tableName, indexName, columns) {
    await knex.raw(
      `CREATE FULLTEXT INDEX ${indexName} ON ${tableName}(${columns.join(',')})`
    )
  }

  static async dropFullTextIndex(knex, tableName, indexName) {
    return this.dropIndex(knex, tableName, indexName)
  }

  addColumnIfNotExists(columnName, createColumn) {
    return this.knex.schema
      .hasColumn(this.tableName, columnName)
      .then(exists => {
        if (!exists) {
          if (
            typeof createColumn === 'string' &&
            ['string', 'integer', 'boolean'].includes(createColumn)
          ) {
            return this.knex.schema.table(this.tableName, function(t) {
              return t[createColumn](columnName)
            })
          } else {
            return this.knex.schema.table(this.tableName, function(t) {
              return createColumn(t)
            })
          }
        }
        return exists
      })
  }

  dropColumnIfExists(columnName) {
    return this.knex.schema
      .hasColumn(this.tableName, columnName)
      .then(exists => {
        if (exists) {
          return this.knex.schema.table(this.tableName, function(t) {
            t.dropColumn(columnName)
          })
        }
        return exists
      })
  }

  dropColumn(columnName) {
    return this.dropColumnIfExists(columnName)
  }

  string(columnName) {
    return this.addColumnIfNotExists(columnName, 'string')
  }

  integer(columnName) {
    return this.addColumnIfNotExists(columnName, 'integer')
  }

  boolean(columnName) {
    return this.addColumnIfNotExists(columnName, 'boolean')
  }
}

module.exports = exports = DatabaseHelper
