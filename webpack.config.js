module.exports = {
  mode: 'development',
  entry: __dirname + '/web/js/entry.js',
  output: {
    path: __dirname + '/web/scripts',
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        include: __dirname + '/web/js',
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
  },
  performance: {
    hints: false
  }
}
