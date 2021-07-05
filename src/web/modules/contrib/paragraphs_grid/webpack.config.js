const autoprefixer = require('autoprefixer');

module.exports = [{
  entry: {
    grid_widget: ['./js/src/grid_widget.js', './css/src/grid_widget.scss'],
    mdc_grid: ['./css/src/mdc_grid.scss'],
    bs3_grid: ['./css/src/bootstrap3-grid.scss'],
    bs4_grid: ['./css/src/bootstrap4-grid.scss']
  },
  output: {
    filename: '[name].min.js',
    path: __dirname + '/js'
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '../css/[name].css'
            }
          },
          {loader: 'extract-loader'},
          {loader: 'css-loader'},
          {
            loader: 'postcss-loader',
            options: {
              plugins: () => {
                'use strict';
                return [autoprefixer()];
              }
            }
          },
          {
            loader: 'sass-loader',
            options: {
              includePaths: ['./node_modules']
            }
          }
        ]
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        query: {
          presets: ['env'],
          plugins: ['transform-object-assign']
        }
      }
    ]
  }
}];
