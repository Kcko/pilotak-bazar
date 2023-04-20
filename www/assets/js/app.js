import { preloaderInit } from './modules/preloader.js'
import naja from './vndr/naja/index.esm.js'

window.naja = naja

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
  const originalTitle = document.title
  const backTitle = 'Hej Piloťáku, vrať se brzy ;-)'

  window.addEventListener('focus', () => {
    document.title = originalTitle
  })

  window.addEventListener('blur', () => {
    document.title = backTitle
  })

  // Naja / ajax snippets
  naja.initialize()
  googleRecaptcha()

  naja.addEventListener('success', (event) => {
    console.log('success')
    console.log(event.detail.payload)
    console.log(dropzone)
  })

  naja.addEventListener('complete', (event) => {
    console.debug(`Finished processing request ${event.detail.request.url}`)
  })

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
