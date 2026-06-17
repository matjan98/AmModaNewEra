<template>
  <div class="index-page-shop-bottom-sections">
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
        class="index-page-shop-bottom-sections__facebook-cta-btn index-page-shop-bottom-sections__facebook-cta-btn--below am-shine-glow"
        :class="{ 'am-shine-glow--paused': !facebookShopFollowInView }"
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

    <div class="index-page-shop-bottom-sections__google-reviews-slot" aria-label="Ocena Google">
      <GoogleReviewsCard
        :with-margin="false"
        class="index-page-shop-bottom-sections__google-reviews"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import GoogleReviewsCard from './GoogleReviewsCard.vue'
import { useIntersectionObserver } from '../composables/useIntersectionObserver.js'

defineProps({
  facebookUrl: {
    type: String,
    default: 'https://www.facebook.com/AMModaDamska/',
  },
})

const facebookShopFollowRef = ref(null)
const facebookShopFollowInView = ref(false)

useIntersectionObserver(
  (entry) => {
    facebookShopFollowInView.value = Boolean(entry?.isIntersecting)
  },
  { threshold: 0 },
  facebookShopFollowRef,
)
</script>

<style scoped>
.index-page-shop-bottom-sections {
  box-sizing: border-box;
  position: relative;
  left: 50%;
  width: 100vw;
  margin-left: -50vw;
  display: flex;
  flex-direction: column;
}

.index-page-shop-bottom-sections__facebook-shop-follow {
  position: relative;
  z-index: 1650;
  width: 100%;
  margin-top: 0;
  flex: 0 0 auto;
  padding: clamp(28px, 6vw, 56px) 16px clamp(24px, 5vw, 40px);
  box-sizing: border-box;
  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.08) 0%,
      rgba(255, 255, 255, 0.02) 38%,
      rgba(0, 0, 0, 0.06) 100%
    ),
    rgba(10, 10, 14, 0.9);
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
}

.index-page-shop-bottom-sections__facebook-shop-follow-text {
  margin: 0;
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

.index-page-shop-bottom-sections__facebook-cta-btn--below {
  position: relative;
  overflow: hidden;
  gap: 15px;
  color: #ffffff;
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
  padding: 5vh 0;
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}

.index-page-shop-bottom-sections__google-reviews {
  flex: 0 0 auto;
  width: 100%;
  height: auto;
  align-items: center;
}
</style>
