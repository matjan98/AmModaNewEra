<template>
  <div class="index-page-shop-bottom-sections index-page-shop-bottom-sections__root">
    <!-- 1) Facebook CTA above shop block -->
    <section
      ref="facebookShopFollowRef"
      class="index-page-shop-bottom-sections__facebook-shop-follow"
      aria-label="Facebook AM Moda Damska"
    >
      <p class="index-page-shop-bottom-sections__facebook-shop-follow-text">
        Zobacz nasze najnowsze kolekcje na Facebooku:
      </p>
      <a
        :href="facebookUrl"
        target="_blank"
        rel="noopener"
        class="index-page-shop-bottom-sections__facebook-cta-btn index-page-shop-bottom-sections__facebook-cta-btn--below"
        aria-label="Otwórz profil AM Moda Damska na Facebooku"
      >
        Przejdź na Facebook
        <q-icon
          name="fa-brands fa-facebook"
          size="20px"
          class="index-page-shop-bottom-sections__facebook-cta-btn-icon index-page-shop-bottom-sections__facebook-cta-btn-icon--external"
          aria-hidden="true"
        />
      </a>
    </section>

    <!-- 2) Address + Google Maps (sticky / float, matches fixed shop photo) -->
    <section
      ref="heroIntroFacebookRef"
      class="index-page-shop-bottom-sections__hero-intro index-page-shop-bottom-sections__hero-intro--facebook-fixed-bg"
      :class="{ 'index-page-shop-bottom-sections__hero-intro--inview': heroIntroFacebookInView }"
      :style="ctaCssVars"
      aria-label="Sklep — zdjęcie i nawigacja"
    >
      <div
        ref="heroIntroFacebookPhotoRef"
        class="index-page-shop-bottom-sections__hero-intro-photo index-page-shop-bottom-sections__hero-intro-photo--facebook-spacer"
        aria-hidden="true"
      />
      <div
        class="index-page-shop-bottom-sections__hero-intro-sticky-cta index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop"
        :class="{
          'index-page-shop-bottom-sections__hero-intro-sticky-cta--floated': heroIntroFacebookCtaFloated,
          'index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop': facebookShopFloatPop,
        }"
      >
        <div
          ref="heroIntroFacebookCtaRef"
          class="index-page-shop-bottom-sections__hero-intro-cta-block index-page-shop-bottom-sections__hero-intro-cta-block--facebook-shop"
        >
          <div class="index-page-shop-bottom-sections__hero-intro-address-row">
            <p
              class="index-page-shop-bottom-sections__hero-intro-address index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last"
              :class="{ 'index-page-shop-bottom-sections__hero-intro-address--visible': bigMapsCtaVisible }"
            >
              Kozy, ul. Bielska 166
            </p>
          </div>
          <div class="index-page-shop-bottom-sections__hero-intro-btn-row">
            <a
              ref="bigMapsCtaBtnRef"
              :href="mapsUrl"
              target="_blank"
              rel="noopener"
              class="index-page-shop-bottom-sections__facebook-cta-btn index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay"
              :class="{ 'index-page-shop-bottom-sections__facebook-cta-btn--visible': bigMapsCtaVisible }"
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
              />
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- 3) Small fixed Maps CTA (shows after Facebook container appears) -->
    <div
      v-show="smallFixedMapsCtaVisible"
      class="index-page-shop-bottom-sections__small-fixed-maps-cta"
      aria-label="Szybka nawigacja do sklepu"
    >
      <p class="index-page-shop-bottom-sections__small-fixed-maps-address">
        <span class="index-page-shop-bottom-sections__small-fixed-maps-address-text">
          Kozy, ul. Bielska 166
        </span>
      </p>
      <a
        :href="mapsUrl"
        target="_blank"
        rel="noopener"
        class="index-page-shop-bottom-sections__facebook-cta-btn index-page-shop-bottom-sections__facebook-cta-btn--small-fixed"
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
          class="index-page-shop-bottom-sections__facebook-cta-btn-maps-pin index-page-shop-bottom-sections__facebook-cta-btn-maps-pin--small"
          aria-hidden="true"
          decoding="async"
        />
      </a>
    </div>

    <div class="index-page-shop-bottom-sections__google-reviews-slot" aria-label="Ocena Google">
      <GoogleReviewsCard
        v-if="isSmallScreen"
        variant="mini"
        :with-margin="false"
        class="index-page-shop-bottom-sections__google-reviews"
      />
      <GoogleReviewsCard
        v-else
        :with-margin="false"
        class="index-page-shop-bottom-sections__google-reviews"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import GoogleReviewsCard from './GoogleReviewsCard.vue'
