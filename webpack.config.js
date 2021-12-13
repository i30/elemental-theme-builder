const path = require('path');

module.exports = {
  mode: 'production',
  entry: {
    'meta-sidebar': './assets/js/meta-sidebar.js'
  },
  output: {
    path: path.resolve(__dirname, 'assets/js'),
    filename: '[name].min.js'
  },
  module: {
    rules: [{
      test: /\.js$/,
      exclude: /node_modules/,
      loader: 'babel-loader'
    }]
  },
  externals: {
    'lodash': 'lodash',
    '@wordpress/data': 'wp.data',
    '@wordpress/i18n': 'wp.i18n',
    '@wordpress/hooks': 'wp.hooks',
    '@wordpress/blocks': 'wp.blocks',
    '@wordpress/editor': 'wp.editor',
    '@wordpress/plugins': 'wp.plugins',
    '@wordpress/compose': 'wp.compose',
    '@wordpress/element': 'wp.element',
    '@wordpress/api-fetch': 'wp.apiFetch',
    '@wordpress/edit-post': 'wp.editPost',
    '@wordpress/components': 'wp.components',
    '@wordpress/block-editor': 'wp.blockEditor',
    '@wordpress/html-entities': 'wp.htmlEntities'
  },
  watch: true,
  watchOptions: {
    ignored: ['**/*.min.js']
  }
}
