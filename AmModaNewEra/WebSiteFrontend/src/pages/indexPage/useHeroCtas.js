import { ref, watch } from 'vue'
import { useIntersectionObserver } from '../../composables/useIntersectionObserver.js'

/** Dense enough for section enter/leave; scroll listener handles continuous updates. */
const HERO_CTA_IO_THRESHOLDS = Array.from({ length: 21 }, (_, i) => i / 20)

export function useHeroCtas({
  onUpdate,
  getExtraObservedElements,
  getFacebookPhotoEl,
  getFacebookSectionEl,
  heroCtaStackHeightPx = 96,
  heroCtaImageGapPx = 20,
  takeoverEpsilonPx = 0,
}) {
  const heroIntroRef = ref(null)
  const heroIntroPhotoRef = ref(null)
  const heroIntroCtaRef = ref(null)

  const heroIntroAfterRef = ref(null)
  const heroIntroAfterPhotoRef = ref(null)
  const heroIntroAfterCtaRef = ref(null)
  const heroIntroAfterTopCtaBtnRef = ref(null)

  const heroIntroThirdRef = ref(null)
  const heroIntroThirdPhotoRef = ref(null)
  const heroIntroThirdCtaRef = ref(null)
  const heroIntroThirdTopCtaBtnRef = ref(null)

  const heroIntroCtaFloated = ref(false)
  const heroIntroAfterCtaFloated = ref(false)
  const heroIntroThirdCtaFloated = ref(false)

  const heroIntroAfterRedTakesOver = ref(false)
  const heroIntroThirdRedTakesOver = ref(false)

  const heroIntroAfterBottomCtaInViewport = ref(false)
  const heroIntroThirdBottomCtaInViewport = ref(false)

  const heroIntroFacebookCtaFloated = ref(false)
  const facebookShopCtaPassedOnce = ref(false)

  let heroCtaResizeAttached = false
  let heroCtaScrollAttached = false
  let heroCtaVisualViewportAttached = false
  let scheduledRafId = 0
  const heroCtaIntersectionEnabled = ref(false)

  /** @type {ResizeObserver|null} */
  let heroCtaSectionResizeObserver = null

  function syncHeroSectionResizeObserver() {
    if (typeof ResizeObserver === 'undefined') return
    if (!heroCtaSectionResizeObserver) {
      heroCtaSectionResizeObserver = new ResizeObserver(() => {
        scheduleUpdateHeroCtaModes()
      })
    }
    heroCtaSectionResizeObserver.disconnect()
    const after = heroIntroAfterRef.value
    const third = heroIntroThirdRef.value
    if (after) heroCtaSectionResizeObserver.observe(after)
    if (third) heroCtaSectionResizeObserver.observe(third)
  }

  watch(
    [heroIntroAfterRef, heroIntroThirdRef],
    () => {
      if (!heroCtaIntersectionEnabled.value) return
      syncHeroSectionResizeObserver()
    },
    { flush: 'post' },
  )

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

  function computeRedTakesOverFromYellow(redEl) {
    if (!redEl) return false
    const rect = redEl.getBoundingClientRect()
    if (rect.height <= 0) return false
    const viewportBottom = getViewportBottomClientY()
    const yellowBottom = viewportBottom - heroCtaImageGapPx + takeoverEpsilonPx
    const epsilon = 0.5
    return rect.bottom >= yellowBottom - epsilon
  }

  function computeBottomCtaInViewport(ctaEl) {
    if (!ctaEl) return false
    const rect = ctaEl.getBoundingClientRect()
    if (rect.height <= 0) return false
    const viewportBottom = getViewportBottomClientY()
    const epsilon = 0.5
    if (rect.bottom <= epsilon) return false
    if (rect.top >= viewportBottom - epsilon) return false
    return true
  }

  function computeFacebookShopCtaFloated(photoEl, sectionFallbackEl) {
    if (facebookShopCtaPassedOnce.value) return false

    const el = photoEl ?? sectionFallbackEl
    if (!el) return false
    const rect = el.getBoundingClientRect()
    if (rect.height <= 0) return false

    const viewportBottom = getViewportBottomClientY()
    const epsilon = 0.5

    if (rect.bottom <= 0 || rect.top >= viewportBottom) return false
    if (rect.bottom <= viewportBottom + epsilon) return false

    const floatedCtaCenterY = viewportBottom - heroCtaImageGapPx - heroCtaStackHeightPx / 2
    const meetImageTop = floatedCtaCenterY - rect.height / 2

    return rect.top > meetImageTop + epsilon
  }

  function updateFacebookShopSectionPassed() {
    if (facebookShopCtaPassedOnce.value) return
    const section = getFacebookSectionEl?.() ?? null
    if (!section) return
    const r = section.getBoundingClientRect()
    if (r.bottom < 0) {
      facebookShopCtaPassedOnce.value = true
    }
  }

  function updateHeroCtaModes() {
    heroIntroCtaFloated.value = computeHeroCtaFloated(heroIntroPhotoRef.value, heroIntroRef.value)

    const heroIntroAfterBaseFloated = computeHeroCtaFloated(
      heroIntroAfterPhotoRef.value,
      heroIntroAfterRef.value,
    )
    const heroIntroAfterRedTakesOverNow = computeRedTakesOverFromYellow(
      heroIntroAfterTopCtaBtnRef.value,
    )
    heroIntroAfterRedTakesOver.value = heroIntroAfterRedTakesOverNow
    heroIntroAfterCtaFloated.value = heroIntroAfterBaseFloated && !heroIntroAfterRedTakesOverNow

    const heroIntroThirdBaseFloated = computeHeroCtaFloated(
      heroIntroThirdPhotoRef.value,
      heroIntroThirdRef.value,
    )
    const heroIntroThirdRedTakesOverNow = computeRedTakesOverFromYellow(
      heroIntroThirdTopCtaBtnRef.value,
    )
    heroIntroThirdRedTakesOver.value = heroIntroThirdRedTakesOverNow
    heroIntroThirdCtaFloated.value = heroIntroThirdBaseFloated && !heroIntroThirdRedTakesOverNow

    heroIntroAfterBottomCtaInViewport.value = computeBottomCtaInViewport(
      heroIntroAfterCtaRef.value,
    )
    heroIntroThirdBottomCtaInViewport.value = computeBottomCtaInViewport(
      heroIntroThirdCtaRef.value,
    )

    heroIntroFacebookCtaFloated.value = computeFacebookShopCtaFloated(
      getFacebookPhotoEl?.() ?? null,
      getFacebookSectionEl?.() ?? null,
    )
    updateFacebookShopSectionPassed()

    onUpdate?.()
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
        document.removeEventListener('scroll', scheduleUpdateHeroCtaModes, {
          capture: true,
          passive: true,
        })
        heroCtaScrollAttached = false
      }
      if (heroCtaVisualViewportAttached && window.visualViewport) {
        const vv = window.visualViewport
        vv.removeEventListener('resize', scheduleUpdateHeroCtaModes)
        vv.removeEventListener('scroll', scheduleUpdateHeroCtaModes)
        heroCtaVisualViewportAttached = false
      }
      if (heroCtaSectionResizeObserver) {
        heroCtaSectionResizeObserver.disconnect()
      }
      return
    }

    if (!heroCtaResizeAttached) {
      window.addEventListener('resize', scheduleUpdateHeroCtaModes)
      heroCtaResizeAttached = true
    }

    if (!heroCtaScrollAttached) {
      document.addEventListener('scroll', scheduleUpdateHeroCtaModes, { capture: true, passive: true })
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
    const extraEls = getExtraObservedElements?.() ?? []
    const sections = [heroIntroRef.value, heroIntroAfterRef.value, heroIntroThirdRef.value, ...extraEls]
    for (const el of sections) {
      if (el) heroCtaIntersection.observe(el)
    }
    syncHeroSectionResizeObserver()
    updateHeroCtaModes()
  }

  return {
    getViewportBottomClientY,
    heroIntroRef,
    heroIntroPhotoRef,
    heroIntroCtaRef,
    heroIntroAfterRef,
    heroIntroAfterPhotoRef,
    heroIntroAfterCtaRef,
    heroIntroAfterTopCtaBtnRef,
    heroIntroThirdRef,
    heroIntroThirdPhotoRef,
    heroIntroThirdCtaRef,
    heroIntroThirdTopCtaBtnRef,
    heroIntroCtaFloated,
    heroIntroAfterCtaFloated,
    heroIntroThirdCtaFloated,
    heroIntroAfterRedTakesOver,
    heroIntroThirdRedTakesOver,
    heroIntroFacebookCtaFloated,
    facebookShopCtaPassedOnce,
    updateHeroCtaModes,
    setupHeroCtaIntersection,
  }
}

