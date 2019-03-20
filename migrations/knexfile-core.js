module.exports = {
  development: {
    client: 'sqlite3',
    connection: {
      filename: `${__dirname}/core.sqlite3`
    },
    migrations: {
      directory: `${__dirname}/core`
    }
  }
}
