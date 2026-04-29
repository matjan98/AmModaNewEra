<template>
  <q-page class="index-page">
    <div class="index-page__fixed-bottom-photo" aria-hidden="true">
      <img
        :src="facebookShopPhoto"
        alt=""
        class="index-page__fixed-bottom-photo-img"
        decoding="async"
      >
    </div>

    <IndexPageNavButtons
      :active-tab="activeTab"
      :show-gallery-nav-button="SHOW_GALLERY_NAV_BUTTON"
      @go-to-gallery="goToGallery"
      @go-to-info="goToInfo"
    />
    <div class="index-page__shell">
      <main ref="mainRef" class="index-page__main">
        <div class="index-page__panels-transition-wrap">
          <transition :name="'index-page__slide-' + panelSlideDirection">
            <div v-if="activeTab === 'info'" key="info" class="index-page__panels">
              <div class="index-page__panels-inner">
                <section
                  ref="heroIntroRef"
                  class="index-page__hero-intro"
                  :style="heroIntroCtaCssVars"
                >
                  <div ref="heroIntroPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                    <img
                      :src="heroIntroSecondImage"
                      alt="AM Moda"
                      class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                      @load="onHeroIntroImageLoad"
                    >
                  </div>
                  <div
                    class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--first"
                    :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroCtaFloated }"
                  >
                    <div ref="heroIntroCtaRef" class="index-page__hero-intro-cta-block">
                      <div class="index-page__hero-intro-address-row">
                        <div
                          class="index-page__hero-intro-google-mini"
                          :class="{ 'index-page__hero-intro-google-mini--visible': heroIntroCtaVisible }"
                        >
                          <GoogleReviewsCard variant="mini" :with-margin="false" />
                        </div>
                      </div>
                      <div class="index-page__hero-intro-btn-row">
                        <button
                          type="button"
                          class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--solid"
                          :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                          @click="scrollToLocationSection"
                        >
                          Odwiedź nas
                        </button>
                      </div>
                    </div>
                  </div>
                </section>

            <IndexPageQuickInfo
              :phone-tel-href="PHONE_TEL_HREF"
              :phone-display="PHONE_DISPLAY"
              :quick-info-today-line="quickInfoTodayLine"
              :facebook-url="facebookUrl"
            />

            <IndexPageProductCategories
              :rows="sectionTwoRows"
              :active-name="activeSectionTwoName"
              :facebook-url="facebookUrl"
              @toggle="toggleSectionTwoOverlay"
              @image-load="onRevealSectionTwoImageLoad"
            />

            <section
              ref="heroIntroAfterRef"
              class="index-page__hero-intro"
              :style="heroIntroCtaCssVars"
            >
              <div ref="heroIntroAfterPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                <img
                  :src="heroIntroFirstImage"
                  alt=""
                  class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroAfterCtaFloated }"
              >
                <div ref="heroIntroAfterCtaRef" class="index-page__hero-intro-cta-block">
                  <div class="index-page__hero-intro-address-row">
                    <p
                      class="index-page__hero-intro-address index-page__hero-intro-address--second"
                      :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                    >
                      {{ ADDRESS_LINE }}
                    </p>
                  </div>
                  <div class="index-page__hero-intro-btn-row">
                    <button
                      type="button"
                      class="index-page__hero-intro-cta-btn"
                      :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                      @click="scrollToLocationSection"
                    >
                      Odwiedź nas
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <IndexPageStoreHours
              v-model:expanded="storeHoursExpanded"
              :opening-hours="STORE_OPENING_HOURS"
              :today-store-day-index="todayStoreDayIndex"
              :store-hours-heading-label="storeHoursHeadingLabel"
              :intro-revealed="storeHoursIntroRevealed"
            />

            <section
              ref="heroIntroThirdRef"
              class="index-page__hero-intro index-page__hero-intro--maps-underlay-cover"
              :style="heroIntroCtaCssVars"
            >
              <div ref="heroIntroThirdPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                <img
                  :src="heroIntroThirdImage"
                  alt=""
                  class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroThirdCtaFloated }"
              >
                <div ref="heroIntroThirdCtaRef" class="index-page__hero-intro-cta-block">
                  <div class="index-page__hero-intro-address-row">
                    <p
                      class="index-page__hero-intro-address"
                      :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                    >
                      <span class="index-page__hero-intro-quote">
                        <span class="index-page__hero-intro-quote-text">”Najlepszy taki sklep w okolicy“</span>
                        <br>
                        <span class="index-page__hero-intro-quote-signature">~Jakub Pilarz</span>
                      </span>
                    </p>
                  </div>
                  <div class="index-page__hero-intro-btn-row">
                    <button
                      type="button"
                      class="index-page__hero-intro-cta-btn"
                      :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                      @click="scrollToLocationSection"
                    >
                      Odwiedź nas
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <IndexPageShopBottomSections
              ref="shopBottomSectionsRef"
              :cta-css-vars="heroIntroCtaCssVars"
              :hero-intro-cta-visible="heroIntroCtaVisible"
              :hero-intro-facebook-cta-floated="heroIntroFacebookCtaFloated"
              :facebook-shop-float-pop="facebookShopFloatPop"
              :facebook-url="facebookUrl"
              :maps-url="mapsUrl"
            />
            </div>
          </div>
          <IndexPageGalleryPanel
            v-else
            key="gallery"
            :observe-reveal-zoom-targets="observeRevealZoomTargets"
          />
        </transition>
        </div>
      </main>
    </div>
  </q-page>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import GoogleReviewsCard from '../components/GoogleReviewsCard.vue'
