<template>
  <section
    ref="rootEl"
    class="index-page__hero-intro"
    :style="ctaCssVars"
  >
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
      class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--first"
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
            class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--solid"
            :class="{ 'index-page__hero-intro-cta-btn--visible': ctaVisible }"
            aria-label="Otwórz nawigację do sklepu w Google Maps"
          >
            Nawiguj
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
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
