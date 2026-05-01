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
                <IndexPageHeroIntroAtf
                  :cta-css-vars="heroIntroCtaCssVars"
                  :image-src="heroIntroSecondImage"
                  :cta-visible="heroIntroCtaVisible"
                  :cta-floated="heroIntroCtaFloated"
                  :on-image-load="onHeroIntroImageLoad"
                  :on-cta-click="scrollToPageBottom"
                  :set-hero-intro-atf-el="setHeroIntroAtfEl"
                  :set-hero-intro-atf-photo-el="setHeroIntroAtfPhotoEl"
                  :set-hero-intro-atf-cta-el="setHeroIntroAtfCtaEl"
                />

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

            <IndexPageHeroIntroAfter
              :cta-css-vars="heroIntroCtaCssVars"
              :image-src="heroIntroFirstImage"
              :address-line="ADDRESS_LINE"
              :cta-visible="heroIntroCtaVisible"
              :yellow-top-hidden="heroIntroCtaVisible && !heroIntroAfterRedTakesOver"
              :cta-floated="heroIntroAfterCtaFloated"
              :on-image-load="onHeroIntroImageLoad"
              :on-cta-click="scrollToPageBottom"
              :set-hero-intro-after-el="setHeroIntroAfterEl"
              :set-hero-intro-after-photo-el="setHeroIntroAfterPhotoEl"
              :set-hero-intro-after-cta-el="setHeroIntroAfterCtaEl"
              :set-hero-intro-after-top-cta-btn-el="setHeroIntroAfterTopCtaBtnEl"
            />

            <IndexPageStoreHours
              v-model:expanded="storeHoursExpanded"
              :opening-hours="OPENING_HOURS"
              :today-store-day-index="todayStoreDayIndex"
              :store-hours-heading-label="storeHoursHeadingLabel"
              :intro-revealed="storeHoursIntroRevealed"
            />

            <IndexPageHeroIntroThird
              :cta-css-vars="heroIntroCtaCssVars"
              :image-src="heroIntroThirdImage"
              :cta-visible="heroIntroCtaVisible"
              :yellow-top-hidden="heroIntroCtaVisible && !heroIntroThirdRedTakesOver"
              :cta-floated="heroIntroThirdCtaFloated"
              :on-image-load="onHeroIntroImageLoad"
              :on-cta-click="scrollToPageBottom"
              :set-hero-intro-third-el="setHeroIntroThirdEl"
              :set-hero-intro-third-photo-el="setHeroIntroThirdPhotoEl"
              :set-hero-intro-third-cta-el="setHeroIntroThirdCtaEl"
              :set-hero-intro-third-top-cta-btn-el="setHeroIntroThirdTopCtaBtnEl"
            />

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
import IndexPageShopBottomSections from '../components/IndexPageShopBottomSections.vue'
import IndexPageGalleryPanel from '../components/IndexPageGalleryPanel.vue'
import IndexPageHeroIntroAtf from '../components/indexPage/IndexPageHeroIntroAtf.vue'
import IndexPageHeroIntroAfter from '../components/indexPage/IndexPageHeroIntroAfter.vue'
import IndexPageHeroIntroThird from '../components/indexPage/IndexPageHeroIntroThird.vue'
import IndexPageNavButtons from '../components/indexPage/IndexPageNavButtons.vue'
import IndexPageProductCategories from '../components/indexPage/IndexPageProductCategories.vue'
import IndexPageQuickInfo from '../components/indexPage/IndexPageQuickInfo.vue'
import IndexPageStoreHours from '../components/indexPage/IndexPageStoreHours.vue'
import { ADDRESS_LINE, FACEBOOK_URL, MAPS_URL, OPENING_HOURS, PHONE_DISPLAY, PHONE_TEL_HREF } from '../constants/siteInfo.js'
import { sectionTwoItems } from './indexPage/indexPageProducts.js'
import { useRevealZoom } from './indexPage/useRevealZoom.js'
import { useHeroCtas } from './indexPage/useHeroCtas.js'
import { useSectionTwoGrid } from './indexPage/useSectionTwoGrid.js'
import { useSectionTwoOverlay } from './indexPage/useSectionTwoOverlay.js'
import { useOpeningHours } from '../composables/useOpeningHours.js'
import { animateWindowScrollTo } from '../utils/scrollAnimation.js'
import heroIntroFirstImage from '../assets/Main photos/main2.webp'
import heroIntroSecondImage from '../assets/Main photos/atf_photo.webp'
import heroIntroThirdImage from '../assets/Main photos/main3.webp'
import facebookShopPhoto from '../assets/Main photos/shop.webp'

