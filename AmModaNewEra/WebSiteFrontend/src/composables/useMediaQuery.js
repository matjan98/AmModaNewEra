import { onMounted, onUnmounted, ref } from 'vue'

/**
 * Tracks a media query match state.
 *
 * @param {string} query
 * @param {{ ssrDefault?: boolean }} [options]
 */
export function useMediaQuery(query, options = {}) {
  const ssrDefault = options.ssrDefault === true
  const matches = ref(ssrDefault)

  /** @type {MediaQueryList|null} */
  let mql = null
  /** @type {(e: MediaQueryListEvent) => void | null} */
  let onChange = null

  const updateFromMql = () => {
    if (!mql) return
    matches.value = Boolean(mql.matches)
  }

  onMounted(() => {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') return
    mql = window.matchMedia(query)
    updateFromMql()
    onChange = () => updateFromMql()
    mql.addEventListener?.('change', onChange)
  })

  onUnmounted(() => {
    if (mql && onChange) {
      mql.removeEventListener?.('change', onChange)
    }
    mql = null
    onChange = null
  })

  return {
    matches,
    get mql() {
      return mql
    },
  }
}

