export function useRevealZoom({ mainRef }) {
  let revealZoomObserver = null
  const revealZoomRegistered = new WeakSet()

  function teardownRevealZoomObserver() {
    if (revealZoomObserver) {
      revealZoomObserver.disconnect()
      revealZoomObserver = null
    }
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

    if (!revealZoomObserver) {
      revealZoomObserver = new IntersectionObserver(
        (entries) => {
          for (const entry of entries) {
            if (!entry.isIntersecting) continue
            entry.target.classList.add('index-page__reveal-media--in')
            revealZoomRegistered.delete(entry.target)
            revealZoomObserver?.unobserve(entry.target)
          }
        },
        {
          root: null,
          rootMargin: '0px',
          threshold: 0,
        },
      )
    }

    scope.querySelectorAll('.index-page__reveal-media').forEach((el) => {
      if (el.classList.contains('index-page__reveal-media--in')) return
      if (revealZoomRegistered.has(el)) return
      revealZoomRegistered.add(el)
      revealZoomObserver.observe(el)
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
    teardownRevealZoomObserver,
    observeRevealZoomTargets,
    setupRevealZoomObserver,
    scheduleRevealZoomAfterLayout,
  }
}