import googleMapsPinImg from '../assets/google-maps.png'
import { useMediaQuery } from '../composables/useMediaQuery.js'
import { useIntersectionObserver } from '../composables/useIntersectionObserver.js'

defineProps({
  ctaCssVars: {
    type: Object,
    required: true,
  },
  heroIntroCtaVisible: {
    type: Boolean,
    required: true,
  },
  heroIntroFacebookCtaFloated: {
    type: Boolean,
    required: true,
  },
  facebookShopFloatPop: {
    type: Boolean,
    required: true,
  },
  facebookUrl: {
    type: String,
    default: 'https://www.facebook.com/AMModaDamska/',
  },
  mapsUrl: {
    type: String,
    default: 'https://www.google.com/maps/dir/?api=1&destination=Kozy%2C%20Bielska%20166',
  },
})

const facebookShopFollowRef = ref(null)
const heroIntroFacebookRef = ref(null)
const heroIntroFacebookPhotoRef = ref(null)
const heroIntroFacebookCtaRef = ref(null)
const bigMapsCtaBtnRef = ref(null)
const heroIntroFacebookInView = ref(false)
const facebookShopFollowInView = ref(false)
const bigMapsCtaFullyVisible = ref(false)
const { matches: isSmallScreen } = useMediaQuery('(max-width: 749px)')

const bigMapsCtaVisible = computed(() => Boolean(bigMapsCtaFullyVisible.value))
const smallFixedMapsCtaVisible = computed(() =>
  Boolean(facebookShopFollowInView.value && !bigMapsCtaFullyVisible.value)
)

const isElementFullyInViewport = (el, tolerancePx = 2) => {
  if (!el || typeof el.getBoundingClientRect !== 'function') return false
  const rect = el.getBoundingClientRect()
  const viewportWidth = window.innerWidth || document.documentElement.clientWidth
  const viewportHeight = window.innerHeight || document.documentElement.clientHeight

  return (
    rect.top >= 0 - tolerancePx &&
    rect.left >= 0 - tolerancePx &&
    rect.bottom <= viewportHeight + tolerancePx &&
    rect.right <= viewportWidth + tolerancePx
  )
}

useIntersectionObserver(
  (entry) => {
    heroIntroFacebookInView.value = Boolean(entry?.isIntersecting)
  },
  { threshold: 0.35 },
  heroIntroFacebookRef,
)

useIntersectionObserver(
  (entry) => {
    facebookShopFollowInView.value = Boolean(entry?.isIntersecting)
  },
  { threshold: 0 },
  facebookShopFollowRef,
)

useIntersectionObserver(
  (entry) => {
    const el = bigMapsCtaBtnRef.value
    if (!el) return
    const ratio = entry?.intersectionRatio ?? 0
    if (ratio <= 0) {
      bigMapsCtaFullyVisible.value = false
      return
    }

    if (ratio >= 0.98) {
      bigMapsCtaFullyVisible.value = isElementFullyInViewport(el, 2)
      return
    }

    bigMapsCtaFullyVisible.value = false
  },
  { threshold: [0, 0.25, 0.5, 0.75, 0.9, 0.95, 0.98, 0.99, 1] },
  bigMapsCtaBtnRef,
)

defineExpose({
  facebookShopFollowRef,
  heroIntroFacebookRef,
  heroIntroFacebookPhotoRef,
  heroIntroFacebookCtaRef,
  bigMapsCtaBtnRef,
})
</script>

<style scoped>
.index-page-shop-bottom-sections__root {
  --index-address-above-cta-shadow:
    0 1px 2px rgba(0, 0, 0, 0.75),
    0 2px 12px rgba(0, 0, 0, 0.55),
    0 4px 24px rgba(0, 0, 0, 0.4);
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  position: relative;
  left: 50%;
  width: 100vw;
  margin-left: -50vw;
  min-height: 100vh;
  height: 100vh;
  max-height: 100vh;
  overflow-x: visible;
  overflow-y: hidden;
}

