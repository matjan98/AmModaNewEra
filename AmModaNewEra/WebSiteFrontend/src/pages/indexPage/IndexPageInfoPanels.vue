<template>
  <div class="index-page__panels">
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
        :facebook-url="FACEBOOK_URL"
      />

      <IndexPageProductCategories
        :rows="sectionTwoRows"
        :active-name="activeSectionTwoName"
        :facebook-url="FACEBOOK_URL"
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
        :expanded="storeHoursExpanded"
        :opening-hours="OPENING_HOURS"
        :today-store-day-index="todayStoreDayIndex"
        :store-hours-heading-label="storeHoursHeadingLabel"
        @update:expanded="onUpdateStoreHoursExpanded"
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
        :facebook-url="FACEBOOK_URL"
        :maps-url="MAPS_URL"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import IndexPageShopBottomSections from '../../components/IndexPageShopBottomSections.vue'
import IndexPageHeroIntroAtf from '../../components/indexPage/IndexPageHeroIntroAtf.vue'
import IndexPageHeroIntroAfter from '../../components/indexPage/IndexPageHeroIntroAfter.vue'
import IndexPageHeroIntroThird from '../../components/indexPage/IndexPageHeroIntroThird.vue'
import IndexPageProductCategories from '../../components/indexPage/IndexPageProductCategories.vue'
import IndexPageQuickInfo from '../../components/indexPage/IndexPageQuickInfo.vue'
import IndexPageStoreHours from '../../components/indexPage/IndexPageStoreHours.vue'
import {
  ADDRESS_LINE,
  FACEBOOK_URL,
  MAPS_URL,
  OPENING_HOURS,
  PHONE_DISPLAY,
  PHONE_TEL_HREF,
} from '../../constants/siteInfo.js'
import heroIntroFirstImage from '../../assets/main-photos/hero-2.webp'
import heroIntroSecondImage from '../../assets/main-photos/hero-1.webp'
import heroIntroThirdImage from '../../assets/main-photos/hero-3.webp'

defineOptions({ name: 'IndexPageInfoPanels' })

defineProps({
  heroIntroCtaCssVars: { type: Object, required: true },
  heroIntroCtaVisible: { type: Boolean, required: true },
  heroIntroCtaFloated: { type: Boolean, required: true },
  heroIntroAfterCtaFloated: { type: Boolean, required: true },
  heroIntroThirdCtaFloated: { type: Boolean, required: true },
  heroIntroAfterRedTakesOver: { type: Boolean, required: true },
  heroIntroThirdRedTakesOver: { type: Boolean, required: true },
  heroIntroFacebookCtaFloated: { type: Boolean, required: true },
  facebookShopFloatPop: { type: Boolean, required: true },
  storeHoursExpanded: { type: Boolean, required: true },
  sectionTwoRows: { type: Array, required: true },
  activeSectionTwoName: { type: String, default: null },
  quickInfoTodayLine: { type: String, required: true },
  todayStoreDayIndex: { type: Number, required: true },
  storeHoursHeadingLabel: { type: String, required: true },
  onHeroIntroImageLoad: { type: Function, required: true },
  onRevealSectionTwoImageLoad: { type: Function, required: true },
  scrollToPageBottomSlow: { type: Function, required: true },
  scrollToPageBottomFast: { type: Function, required: true },
  toggleSectionTwoOverlay: { type: Function, required: true },
})

const emit = defineEmits(['update:storeHoursExpanded'])

function onUpdateStoreHoursExpanded(value) {
  emit('update:storeHoursExpanded', value)
}

const heroIntroAtfComp = ref(null)
const heroIntroAfterComp = ref(null)
const heroIntroThirdComp = ref(null)
const shopBottomSectionsRef = ref(null)

defineExpose({
  heroIntroAtfComp,
  heroIntroAfterComp,
  heroIntroThirdComp,
  shopBottomSectionsRef,
})
</script>
