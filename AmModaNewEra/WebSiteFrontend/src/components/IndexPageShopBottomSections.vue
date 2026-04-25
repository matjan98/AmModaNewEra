<template>
  <div class="index-page-shop-bottom">
    <!-- 1) Facebook CTA above shop block -->
    <section class="index-page__facebook-shop-follow" aria-label="Facebook AM Moda Damska">
      <p class="index-page__facebook-shop-follow-text">
        Zobacz nasze najnowsze kolekcje na Facebooku:
      </p>
      <a
        :href="facebookUrl"
        target="_blank"
        rel="noopener"
        class="index-page__facebook-cta-btn index-page__facebook-cta-btn--below"
        aria-label="Otwórz profil AM Moda Damska na Facebooku"
      >
        Przejdź na Facebook
        <q-icon
          name="fa-brands fa-facebook"
          size="20px"
          class="index-page__facebook-cta-btn-icon index-page__facebook-cta-btn-icon--external"
          aria-hidden="true"
        />
      </a>
    </section>

    <!-- 2) Address + Google Maps (sticky / float, matches fixed shop photo) -->
    <section
      ref="heroIntroFacebookRef"
      class="index-page__hero-intro index-page__hero-intro--facebook-fixed-bg"
      :style="ctaCssVars"
      aria-label="Sklep — zdjęcie i nawigacja"
    >
      <div
        ref="heroIntroFacebookPhotoRef"
        class="index-page__hero-intro-photo index-page__hero-intro-photo--facebook-spacer"
        aria-hidden="true"
      />
      <div
        class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--facebook-shop"
        :class="{
          'index-page__hero-intro-sticky-cta--floated': heroIntroFacebookCtaFloated,
          'index-page__hero-intro-sticky-cta--facebook-float-pop': facebookShopFloatPop,
        }"
      >
        <div
          ref="heroIntroFacebookCtaRef"
          class="index-page__hero-intro-cta-block index-page__hero-intro-cta-block--facebook-shop"
        >
          <div class="index-page__hero-intro-address-row">
            <p
              class="index-page__hero-intro-address index-page__hero-intro-address--facebook-shop-last"
              :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
            >
              Kozy, ul. Bielska 166
            </p>
          </div>
          <div class="index-page__hero-intro-btn-row">
            <a
              :href="mapsUrl"
              target="_blank"
              rel="noopener"
              class="index-page__facebook-cta-btn index-page__facebook-cta-btn--hero-overlay"
              :class="{ 'index-page__facebook-cta-btn--visible': heroIntroCtaVisible }"
              aria-label="Otwórz nawigację do sklepu w Google Maps"
            >
              <span class="index-page__facebook-cta-btn-textstack">
                <span class="index-page__facebook-cta-btn-label">Nawiguj</span>
                <span class="index-page__facebook-cta-btn-subline">Google Maps</span>
              </span>
              <img
                :src="googleMapsPinImg"
                alt=""
                width="26"
                height="36"
                class="index-page__facebook-cta-btn-maps-pin"
                aria-hidden="true"
                decoding="async"
              />
            </a>
          </div>
        </div>
      </div>
    </section>

    <GoogleReviewsCard class="index-page__google-reviews" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import GoogleReviewsCard from './GoogleReviewsCard.vue'
import googleMapsPinImg from '../assets/google-maps.png'

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

const heroIntroFacebookRef = ref(null)
const heroIntroFacebookPhotoRef = ref(null)
const heroIntroFacebookCtaRef = ref(null)

defineExpose({
  heroIntroFacebookRef,
  heroIntroFacebookPhotoRef,
  heroIntroFacebookCtaRef,
})
</script>

