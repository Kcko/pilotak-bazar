import { preloaderInit } from './modules/preloader.js'
import naja from './vndr/naja/index.esm.js'

function googleRecaptcha() {
  let g_recaptcha_response = document.querySelector('#g_recaptcha_response')

  if (g_recaptcha_response) {
    grecaptcha.ready(function () {
      grecaptcha
        .execute(g_recaptcha_response.getAttribute('data-site-key'), {
          action: 'validate_captcha'
        })
        .then(function (token) {
          document.getElementById('g_recaptcha_response').value = token
        })
    })
  }
}

window.addEventListener('DOMContentLoaded', (event) => {
  // Naja / ajax snippets
  naja.initialize()
  googleRecaptcha()

  naja.snippetHandler.addEventListener('beforeUpdate', (event) => {
    if (event.detail.snippet.classList.contains('NajaShowPreloader')) {
      const parent = event.detail.snippet.closest('.NajaPreloaderWrapper')
      if (parent) {
        const preloader = parent.querySelector('.NajaPreloader')
        preloader.classList.remove('hidden')
      }
    }
  })

  naja.snippetHandler.addEventListener('afterUpdate', (event) => {
    setTimeout(() => {
      const parent = event.detail.snippet.closest('.NajaPreloaderWrapper')
      if (parent) {
        const preloader = parent.querySelector('.NajaPreloader')
        preloader.classList.add('hidden')
      }
    }, 1000)

    googleRecaptcha()
  })

  // welcome preloader
  preloaderInit().then(() => {})
})