import IndexPageShopBottomSections from '../components/IndexPageShopBottomSections.vue'
import IndexPageGalleryPanel from '../components/IndexPageGalleryPanel.vue'
import IndexPageNavButtons from '../components/indexPage/IndexPageNavButtons.vue'
import IndexPageProductCategories from '../components/indexPage/IndexPageProductCategories.vue'
import IndexPageQuickInfo from '../components/indexPage/IndexPageQuickInfo.vue'
import IndexPageStoreHours from '../components/indexPage/IndexPageStoreHours.vue'
import { ADDRESS_LINE, FACEBOOK_URL, MAPS_URL, OPENING_HOURS, PHONE_DISPLAY, PHONE_TEL_HREF } from '../constants/siteInfo.js'
import { sectionTwoItems } from './indexPage/indexPageProducts.js'
import { useRevealZoom } from './indexPage/useRevealZoom.js'
import { useSectionTwoGrid } from './indexPage/useSectionTwoGrid.js'
import { useSectionTwoOverlay } from './indexPage/useSectionTwoOverlay.js'
import heroIntroFirstImage from '../assets/Main photos/main2.webp'
import heroIntroSecondImage from '../assets/Main photos/atf_photo.webp'
import heroIntroThirdImage from '../assets/Main photos/main3.webp'
import facebookShopPhoto from '../assets/Main photos/shop.webp'

const TAB_STORAGE_KEY = 'index-page-active-tab'

const SHOW_GALLERY_NAV_BUTTON = true

const SECTION_TWO_BREAKPOINT_PX = 751


const STORE_OPENING_HOURS = OPENING_HOURS

function getStoredTab() {
  try {
    const stored = localStorage.getItem(TAB_STORAGE_KEY)
    return stored === 'gallery' || stored === 'info' ? stored : 'info'
  } catch {
    return 'info'
  }
}

const activeTab = ref(getStoredTab())

watch(activeTab, (value) => {
  try {
    localStorage.setItem(TAB_STORAGE_KEY, value)
  } catch {
    void 0
  }
  setupRevealZoomObserver(false)
  if (value === 'info') {
    nextTick(() => {
      setupHeroCtaIntersection(true)
      setupRevealZoomObserver(true)
      updateHeroCtaModes()
    })
  } else {
    setupHeroCtaIntersection(false)
    nextTick(() => {
      setupRevealZoomObserver(true)
    })
  }
})

const panelSlideDirection = ref('left')
const mainRef = ref(null)
const heroIntroRef = ref(null)
const heroIntroPhotoRef = ref(null)
const heroIntroCtaRef = ref(null)
const heroIntroAfterRef = ref(null)
const heroIntroAfterPhotoRef = ref(null)
const heroIntroAfterCtaRef = ref(null)
const heroIntroThirdRef = ref(null)
const heroIntroThirdPhotoRef = ref(null)
const heroIntroThirdCtaRef = ref(null)
const shopBottomSectionsRef = ref(null)

