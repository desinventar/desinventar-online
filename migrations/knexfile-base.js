module.exports = {
  development: {
    client: 'sqlite3',
    connection: {
      filename: `${__dirname}/base.sqlite3`
    },
    migrations: {
      directory: `${__dirname}/base`
    }
  }
}
