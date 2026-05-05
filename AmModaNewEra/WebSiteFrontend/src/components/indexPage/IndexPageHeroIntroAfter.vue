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
        alt=""
        class="index-page__hero-intro-photo-img index-page__reveal-media-img"
        decoding="async"
        fetchpriority="low"
        @load="onImageLoad"
      >
    </div>
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
            ref="topCtaBtnEl"
            type="button"
            class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--visible"
            @click="onCtaClick"
          >
            Odwiedź nas
          </button>
        </div>
      </div>
    </div>
    <div
      class="index-page__hero-intro-sticky-cta"
      :class="{ 'index-page__hero-intro-sticky-cta--floated': ctaFloated }"
    >
      <div ref="ctaEl" class="index-page__hero-intro-cta-block">
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
import { ref } from 'vue'

defineProps({
  ctaCssVars: { type: Object, required: true },
  imageSrc: { type: String, required: true },
  addressLine: { type: String, required: true },
  ctaVisible: { type: Boolean, required: true },
  yellowTopHidden: { type: Boolean, required: true },
  ctaFloated: { type: Boolean, required: true },
  onImageLoad: { type: Function, required: true },
  onCtaClick: { type: Function, required: true },
})

const rootEl = ref(null)
const photoEl = ref(null)
const ctaEl = ref(null)
const topCtaBtnEl = ref(null)

defineExpose({ rootEl, photoEl, ctaEl, topCtaBtnEl })
</script>