.index-page-shop-bottom-sections__hero-intro--facebook-fixed-bg {
  background: transparent;
  flex: 1 1 0;
  min-height: 0;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.index-page-shop-bottom-sections__hero-intro-photo--facebook-spacer {
  width: 100%;
  flex: 1 1 auto;
  min-height: 0;
}

.index-page-shop-bottom-sections__hero-intro {
  position: relative;
  margin-top: 0;
  margin-bottom: 0;
  width: 100%;
  background: transparent;
  box-sizing: border-box;
  overflow: visible;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 6;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-right: 16px;
  padding-bottom: max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  padding-left: 16px;
  box-sizing: border-box;
  pointer-events: none;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated {
  position: fixed;
  top: auto;
  bottom: max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  left: 0;
  right: 0;
  height: auto;
  justify-content: center;
  align-items: flex-end;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-right: 16px;
  padding-bottom: 0;
  padding-left: 16px;
  z-index: 1500;
}

/*
 * Shop Nawiguj bar: when it switches to fixed (`--floated`), `--facebook-float-pop` holds one paint at the
 * same typography + scale as the in-photo hero overlay; JS clears it → CSS settles to the compact bar.
 */
.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  .index-page-shop-bottom-sections__hero-intro-cta-block--facebook-shop {
  height: auto;
  min-height: 0;
  max-height: none;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  .index-page-shop-bottom-sections__hero-intro-address-row {
  height: auto;
  min-height: 0;
  max-height: none;
}

@media (min-width: 750px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__hero-intro-cta-block--facebook-shop {
    gap: 10px;
  }
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(calc(0.34rem + 1px), calc(1.1vw + 1px), calc(0.49rem + 1px));
  line-height: 1.2;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  font-weight: 600;
  color: #ffffff;
  -webkit-font-smoothing: antialiased;
  text-shadow:
    0 1px 2px rgba(0, 0, 0, 0.88),
    0 2px 10px rgba(0, 0, 0, 0.55);
  transform: translateY(0);
  transition:
    font-size 0.26s cubic-bezier(0.4, 0, 0.2, 1),
    line-height 0.26s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(calc(0.28rem + 1px), calc(1.325vw + 1px), calc(0.41rem + 1px));
    line-height: 1.22;
  }
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  transform: translateY(0) scale(1);
  transition:
    transform 0.26s cubic-bezier(0.4, 0, 0.2, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(0.36rem, 2.625vw, 1.525rem);
  line-height: 1.15;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  font-weight: 600;
  color: #ffffff;
  -webkit-font-smoothing: antialiased;
  text-shadow:
    0 0 2px rgba(0, 0, 0, 0.95),
    0 1px 3px rgba(0, 0, 0, 0.9),
    0 2px 14px rgba(0, 0, 0, 0.78),
    0 4px 28px rgba(0, 0, 0, 0.55),
    0 0 48px rgba(0, 0, 0, 0.35);
  transition: none;
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(0.325rem, 2.9vw, 1.08rem);
    line-height: 1.18;
    letter-spacing: 0.05em;
  }
}

@media (min-width: 750px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    transform: translateY(-20px);
  }
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  transform: translateY(0) scale(1.07);
  transition: none;
}

/* Last hero (shop photo): dock address + Nawiguj at vertical center of the image when not floated; enlarge Nawiguj + address. */
.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated) {
  align-items: center;
  padding-top: max(8px, env(safe-area-inset-top, 0px));
  padding-bottom: max(22px, env(safe-area-inset-bottom, 0px));
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-cta-block--facebook-shop {
  height: auto;
  min-height: var(--hero-cta-stack-height, 96px);
  max-height: none;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-address-row {
  height: auto;
  min-height: var(--hero-cta-address-row-height, 49px);
  max-height: none;
  padding-bottom: 0;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(0.36rem, 2.625vw, 1.525rem);
  line-height: 1.15;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  font-weight: 600;
  color: #ffffff;
  -webkit-font-smoothing: antialiased;
  text-shadow:
    0 0 2px rgba(0, 0, 0, 0.95),
    0 1px 3px rgba(0, 0, 0, 0.9),
    0 2px 14px rgba(0, 0, 0, 0.78),
    0 4px 28px rgba(0, 0, 0, 0.55),
    0 0 48px rgba(0, 0, 0, 0.35);
  transition:
    font-size 0.38s cubic-bezier(0.16, 1, 0.3, 1),
    line-height 0.38s cubic-bezier(0.16, 1, 0.3, 1);
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last:not(.index-page-shop-bottom-sections__hero-intro-address--visible) {
  font-size: clamp(calc(0.41rem + 1px), calc(1.175vw + 1px), calc(0.54rem + 1px));
  transition:
    font-size 0.26s cubic-bezier(0.42, 0, 0.58, 1),
    line-height 0.26s cubic-bezier(0.42, 0, 0.58, 1);
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(0.325rem, 2.9vw, 1.08rem);
    line-height: 1.18;
    letter-spacing: 0.05em;
  }
}

@media (min-width: 750px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    transform: translateY(-20px);
  }
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  transform: translateY(0) scale(1.07);
  transition:
    transform 0.38s cubic-bezier(0.16, 1, 0.3, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

@media (prefers-reduced-motion: reduce) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    transition: font-size 0.38s ease, line-height 0.38s ease;
  }

  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last:not(.index-page-shop-bottom-sections__hero-intro-address--visible) {
    transition: font-size 0.26s ease, line-height 0.26s ease;
  }

  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
    transform: translateY(0) scale(1);
    transition: none;
  }

  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay:not(.index-page-shop-bottom-sections__facebook-cta-btn--visible) {
    transition: transform 0.26s ease;
  }
}