<style scoped>
.index-page-shop-bottom {
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

.index-page__hero-intro--facebook-fixed-bg {
  background: transparent;
  flex: 1 1 0;
  min-height: 0;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.index-page__hero-intro-photo--facebook-spacer {
  width: 100%;
  flex: 1 1 auto;
  min-height: 0;
}

.index-page__hero-intro {
  position: relative;
  margin-top: 0;
  margin-bottom: 0;
  width: 100%;
  background: transparent;
  box-sizing: border-box;
  overflow: visible;
}

.index-page__hero-intro-sticky-cta {
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

.index-page__hero-intro-sticky-cta--floated {
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
.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-cta-block--facebook-shop {
  height: auto;
  min-height: 0;
  max-height: none;
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-address-row {
  height: auto;
  min-height: 0;
  max-height: none;
}

@media (min-width: 750px) {
  .index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated:not(
      .index-page__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page__hero-intro-cta-block--facebook-shop {
    gap: 10px;
  }
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated:not(
    .index-page__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(calc(0.68rem + 2px), calc(2.2vw + 2px), calc(0.98rem + 2px));
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
    line-height 0.26s cubic-bezier(0.4, 0, 0.2, 1),
    transform 0.26s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.26s ease;
}

@media (max-width: 749px) {
  .index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated:not(
      .index-page__hero-intro-sticky-cta--facebook-float-pop
    )
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    font-size: clamp(calc(0.56rem + 2px), calc(2.65vw + 2px), calc(0.82rem + 2px));
    line-height: 1.22;
  }
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated:not(
    .index-page__hero-intro-sticky-cta--facebook-float-pop
  )
  .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible {
  transform: translateY(0) scale(1);
  transition:
    opacity 0.26s ease,
    transform 0.26s cubic-bezier(0.4, 0, 0.2, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated.index-page__hero-intro-sticky-cta--facebook-float-pop
  .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(0.72rem, 5.25vw, 3.05rem);
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
  .index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated.index-page__hero-intro-sticky-cta--facebook-float-pop
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    font-size: clamp(0.65rem, 5.8vw, 2.16rem);
    line-height: 1.18;
    letter-spacing: 0.05em;
  }
}

@media (min-width: 750px) {
  .index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated.index-page__hero-intro-sticky-cta--facebook-float-pop
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    transform: translateY(-20px);
  }
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated.index-page__hero-intro-sticky-cta--facebook-float-pop
  .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible {
  transform: translateY(0) scale(1.07);
  transition: none;
}

/* Last hero (shop photo): dock address + Nawiguj at vertical center of the image when not floated; enlarge Nawiguj + address. */
.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated) {
  align-items: center;
  padding-top: max(8px, env(safe-area-inset-top, 0px));
  padding-bottom: max(22px, env(safe-area-inset-bottom, 0px));
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-cta-block--facebook-shop {
  height: auto;
  min-height: var(--hero-cta-stack-height, 96px);
  max-height: none;
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-address-row {
  height: auto;
  min-height: var(--hero-cta-address-row-height, 49px);
  max-height: none;
  padding-bottom: 0;
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
  white-space: nowrap;
  font-size: clamp(0.72rem, 5.25vw, 3.05rem);
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
    line-height 0.38s cubic-bezier(0.16, 1, 0.3, 1),
    opacity 0.38s ease 0.03s,
    transform 0.38s cubic-bezier(0.16, 1, 0.3, 1) 0.03s;
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-address--facebook-shop-last:not(.index-page__hero-intro-address--visible) {
  font-size: clamp(calc(0.82rem + 2px), calc(2.35vw + 2px), calc(1.08rem + 2px));
  transition:
    font-size 0.26s cubic-bezier(0.42, 0, 0.58, 1),
    line-height 0.26s cubic-bezier(0.42, 0, 0.58, 1),
    opacity 0.26s ease 0.02s,
    transform 0.26s cubic-bezier(0.42, 0, 0.58, 1) 0.02s;
}

@media (max-width: 749px) {
  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    font-size: clamp(0.65rem, 5.8vw, 2.16rem);
    line-height: 1.18;
    letter-spacing: 0.05em;
  }
}

@media (min-width: 750px) {
  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    transform: translateY(-20px);
  }
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible {
  transform: translateY(0) scale(1.07);
  transition:
    opacity 0.38s ease 0.03s,
    transform 0.38s cubic-bezier(0.16, 1, 0.3, 1) 0.03s,
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__hero-intro-address--facebook-shop-last.index-page__hero-intro-address--visible {
    transition: opacity 0.38s ease 0.03s, transform 0.38s cubic-bezier(0.16, 1, 0.3, 1) 0.03s;
  }

  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__hero-intro-address--facebook-shop-last:not(.index-page__hero-intro-address--visible) {
    transition: opacity 0.26s ease 0.02s, transform 0.26s cubic-bezier(0.16, 1, 0.3, 1) 0.02s;
  }

  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible {
    transform: translateY(0) scale(1);
    transition: none;
  }

  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
    .index-page__facebook-cta-btn--hero-overlay:not(.index-page__facebook-cta-btn--visible) {
    transition: opacity 0.26s ease, transform 0.26s ease;
  }
}

.index-page__hero-intro-cta-block {
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

.index-page__hero-intro-address-row {
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

.index-page__hero-intro-btn-row {
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

.index-page__hero-intro-address {
  margin: 0;
  padding: 0 8px;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.82rem, 2.35vw, 1.08rem);
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
  transform: translateY(8px);
  transition:
    opacity 0.45s ease 0.05s,
    transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
}

.index-page__hero-intro-address--visible {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 768px) {
  .index-page__hero-intro-sticky-cta--floated {
    padding: 0 12px;
  }

  .index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated) {
    padding-left: 12px;
    padding-right: 12px;
  }

  .index-page__hero-intro-btn-row {
    height: auto;
    min-height: var(--hero-cta-button-row-height, 47px);
    max-height: none;
  }
}

/* Nawiguj on shop hero */
.index-page__facebook-cta-btn--hero-overlay {
  pointer-events: auto;
  opacity: 0;
  transform: translateY(8px) scale(0.96);
  transition:
    opacity 0.4s ease 0.08s,
    transform 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.08s,
    padding 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    gap 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__facebook-cta-btn--hero-overlay:not(.index-page__facebook-cta-btn--visible) {
  transition:
    opacity 0.26s ease 0.02s,
    transform 0.26s cubic-bezier(0.42, 0, 0.58, 1) 0.02s,
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

.index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

@media (prefers-reduced-motion: reduce) {
  .index-page__facebook-cta-btn--hero-overlay {
    opacity: 1;
    transform: none;
    transition: none;
  }

  .index-page__facebook-cta-btn.index-page__facebook-cta-btn--hero-overlay {
    transition: none;
  }
}

.index-page__facebook-cta-btn--hero-overlay .index-page__facebook-cta-btn-textstack {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  text-align: center;
  transform: translateY(-2px);
}

.index-page__facebook-cta-btn--hero-overlay .index-page__facebook-cta-btn-label {
  letter-spacing: 0.08em;
  font-size: clamp(1.05rem, 2.75vw, 1.22rem);
  font-weight: 600;
}

.index-page__facebook-cta-btn--hero-overlay .index-page__facebook-cta-btn-subline {
  font-size: clamp(0.52rem, 1.65vw, 0.62rem);
  font-weight: 500;
  letter-spacing: 0.06em;
  color: rgba(255, 255, 255, 0.72);
  white-space: nowrap;
  line-height: 1.05;
}

.index-page__facebook-cta-btn-maps-pin {
  flex-shrink: 0;
  display: block;
  width: 26px;
  height: auto;
  object-fit: contain;
  object-position: center;
}

.index-page__facebook-shop-follow {
  position: relative;
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
  padding-top: 10vh;
}

.index-page__facebook-shop-follow-text {
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

.index-page__facebook-cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 22px;
  text-decoration: none;
  color: rgba(255, 255, 255, 0.96);
  font-weight: 600;
  font-size: 1rem;
  background: rgba(14, 14, 18, 0.72);
  backdrop-filter: blur(14px) saturate(1.15);
  -webkit-backdrop-filter: blur(14px) saturate(1.15);
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 10px 32px rgba(0, 0, 0, 0.45);
  transition:
    background 0.22s ease,
    border-color 0.22s ease,
    transform 0.18s ease,
    box-shadow 0.22s ease;
}

.index-page__facebook-cta-btn.index-page__facebook-cta-btn--hero-overlay {
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

.index-page__facebook-cta-btn.index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(0.97);
}

.index-page__facebook-cta-btn.index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible:active {
  transform: translateY(0) scale(0.95);
}

/* “Przejdź na Facebook” above shop hero */
.index-page__facebook-cta-btn--below {
  position: relative;
  overflow: hidden;
  gap: 15px;
  color: #ffffff;
  background: linear-gradient(
    145deg,
    rgba(46, 210, 255, 0.54) 0%,
    rgba(0, 176, 255, 0.44) 45%,
    rgba(0, 142, 235, 0.48) 100%
  );
  backdrop-filter: blur(18px) saturate(1.45);
  -webkit-backdrop-filter: blur(18px) saturate(1.45);
  border: 1px solid rgba(185, 245, 255, 0.6);
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
  .index-page__facebook-cta-btn--below {
    animation: none;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.28),
      0 10px 34px rgba(24, 119, 242, 0.38),
      0 12px 40px rgba(0, 0, 0, 0.42);
  }
}

.index-page__facebook-cta-btn:not(.index-page__facebook-cta-btn--below):not(.index-page__quick-info-fb):hover {
  background: rgba(26, 26, 32, 0.88);
  border-color: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.12),
    0 14px 36px rgba(0, 0, 0, 0.5);
}

.index-page__facebook-cta-btn--below:hover {
  background: linear-gradient(
    145deg,
    rgba(88, 230, 255, 0.58) 0%,
    rgba(28, 198, 255, 0.52) 45%,
    rgba(0, 160, 245, 0.58) 100%
  );
  border-color: rgba(210, 250, 255, 0.66);
  transform: translateY(-1px);
}

.index-page__hero-intro-sticky-cta--facebook-shop:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(1.03);
}

.index-page__hero-intro-sticky-cta--facebook-shop.index-page__hero-intro-sticky-cta--floated
  .index-page__facebook-cta-btn--hero-overlay.index-page__facebook-cta-btn--visible:hover {
  transform: translateY(0) scale(0.97);
}

.index-page__facebook-cta-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.65);
  outline-offset: 3px;
}

.index-page__facebook-cta-btn-icon {
  flex-shrink: 0;
  opacity: 0.95;
}

.index-page__google-reviews {
  flex: 0 0 auto;
  height: 5vh;
  min-height: 0;
  flex-shrink: 0;
}
</style>
