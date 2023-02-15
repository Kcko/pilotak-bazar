import { preloaderInit } from './modules/preloader.js'

import Swiper from './vndr/swiper/swiper-bundle.esm.browser.js'
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

  // sliders
  const sliders = document.querySelectorAll('.swiper')
  sliders.forEach((slider) => {
    const uniqid = slider.getAttribute('data-uniqid')

    const swiper = new Swiper(slider, {
      loop: true,
      slidesPerView: 'auto',
      speed: 750,
      spaceBetween: 32,
      pagination: {
        el: '.swiper-pagination-h2o',
        clickable: true,
        bulletClass: 'swiper-bullet',
        bulletActiveClass: 'swiper-bullet-active',
        renderBullet: function (index, className) {
          return '<span class="' + className + '"></span>'
        }
      },

      // Navigation arrows
      navigation: {
        nextEl: '.swiper-button-next[data-uniqid="' + uniqid + '"]',
        prevEl: '.swiper-button-prev[data-uniqid="' + uniqid + '"]'
      }

      // // And if we need scrollbar
      // scrollbar: {
      //   el: '.swiper-scrollbar'
      // }
    })
  })
})