const TAB_STORAGE_KEY = 'index-page-active-tab'

const SHOW_GALLERY_NAV_BUTTON = true

const SECTION_TWO_BREAKPOINT_PX = 751


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

const heroIntroCtaCssVars = computed(() => ({
  '--hero-cta-img-gap': `${HERO_CTA_IMAGE_BOTTOM_GAP_PX}px`,
  '--hero-cta-stack-height': `${HERO_CTA_STACK_HEIGHT_PX}px`,
  '--hero-cta-address-row-height': `${HERO_CTA_ADDRESS_ROW_HEIGHT_PX}px`,
  '--hero-cta-button-row-height': `${HERO_CTA_BUTTON_ROW_HEIGHT_PX}px`,
}))

function updateFacebookCtaModes() {
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

const {
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
  updateHeroCtaModes,
  setupHeroCtaIntersection,
} = useHeroCtas({
  heroIntroCtaVisible,
  onUpdate: updateFacebookCtaModes,
  getExtraObservedElements: () => [shopBottomSectionsRef.value?.heroIntroFacebookRef?.value],
  heroCtaImageGapPx: HERO_CTA_IMAGE_BOTTOM_GAP_PX,
  takeoverEpsilonPx: -12,
})

const setHeroIntroAtfEl = (el) => {
  heroIntroRef.value = el
}
const setHeroIntroAtfPhotoEl = (el) => {
  heroIntroPhotoRef.value = el
}
const setHeroIntroAtfCtaEl = (el) => {
  heroIntroCtaRef.value = el
}

const setHeroIntroAfterEl = (el) => {
  heroIntroAfterRef.value = el
}
const setHeroIntroAfterPhotoEl = (el) => {
  heroIntroAfterPhotoRef.value = el
}
const setHeroIntroAfterCtaEl = (el) => {
  heroIntroAfterCtaRef.value = el
}
const setHeroIntroAfterTopCtaBtnEl = (el) => {
  heroIntroAfterTopCtaBtnRef.value = el
}

const setHeroIntroThirdEl = (el) => {
  heroIntroThirdRef.value = el
}
const setHeroIntroThirdPhotoEl = (el) => {
  heroIntroThirdPhotoRef.value = el
}
const setHeroIntroThirdCtaEl = (el) => {
  heroIntroThirdCtaRef.value = el
}
const setHeroIntroThirdTopCtaBtnEl = (el) => {
  heroIntroThirdTopCtaBtnRef.value = el
}


function scrollToPageBottom() {
  if (typeof window === 'undefined') return
  const root = document.documentElement
  const body = document.body
  const scrollHeight = Math.max(root?.scrollHeight ?? 0, body?.scrollHeight ?? 0)
  const clientHeight = root?.clientHeight ?? window.innerHeight ?? 0
  const maxScrollTop = Math.max(0, scrollHeight - clientHeight)

  animateWindowScrollTo({ top: maxScrollTop, totalMs: 1500 })
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

const { todayDayIndex: todayStoreDayIndex, todayLine: quickInfoTodayLine, storeHoursHeadingLabel } =
  useOpeningHours(OPENING_HOURS)

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

<style>
.index-page {
  --index-address-above-cta-shadow:
  0 2px 5px rgb(0, 0, 0),
  0 0 10px rgb(0, 0, 0);
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
