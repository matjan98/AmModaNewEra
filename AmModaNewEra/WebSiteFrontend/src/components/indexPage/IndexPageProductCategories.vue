<template>
  <section ref="rootRef" class="index-page-product-categories" aria-label="Kategorie produktów">
    <div class="index-page-product-categories__grid">
      <div
        v-for="(row, rowIndex) in rows"
        :key="'section-two-row-' + rowIndex"
        class="index-page-product-categories__row index-page__reveal-media"
      >
        <article
          v-for="item in row"
          :key="item.name"
          class="index-page-product-categories__card"
          :class="{ 'index-page-product-categories__card--active': activeName === item.name }"
          @click="emit('toggle', item.name)"
        >
          <div class="index-page-product-categories__photo-wrap">
            <img
              :src="item.photo"
              :alt="item.name"
              class="index-page-product-categories__img index-page__reveal-media-img"
              loading="lazy"
              @load="emit('imageLoad')"
            >
            <div class="index-page-product-categories__overlay" aria-hidden="true">
              <div class="index-page-product-categories__overlay-inner">
                <div class="index-page-product-categories__overlay-title">
                  Sprawdź aktualności:
                </div>
                <a
                  :href="facebookUrl"
                  target="_blank"
                  rel="noopener"
                  class="index-page-product-categories__facebook-cta-btn index-page-product-categories__facebook-cta-btn--below index-page-product-categories__facebook-cta-btn--section-two-overlay am-shine-glow"
                  :class="{ 'am-shine-glow--paused': !shineActive }"
                  aria-label="Otwórz Facebook AM Moda Damska w nowej karcie"
                  @click.stop
                >
                  Przejdź na Facebook
                  <q-icon
                    name="fa-brands fa-facebook"
                    size="18px"
                    class="index-page-product-categories__facebook-cta-btn-icon index-page-product-categories__facebook-cta-btn-icon--external"
                    aria-hidden="true"
                  />
                </a>
              </div>
            </div>
          </div>
          <div class="index-page-product-categories__caption">
            {{ item.name }}
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useIntersectionObserver } from '../../composables/useIntersectionObserver.js'

defineProps({
  rows: {
    type: Array,
    required: true,
  },
  activeName: {
    type: String,
    default: null,
  },
  facebookUrl: {
    type: String,
    required: true,
  },
})

const emit = defineEmits(['toggle', 'imageLoad'])

const rootRef = ref(null)
const shineActive = ref(false)

useIntersectionObserver(
  (entry) => {
    shineActive.value = Boolean(entry?.isIntersecting)
  },
  { threshold: 0 },
  rootRef,
)
</script>

<style scoped>
.index-page-product-categories {
  margin-top: 0;
  margin-bottom: 0;
  background: transparent;
  overflow: visible;
}

.index-page-product-categories__grid {
  display: flex;
  flex-direction: column;
  gap: 0;
  width: 100%;
}

.index-page-product-categories__row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0;
  width: 100%;
  min-width: 0;
}

.index-page-product-categories__card {
  min-width: 0;
  background: transparent;
  cursor: pointer;
}

.index-page-product-categories__photo-wrap {
  width: 100%;
  position: relative;
  overflow: hidden;
}

.index-page-product-categories__photo-wrap::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.44);
  opacity: 0;
  transition: opacity 0.22s ease;
  pointer-events: none;
  z-index: 1;
}

.index-page-product-categories__overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: clamp(12px, 2.2vw, 18px);
  box-sizing: border-box;
  opacity: 0;
  transform: translateY(6px);
  transition:
    opacity 0.22s ease,
    transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
  z-index: 2;
  pointer-events: none;
}

.index-page-product-categories__overlay-inner {
  width: 100%;
  max-width: min(320px, 92%);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 12px;
}

.index-page-product-categories__overlay-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.82rem, 1.65vw, 1.05rem);
  font-weight: 500;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.96);
  text-shadow: 0 10px 26px rgba(0, 0, 0, 0.55);
}

.index-page-product-categories__facebook-cta-btn--section-two-overlay {
  pointer-events: none;
  padding: 10px 16px;
  font-size: 0.92rem;
  font-weight: 600;
  border-radius: 12px;
  gap: 10px;
}

@media (hover: hover) and (pointer: fine) {
  .index-page-product-categories__card:hover .index-page-product-categories__photo-wrap::before {
    opacity: 1;
  }

  .index-page-product-categories__card:hover .index-page-product-categories__overlay {
    opacity: 1;
    transform: translateY(0);
  }

  .index-page-product-categories__card:hover .index-page-product-categories__facebook-cta-btn--section-two-overlay {
    pointer-events: auto;
  }
}

.index-page-product-categories__card--active .index-page-product-categories__photo-wrap::before {
  opacity: 1;
}

.index-page-product-categories__card--active .index-page-product-categories__overlay {
  opacity: 1;
  transform: translateY(0);
}

.index-page-product-categories__card--active .index-page-product-categories__facebook-cta-btn--section-two-overlay {
  pointer-events: auto;
}

.index-page-product-categories__img {
  width: 100%;
  aspect-ratio: 3 / 4;
  object-fit: cover;
  display: block;
}

.index-page-product-categories__card:hover .index-page-product-categories__img {
  transition: transform 0.2s ease;
  transform: scale(1.02);
}

.index-page-product-categories__caption {
  min-height: 28px;
  padding: 12px 12px 18px;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(10px) saturate(1.1);
  -webkit-backdrop-filter: blur(10px) saturate(1.1);
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  color: #ffffff;
  text-align: center;
  font-size: clamp(0.95rem, 1.35vw, 1.15rem);
  font-weight: 400;
  line-height: 1.2;
  letter-spacing: 0.01em;
  text-transform: none;
}

@media (max-width: 749.98px) {
  .index-page-product-categories__row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.index-page-product-categories__facebook-cta-btn--below {
  position: relative;
  overflow: hidden;
  gap: 10px;
  color: #ffffff;
}

.index-page-product-categories__facebook-cta-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.65);
  outline-offset: 3px;
}

.index-page-product-categories__facebook-cta-btn-icon {
  flex-shrink: 0;
  opacity: 0.95;
}
</style>

