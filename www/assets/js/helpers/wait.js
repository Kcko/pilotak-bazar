function wait(waitTime, callback = () => {}) {
  return new Promise(resolve => {
    setTimeout(() => {
      callback()
      resolve(true)
    }, waitTime)
  })
}

export default wait
