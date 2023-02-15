const loadScript = (url, widgetId) => {
  return new Promise((resolve, reject) => {
    const id = widgetId

    // only once
    let script = document.getElementById(id)
    if (script) {
      resolve()
      return
    }

    let head = document.getElementsByTagName('head')[0]
    script = document.createElement('script')
    script.id = id
    script.src = url
    script.async = true
    head.appendChild(script)

    script.onload = () => {
      resolve()
    }

    script.onerror = () => {
      reject('Interní error, zkuste to znovu ...')
    }
  })
}

const loadCss = (url, widgetId) => {
  return new Promise((resolve, reject) => {
    const id = widgetId

    // only once
    let script = document.getElementById(id)
    if (script) {
      resolve()
      return
    }

    let head = document.getElementsByTagName('head')[0]
    script = document.createElement('link')
    script.id = id
    script.rel = 'stylesheet'
    script.href = url
    head.appendChild(script)

    script.onload = () => {
      resolve()
    }

    script.onerror = () => {
      reject('Interní error, zkuste to znovu ...')
    }
  })
}

const loadIframe = (url, iframeId) => {
  return new Promise((resolve, reject) => {
    let iframe = document.getElementById(iframeId)

    if (!iframe) {
      reject('Interní error, zkuste to znovu ...(iframe not founded')
      return
    }

    // uz byl nacten
    if (iframe.src == url) {
      resolve()
      return
    }

    iframe.src = url

    iframe.onload = () => {
      resolve()
    }

    iframe.onerror = () => {
      reject('Interní error, zkuste to znovu ...(iframe with bad url)')
    }
  })
}

const removeScript = widgetId => {
  let script = document.getElementById(widgetId)
  if (script) {
    script.remove()
  }
}

const removeCss = removeScript

export { loadScript, loadCss, loadIframe, removeScript, removeCss }
