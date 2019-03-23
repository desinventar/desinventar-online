module.exports = {
  development: {
    client: 'sqlite3',
    connection: {
      filename: `${__dirname}/core.sqlite3`
    },
    useNullAsDefault: true,
    migrations: {
      directory: `${__dirname}/core`
    }
  }
}
