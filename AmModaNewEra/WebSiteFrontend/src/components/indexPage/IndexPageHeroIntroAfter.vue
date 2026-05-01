<template>
  <section
    :ref="setHeroIntroAfterEl"
    class="index-page__hero-intro"
    :style="ctaCssVars"
  >
    <div
      :ref="setHeroIntroAfterPhotoEl"
      class="index-page__hero-intro-photo index-page__reveal-media"
    >
      <img
        :src="imageSrc"
        alt=""
        class="index-page__hero-intro-photo-img index-page__reveal-media-img"
        @load="onImageLoad"
      >
    </div>
    <!-- AI_ALIAS: heroIntroMain2_topStickyCta ("górny stały") -->
    <div
      class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--top"
      :class="{ 'index-page__hero-intro-sticky-cta--hidden': yellowTopHidden }"
    >
      <div class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <p
            class="index-page__hero-intro-address index-page__hero-intro-address--second index-page__hero-intro-address--visible"
          >
            {{ addressLine }}
          </p>
        </div>
        <div class="index-page__hero-intro-btn-row">
          <button
            :ref="setHeroIntroAfterTopCtaBtnEl"
            type="button"
            class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--visible"
            @click="onCtaClick"
          >
            Odwiedź nas
          </button>
        </div>
      </div>
    </div>
    <!-- AI_ALIAS: heroIntroMain2_bottomStickyCta ("dolny stały" when not floated; "fixed" when --floated) -->
    <div
      class="index-page__hero-intro-sticky-cta"
      :class="{ 'index-page__hero-intro-sticky-cta--floated': ctaFloated }"
    >
      <div :ref="setHeroIntroAfterCtaEl" class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <p
            class="index-page__hero-intro-address index-page__hero-intro-address--second"
            :class="{ 'index-page__hero-intro-address--visible': ctaVisible }"
          >
            {{ addressLine }}
          </p>
        </div>
        <div class="index-page__hero-intro-btn-row">
          <button
            type="button"
            class="index-page__hero-intro-cta-btn"
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
defineProps({
  ctaCssVars: { type: Object, required: true },
  imageSrc: { type: String, required: true },
  addressLine: { type: String, required: true },
  ctaVisible: { type: Boolean, required: true },
  yellowTopHidden: { type: Boolean, required: true },
  ctaFloated: { type: Boolean, required: true },
  onImageLoad: { type: Function, required: true },
  onCtaClick: { type: Function, required: true },
  setHeroIntroAfterEl: { type: Function, required: true },
  setHeroIntroAfterPhotoEl: { type: Function, required: true },
  setHeroIntroAfterCtaEl: { type: Function, required: true },
  setHeroIntroAfterTopCtaBtnEl: { type: Function, required: true },
})
</script>
