module.exports = {
  mode: 'development',
  entry: __dirname + '/js/entry.js',
  output: {
    path: __dirname + '/web/scripts',
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        include: __dirname + '/js',
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['es2015', { modules: false }]]
            }
          }
        ]
      }
    ]
  },
  resolve: {
    modules: [__dirname + '/node_modules'],
    descriptionFiles: ['package.json'],
    mainFields: ['main', 'browser'],
    mainFiles: ['index']
  }
}
