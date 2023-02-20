import Alpine from './vndr/alpinejs/dist/module.esm.js'
window.Alpine = Alpine

import Swiper from './vndr/swiper/swiper-bundle.esm.browser.js'

import test from './alpine/test.js'

document.addEventListener('alpine:init', () => {
  Alpine.data('test', test)
  window.Swiper = Swiper
})

Alpine.start()
