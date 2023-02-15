export default class LoadingIndicatorExtension {
  constructor(defaultLoadingIndicatorSelector) {
    this.defaultLoadingIndicatorSelector = defaultLoadingIndicatorSelector
  }

  initialize(naja) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        this.defaultLoadingIndicator = document.querySelector(this.defaultLoadingIndicatorSelector)
      })
    } else {
      this.defaultLoadingIndicator = document.querySelector(this.defaultLoadingIndicatorSelector)
    }

    naja.uiHandler.addEventListener('interaction', this.locateLoadingIndicator.bind(this))
    naja.addEventListener('start', this.showLoader.bind(this))
    naja.addEventListener('complete', this.hideLoader.bind(this))
  }

  locateLoadingIndicator(event) {
    const loadingIndicator = event.detail.element.closest('[data-loading-indicator]')
    event.detail.options.loadingIndicator = loadingIndicator || this.defaultLoadingIndicator
  }

  showLoader(event) {
    event.detail.options.loadingIndicator?.classList.add('is-loading')
  }

  hideLoader(event) {
    event.detail.options.loadingIndicator?.classList.remove('is-loading')
  }
}
