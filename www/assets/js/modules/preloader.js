import { fadeOut } from '../helpers/fade.js'

export const preloaderInit = async () => {
  const webStatus = document.querySelector('#web-status')
  const webPreloader = document.querySelector('#web-preloader')

  return new Promise((resolve) => {
    if (webStatus && webPreloader) {
      setTimeout(() => {
        fadeOut(webStatus, 25, () => {
          webStatus.remove()
        })
      }, 1500)

      setTimeout(() => {
        fadeOut(webPreloader, 25, () => {
          webPreloader.remove()

          resolve()
        })
      }, 2000)
    } else {
      resolve()
    }
  })
}