const { sectionTwoRows, updateSectionTwoWindowWidth } = useSectionTwoGrid({
  sectionTwoItems,
  breakpointPx: SECTION_TWO_BREAKPOINT_PX,
})

const {
  activeSectionTwoName,
  onSectionTwoOverlayDocumentPointerDown,
  onSectionTwoOverlayKeydown,
  toggleSectionTwoOverlay,
} = useSectionTwoOverlay()

const { observeRevealZoomTargets, scheduleRevealZoomAfterLayout, setupRevealZoomObserver } =
  useRevealZoom({ mainRef })

const heroIntroCtaFloated = ref(false)
const heroIntroAfterCtaFloated = ref(false)
const heroIntroThirdCtaFloated = ref(false)

const heroIntroFacebookCtaFloated = ref(false)

const facebookShopFloatPop = ref(false)
let facebookShopFloatSettleTimer = null
const facebookShopCtaPassedOnce = ref(false)
const heroIntroCtaVisible = ref(false)
const storeHoursExpanded = ref(false)
const storeHoursIntroRevealed = true

const HERO_CTA_IMAGE_BOTTOM_GAP_PX = 20

const HERO_CTA_STACK_HEIGHT_PX = 96
const HERO_CTA_ADDRESS_ROW_HEIGHT_PX = 49
const HERO_CTA_BUTTON_ROW_HEIGHT_PX = 47

const HERO_CTA_IO_THRESHOLDS = Array.from({ length: 100 }, (_, i) => i / 99)

const heroIntroCtaCssVars = computed(() => ({
  '--hero-cta-img-gap': `${HERO_CTA_IMAGE_BOTTOM_GAP_PX}px`,
  '--hero-cta-stack-height': `${HERO_CTA_STACK_HEIGHT_PX}px`,
  '--hero-cta-address-row-height': `${HERO_CTA_ADDRESS_ROW_HEIGHT_PX}px`,
  '--hero-cta-button-row-height': `${HERO_CTA_BUTTON_ROW_HEIGHT_PX}px`,
}))

let heroCtaIntersectionObserver = null
let heroCtaResizeAttached = false
let heroCtaVisualViewportAttached = false

function getViewportBottomClientY() {
  const vv = window.visualViewport
  if (vv) {
    return vv.offsetTop + vv.height
  }
  return window.innerHeight || document.documentElement.clientHeight || 0
}


