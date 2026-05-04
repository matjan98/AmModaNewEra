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
                  ref="heroIntroAtfComp"
                  :cta-css-vars="heroIntroCtaCssVars"
                  :image-src="heroIntroSecondImage"
                  :cta-visible="heroIntroCtaVisible"
                  :cta-floated="heroIntroCtaFloated"
                  :on-image-load="onHeroIntroImageLoad"
                  :on-cta-click="scrollToPageBottomSlow"
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
              ref="heroIntroAfterComp"
              :cta-css-vars="heroIntroCtaCssVars"
              :image-src="heroIntroFirstImage"
              :address-line="ADDRESS_LINE"
              :cta-visible="heroIntroCtaVisible"
              :yellow-top-hidden="heroIntroCtaVisible && !heroIntroAfterRedTakesOver"
              :cta-floated="heroIntroAfterCtaFloated"
              :on-image-load="onHeroIntroImageLoad"
              :on-cta-click="scrollToPageBottomFast"
            />

            <IndexPageStoreHours
              v-model:expanded="storeHoursExpanded"
              :opening-hours="OPENING_HOURS"
              :today-store-day-index="todayStoreDayIndex"
              :store-hours-heading-label="storeHoursHeadingLabel"
            />

            <IndexPageHeroIntroThird
              ref="heroIntroThirdComp"
              :cta-css-vars="heroIntroCtaCssVars"
              :image-src="heroIntroThirdImage"
              :cta-visible="heroIntroCtaVisible"
              :yellow-top-hidden="heroIntroCtaVisible && !heroIntroThirdRedTakesOver"
              :cta-floated="heroIntroThirdCtaFloated"
              :on-image-load="onHeroIntroImageLoad"
              :on-cta-click="scrollToPageBottomFast"
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
import { computed, defineAsyncComponent, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import IndexPageShopBottomSections from '../components/IndexPageShopBottomSections.vue'
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
import { SMALL_SCREEN_MAX_WIDTH_PX } from '../constants/breakpoints.js'
import heroIntroFirstImage from '../assets/main-photos/hero-2.webp'
import heroIntroSecondImage from '../assets/main-photos/hero-1.webp'
import heroIntroThirdImage from '../assets/main-photos/hero-3.webp'
import facebookShopPhoto from '../assets/main-photos/hero-shop.webp'

const IndexPageGalleryPanel = defineAsyncComponent(() =>
  import('../components/IndexPageGalleryPanel.vue'),
)

const TAB_STORAGE_KEY = 'index-page-active-tab'
const HERO_CTA_IMAGE_BOTTOM_GAP_PX = 20
const HERO_CTA_STACK_HEIGHT_PX = 96
const HERO_CTA_ADDRESS_ROW_HEIGHT_PX = 49
const HERO_CTA_BUTTON_ROW_HEIGHT_PX = 47

function getStoredTab() {
  try {
    const stored = localStorage.getItem(TAB_STORAGE_KEY)
    return stored === 'gallery' || stored === 'info' ? stored : 'info'
  } catch {
    return 'info'
  }
}

const activeTab = ref(getStoredTab())
const panelSlideDirection = ref('left')
const mainRef = ref(null)
const shopBottomSectionsRef = ref(null)
const facebookShopFloatPop = ref(false)
const heroIntroCtaVisible = ref(false)
const storeHoursExpanded = ref(false)

let facebookShopFloatSettleTimer = null

const { sectionTwoRows, updateSectionTwoWindowWidth } = useSectionTwoGrid({
  sectionTwoItems,
  breakpointPx: SMALL_SCREEN_MAX_WIDTH_PX,
})

const {
  activeSectionTwoName,
  onSectionTwoOverlayDocumentPointerDown,
  onSectionTwoOverlayKeydown,
  toggleSectionTwoOverlay,
} = useSectionTwoOverlay()

const { observeRevealZoomTargets, scheduleRevealZoomAfterLayout, setupRevealZoomObserver } =
  useRevealZoom({ mainRef })

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

const heroIntroCtaCssVars = computed(() => ({
  '--hero-cta-img-gap': `${HERO_CTA_IMAGE_BOTTOM_GAP_PX}px`,
  '--hero-cta-stack-height': `${HERO_CTA_STACK_HEIGHT_PX}px`,
  '--hero-cta-address-row-height': `${HERO_CTA_ADDRESS_ROW_HEIGHT_PX}px`,
  '--hero-cta-button-row-height': `${HERO_CTA_BUTTON_ROW_HEIGHT_PX}px`,
}))

let prevFacebookShopFloated = false

function onHeroCtasUpdate() {
  const isFloated = heroIntroFacebookCtaFloated.value

  if (!isFloated) {
    if (typeof window !== 'undefined' && facebookShopFloatSettleTimer != null) {
      clearTimeout(facebookShopFloatSettleTimer)
      facebookShopFloatSettleTimer = null
    }
    facebookShopFloatPop.value = false
  } else if (!prevFacebookShopFloated) {
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

  prevFacebookShopFloated = isFloated
}

const {
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
  updateHeroCtaModes,
  setupHeroCtaIntersection,
} = useHeroCtas({
  onUpdate: onHeroCtasUpdate,
  getExtraObservedElements: () => [shopBottomSectionsRef.value?.heroIntroFacebookRef?.value],
  getFacebookPhotoEl: () => shopBottomSectionsRef.value?.heroIntroFacebookPhotoRef?.value ?? null,
  getFacebookSectionEl: () => shopBottomSectionsRef.value?.heroIntroFacebookRef?.value ?? null,
  heroCtaStackHeightPx: HERO_CTA_STACK_HEIGHT_PX,
  heroCtaImageGapPx: HERO_CTA_IMAGE_BOTTOM_GAP_PX,
  takeoverEpsilonPx: -12,
})

const heroIntroAtfComp = ref(null)
const heroIntroAfterComp = ref(null)
const heroIntroThirdComp = ref(null)

watch(
  heroIntroAtfComp,
  (comp) => {
    heroIntroRef.value = comp?.rootEl ?? null
    heroIntroPhotoRef.value = comp?.photoEl ?? null
    heroIntroCtaRef.value = comp?.ctaEl ?? null
  },
  { flush: 'post' },
)

watch(
  heroIntroAfterComp,
  (comp) => {
    heroIntroAfterRef.value = comp?.rootEl ?? null
    heroIntroAfterPhotoRef.value = comp?.photoEl ?? null
    heroIntroAfterCtaRef.value = comp?.ctaEl ?? null
    heroIntroAfterTopCtaBtnRef.value = comp?.topCtaBtnEl ?? null
  },
  { flush: 'post' },
)

watch(
  heroIntroThirdComp,
  (comp) => {
    heroIntroThirdRef.value = comp?.rootEl ?? null
    heroIntroThirdPhotoRef.value = comp?.photoEl ?? null
    heroIntroThirdCtaRef.value = comp?.ctaEl ?? null
    heroIntroThirdTopCtaBtnRef.value = comp?.topCtaBtnEl ?? null
  },
  { flush: 'post' },
)


function scrollToPageBottomWithDuration(totalMs) {
  if (typeof window === 'undefined') return
  const root = document.documentElement
  const body = document.body
  const scrollHeight = Math.max(root?.scrollHeight ?? 0, body?.scrollHeight ?? 0)
  const clientHeight = root?.clientHeight ?? window.innerHeight ?? 0
  const maxScrollTop = Math.max(0, scrollHeight - clientHeight)

  animateWindowScrollTo({ top: maxScrollTop, totalMs })
}

function scrollToPageBottomFast() {
  scrollToPageBottomWithDuration(1000)
}

function scrollToPageBottomSlow() {
  scrollToPageBottomWithDuration(2500)
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

let mainResizeObserver = null

async function decodeVisibleHeroImages() {
  const root = mainRef.value
  if (!root) return
  const imgs = Array.from(root.querySelectorAll('.index-page__hero-intro-photo-img'))
  if (imgs.length === 0) return
  const decodePromises = imgs.map((img) => {
    if (typeof img.decode === 'function') return img.decode().catch(() => {})
    return Promise.resolve()
  })
  await Promise.race([
    Promise.all(decodePromises),
    new Promise((resolve) => window.setTimeout(resolve, 1500)),
  ])
}

onMounted(async () => {
  updateSectionTwoWindowWidth()
  document.addEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown, {
    passive: true,
  })
  window.addEventListener('keydown', onSectionTwoOverlayKeydown)
  await nextTick()
  if (activeTab.value === 'info') {
    setupHeroCtaIntersection(true)
  }
  setupRevealZoomObserver(true)
  scheduleRevealZoomAfterLayout()
  window.addEventListener('resize', onRevealZoomWindowResize, { passive: true })

  if (typeof ResizeObserver !== 'undefined' && mainRef.value) {
    mainResizeObserver = new ResizeObserver(() => {
      observeRevealZoomTargets()
      updateHeroCtaModes()
    })
    mainResizeObserver.observe(mainRef.value)
  }

  await decodeVisibleHeroImages()
  observeRevealZoomTargets()
  updateHeroCtaModes()
  heroIntroCtaVisible.value = true
})

onUnmounted(() => {
  setupHeroCtaIntersection(false)
  setupRevealZoomObserver(false)
  window.removeEventListener('resize', onRevealZoomWindowResize)
  document.removeEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown)
  window.removeEventListener('keydown', onSectionTwoOverlayKeydown)
  if (mainResizeObserver) {
    mainResizeObserver.disconnect()
    mainResizeObserver = null
  }
  if (facebookShopFloatSettleTimer != null) {
    clearTimeout(facebookShopFloatSettleTimer)
    facebookShopFloatSettleTimer = null
  }
})
</script>