.index-page-shop-bottom-sections__hero-intro-cta-block {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  gap: 0;
  box-sizing: border-box;
  width: 100%;
  max-width: min(92vw, 560px);
  height: var(--hero-cta-stack-height, 96px);
  min-height: var(--hero-cta-stack-height, 96px);
  max-height: var(--hero-cta-stack-height, 96px);
  margin: 0;
  padding: 0;
  pointer-events: none;
}

.index-page-shop-bottom-sections__hero-intro-address-row {
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  height: var(--hero-cta-address-row-height, 49px);
  min-height: var(--hero-cta-address-row-height, 49px);
  max-height: var(--hero-cta-address-row-height, 49px);
  margin: 0;
  padding: 0;
  flex-shrink: 0;
}

.index-page-shop-bottom-sections__hero-intro-btn-row {
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  height: var(--hero-cta-button-row-height, 47px);
  min-height: var(--hero-cta-button-row-height, 47px);
  max-height: var(--hero-cta-button-row-height, 47px);
  margin: 0;
  padding: 0;
  flex-shrink: 0;
}

.index-page-shop-bottom-sections__hero-intro-address {
  margin: 0;
  padding: 0 8px;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.41rem, 1.175vw, 0.54rem);
  font-weight: 500;
  line-height: 1.25;
  letter-spacing: 0.04em;
  text-align: center;
  color: #ffffff;
  text-transform: none;
  text-shadow: var(--index-address-above-cta-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  width: 100%;
  min-width: 0;
  height: 100%;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transform: translateY(0);
  transition: font-size 0.45s cubic-bezier(0.16, 1, 0.3, 1), line-height 0.45s cubic-bezier(0.16, 1, 0.3, 1);
}

.index-page-shop-bottom-sections__hero-intro-address--visible {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

@media (max-width: 768px) {
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated {
    padding: 0 12px;
  }

  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated) {
    padding-left: 12px;
    padding-right: 12px;
  }

  .index-page-shop-bottom-sections__hero-intro-btn-row {
    height: auto;
    min-height: var(--hero-cta-button-row-height, 47px);
    max-height: none;
  }
}

