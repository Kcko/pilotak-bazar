import Alpine from './vndr/alpinejs/dist/module.esm'
window.Alpine = Alpine

import test from './alpine/test'

document.addEventListener('alpine:init', () => {
  Alpine.data('test', test)
})

Alpine.start()
