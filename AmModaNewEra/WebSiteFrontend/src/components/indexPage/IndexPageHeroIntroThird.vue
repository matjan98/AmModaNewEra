<template>
  <section
    :ref="setHeroIntroThirdEl"
    class="index-page__hero-intro index-page__hero-intro--maps-underlay-cover"
    :style="ctaCssVars"
  >
    <div
      :ref="setHeroIntroThirdPhotoEl"
      class="index-page__hero-intro-photo index-page__reveal-media"
    >
      <img
        :src="imageSrc"
        alt=""
        class="index-page__hero-intro-photo-img index-page__reveal-media-img"
        @load="onImageLoad"
      >
    </div>
    <!-- AI_ALIAS: heroIntroMain3_topStickyCta ("górny stały") -->
    <div
      class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--top"
      :class="{ 'index-page__hero-intro-sticky-cta--hidden': yellowTopHidden }"
    >
      <div class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <p class="index-page__hero-intro-address index-page__hero-intro-address--visible">
            <span class="index-page__hero-intro-quote">
              <span class="index-page__hero-intro-quote-text">”Najlepszy taki sklep w okolicy“</span>
              <br>
              <span class="index-page__hero-intro-quote-signature">~Jakub Pilarz</span>
            </span>
          </p>
        </div>
        <div class="index-page__hero-intro-btn-row">
          <button
            :ref="setHeroIntroThirdTopCtaBtnEl"
            type="button"
            class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--visible"
            @click="onCtaClick"
          >
            Odwiedź nas
          </button>
        </div>
      </div>
    </div>
    <!-- AI_ALIAS: heroIntroMain3_bottomStickyCta ("dolny stały" when not floated; "fixed" when --floated) -->
    <div
      class="index-page__hero-intro-sticky-cta"
      :class="{ 'index-page__hero-intro-sticky-cta--floated': ctaFloated }"
    >
      <div :ref="setHeroIntroThirdCtaEl" class="index-page__hero-intro-cta-block">
        <div class="index-page__hero-intro-address-row">
          <p
            class="index-page__hero-intro-address"
            :class="{ 'index-page__hero-intro-address--visible': ctaVisible }"
          >
            <span class="index-page__hero-intro-quote">
              <span class="index-page__hero-intro-quote-text">”Najlepszy taki sklep w okolicy“</span>
              <br>
              <span class="index-page__hero-intro-quote-signature">~Jakub Pilarz</span>
            </span>
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
  ctaVisible: { type: Boolean, required: true },
  yellowTopHidden: { type: Boolean, required: true },
  ctaFloated: { type: Boolean, required: true },
  onImageLoad: { type: Function, required: true },
  onCtaClick: { type: Function, required: true },
  setHeroIntroThirdEl: { type: Function, required: true },
  setHeroIntroThirdPhotoEl: { type: Function, required: true },
  setHeroIntroThirdCtaEl: { type: Function, required: true },
  setHeroIntroThirdTopCtaBtnEl: { type: Function, required: true },
})
</script>
