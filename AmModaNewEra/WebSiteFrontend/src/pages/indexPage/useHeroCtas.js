import { ref } from 'vue'
import { useIntersectionObserver } from '../../composables/useIntersectionObserver.js'

/** Dense enough for section enter/leave; scroll listener handles continuous updates. */
const HERO_CTA_IO_THRESHOLDS = Array.from({ length: 21 }, (_, i) => i / 20)

export function useHeroCtas() {
  const heroIntroRef = ref(null)
  const heroIntroPhotoRef = ref(null)
  const heroIntroCtaRef = ref(null)

  const heroIntroCtaFloated = ref(false)

  let heroCtaResizeAttached = false
  let heroCtaScrollAttached = false
  let heroCtaVisualViewportAttached = false
  let scheduledRafId = 0
  const heroCtaIntersectionEnabled = ref(false)

  const heroCtaIntersection = useIntersectionObserver(
    () => {
      scheduleUpdateHeroCtaModes()
    },
    { threshold: HERO_CTA_IO_THRESHOLDS, enabled: heroCtaIntersectionEnabled },
  )

  function scheduleUpdateHeroCtaModes() {
    if (typeof window === 'undefined' || typeof requestAnimationFrame === 'undefined') {
      updateHeroCtaModes()
      return
    }
    if (scheduledRafId) return
    scheduledRafId = requestAnimationFrame(() => {
      scheduledRafId = 0
      updateHeroCtaModes()
    })
  }

  function getViewportBottomClientY() {
    const vv = window.visualViewport
    if (vv) {
      return vv.offsetTop + vv.height
    }
    return window.innerHeight || document.documentElement.clientHeight || 0
  }

  function computeHeroCtaFloated(photoEl, sectionFallbackEl) {
    const el = photoEl ?? sectionFallbackEl
    if (!el) return false
    const rect = el.getBoundingClientRect()
    if (rect.height <= 0) return false

    const viewportBottom = getViewportBottomClientY()
    const epsilon = 0.5

    if (rect.bottom <= 0 || rect.top >= viewportBottom) return false

    return rect.bottom > viewportBottom - epsilon
  }

  function updateHeroCtaModes() {
    heroIntroCtaFloated.value = computeHeroCtaFloated(heroIntroPhotoRef.value, heroIntroRef.value)
  }

  function setupHeroCtaIntersection(on) {
    if (!on) {
      heroCtaIntersectionEnabled.value = false
      heroCtaIntersection.stop()
      if (scheduledRafId && typeof cancelAnimationFrame !== 'undefined') {
        cancelAnimationFrame(scheduledRafId)
        scheduledRafId = 0
      }
      if (heroCtaResizeAttached) {
        window.removeEventListener('resize', scheduleUpdateHeroCtaModes)
        heroCtaResizeAttached = false
      }
      if (heroCtaScrollAttached) {
        window.removeEventListener('scroll', scheduleUpdateHeroCtaModes)
        heroCtaScrollAttached = false
      }
      if (heroCtaVisualViewportAttached && window.visualViewport) {
        const vv = window.visualViewport
        vv.removeEventListener('resize', scheduleUpdateHeroCtaModes)
        vv.removeEventListener('scroll', scheduleUpdateHeroCtaModes)
        heroCtaVisualViewportAttached = false
      }
      return
    }

    if (!heroCtaResizeAttached) {
      window.addEventListener('resize', scheduleUpdateHeroCtaModes)
      heroCtaResizeAttached = true
    }

    if (!heroCtaScrollAttached) {
      window.addEventListener('scroll', scheduleUpdateHeroCtaModes, { passive: true })
      heroCtaScrollAttached = true
    }

    if (!heroCtaVisualViewportAttached && window.visualViewport) {
      const vv = window.visualViewport
      vv.addEventListener('resize', scheduleUpdateHeroCtaModes)
      vv.addEventListener('scroll', scheduleUpdateHeroCtaModes)
      heroCtaVisualViewportAttached = true
    }

    if (typeof IntersectionObserver === 'undefined') {
      updateHeroCtaModes()
      return
    }

    heroCtaIntersection.stop()
    heroCtaIntersectionEnabled.value = true
    if (heroIntroRef.value) {
      heroCtaIntersection.observe(heroIntroRef.value)
    }
    updateHeroCtaModes()
  }

  return {
    heroIntroRef,
    heroIntroPhotoRef,
    heroIntroCtaRef,
    heroIntroCtaFloated,
    updateHeroCtaModes,
    setupHeroCtaIntersection,
  }
}
