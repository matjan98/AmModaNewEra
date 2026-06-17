<template>

  <q-page class="index-page">

    <div class="index-page__fixed-bottom-photo" aria-hidden="true">
      <img
        :src="fixedBottomPhoto"
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

          <transition
            :name="'index-page__slide-' + panelSlideDirection"
            @after-enter="onPanelTransitionAfterEnter"
          >

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

import { useRevealZoom } from './indexPage/useRevealZoom.js'

import { useHeroCtas } from './indexPage/useHeroCtas.js'

import { useOpeningHours } from '../composables/useOpeningHours.js'
import fixedBottomPhoto from '../assets/main-photos/background.webp'



const IndexPageGalleryPanel = defineAsyncComponent(() =>

  import('../components/IndexPageGalleryPanel.vue'),

)



const TAB_STORAGE_KEY = 'index-page-active-tab'

const HERO_CTA_IMAGE_BOTTOM_GAP_PX = 20

const HERO_CTA_STACK_HEIGHT_PX = 114

const HERO_CTA_ADDRESS_ROW_HEIGHT_PX = 62

const HERO_CTA_BUTTON_ROW_HEIGHT_PX = 52



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



let facebookShopFloatSettleTimer = null



const { observeRevealZoomTargets, scheduleRevealZoomAfterLayout, setupRevealZoomObserver } =

  useRevealZoom({ mainRef })



const { todayDayIndex: todayStoreDayIndex, storeHoursHeadingLabel } =

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

  heroIntroCtaFloated,

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

})



const panelBindings = computed(() => {

  if (activeTab.value === 'gallery') {

    return { observeRevealZoomTargets }

  }

  return {

    heroIntroCtaCssVars: heroIntroCtaCssVars.value,

    heroIntroCtaVisible: heroIntroCtaVisible.value,

    heroIntroCtaFloated: heroIntroCtaFloated.value,

    heroIntroFacebookCtaFloated: heroIntroFacebookCtaFloated.value,

    facebookShopFloatPop: facebookShopFloatPop.value,

    todayStoreDayIndex: todayStoreDayIndex.value,

    storeHoursHeadingLabel: storeHoursHeadingLabel.value,

    onHeroIntroImageLoad,
  }
})



watch(activeTab, (value) => {

  try {

    localStorage.setItem(TAB_STORAGE_KEY, value)

  } catch {

    void 0

  }

  setupRevealZoomObserver(false)

  if (value !== 'info') {

    setupHeroCtaIntersection(false)

  }

})



function onPanelTransitionAfterEnter() {

  setupRevealZoomObserver(true)

  if (activeTab.value === 'info') {

    setupHeroCtaIntersection(true)

    updateHeroCtaModes()

  }

}



function clearHeroLayoutRefs() {

  heroIntroRef.value = null

  heroIntroPhotoRef.value = null

  heroIntroCtaRef.value = null

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

  const shop = unref(panel.shopBottomSectionsRef)



  heroIntroRef.value = atf?.rootEl ?? null

  heroIntroPhotoRef.value = atf?.photoEl ?? null

  heroIntroCtaRef.value = atf?.ctaEl ?? null



  shopBottomSectionsRef.value = shop ?? null

}



watch([activeTab, activePanelRef], () => nextTick(() => syncHeroRefsFromInfoPanel()), {

  flush: 'post',

})



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

  observeRevealZoomTargets()

}



function onHeroIntroImageLoad() {

  updateHeroCtaModes()

  if (!heroIntroCtaVisible.value) {

    heroIntroCtaVisible.value = true

  }

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

