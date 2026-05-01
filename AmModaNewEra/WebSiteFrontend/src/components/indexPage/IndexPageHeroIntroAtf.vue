<template>
  <section
    :ref="setHeroIntroAtfEl"
    class="index-page__hero-intro"
    :style="ctaCssVars"
  >
    <div
      :ref="setHeroIntroAtfPhotoEl"
      class="index-page__hero-intro-photo index-page__reveal-media"
    >
      <img
        :src="imageSrc"
        alt="AM Moda"
        class="index-page__hero-intro-photo-img index-page__reveal-media-img"
        @load="onImageLoad"
      >
    </div>
    <div
      class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--first"
      :class="{ 'index-page__hero-intro-sticky-cta--floated': ctaFloated }"
    >
      <div :ref="setHeroIntroAtfCtaEl" class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <div
            class="index-page__hero-intro-google-mini"
            :class="{ 'index-page__hero-intro-google-mini--visible': ctaVisible }"
          >
            <GoogleReviewsCard variant="mini" :with-margin="false" />
          </div>
        </div>
        <div class="index-page__hero-intro-btn-row">
          <button
            type="button"
            class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--solid"
            :class="{ 'index-page__hero-intro-cta-btn--visible': ctaVisible }"
            @click="onCtaClick"
          >
            Odwiedź nas
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import GoogleReviewsCard from '../GoogleReviewsCard.vue'

defineProps({
  ctaCssVars: { type: Object, required: true },
  imageSrc: { type: String, required: true },
  ctaVisible: { type: Boolean, required: true },
  ctaFloated: { type: Boolean, required: true },
  onImageLoad: { type: Function, required: true },
  onCtaClick: { type: Function, required: true },
  setHeroIntroAtfEl: { type: Function, required: true },
  setHeroIntroAtfPhotoEl: { type: Function, required: true },
  setHeroIntroAtfCtaEl: { type: Function, required: true },
})
</script>
