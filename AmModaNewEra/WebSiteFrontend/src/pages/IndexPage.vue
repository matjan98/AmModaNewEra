<template>
  <q-page class="index-page">
    <div class="index-page__fixed-bottom-photo" aria-hidden="true">
      <img
        :src="facebookShopPhoto"
        alt=""
        class="index-page__fixed-bottom-photo-img"
        decoding="async"
        fetchpriority="low"
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
            <keep-alive :include="['IndexPageGalleryPanel', 'IndexPageInfoPanels']">
              <component
                :is="activeTab === 'info' ? IndexPageInfoPanels : IndexPageGalleryPanel"
                :key="activeTab"
                ref="activePanelRef"
                v-bind="panelBindings"
              />
            </keep-alive>
          </transition>
        </div>
      </main>
    </div>
  </q-page>
</template>

<script setup>
import { computed, defineAsyncComponent, nextTick, onMounted, onUnmounted, ref, unref, watch } from 'vue'
import IndexPageNavButtons from '../components/indexPage/IndexPageNavButtons.vue'
import IndexPageInfoPanels from './indexPage/IndexPageInfoPanels.vue'
import { OPENING_HOURS } from '../constants/siteInfo.js'
import { sectionTwoItems } from './indexPage/indexPageProducts.js'
import { useRevealZoom } from './indexPage/useRevealZoom.js'
import { useHeroCtas } from './indexPage/useHeroCtas.js'
import { useSectionTwoGrid } from './indexPage/useSectionTwoGrid.js'
import { useSectionTwoOverlay } from './indexPage/useSectionTwoOverlay.js'
import { useOpeningHours } from '../composables/useOpeningHours.js'
import { animateWindowScrollTo } from '../utils/scrollAnimation.js'
import { SMALL_SCREEN_MAX_WIDTH_PX } from '../constants/breakpoints.js'
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
const activePanelRef = ref(null)
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

const { todayDayIndex: todayStoreDayIndex, todayLine: quickInfoTodayLine, storeHoursHeadingLabel } =
  useOpeningHours(OPENING_HOURS)

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
  /** Align top ↔ floated swap with `viewportBottom - gap` (same as CSS `--hero-cta-img-gap`). */
  takeoverEpsilonPx: 0,
})

const panelBindings = computed(() => {
  if (activeTab.value === 'gallery') {
    return { observeRevealZoomTargets }
  }
  return {
    heroIntroCtaCssVars: heroIntroCtaCssVars.value,
    heroIntroCtaVisible: heroIntroCtaVisible.value,
    heroIntroCtaFloated: heroIntroCtaFloated.value,
    heroIntroAfterCtaFloated: heroIntroAfterCtaFloated.value,
    heroIntroThirdCtaFloated: heroIntroThirdCtaFloated.value,
    heroIntroAfterRedTakesOver: heroIntroAfterRedTakesOver.value,
    heroIntroThirdRedTakesOver: heroIntroThirdRedTakesOver.value,
    heroIntroFacebookCtaFloated: heroIntroFacebookCtaFloated.value,
    facebookShopFloatPop: facebookShopFloatPop.value,
    storeHoursExpanded: storeHoursExpanded.value,
    sectionTwoRows: sectionTwoRows.value,
    activeSectionTwoName: activeSectionTwoName.value,
    quickInfoTodayLine: quickInfoTodayLine.value,
    todayStoreDayIndex: todayStoreDayIndex.value,
    storeHoursHeadingLabel: storeHoursHeadingLabel.value,
    onHeroIntroImageLoad,
    onRevealSectionTwoImageLoad,
    scrollToPageBottomSlow,
    scrollToPageBottomFast,
    toggleSectionTwoOverlay,
    'onUpdate:storeHoursExpanded': (v) => {
      storeHoursExpanded.value = v
    },
  }
})

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

function clearHeroLayoutRefs() {
  heroIntroRef.value = null
  heroIntroPhotoRef.value = null
  heroIntroCtaRef.value = null
  heroIntroAfterRef.value = null
  heroIntroAfterPhotoRef.value = null
  heroIntroAfterCtaRef.value = null
  heroIntroAfterTopCtaBtnRef.value = null
  heroIntroThirdRef.value = null
  heroIntroThirdPhotoRef.value = null
  heroIntroThirdCtaRef.value = null
  heroIntroThirdTopCtaBtnRef.value = null
  shopBottomSectionsRef.value = null
}

function syncHeroRefsFromInfoPanel() {
  if (activeTab.value !== 'info') {
    clearHeroLayoutRefs()
    return
  }
  const panel = activePanelRef.value
  if (!panel) {
    clearHeroLayoutRefs()
    return
  }

  const atf = unref(panel.heroIntroAtfComp)
  const after = unref(panel.heroIntroAfterComp)
  const third = unref(panel.heroIntroThirdComp)
  const shop = unref(panel.shopBottomSectionsRef)

  heroIntroRef.value = atf?.rootEl ?? null
  heroIntroPhotoRef.value = atf?.photoEl ?? null
  heroIntroCtaRef.value = atf?.ctaEl ?? null

  heroIntroAfterRef.value = after?.rootEl ?? null
  heroIntroAfterPhotoRef.value = after?.photoEl ?? null
  heroIntroAfterCtaRef.value = after?.ctaEl ?? null
  heroIntroAfterTopCtaBtnRef.value = after?.topCtaBtnEl ?? null

  heroIntroThirdRef.value = third?.rootEl ?? null
  heroIntroThirdPhotoRef.value = third?.photoEl ?? null
  heroIntroThirdCtaRef.value = third?.ctaEl ?? null
  heroIntroThirdTopCtaBtnRef.value = third?.topCtaBtnEl ?? null

  shopBottomSectionsRef.value = shop ?? null
}

watch([activeTab, activePanelRef], () => nextTick(() => syncHeroRefsFromInfoPanel()), {
  flush: 'post',
})

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
  syncHeroRefsFromInfoPanel()
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