function scrollToLocationSection() {
  shopBottomSectionsRef.value?.heroIntroFacebookRef?.value?.scrollIntoView({
    behavior: 'smooth',
    block: 'start',
  })
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

function computeHeroFacebookCtaFloated(photoEl, sectionFallbackEl) {
  if (facebookShopCtaPassedOnce.value) return false

  const el = photoEl ?? sectionFallbackEl
  if (!el) return false
  const rect = el.getBoundingClientRect()
  if (rect.height <= 0) return false

  const viewportBottom = getViewportBottomClientY()
  const epsilon = 0.5

  if (rect.bottom <= 0 || rect.top >= viewportBottom) return false

  const stackH = HERO_CTA_STACK_HEIGHT_PX
  const gap = HERO_CTA_IMAGE_BOTTOM_GAP_PX
  const floatedCtaCenterY = viewportBottom - gap - stackH / 2

  const meetImageTop = floatedCtaCenterY - rect.height / 2

  if (rect.bottom <= viewportBottom + epsilon) return false

  return rect.top > meetImageTop + epsilon
}

function updateFacebookShopSectionPassed() {
  if (facebookShopCtaPassedOnce.value) return
  const section = shopBottomSectionsRef.value?.heroIntroFacebookRef?.value
  if (!section) return
  const r = section.getBoundingClientRect()
  if (r.bottom < 0) {
    facebookShopCtaPassedOnce.value = true
  }
}

function scheduleFacebookShopFloatSettle() {
  if (typeof window === 'undefined') return
  if (facebookShopFloatSettleTimer != null) {
    clearTimeout(facebookShopFloatSettleTimer)
    facebookShopFloatSettleTimer = null
  }
  nextTick(() => {
    facebookShopFloatSettleTimer = window.setTimeout(() => {
      facebookShopFloatPop.value = false
      facebookShopFloatSettleTimer = null
    }, 48)
  })
}

function updateHeroCtaModes() {
  heroIntroCtaFloated.value = computeHeroCtaFloated(heroIntroPhotoRef.value, heroIntroRef.value)
  heroIntroAfterCtaFloated.value = computeHeroCtaFloated(
    heroIntroAfterPhotoRef.value,
    heroIntroAfterRef.value,
  )
  heroIntroThirdCtaFloated.value = computeHeroCtaFloated(
    heroIntroThirdPhotoRef.value,
    heroIntroThirdRef.value,
  )
  const prevFbFloat = heroIntroFacebookCtaFloated.value
  const shop = shopBottomSectionsRef.value
  heroIntroFacebookCtaFloated.value = computeHeroFacebookCtaFloated(
    shop?.heroIntroFacebookPhotoRef?.value ?? null,
    shop?.heroIntroFacebookRef?.value ?? null,
  )

  if (!heroIntroFacebookCtaFloated.value) {
    if (typeof window !== 'undefined' && facebookShopFloatSettleTimer != null) {
      clearTimeout(facebookShopFloatSettleTimer)
      facebookShopFloatSettleTimer = null
    }
    facebookShopFloatPop.value = false
  } else if (!prevFbFloat && heroIntroFacebookCtaFloated.value) {
    if (
      typeof window !== 'undefined' &&
      window.matchMedia?.('(prefers-reduced-motion: reduce)').matches
    ) {
      facebookShopFloatPop.value = false
    } else {
      facebookShopFloatPop.value = true
      scheduleFacebookShopFloatSettle()
    }
  }

  updateFacebookShopSectionPassed()
}

function onHeroCtaIntersection() {
  updateHeroCtaModes()
}

function setupHeroCtaIntersection(on) {
  if (!on) {
    if (heroCtaIntersectionObserver) {
      heroCtaIntersectionObserver.disconnect()
      heroCtaIntersectionObserver = null
    }
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

  if (heroCtaIntersectionObserver) {
    heroCtaIntersectionObserver.disconnect()
  }

  heroCtaIntersectionObserver = new IntersectionObserver(onHeroCtaIntersection, {
    root: null,
    rootMargin: '0px',
    threshold: HERO_CTA_IO_THRESHOLDS,
  })

  const sections = [
    heroIntroRef.value,
    heroIntroAfterRef.value,
    heroIntroThirdRef.value,
    shopBottomSectionsRef.value?.heroIntroFacebookRef?.value,
  ]
  for (const el of sections) {
    if (el) heroCtaIntersectionObserver.observe(el)
  }
  updateHeroCtaModes()
}


function onRevealZoomWindowResize() {
  updateSectionTwoWindowWidth()
  observeRevealZoomTargets()
}

function onHeroIntroImageLoad() {
  updateHeroCtaModes()
  if (!heroIntroCtaVisible.value) {
    heroIntroCtaVisible.value = true
  }
  observeRevealZoomTargets()
}

function onRevealSectionTwoImageLoad() {
  observeRevealZoomTargets()
}

function goToGallery() {
  panelSlideDirection.value = 'left'
  activeTab.value = 'gallery'
}

function goToInfo() {
  panelSlideDirection.value = 'right'
  activeTab.value = 'info'
}

const facebookUrl = FACEBOOK_URL

const mapsUrl = MAPS_URL

const todayStoreDayIndex = computed(() => new Date().getDay())


const quickInfoTodayLine = computed(() => {
  const row = STORE_OPENING_HOURS.find((h) => h.dayIndex === todayStoreDayIndex.value)
  if (!row) return '—'
  if (row.hours === 'Zamknięte') return 'Zamknięte'
  return `Otwarte ${row.hours}`
})

const storeHoursHeadingLabel = computed(() =>
  todayStoreDayIndex.value === 0 ? 'Godziny otwarcia' : 'dziś otwarte!',
)

onMounted(async () => {
  updateSectionTwoWindowWidth()
  document.addEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown, {
    passive: true,
  })
  window.addEventListener('keydown', onSectionTwoOverlayKeydown)
  await nextTick()
  if (activeTab.value === 'info') {
    setupHeroCtaIntersection(true)
    setupRevealZoomObserver(true)
  } else {
    setupRevealZoomObserver(true)
  }
  scheduleRevealZoomAfterLayout()
  window.setTimeout(() => observeRevealZoomTargets(), 120)
  window.setTimeout(() => observeRevealZoomTargets(), 480)
  window.addEventListener('resize', onRevealZoomWindowResize, { passive: true })
  await nextTick()
  updateHeroCtaModes()
  window.setTimeout(() => {
    updateHeroCtaModes()
    heroIntroCtaVisible.value = true
  }, 300)
})
onUnmounted(() => {
  setupHeroCtaIntersection(false)
  setupRevealZoomObserver(false)
  window.removeEventListener('resize', onRevealZoomWindowResize)
  document.removeEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown)
  window.removeEventListener('keydown', onSectionTwoOverlayKeydown)
  if (facebookShopFloatSettleTimer != null) {
    clearTimeout(facebookShopFloatSettleTimer)
    facebookShopFloatSettleTimer = null
  }
})
</script>

