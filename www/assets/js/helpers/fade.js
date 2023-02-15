export function fadeOut(target, speed, callback) {
  var fadeTarget = target
  var fadeEffect = setInterval(function () {
    if (!fadeTarget.style.opacity) {
      fadeTarget.style.opacity = 1
    }
    if (fadeTarget.style.opacity > 0) {
      fadeTarget.style.opacity -= 0.1
    } else {
      clearInterval(fadeEffect)
      callback()
    }
  }, speed)
}
