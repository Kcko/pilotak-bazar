const plugin = require('tailwindcss/plugin')

module.exports = {
  corePlugins: {
    preflight: true
  },
  plugins: [
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/forms'),
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
        _yellow: '#FFD015',
        _black: '#020101',
        '_black-light': '#202020',
        _white: '#BDBDBD',
        bg: '#202124'
      },
      fontFamily: {
        inter: ['Inter']
      },
      fontSize: {
        base: '0.9375rem',
        basexl: '1rem'
      },
      screens: {
        betterhover: { raw: '(hover: hover)' }
      }
    }
  },
  variants: {
    extend: {}
  }
}
