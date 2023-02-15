// // postcss.config.js
// module.exports = (context) => ({
//   parser: 'postcss-scss',
//   plugins: {
//     //'postcss-import': {},
//     'postcss-easy-import': { prefix: '_', extensions: ['.css', '.scss'] },
//     'tailwindcss/nesting': {},
//     tailwindcss: {},
//     autoprefixer: {},
//     'postcss-advanced-variables': {},
//     cssnano: context.env === 'prod' ? {} : false,
//     'postcss-cachebuster': {
//       imagesPath: '',
//       cssPath: '/assets/css/'
//     }
//   }
// })

module.exports = {
  parser: 'postcss-scss',
  plugins: {
    // 'postcss-import': {},
    'postcss-easy-import': { prefix: '_', extensions: ['.css', '.scss'] },
    'tailwindcss/nesting': {},
    tailwindcss: {},
    autoprefixer: {}
  }
}
