import Alpine from './vndr/alpinejs/dist/module.esm.js'
import persist from './vndr/alpine-plugins/persist/dist/module.esm.js'
import focus from './vndr/alpine-plugins/focus/dist/module.esm.js'
window.Alpine = Alpine

import Swiper from './vndr/swiper/swiper-bundle.esm.browser.js'
import test from './alpine/test.js'

document.addEventListener('alpine:init', () => {
  Alpine.plugin(focus)
  Alpine.plugin(persist)
  Alpine.data('test', test)
  window.Swiper = Swiper
})

Alpine.start()
