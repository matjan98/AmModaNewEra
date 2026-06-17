import { onMounted, onUnmounted, unref, watch } from 'vue'

const PRELOAD_ATTR = 'data-am-hero-preload'
const MOBILE_MEDIA = '(max-width: 999.98px)'
const DESKTOP_MEDIA = '(min-width: 1000px)'

/**
 * Injects responsive `<link rel="preload" as="image">` tags for LCP hero images.
 *
 * @param {{ mobile: import('vue').MaybeRefOrGetter<string>, desktop: import('vue').MaybeRefOrGetter<string> }} sources
 */
export function useHeroImagePreload(sources) {
  /** @type {Map<string, HTMLLinkElement>} */
  const linkEls = new Map()

  function ensureLink(key, media) {
    if (linkEls.has(key)) return linkEls.get(key)

    const linkEl = document.createElement('link')
    linkEl.rel = 'preload'
    linkEl.as = 'image'
    linkEl.setAttribute(PRELOAD_ATTR, key)
    if (media) linkEl.media = media
    linkEl.fetchPriority = 'high'
    document.head.appendChild(linkEl)
    linkEls.set(key, linkEl)
    return linkEl
  }

  function sync() {
    if (typeof document === 'undefined') return

    const mobileHref = unref(sources.mobile)
    const desktopHref = unref(sources.desktop)

    if (mobileHref) ensureLink('mobile', MOBILE_MEDIA).href = mobileHref
    if (desktopHref) ensureLink('desktop', DESKTOP_MEDIA).href = desktopHref
  }

  onMounted(sync)

  watch(
    () => [unref(sources.mobile), unref(sources.desktop)],
    () => sync(),
  )

  onUnmounted(() => {
    for (const linkEl of linkEls.values()) {
      linkEl.remove()
    }
    linkEls.clear()
  })
}
