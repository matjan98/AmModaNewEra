import { useIntersectionObserver } from '../../composables/useIntersectionObserver.js'
import { ref } from 'vue'

export function useRevealZoom({ mainRef }) {
  const revealZoomRegistered = new WeakSet()
  const revealZoomEnabled = ref(false)
  const { observe, unobserve, stop } = useIntersectionObserver(
    (entry) => {
      if (!entry?.isIntersecting) return
      entry.target.classList.add('index-page__reveal-media--in')
      revealZoomRegistered.delete(entry.target)
      unobserve(entry.target)
    },
    { threshold: 0, enabled: revealZoomEnabled },
  )

  function teardownRevealZoomObserver() {
    revealZoomEnabled.value = false
    stop()
  }

  function observeRevealZoomTargets() {
    const scope = mainRef.value
    if (!scope) return

    if (typeof IntersectionObserver === 'undefined') {
      scope.querySelectorAll('.index-page__reveal-media').forEach((el) => {
        el.classList.add('index-page__reveal-media--in')
      })
      return
    }

    revealZoomEnabled.value = true
    scope.querySelectorAll('.index-page__reveal-media').forEach((el) => {
      if (el.classList.contains('index-page__reveal-media--in')) return
      if (revealZoomRegistered.has(el)) return
      revealZoomRegistered.add(el)
      observe(el)
    })
  }

  function setupRevealZoomObserver(enabled) {
    if (!enabled) {
      teardownRevealZoomObserver()
      return
    }
    observeRevealZoomTargets()
  }

  function scheduleRevealZoomAfterLayout() {
    observeRevealZoomTargets()
    requestAnimationFrame(() => {
      requestAnimationFrame(() => observeRevealZoomTargets())
    })
  }

  return {
    observeRevealZoomTargets,
    setupRevealZoomObserver,
    scheduleRevealZoomAfterLayout,
  }
}