<style scoped>
.index-page {
  --index-address-above-cta-shadow:
    0 1px 2px rgba(0, 0, 0, 0.75),
    0 2px 12px rgba(0, 0, 0, 0.55),
    0 4px 24px rgba(0, 0, 0, 0.4);
  background: transparent;
  color: rgba(255, 255, 255, 0.92);
  min-height: 100vh;
  
  padding: 0;
  box-sizing: border-box;
  position: relative;
  isolation: isolate;
}

.index-page__fixed-bottom-photo {
  position: fixed;
  inset: 0;
  width: 100dvw;
  height: 100dvh;
  overflow: hidden;
  z-index: -1;
  pointer-events: none;
}

.index-page__fixed-bottom-photo-img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  object-position: center center;
}

.index-page__shell {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: relative;
  z-index: 2;
}

.index-page__main {
  position: relative;
  width: 100%;
  max-width: 100%;
  background: transparent;
  border-radius: 0;
  overflow: visible;
}


.index-page__panels-transition-wrap {
  position: relative;
  min-height: 50vh;
}

.index-page__panels {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  
  overflow: visible;
  overflow-x: clip;
  box-sizing: border-box;
}

.index-page__panels-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 6px 0;
  box-sizing: border-box;
  background: transparent;
}


.index-page__slide-left-enter-active,
.index-page__slide-left-leave-active,
.index-page__slide-right-enter-active,
.index-page__slide-right-leave-active {
  position: absolute;
  left: 50%;
  margin-left: -50dvw;
  top: 0;
  width: 100dvw;
  transition: transform 0.3s ease;
}

.index-page__slide-left-enter-active,
.index-page__slide-right-enter-active {
  z-index: 2;
}

.index-page__slide-left-leave-active,
.index-page__slide-right-leave-active {
  z-index: 1;
}

.index-page__slide-left-enter-from {
  transform: translateX(100%);
}

.index-page__slide-left-enter-to {
  transform: translateX(0);
}

.index-page__slide-left-leave-from {
  transform: translateX(0);
}

.index-page__slide-left-leave-to {
  transform: translateX(-100%);
}

.index-page__slide-right-enter-from {
  transform: translateX(-100%);
}

.index-page__slide-right-enter-to {
  transform: translateX(0);
}

.index-page__slide-right-leave-from {
  transform: translateX(0);
}

.index-page__slide-right-leave-to {
  transform: translateX(100%);
}


.index-page__hero-intro {
  position: relative;
  margin-top: 0;
  margin-bottom: 0;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  background: transparent;
  box-sizing: border-box;
  overflow: visible;
}


.index-page__hero-intro--maps-underlay-cover {
  z-index: 1650;
}

.index-page__hero-intro-photo {
  width: 100%;
  overflow: visible;
}

.index-page__hero-intro-photo-img {
  width: 100%;
  height: auto;
  display: block;
  object-fit: contain;
}

