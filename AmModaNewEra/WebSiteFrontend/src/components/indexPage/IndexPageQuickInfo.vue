<template>
  <section class="index-page-quick-info index-page-quick-info__root" aria-label="Kontakt i skróty">
    <div class="index-page-quick-info__inner">
      <div class="index-page-quick-info__row">
        <span class="index-page-quick-info__label">Kontakt:</span>
        <a
          :href="phoneTelHref"
          class="index-page-quick-info__value index-page-quick-info__link"
        >
          {{ phoneDisplay }}
        </a>
      </div>
      <div class="index-page-quick-info__row">
        <span class="index-page-quick-info__label">Dziś:</span>
        <span class="index-page-quick-info__value">
          <span class="index-page-quick-info__today-pill">
            <template v-if="todayPillParts.isSplit">
              <span class="index-page-quick-info__today-prefix">{{ todayPillParts.prefix }}</span>
              <span class="index-page-quick-info__today-hours">{{ todayPillParts.hours }}</span>
            </template>
            <template v-else>{{ todayPillParts.text }}</template>
          </span>
        </span>
      </div>
      <div class="index-page-quick-info__row index-page-quick-info__row--gallery">
        <span class="index-page-quick-info__label">Aktualności:</span>
        <a
          :href="facebookUrl"
          target="_blank"
          rel="noopener"
          class="index-page-quick-info__facebook-btn"
          aria-label="Otwórz galerię na Facebooku AM Moda Damska w nowej karcie"
        >
          Facebook
          <q-icon
            name="fa-brands fa-facebook"
            size="18px"
            class="index-page-quick-info__facebook-icon"
            aria-hidden="true"
          />
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  phoneTelHref: {
    type: String,
    required: true,
  },
  phoneDisplay: {
    type: String,
    required: true,
  },
  quickInfoTodayLine: {
    type: String,
    required: true,
  },
  facebookUrl: {
    type: String,
    required: true,
  },
})

const todayPillParts = computed(() => {
  const line = (props.quickInfoTodayLine ?? '').trim()
  const match = line.match(/^(.+?!)(?:\s+)(\d{1,2}:\d{2}\s*-\s*\d{1,2}:\d{2})$/)
  if (!match) {
    return { isSplit: false, text: line }
  }
  return { isSplit: true, prefix: match[1], hours: match[2] }
})
</script>

<style scoped>
.index-page-quick-info__root {
  display: none;
  margin-top: 0;
  padding: 20px 16px;
  box-sizing: border-box;
  height: 150px;
  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.08) 0%,
      rgba(255, 255, 255, 0.02) 38%,
      rgba(0, 0, 0, 0.06) 100%
    ),
    rgba(10, 10, 14, 0.9);
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 18px 60px rgba(0, 0, 0, 0.38);
}

@media (max-width: 749.98px) {
  .index-page-quick-info__root {
    display: block;
  }
}

.index-page-quick-info__inner {
  min-width: 270px;
  max-width: min(320px, 100%);
  width: 100%;
  height: 100%;
  margin-inline: auto;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-sizing: border-box;
}

.index-page-quick-info__row {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  gap: 9px;
  width: 100%;
  min-height: 28px;
  font-family: 'Poppins', sans-serif;
  font-size: 17px;
  line-height: 1.35;
}

.index-page-quick-info__row--gallery {
  align-items: center;
}

.index-page-quick-info__label {
  flex: 0 0 auto;
  align-self: flex-center;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: rgba(255, 255, 255, 0.72);
}

.index-page-quick-info__value {
  flex: 1 1 0;
  min-width: 0;
  text-align: right;
  color: #ffffff;
  font-weight: 500;
}

.index-page-quick-info__today-pill {
  display: inline-flex;
  align-items: baseline;
  gap: 12px;
  padding: 5px 10px;
  max-width: 100%;
  box-sizing: border-box;
  vertical-align: baseline;
  font-size: 15px;
  font-weight: 600;
  font-variant-numeric: tabular-nums;
  line-height: 1.25;
  color: #ffffff;
  border-radius: 8px;
  background: rgba(255, 105, 180, 0.14);
  backdrop-filter: blur(12px) saturate(1.35);
  -webkit-backdrop-filter: blur(12px) saturate(1.35);
  box-shadow:
    inset 0 0 0 1px rgba(255, 170, 210, 0.5),
    0 1px 12px rgba(255, 120, 180, 0.12);
}

.index-page-quick-info__row > .index-page-quick-info__link {
  flex: 1 1 0;
  min-width: 0;
  text-align: right;
}

.index-page-quick-info__row--gallery > .index-page-quick-info__facebook-btn {
  flex: 0 0 auto;
  margin-left: auto;
}

.index-page-quick-info__link {
  text-decoration: none;
  color: #ffffff;
  font-weight: 600;
  transition: color 0.2s ease, opacity 0.2s ease;
}

.index-page-quick-info__link:hover {
  color: rgba(255, 255, 255, 0.92);
}

.index-page-quick-info__link:focus-visible {
  outline: none;
}

.index-page-quick-info__facebook-btn {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  padding: 5px 10px;
  font-size: clamp(0.92rem, 1.92vw, 0.86rem);
  font-weight: 500;
  line-height: 1.25;
  border-radius: 8px;
  border: 0;
  text-decoration: none;
}

.index-page-quick-info__facebook-icon {
  flex-shrink: 0;
  margin-top: 1px;
  opacity: 0.92;
}

/* hover handled by shared global selector */
</style>

