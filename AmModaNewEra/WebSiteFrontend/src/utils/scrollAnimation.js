function clamp01(x) {
  if (x <= 0) return 0
  if (x >= 1) return 1
  return x
}

function lerp(a, b, t) {
  return a + (b - a) * t
}

function easeInOutCubic(t) {
  return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2
}

function getReducedMotionPreferred() {
  if (typeof window === 'undefined' || !window.matchMedia) return false
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function getMaxScrollTopWindow() {
  const root = document.documentElement
  const body = document.body
  const scrollHeight = Math.max(root?.scrollHeight ?? 0, body?.scrollHeight ?? 0)
  const clientHeight = root?.clientHeight ?? window.innerHeight ?? 0
  return Math.max(0, scrollHeight - clientHeight)
}

function getMaxScrollTopElement(el) {
  return Math.max(0, el.scrollHeight - el.clientHeight)
}

/**
 * Animates scrolling on `window` or a scrollable element to a target `top` with easing.
 * Returns a cancel function.
 */
export function animateScrollTo({ scroller = window, top, totalMs = 1500 } = {}) {
  if (typeof window === 'undefined') return () => void 0

  const isWindow = scroller === window
  if (!isWindow && !(scroller instanceof HTMLElement)) return () => void 0

  if (getReducedMotionPreferred()) {
    if (isWindow) {
      window.scrollTo(0, Math.max(0, top ?? 0))
    } else {
      const max = getMaxScrollTopElement(scroller)
      scroller.scrollTop = Math.max(0, Math.min(top ?? 0, max))
    }
    return () => void 0
  }

  const startTop = isWindow
    ? (window.scrollY ?? window.pageYOffset ?? 0)
    : scroller.scrollTop
  const maxScroll = isWindow ? getMaxScrollTopWindow() : getMaxScrollTopElement(scroller)
  const targetTop = Math.max(0, Math.min(top ?? 0, maxScroll))
  const delta = targetTop - startTop
  if (!Number.isFinite(delta) || Math.abs(delta) < 1) return () => void 0

  const total = Math.max(0, Number(totalMs) || 0)

  let rafId = 0
  let cancelled = false
  let startTs = 0

  const cancel = () => {
    cancelled = true
    if (rafId) cancelAnimationFrame(rafId)
    rafId = 0
    removeCancelListeners()
  }

  const onUserInterrupt = () => cancel()
  const cancelListenerOptions = { passive: true }

  function addCancelListeners() {
    window.addEventListener('wheel', onUserInterrupt, cancelListenerOptions)
    window.addEventListener('touchstart', onUserInterrupt, cancelListenerOptions)
    window.addEventListener('keydown', onUserInterrupt)
  }

  function removeCancelListeners() {
    window.removeEventListener('wheel', onUserInterrupt, cancelListenerOptions)
    window.removeEventListener('touchstart', onUserInterrupt, cancelListenerOptions)
    window.removeEventListener('keydown', onUserInterrupt)
  }

  addCancelListeners()

  function frame(ts) {
    if (cancelled) return
    if (!startTs) startTs = ts
    const elapsed = ts - startTs

    const t = total > 0 ? clamp01(elapsed / total) : 1
    const easedProgress = easeInOutCubic(t)

    const nextTop = lerp(startTop, targetTop, clamp01(easedProgress))
    if (isWindow) {
      window.scrollTo(0, nextTop)
    } else {
      scroller.scrollTop = nextTop
    }

    if (elapsed < total) {
      rafId = requestAnimationFrame(frame)
    } else {
      removeCancelListeners()
    }
  }

  rafId = requestAnimationFrame(frame)
  return cancel
}
