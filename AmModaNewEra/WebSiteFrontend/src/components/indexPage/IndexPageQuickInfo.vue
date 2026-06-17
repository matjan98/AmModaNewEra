<template>
  <section
    class="index-page-quick-info index-page-quick-info__root"
    :aria-label="`${storeHoursHeadingLabel}, kontakt i skróty`"
  >
    <div class="index-page-quick-info__inner">
      <div
        v-if="homepageNewsSectionEnabled"
        class="index-page-quick-info__news-block"
        aria-label="Aktualności sklepu"
      >
        <h3 class="index-page-quick-info__hours-heading index-page-quick-info__news-heading">
          Aktualności:
        </h3>
        <p class="index-page-quick-info__news-text">
          {{ homepageNewsSectionText }}
        </p>
      </div>

      <div
        class="index-page-quick-info__contact-block"
        :class="{ 'index-page-quick-info__contact-block--with-top-divider': homepageNewsSectionEnabled }"
      >
        <div class="index-page-quick-info__row">
          <span class="index-page-quick-info__label">Kontakt:</span>
          <a
            :href="phoneTelHref"
            class="index-page-quick-info__value index-page-quick-info__link"
          >
            {{ phoneDisplay }}
          </a>
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

      <div class="index-page-quick-info__hours-block">
        <h3 class="index-page-quick-info__hours-heading">
          {{ storeHoursHeadingLabel }}
        </h3>
        <ul class="index-page-quick-info__hours-list">
          <li
            v-for="row in openingHours"
            :key="row.dayIndex"
            class="index-page-quick-info__hours-row"
            :class="{ 'index-page-quick-info__hours-row--today': row.dayIndex === todayStoreDayIndex }"
          >
            <span class="index-page-quick-info__hours-day">{{ row.label }}</span>
            <span class="index-page-quick-info__hours-time">{{ row.hours }}</span>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script setup>
import {
  HOMEPAGE_NEWS_SECTION_ENABLED,
  HOMEPAGE_NEWS_SECTION_TEXT,
} from '../../constants/siteInfo.js'

const homepageNewsSectionEnabled = Boolean(HOMEPAGE_NEWS_SECTION_ENABLED)
const homepageNewsSectionText = HOMEPAGE_NEWS_SECTION_TEXT

defineProps({
  phoneTelHref: {
    type: String,
    required: true,
  },
  phoneDisplay: {
    type: String,
    required: true,
  },
  facebookUrl: {
    type: String,
    required: true,
  },
  openingHours: {
    type: Array,
    required: true,
  },
  todayStoreDayIndex: {
    type: Number,
    required: true,
  },
  storeHoursHeadingLabel: {
    type: String,
    required: true,
  },
})
</script>

<style scoped>
.index-page-quick-info__root {
  display: block;
  position: relative;
  left: 50%;
  width: 100dvw;
  margin: 0;
  margin-left: -50dvw;
  margin-top: 0;
  padding: 20px 16px clamp(18px, 3vw, 24px);
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
}

.index-page-quick-info__inner {
  min-width: 270px;
  max-width: min(520px, 100%);
  width: 100%;
  margin-inline: auto;
  display: flex;
  flex-direction: column;
  gap: clamp(16px, 3.5vw, 22px);
  box-sizing: border-box;
}

.index-page-quick-info__contact-block {
  display: flex;
  flex-direction: column;
  gap: 10px;
  position: relative;
  padding: clamp(28px, 6vw, 36px) 0;
}

.index-page-quick-info__contact-block::after {
  content: '';
  position: absolute;
  left: calc(50% - 50vw + 5vw);
  width: calc(100vw - 10vw);
  height: 1px;
  background: rgba(255, 255, 255, 0.2);
  bottom: 0;
}

.index-page-quick-info__contact-block--with-top-divider::before {
  content: '';
  position: absolute;
  left: calc(50% - 50vw + 5vw);
  width: calc(100vw - 10vw);
  height: 1px;
  background: rgba(255, 255, 255, 0.2);
  top: 0;
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

.index-page-quick-info__news-block {
  text-align: center;
}

.index-page-quick-info__news-heading {
  margin-bottom: clamp(8px, 1.65vw, 12px);
}

.index-page-quick-info__news-text {
  margin: 0 auto;
  max-width: min(420px, 100%);
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.88rem, 2.4vw, 1rem);
  font-weight: 400;
  line-height: 1.45;
  color: #ffffff;
  text-align: center;
}

.index-page-quick-info__hours-block {
  text-align: center;
}

.index-page-quick-info__hours-heading {
  margin: 0 0 clamp(8px, 1.65vw, 12px);
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.85rem, 2.2vw, 1rem);
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #ffffff;
}

.index-page-quick-info__hours-list {
  list-style: none;
  margin: 0 auto;
  padding: 0;
  width: 100%;
  max-width: min(420px, 100%);
  text-align: left;
}

.index-page-quick-info__hours-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: clamp(36px, 12vw, 72px);
  box-sizing: border-box;
  padding: 6px 10px;
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.88rem, 2.4vw, 1rem);
  color: #ffffff;
  border-radius: 8px;
}

.index-page-quick-info__hours-row--today {
  background: rgba(255, 105, 180, 0.14);
  border: 1px solid rgba(255, 170, 210, 0.5);
  box-shadow:
    inset 0 0 0 1px rgba(255, 170, 210, 0.5),
    0 1px 12px rgba(255, 120, 180, 0.12);
}

.index-page-quick-info__hours-row:not(.index-page-quick-info__hours-row--today) .index-page-quick-info__hours-day,
.index-page-quick-info__hours-row:not(.index-page-quick-info__hours-row--today) .index-page-quick-info__hours-time {
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.55);
}

.index-page-quick-info__hours-day {
  text-transform: capitalize;
  font-weight: 400;
}

.index-page-quick-info__hours-time {
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
  font-weight: 600;
  color: #ffffff;
}

@media (max-width: 999.98px) {
  .index-page-quick-info__root {
    padding: 20px 18px 22px;
  }

  .index-page-quick-info__hours-heading {
    margin-bottom: 10px;
    font-size: 1.15rem;
    letter-spacing: 0.1em;
  }

  .index-page-quick-info__news-text {
    font-size: 1.08rem;
  }

  .index-page-quick-info__hours-row {
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 1.08rem;
  }

  .index-page-quick-info__hours-row:not(.index-page-quick-info__hours-row--today) {
    padding-top: 6px;
    padding-bottom: 6px;
  }
}

@media (min-width: 1000px) {
  .index-page-quick-info__root {
    padding: 28px 24px 32px;
  }

  .index-page-quick-info__inner {
    max-width: min(640px, 100%);
    gap: 26px;
  }

  .index-page-quick-info__contact-block {
    gap: 14px;
  }

  .index-page-quick-info__row {
    min-height: 36px;
    gap: 12px;
    font-size: 1.375rem;
  }

  .index-page-quick-info__facebook-btn {
    gap: 16px;
    padding: 8px 14px;
    font-size: 1.125rem;
  }

  .index-page-quick-info__facebook-icon :deep(.q-icon) {
    font-size: 22px !important;
  }

  .index-page-quick-info__hours-heading {
    margin-bottom: 16px;
    font-size: 1.4rem;
    letter-spacing: 0.1em;
  }

  .index-page-quick-info__news-text {
    max-width: min(520px, 100%);
    font-size: 1.3rem;
  }

  .index-page-quick-info__hours-list {
    max-width: min(520px, 100%);
  }

  .index-page-quick-info__hours-row {
    gap: 48px;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 1.3rem;
  }
}
</style>
