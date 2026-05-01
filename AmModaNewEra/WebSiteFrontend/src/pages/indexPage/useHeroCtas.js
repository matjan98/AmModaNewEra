import { computed, ref } from 'vue'
import { useIntersectionObserver } from '../../composables/useIntersectionObserver.js'

const HERO_CTA_IMAGE_BOTTOM_GAP_PX = 20
const HERO_CTA_IO_THRESHOLDS = Array.from({ length: 100 }, (_, i) => i / 99)

export function useHeroCtas({ heroIntroCtaVisible, onUpdate, getExtraObservedElements }) {
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

  const heroIntroAfterYellowVisible = computed(
    () => heroIntroAfterCtaFloated.value && heroIntroCtaVisible.value,
  )

  const heroIntroThirdYellowVisible = computed(
    () => heroIntroThirdCtaFloated.value && heroIntroCtaVisible.value,
  )

  let heroCtaResizeAttached = false
  let heroCtaVisualViewportAttached = false
  const heroCtaIntersectionEnabled = ref(false)

  const heroCtaIntersection = useIntersectionObserver(
    () => {
      onHeroCtaIntersection()
    },
    { threshold: HERO_CTA_IO_THRESHOLDS, enabled: heroCtaIntersectionEnabled },
  )

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
    const yellowBottom = viewportBottom - HERO_CTA_IMAGE_BOTTOM_GAP_PX
    const epsilon = 0.5
    return rect.bottom >= yellowBottom - epsilon
  }

  function updateHeroCtaModes() {
    heroIntroCtaFloated.value = computeHeroCtaFloated(heroIntroPhotoRef.value, heroIntroRef.value)

    const heroIntroAfterBaseFloated = computeHeroCtaFloated(
      heroIntroAfterPhotoRef.value,
      heroIntroAfterRef.value,
    )
    const heroIntroAfterRedTakesOver = computeRedTakesOverFromYellow(
      heroIntroAfterTopCtaBtnRef.value,
    )
    heroIntroAfterCtaFloated.value = heroIntroAfterBaseFloated && !heroIntroAfterRedTakesOver

    const heroIntroThirdBaseFloated = computeHeroCtaFloated(
      heroIntroThirdPhotoRef.value,
      heroIntroThirdRef.value,
    )
    const heroIntroThirdRedTakesOver = computeRedTakesOverFromYellow(
      heroIntroThirdTopCtaBtnRef.value,
    )
    heroIntroThirdCtaFloated.value = heroIntroThirdBaseFloated && !heroIntroThirdRedTakesOver

    onUpdate?.()
  }

  function onHeroCtaIntersection() {
    updateHeroCtaModes()
  }

  function setupHeroCtaIntersection(on) {
    if (!on) {
      heroCtaIntersectionEnabled.value = false
      heroCtaIntersection.stop()
      if (heroCtaResizeAttached) {
        window.removeEventListener('resize', updateHeroCtaModes)
        heroCtaResizeAttached = false
      }
      if (heroCtaVisualViewportAttached && window.visualViewport) {
        const vv = window.visualViewport
        vv.removeEventListener('resize', updateHeroCtaModes)
        vv.removeEventListener('scroll', updateHeroCtaModes)
        heroCtaVisualViewportAttached = false
      }
      return
    }

    if (!heroCtaResizeAttached) {
      window.addEventListener('resize', updateHeroCtaModes)
      heroCtaResizeAttached = true
    }

    if (!heroCtaVisualViewportAttached && window.visualViewport) {
      const vv = window.visualViewport
      vv.addEventListener('resize', updateHeroCtaModes)
      vv.addEventListener('scroll', updateHeroCtaModes)
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
    heroIntroAfterYellowVisible,
    heroIntroThirdYellowVisible,
    updateHeroCtaModes,
    setupHeroCtaIntersection,
  }
}

