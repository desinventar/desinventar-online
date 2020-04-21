var webpack = require('webpack')

module.exports = {
  mode: 'development',
  entry: ['@babel/polyfill', `${__dirname}/web/js/entry.js`],
  output: {
    path: `${__dirname}/web/scripts`,
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /(node_modules|bower_components)/,
        include: [
          `${__dirname}/frontend`,
          `${__dirname}/web/js`,
          `${__dirname}/web/js2`
        ],
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env']
        }
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  resolve: {
    extensions: ['*', '.js', '.jsx'],
    modules: [`${__dirname}/node_modules`],
    descriptionFiles: ['package.json'],
    mainFields: ['main', 'browser'],
    mainFiles: ['index']
  },
  plugins: [new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)],
  performance: {
    hints: false
  },
  devtool: 'source-map'
}
