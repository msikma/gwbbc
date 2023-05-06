/** GW BBC <https://github.com/msikma/gwbbc> */

const GWBBC = {
  /**
   * Decorates [hide] bbc codes.
   */
  decorateHide: currentScript => {
    const hide = findLastElement(currentScript, 'hide')
    if (!hide) {
      return
    }
    const link = hide.querySelector('.hide_title')
    
    link.addEventListener('click', ev => {
      ev.preventDefault();
      hide.classList.toggle('is_hidden')
      hide.classList.toggle('is_shown')
    })
  },

  /**
   * Decorates [spoiler] bbc codes.
   */
  decorateSpoiler: currentScript => {
    const spoiler = findLastElement(currentScript, 'spoiler')
    const post = findPostWrapper(currentScript)

    // Toggles a spoiler tag.
    const toggle = (el, value) => {
      el.classList.toggle('is_shown', value ? value : undefined)
      el.classList.toggle('is_hidden', value ? !value : undefined)
    }

    // On click, toggle the given spoiler tag.
    spoiler.addEventListener('click', ev => {
      ev.preventDefault()
      toggle(spoiler)
    })
    // On double click, toggle all tags inside this post (if possible).
    if (post) {
      spoiler.addEventListener('dblclick', ev => {
        ev.preventDefault()
        const value = !spoiler.classList.contains('is_shown')
        const spoilers = post.querySelectorAll('.gwbbc_spoiler')
        spoilers.forEach(spoiler => toggle(spoiler, value))
      })
    }
  },
}

/**
 * Finds the closest div.post.
 * 
 * This assumes the theme has a .post div wrapping the content.
 */
function findPostWrapper(currentScript) {
  let el = currentScript.parentElement
  while (el) {
    if (el.classList.contains('post')) {
      return el
    }
    el = el.parentElement
  }
  return null
}

/**
 * Finds the closest previous GW SMF sibling element.
 */
function findLastElement(currentScript, type) {
  let el = currentScript.previousElementSibling
  while (el) {
    if (el.classList.contains(`gwbbc_${type}`)) {
      return el
    }
    el = el.previousElementSibling
  }
  return null
}