.index-page__hero-intro-photo.index-page__reveal-media {
  overflow: hidden;
}


.index-page__reveal-media {
  overflow: hidden;
}

.index-page__hero-intro-sticky-cta {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 6;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-right: 16px;
  padding-bottom: max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  padding-left: 16px;
  box-sizing: border-box;
  pointer-events: none;
}


.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated) {
  padding-bottom: calc(max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px)) + 14px);
}


.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-cta-block {
  height: auto;
  min-height: 0;
  max-height: none;
  gap: 10px;
}

.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-address-row,
.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-btn-row {
  height: auto;
  min-height: 0;
  max-height: none;
}

.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-cta-block {
  height: auto;
  min-height: 0;
  max-height: none;
  gap: 8px;
}

.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-address-row,
.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-btn-row {
  height: auto;
  min-height: 0;
  max-height: none;
}

.index-page__hero-intro-sticky-cta--floated {
  position: fixed;
  top: auto;
  bottom: max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  left: 0;
  right: 0;
  height: auto;
  justify-content: center;
  align-items: flex-end;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-right: 16px;
  padding-bottom: 0;
  padding-left: 16px;
  z-index: 1500;
}

.index-page__hero-intro-cta-block {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  gap: 0;
  box-sizing: border-box;
  width: 100%;
  max-width: min(92vw, 560px);
  height: var(--hero-cta-stack-height, 96px);
  min-height: var(--hero-cta-stack-height, 96px);
  max-height: var(--hero-cta-stack-height, 96px);
  margin: 0;
  padding: 0;
  pointer-events: none;
}

.index-page__hero-intro-address-row {
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  height: var(--hero-cta-address-row-height, 49px);
  min-height: var(--hero-cta-address-row-height, 49px);
  max-height: var(--hero-cta-address-row-height, 49px);
  margin: 0;
  padding: 0;
  flex-shrink: 0;
}

.index-page__hero-intro-btn-row {
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  height: var(--hero-cta-button-row-height, 47px);
  min-height: var(--hero-cta-button-row-height, 47px);
  max-height: var(--hero-cta-button-row-height, 47px);
  margin: 0;
  padding: 0;
  flex-shrink: 0;
}

.index-page__hero-intro-address {
  margin: 0;
  padding: 0 8px;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.82rem, 2.35vw, 1.08rem);
  font-weight: 500;
  line-height: 1.25;
  letter-spacing: 0.04em;
  text-align: center;
  color: #ffffff;
  text-transform: none;
  text-shadow: var(--index-address-above-cta-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  width: 100%;
  min-width: 0;
  height: 100%;
  white-space: nowrap;
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.45s ease 0.05s,
              transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
}

.index-page__hero-intro-google-mini {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.45s ease 0.05s,
              transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
  pointer-events: auto;
}

.index-page__hero-intro-google-mini--visible {
  opacity: 1;
  transform: translateY(0);
}

.index-page__hero-intro-address--visible {
  opacity: 1;
  transform: translateY(0);
}

.index-page__hero-intro-quote {
  text-shadow:
    0 2px 10px rgba(0, 0, 0, 0.92),
    0 0 18px rgba(0, 0, 0, 0.55);
}

.index-page__hero-intro-quote-text {
  display: inline;
}

.index-page__hero-intro-quote-signature {
  display: block;
  width: 100%;
  text-align: right;
  margin-top: 2px;
  line-height: 1.05;
  font-size: 0.82em;
  letter-spacing: 0.03em;
  color: #ffffff;
  opacity: 1;
}

@media (min-width: 1000px) {
  .index-page__hero-intro-address--second {
    font-size: clamp(0.86rem, 2.45vw, 1.14rem);
  }
}


.index-page__hero-intro-cta-btn {
  pointer-events: auto;
  cursor: pointer;
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  min-width: 140px;
  margin: 0;
  padding: 12px 22px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 4px;
  font-family: inherit;
  font-size: 1.1rem;
  font-weight: 500;
  letter-spacing: normal;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.96);
  background: rgba(14, 14, 18, 0.72);
  backdrop-filter: blur(14px) saturate(1.15);
  -webkit-backdrop-filter: blur(14px) saturate(1.15);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 10px 32px rgba(0, 0, 0, 0.45);
  isolation: isolate;
  opacity: 0;
  transform: scale(0.94);
  transform-origin: center center;
  transition:
    transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    opacity 0.35s ease,
    padding 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    min-width 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

