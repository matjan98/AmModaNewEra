<template>
  <div class="index-page__panels">
    <div class="index-page__panels-inner">
      <IndexPageHeroIntroAtf
        ref="heroIntroAtfComp"
        :cta-css-vars="heroIntroCtaCssVars"
        :image-src="heroIntroImage"
        :cta-visible="heroIntroCtaVisible"
        :cta-floated="heroIntroCtaFloated"
        :on-image-load="onHeroIntroImageLoad"
      />

      <IndexPageQuickInfo
        :phone-tel-href="PHONE_TEL_HREF"
        :phone-display="PHONE_DISPLAY"
        :facebook-url="FACEBOOK_URL"
        :opening-hours="weeklyOpeningHours"
        :today-store-day-index="todayStoreDayIndex"
        :store-hours-heading-label="storeHoursHeadingLabel"
      />
    </div>

    <div class="index-page-shop-photo-divider" aria-hidden="true">
      <img
        :src="shopPhotoSrc"
        alt=""
        class="index-page-shop-photo-divider__img"
        width="1678"
        height="937"
        decoding="async"
      >
    </div>

    <div class="index-page__panels-inner">
      <IndexPageShopBottomSections
        :facebook-url="FACEBOOK_URL"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import IndexPageShopBottomSections from '../../components/IndexPageShopBottomSections.vue'
import IndexPageHeroIntroAtf from '../../components/indexPage/IndexPageHeroIntroAtf.vue'
import IndexPageQuickInfo from '../../components/indexPage/IndexPageQuickInfo.vue'
import { useHeroImagePreload } from '../../composables/useHeroImagePreload.js'
import { useIsSmallScreen } from '../../composables/useIsSmallScreen.js'
import {
  FACEBOOK_URL,
  PHONE_DISPLAY,
  PHONE_TEL_HREF,
} from '../../constants/siteInfo.js'
import { useSiteSettings } from '../../composables/useSiteSettings.js'
import heroIntroDesktopImage from '../../assets/main-photos/hero-1.webp'
import heroIntroMobileImage from '../../assets/main-photos/hero_mobile.webp'
import shopPhotoSrc from '../../assets/main-photos/hero-shop.webp'

const { matches: isSmallScreen } = useIsSmallScreen()
const { weeklyOpeningHours } = useSiteSettings()

const heroIntroImage = computed(() =>
  isSmallScreen.value ? heroIntroMobileImage : heroIntroDesktopImage,
)

useHeroImagePreload({
  mobile: heroIntroMobileImage,
  desktop: heroIntroDesktopImage,
})

defineOptions({ name: 'IndexPageInfoPanels' })

defineProps({
  heroIntroCtaCssVars: { type: Object, required: true },
  heroIntroCtaVisible: { type: Boolean, required: true },
  heroIntroCtaFloated: { type: Boolean, required: true },
  todayStoreDayIndex: { type: Number, required: true },
  storeHoursHeadingLabel: { type: String, required: true },
  onHeroIntroImageLoad: { type: Function, required: true },
})

const heroIntroAtfComp = ref(null)

defineExpose({
  heroIntroAtfComp,
})
</script>
