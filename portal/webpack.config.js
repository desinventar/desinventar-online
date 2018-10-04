module.exports = {
  mode: 'development',
  entry: `${__dirname}/js/entry.js`,
  output: {
    path: `${__dirname}/web/scripts`,
    publicPath: '/scripts/',
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        include: `${__dirname}/js`,
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env']
        }
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      },
      {
        test: /\.(gif|png|jpe?g|svg)$/i,
        use: ['file-loader']
      }
    ]
  },
  resolve: {
    modules: [`${__dirname}/../node_modules`],
    descriptionFiles: ['package.json'],
    mainFields: ['main', 'browser'],
    mainFiles: ['index']
  }
}