button.index-page__hero-intro-cta-btn {
  appearance: none;
  -webkit-appearance: none;
}


.index-page__hero-intro-cta-btn--solid {
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.03em;
  color: #141414;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.14);
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  box-shadow: 0 6px 22px rgba(0, 0, 0, 0.2);
}

.index-page__hero-intro-cta-btn--solid.index-page__hero-intro-cta-btn--visible:hover {
  background: #f3f3f3;
  border-color: rgba(0, 0, 0, 0.2);
  box-shadow: 0 8px 26px rgba(0, 0, 0, 0.22);
}

.index-page__hero-intro-cta-btn--solid.index-page__hero-intro-cta-btn--visible:active {
  background: #e8e8e8;
}


@media (max-width: 749px) {
  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop) {
    --hero-cta-stack-height: 90px;
    --hero-cta-address-row-height: 34px;
    --hero-cta-button-row-height: 56px;
  }

  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop)
    .index-page__hero-intro-address-row {
    align-items: flex-end;
  }

  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop)
    .index-page__hero-intro-btn-row {
    align-items: flex-start;
  }

  .index-page__hero-intro-address:not(.index-page__hero-intro-address--facebook-shop-last) {
    font-size: clamp(0.68rem, 3.45vw, 1.14rem);
    line-height: 1.18;
  }

  .index-page__hero-intro-cta-btn {
    min-width: 130px;
    padding: 12px 17px;
    font-size: 0.8rem;
    border-radius: 4px;
  }

  .index-page__hero-intro-cta-btn--solid {
    font-size: 0.84rem;
    font-weight: 500;
  }

  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 118px;
    padding: 12px 15px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 114px;
    padding: 11px 14px;
  }
}

.index-page__hero-intro-cta-btn--visible {
  opacity: 1;
  transform: scale(1);
}


.index-page__hero-intro-cta-btn--visible:hover {
  min-width: 126px;
  padding: 10px 18px;
}

.index-page__hero-intro-cta-btn--visible:active {
  min-width: 122px;
  padding: 9px 16px;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__hero-intro-cta-btn {
    opacity: 1;
    transform: none;
    transition:
      padding 0.45s ease,
      min-width 0.45s ease;
  }

  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 126px;
    padding: 10px 18px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 122px;
    padding: 9px 16px;
  }
}

@media (prefers-reduced-motion: reduce) and (max-width: 749px) {
  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 118px;
    padding: 7px 13px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 114px;
    padding: 6px 12px;
  }
}

@media (max-width: 768px) {
  .index-page__hero-intro {
    margin-top: 0;
  }

  .index-page__hero-intro-photo {
    height: 75vh;
    overflow: hidden;
  }

  .index-page__hero-intro-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center center;
  }

  .index-page__hero-intro-sticky-cta {
    padding: 0 12px max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  }

  .index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated) {
    padding-bottom: calc(max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px)) + 14px);
  }

  .index-page__hero-intro-sticky-cta--floated {
    padding: 0 12px;
  }

  .index-page__hero-intro-btn-row {
    height: auto;
    min-height: var(--hero-cta-button-row-height, 47px);
    max-height: none;
  }
}

.index-page__reveal-media:not(.index-page__reveal-media--in) .index-page__reveal-media-img {
  transform: scale(1.15);
  transform-origin: center center;
}

.index-page__reveal-media--in .index-page__reveal-media-img {
  transform: scale(1);
  transition: transform 1s cubic-bezier(0.22, 1, 0.36, 1) 0s;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__reveal-media:not(.index-page__reveal-media--in) .index-page__reveal-media-img {
    transform: scale(1);
  }

  .index-page__reveal-media--in .index-page__reveal-media-img {
    transition: none;
  }
}

@media (max-width: 600px) {
  .index-page {
    padding: 0;
  }

  .index-page__shell {
    padding: 0 10px;
    gap: 16px;
  }
}
</style>
