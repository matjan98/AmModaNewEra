<template>
  <section
    ref="rootEl"
    class="index-page__hero-intro"
    :class="{ 'index-page__hero-intro--maps-cta-inview': ctaVisible }"
    :style="ctaCssVars"
  >
    <h1 class="index-page__seo-heading">A&amp;M Moda Damska – butik z modą damską w Kozach</h1>
    <div
      ref="photoEl"
      class="index-page__hero-intro-photo index-page__reveal-media"
    >
      <img
        :src="imageSrc"
        alt="AM Moda"
        class="index-page__hero-intro-photo-img index-page__reveal-media-img"
        decoding="async"
        fetchpriority="high"
        @load="onImageLoad"
      >
    </div>
    <div
      class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--first index-page-maps-cta-host"
      :class="{ 'index-page__hero-intro-sticky-cta--floated': ctaFloated }"
    >
      <div ref="ctaEl" class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <p
            class="index-page__hero-intro-address index-page__hero-intro-address--atf"
            :class="{ 'index-page__hero-intro-address--visible': ctaVisible }"
          >
            {{ addressLine }}
          </p>
        </div>
        <div class="index-page__hero-intro-btn-row">
          <a
            :href="mapsUrl"
            target="_blank"
            rel="noopener"
            class="index-page-shop-bottom-sections__facebook-cta-btn index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay"
            :class="{ 'index-page-shop-bottom-sections__facebook-cta-btn--visible': ctaVisible }"
            aria-label="Otwórz nawigację do sklepu w Google Maps"
          >
            <span class="index-page-shop-bottom-sections__facebook-cta-btn-textstack">
              <span class="index-page-shop-bottom-sections__facebook-cta-btn-label">Nawiguj</span>
              <span class="index-page-shop-bottom-sections__facebook-cta-btn-subline">Google Maps</span>
            </span>
            <img
              :src="googleMapsPinImg"
              alt=""
              width="26"
              height="36"
              class="index-page-shop-bottom-sections__facebook-cta-btn-maps-pin"
              aria-hidden="true"
              decoding="async"
            >
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import googleMapsPinImg from '../../assets/google-maps.png'
import { ADDRESS_LINE, MAPS_URL } from '../../constants/siteInfo.js'

defineProps({
  ctaCssVars: { type: Object, required: true },
  imageSrc: { type: String, required: true },
  ctaVisible: { type: Boolean, required: true },
  ctaFloated: { type: Boolean, required: true },
  onImageLoad: { type: Function, required: true },
})

const addressLine = ADDRESS_LINE
const mapsUrl = MAPS_URL

const rootEl = ref(null)
const photoEl = ref(null)
const ctaEl = ref(null)

defineExpose({ rootEl, photoEl, ctaEl })
</script>
