const plugin = require('tailwindcss/plugin')

module.exports = {
  corePlugins: {
    preflight: true
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
    require('tailwindcss-debug-screens'),
    plugin(function ({ addVariant }) {
      addVariant('important', ({ container }) => {
        container.walkRules((rule) => {
          rule.selector = `.\\!${rule.selector.slice(1)}`
          rule.walkDecls((decl) => {
            decl.important = true
          })
        })
      })
    })
  ],
  content: ['../app/**/*.latte'],
  safelist: ['invalid-feedback', 'Lock', '-Opened'],
  theme: {
    extend: {
      colors: {
        error: '#EB5757',
        brown: '#574328',
        'brown-light': '#7E6749',
        'brown-light-super': '#D2C0A7',
        bg: '#F4F0EA'
      },
      fontFamily: {
        inter: ['Inter']
      },
      fontSize: {
        base: '0.9375rem',
        basexl: '1rem'
      }
    }
  },
  variants: {
    extend: {}
  }
}
