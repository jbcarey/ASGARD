/*!
 * details-polyfill
 * removed css & classes
 */

void (function (root, factory) {
  if (typeof define === 'function' && define.amd) define(factory)
  else if (typeof exports === 'object') module.exports = factory()
  else factory()
}(this, (function () {
  var DETAILS = 'details'
  var SUMMARY = 'summary'

  var supported = checkSupport()
  if (supported) return

  window.addEventListener('click', clickHandler)

  /*
   * Click handler for `<summary>` tags
   */

  function clickHandler (e) {
    if (e.target.nodeName.toLowerCase() === 'summary') {
      var details = e.target.parentNode
      if (!details) return

      if (details.getAttribute('open')) {
        details.open = false
        details.removeAttribute('open')
      } else {
        details.open = true
        details.setAttribute('open', 'open')
      }
    }
  }

  /*
   * Checks for support for `<details>`
   */

  function checkSupport () {
    var el = document.createElement(DETAILS)
    if (!('open' in el)) return false

    el.innerHTML = '<' + SUMMARY + '>a</' + SUMMARY + '>b'
    document.body.appendChild(el)

    var diff = el.offsetHeight
    el.open = true
    var result = (diff != el.offsetHeight)

    document.body.removeChild(el)
    return result
  }

  /*
   * Injects styles (idempotent)
   */

  function injectStyle (id, style) {
    if (document.getElementById(id)) return

    var el = document.createElement('style')
    el.id = id
    el.innerHTML = style

    document.getElementsByTagName('head')[0].appendChild(el)
  }
}))); // eslint-disable-line semi