/* Nawiguj on shop hero */
.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay {
  position: relative;
  pointer-events: none;
  opacity: 0;
  visibility: hidden;
  transform: translateY(0) scale(1);
  transition:
    padding 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    gap 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: inherit;
  padding: 2px;
  background: linear-gradient(
    135deg,
    /* Smooth transitions between fixed segments */
    #42a5f5 0%,
    #42a5f5 calc(15% - var(--border-blend, 3%)),
    #1e88e5 calc(15% + var(--border-blend, 3%)),
    #1e88e5 calc(30% - var(--border-blend, 3%)),
    #f44336 calc(30% + var(--border-blend, 3%)),
    #f44336 calc(50% - var(--border-blend, 3%)),
    #ffc107 calc(50% + var(--border-blend, 3%)),
    #ffc107 calc(70% - var(--border-blend, 3%)),
    #4caf50 calc(70% + var(--border-blend, 3%)),
    #4caf50 100%
  );
  background-size: 200% 200%;
  background-position: 0% 50%;
  mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  pointer-events: none;
  opacity: 0.98;
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible::before {
  animation: index-page-maps-cta-border-shift var(--mapsCtaBorderShiftDuration, 14s) linear infinite;
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay:not(.index-page-shop-bottom-sections__facebook-cta-btn--visible) {
  transition:
    transform 0.26s cubic-bezier(0.42, 0, 0.58, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  pointer-events: auto;
  opacity: 1;
  visibility: visible;
  transform: translateY(0) scale(1);
}

.index-page-shop-bottom-sections__facebook-cta-btn.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible:hover {
  --mapsCtaBorderShiftDuration: 3s;
}

@keyframes index-page-maps-cta-border-shift {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 200% 50%;
  }
}

@media (prefers-reduced-motion: reduce) {
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay {
    opacity: 1;
    transform: none;
    transition: none;
  }

  .index-page-shop-bottom-sections__facebook-cta-btn.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay {
    transition: none;
  }

  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay::before {
    animation: none !important;
    background-position: 0% 50%;
  }
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  text-align: center;
  transform: translateY(-2px);
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay .index-page-shop-bottom-sections__facebook-cta-btn-label {
  letter-spacing: 0.08em;
  font-size: clamp(0.525rem, 1.375vw, 0.61rem);
  font-weight: 600;
  transition: none;
}

.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay .index-page-shop-bottom-sections__facebook-cta-btn-subline {
  font-size: clamp(0.26rem, 0.825vw, 0.31rem);
  font-weight: 500;
  letter-spacing: 0.06em;
  color: rgba(255, 255, 255, 0.72);
  white-space: nowrap;
  line-height: 1.05;
  transition: none;
}

/*
 * Static hero Maps CTA: on first reveal, match the fixed bar’s size (mobile / desktop),
 * then grow to the normal hero button. Keyframe 0% mirrors the small-fixed fixed-bar button styling.
 */
@keyframes index-page-maps-cta-reveal-link-not-floated-mobile {
  0% {
    padding: 11px 19px 11px 23px;
    gap: 17px;
    border-radius: 13px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.1),
      0 14px 38px rgba(0, 0, 0, 0.52);
  }

  100% {
    padding: 12px 22px 12px 30px;
    gap: 20px;
    border-radius: 12px;
    transform: translateY(0) scale(1.07);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.08),
      0 10px 32px rgba(0, 0, 0, 0.45);
  }
}

@keyframes index-page-maps-cta-reveal-link-not-floated-desktop {
  0% {
    padding: 14px 26px 14px 30px;
    gap: 22px;
    border-radius: 15px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.11),
      0 18px 46px rgba(0, 0, 0, 0.52);
  }

  100% {
    padding: 12px 22px 12px 30px;
    gap: 20px;
    border-radius: 12px;
    transform: translateY(0) scale(1.07);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.08),
      0 10px 32px rgba(0, 0, 0, 0.45);
  }
}

@keyframes index-page-maps-cta-reveal-link-floated-mobile {
  0% {
    padding: 11px 19px 11px 23px;
    gap: 17px;
    border-radius: 13px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.1),
      0 14px 38px rgba(0, 0, 0, 0.52);
  }

  100% {
    padding: 12px 22px 12px 30px;
    gap: 20px;
    border-radius: 12px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.08),
      0 10px 32px rgba(0, 0, 0, 0.45);
  }
}

@keyframes index-page-maps-cta-reveal-link-floated-desktop {
  0% {
    padding: 14px 26px 14px 30px;
    gap: 22px;
    border-radius: 15px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.11),
      0 18px 46px rgba(0, 0, 0, 0.52);
  }

  100% {
    padding: 12px 22px 12px 30px;
    gap: 20px;
    border-radius: 12px;
    transform: translateY(0) scale(1);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.08),
      0 10px 32px rgba(0, 0, 0, 0.45);
  }
}

@keyframes index-page-maps-cta-reveal-label-mobile {
  0% {
    font-size: clamp(0.71rem, 3.35vw, 0.89rem);
  }

  100% {
    font-size: clamp(1.05rem, 2.75vw, 1.22rem);
  }
}

