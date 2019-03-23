module.exports = {
  development: {
    client: 'sqlite3',
    connection: {
      filename: `${__dirname}/region.sqlite3`
    },
    useNullAsDefault: true,
    migrations: {
      directory: `${__dirname}/region`
    }
  }
}
