module.exports = {
  development: {
    client: 'sqlite3',
    connection: {
      filename: `${__dirname}/base.sqlite3`
    },
    useNullAsDefault: true,
    migrations: {
      directory: `${__dirname}/base`
    }
  }
}