@keyframes index-page-maps-cta-reveal-label-desktop {
  0% {
    font-size: clamp(0.92rem, 1.65vw, 1.14rem);
  }

  100% {
    font-size: clamp(1.05rem, 2.75vw, 1.22rem);
  }
}

@keyframes index-page-maps-cta-reveal-sub-mobile {
  0% {
    font-size: clamp(0.34rem, 1.85vw, 0.43rem);
  }

  100% {
    font-size: clamp(0.52rem, 1.65vw, 0.62rem);
  }
}

@keyframes index-page-maps-cta-reveal-sub-desktop {
  0% {
    font-size: clamp(0.46rem, 0.95vw, 0.56rem);
  }

  100% {
    font-size: clamp(0.52rem, 1.65vw, 0.62rem);
  }
}

@keyframes index-page-maps-cta-reveal-textstack-mobile {
  0% {
    gap: 3px;
    transform: translateY(-1px);
  }

  100% {
    gap: 2px;
    transform: translateY(-2px);
  }
}

@keyframes index-page-maps-cta-reveal-textstack-desktop {
  0% {
    gap: 4px;
    transform: translateY(-1px);
  }

  100% {
    gap: 2px;
    transform: translateY(-2px);
  }
}

@keyframes index-page-maps-cta-reveal-pin-desktop {
  0% {
    width: 30px;
  }

  100% {
    width: 26px;
  }
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  animation: index-page-maps-cta-reveal-link-not-floated-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
  animation: index-page-maps-cta-reveal-textstack-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-label {
  animation: index-page-maps-cta-reveal-label-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-subline {
  animation: index-page-maps-cta-reveal-sub-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
  animation: index-page-maps-cta-reveal-link-floated-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
  animation: index-page-maps-cta-reveal-textstack-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-label {
  animation: index-page-maps-cta-reveal-label-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
  .index-page-shop-bottom-sections__facebook-cta-btn-subline {
  animation: index-page-maps-cta-reveal-sub-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

@media (min-width: 750px) {
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
    animation-name: index-page-maps-cta-reveal-link-not-floated-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
    animation-name: index-page-maps-cta-reveal-textstack-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-label {
    animation-name: index-page-maps-cta-reveal-label-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-subline {
    animation-name: index-page-maps-cta-reveal-sub-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-maps-pin {
    animation: index-page-maps-cta-reveal-pin-desktop 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible {
    animation-name: index-page-maps-cta-reveal-link-floated-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
    animation-name: index-page-maps-cta-reveal-textstack-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-label {
    animation-name: index-page-maps-cta-reveal-label-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-subline {
    animation-name: index-page-maps-cta-reveal-sub-desktop;
  }

  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-maps-pin {
    animation: index-page-maps-cta-reveal-pin-desktop 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
  }
}

@media (prefers-reduced-motion: reduce) {
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible,
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-textstack,
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-label,
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-subline,
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop
    .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible
    .index-page-shop-bottom-sections__facebook-cta-btn-maps-pin {
    animation: none !important;
  }
}

/* When the shop hero section is in viewport, enlarge typography back to previous sizes. */
.index-page-shop-bottom-sections__hero-intro--inview .index-page-shop-bottom-sections__hero-intro-address {
  font-size: clamp(0.82rem, 2.35vw, 1.08rem);
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  font-size: clamp(calc(0.68rem + 2px), calc(2.2vw + 2px), calc(0.98rem + 2px));
  transition:
    font-size 1s cubic-bezier(0.16, 1, 0.3, 1),
    line-height 1s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated:not(
      .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(calc(0.56rem + 2px), calc(2.65vw + 2px), calc(0.82rem + 2px));
  }
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  font-size: clamp(0.72rem, 5.25vw, 3.05rem);
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-float-pop
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(0.65rem, 5.8vw, 2.16rem);
  }
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
  font-size: clamp(0.72rem, 5.25vw, 3.05rem);
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last:not(.index-page-shop-bottom-sections__hero-intro-address--visible) {
  font-size: clamp(calc(0.82rem + 2px), calc(2.35vw + 2px), calc(1.08rem + 2px));
}

@media (max-width: 749px) {
  .index-page-shop-bottom-sections__hero-intro--inview
    .index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
    .index-page-shop-bottom-sections__hero-intro-address--facebook-shop-last.index-page-shop-bottom-sections__hero-intro-address--visible {
    font-size: clamp(0.65rem, 5.8vw, 2.16rem);
  }
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay
  .index-page-shop-bottom-sections__facebook-cta-btn-label {
  font-size: clamp(1.05rem, 2.75vw, 1.22rem);
}

.index-page-shop-bottom-sections__hero-intro--inview
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay
  .index-page-shop-bottom-sections__facebook-cta-btn-subline {
  font-size: clamp(0.52rem, 1.65vw, 0.62rem);
}

.index-page-shop-bottom-sections__facebook-cta-btn-maps-pin {
  flex-shrink: 0;
  display: block;
  width: 26px;
  height: auto;
  object-fit: contain;
  object-position: center;
}

.index-page-shop-bottom-sections__facebook-cta-btn-maps-pin--small {
  width: 26px;
}

.index-page-shop-bottom-sections__facebook-shop-follow {
  position: relative;
  z-index: 1650;
  width: 100%;
  margin-top: -10px;
  flex: 0 0 auto;
  padding: clamp(20px, 4vw, 40px) 16px clamp(20px, 4vw, 28px);
  box-sizing: border-box;
  background: rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 34px rgba(0, 0, 0, 0.35);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: clamp(16px, 3vw, 28px);
  padding-top: 11vh;
}

.index-page-shop-bottom-sections__facebook-shop-follow-text {
  margin: 0;
  margin-top: 0;
  max-width: min(520px, 92vw);
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.85rem, 2.2vw, 1rem);
  font-weight: 500;
  line-height: 1.35;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #ffffff;
}

.index-page-shop-bottom-sections__facebook-cta-btn {
  gap: 8px;
  border-radius: 12px;
  border: 0;
}

.index-page-shop-bottom-sections__facebook-cta-btn.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay {
  gap: 20px;
  padding: 12px 22px 12px 30px;
  align-items: center;
  transition:
    background 0.22s ease,
    border-color 0.22s ease,
    transform 0.18s ease,
    box-shadow 0.22s ease,
    padding 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    gap 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

.index-page-shop-bottom-sections__facebook-cta-btn.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(0.97);
}

.index-page-shop-bottom-sections__facebook-cta-btn.index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible:active {
  transform: translateY(0) scale(0.95);
}

/*
 * Fixed Maps CTA address: keep final typography unchanged, animate only `transform: scale()`
 * on an inner node to avoid 1-frame layout jumps from font metric changes.
 */
@keyframes index-page-fixed-maps-address-scale-mobile {
  0% {
    transform: scale(2.85);
  }

  100% {
    transform: scale(1);
  }
}

@keyframes index-page-fixed-maps-address-scale-desktop {
  0% {
    transform: scale(3.05);
  }

  100% {
    transform: scale(1);
  }
}

/* Small fixed Maps CTA: address + compact button */
.index-page-shop-bottom-sections__small-fixed-maps-cta {
  position: fixed;
  left: 50%;
  bottom: max(12px, env(safe-area-inset-bottom, 0px));
  transform: translateX(-50%);
  z-index: 1580;
  width: min(94vw, 480px);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  pointer-events: none;
}

.index-page-shop-bottom-sections__small-fixed-maps-address {
  margin: 0;
  padding: 0 10px;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.53rem, 2.55vw, 0.76rem);
  font-weight: 600;
  line-height: 1.15;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #ffffff;
  text-shadow:
    0 1px 2px rgba(0, 0, 0, 0.85),
    0 2px 12px rgba(0, 0, 0, 0.5);
  pointer-events: none;
}

.index-page-shop-bottom-sections__small-fixed-maps-address-text {
  display: inline-block;
  transform-origin: bottom center;
  animation: index-page-fixed-maps-address-scale-mobile 0.45s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.index-page-shop-bottom-sections__facebook-cta-btn--small-fixed {
  pointer-events: auto;
  padding: 11px 19px 11px 23px;
  gap: 17px;
  border-radius: 13px;
  background: rgba(14, 14, 18, 0.78);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.1),
    0 14px 38px rgba(0, 0, 0, 0.52);
}

.index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  text-align: center;
  transform: translateY(-1px);
}

.index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-label {
  font-size: clamp(0.71rem, 3.35vw, 0.89rem);
  letter-spacing: 0.08em;
  font-weight: 600;
}

.index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-subline {
  font-size: clamp(0.34rem, 1.85vw, 0.43rem);
  font-weight: 500;
  letter-spacing: 0.06em;
  color: rgba(255, 255, 255, 0.72);
  white-space: nowrap;
  line-height: 1.05;
}

@media (min-width: 750px) {
  .index-page-shop-bottom-sections__small-fixed-maps-cta {
    width: min(92vw, 560px);
    gap: 10px;
    bottom: max(18px, env(safe-area-inset-bottom, 0px));
  }

  .index-page-shop-bottom-sections__small-fixed-maps-address {
    padding: 0 12px;
    font-size: clamp(0.72rem, 1.35vw, 1rem);
    line-height: 1.18;
  }

  .index-page-shop-bottom-sections__small-fixed-maps-address-text {
    animation-name: index-page-fixed-maps-address-scale-desktop;
  }

  .index-page-shop-bottom-sections__facebook-cta-btn--small-fixed {
    padding: 14px 26px 14px 30px;
    gap: 22px;
    border-radius: 15px;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.11),
      0 18px 46px rgba(0, 0, 0, 0.52);
  }

  .index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-textstack {
    gap: 4px;
  }

  .index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-label {
    font-size: clamp(0.92rem, 1.65vw, 1.14rem);
  }

  .index-page-shop-bottom-sections__facebook-cta-btn--small-fixed .index-page-shop-bottom-sections__facebook-cta-btn-subline {
    font-size: clamp(0.46rem, 0.95vw, 0.56rem);
  }

  .index-page-shop-bottom-sections__facebook-cta-btn-maps-pin--small {
    width: 30px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .index-page-shop-bottom-sections__small-fixed-maps-address-text {
    animation: none !important;
  }
}

/* “Przejdź na Facebook” above shop hero */
.index-page-shop-bottom-sections__facebook-cta-btn--below {
  position: relative;
  overflow: hidden;
  gap: 15px;
  color: #ffffff;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.28),
    0 10px 34px rgba(0, 176, 255, 0.42),
    0 12px 40px rgba(0, 0, 0, 0.42);
  animation: index-page-facebook-below-shine-glow 6s ease-in-out infinite;
}

@keyframes index-page-facebook-below-shine-glow {
  0%,
  52%,
  100% {
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.28),
      0 10px 34px rgba(24, 119, 242, 0.38),
      0 0 14px rgba(140, 200, 255, 0.2),
      0 12px 40px rgba(0, 0, 0, 0.42);
  }
  26% {
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.38),
      0 12px 42px rgba(24, 119, 242, 0.52),
      0 0 28px rgba(180, 230, 255, 0.45),
      0 12px 40px rgba(0, 0, 0, 0.38);
  }
}

@media (prefers-reduced-motion: reduce) {
  .index-page-shop-bottom-sections__facebook-cta-btn--below {
    animation: none;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.28),
      0 10px 34px rgba(24, 119, 242, 0.38),
      0 12px 40px rgba(0, 0, 0, 0.42);
  }
}

.index-page-shop-bottom-sections__facebook-cta-btn:not(.index-page-shop-bottom-sections__facebook-cta-btn--below):not(.index-page-shop-bottom-sections__quick-info-fb):hover {
  background: rgba(26, 26, 32, 0.88);
  border-color: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.12),
    0 14px 36px rgba(0, 0, 0, 0.5);
}

/* hover handled by shared global selector */

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop:not(.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated)
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(1.03);
}

.index-page-shop-bottom-sections__hero-intro-sticky-cta--facebook-shop.index-page-shop-bottom-sections__hero-intro-sticky-cta--floated
  .index-page-shop-bottom-sections__facebook-cta-btn--hero-overlay.index-page-shop-bottom-sections__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(0.97);
}

.index-page-shop-bottom-sections__facebook-cta-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.65);
  outline-offset: 3px;
}

.index-page-shop-bottom-sections__facebook-cta-btn-icon {
  flex-shrink: 0;
  opacity: 0.95;
}

.index-page-shop-bottom-sections__google-reviews-slot {
  flex: 0 0 auto;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  box-sizing: border-box;
  padding-bottom: 5vh;
}

.index-page-shop-bottom-sections__google-reviews {
  flex: 0 0 auto;
  width: 100%;
}
</style>
