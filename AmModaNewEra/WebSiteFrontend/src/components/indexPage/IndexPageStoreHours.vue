<template>
  <section
    class="index-page-store-hours index-page-store-hours__banner"
    :class="{ 'index-page-store-hours__banner--intro-visible': introRevealed }"
    :aria-label="`${storeHoursHeadingLabel}, harmonogram tygodnia`"
    :aria-hidden="introRevealed ? undefined : true"
  >
    <h3 class="index-page-store-hours__heading index-page-store-hours__reveal-line">
      {{ storeHoursHeadingLabel }}
    </h3>
    <div class="index-page-store-hours__block">
      <ul
        id="index-store-hours-list"
        class="index-page-store-hours__list index-page-store-hours__reveal-line"
      >
        <li
          v-for="row in openingHours"
          :key="row.dayIndex"
          class="index-page-store-hours__row"
          :class="{
            'index-page-store-hours__row--today': row.dayIndex === todayStoreDayIndex,
            'index-page-store-hours__row--collapsed-hide':
              !expanded && row.dayIndex !== todayStoreDayIndex,
          }"
          :aria-hidden="!expanded && row.dayIndex !== todayStoreDayIndex ? true : undefined"
        >
          <span class="index-page-store-hours__day">{{ row.label }}</span>
          <span class="index-page-store-hours__time">{{ row.hours }}</span>
        </li>
      </ul>
      <button
        type="button"
        class="index-page-store-hours__toggle index-page-store-hours__reveal-line"
        :aria-expanded="expanded"
        aria-controls="index-store-hours-list"
        aria-label="Pokaż lub ukryj pełny harmonogram tygodnia"
        @click="$emit('update:expanded', !expanded)"
      >
        <q-icon
          :name="expanded ? 'expand_less' : 'expand_more'"
          size="34px"
          class="index-page-store-hours__toggle-icon"
          aria-hidden="true"
        />
      </button>
    </div>
  </section>
</template>

<script setup>
defineProps({
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
  expanded: {
    type: Boolean,
    required: true,
  },
  introRevealed: {
    type: Boolean,
    default: true,
  },
})

defineEmits(['update:expanded'])
</script>

<style scoped>
.index-page-store-hours__banner {
  pointer-events: auto;
}

.index-page-store-hours__reveal-line {
  opacity: 1;
  transform: none;
  animation: none;
}

@media (prefers-reduced-motion: reduce) {
  .index-page-store-hours__banner .index-page-store-hours__reveal-line {
    opacity: 1 !important;
    transform: none !important;
    animation: none !important;
  }

  .index-page-store-hours__banner {
    pointer-events: auto !important;
  }
}

.index-page-store-hours__banner {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin: 0;
  margin-left: -50dvw;
  padding: clamp(11px, 2.35vw, 17px) clamp(11px, 2.65vw, 19px);
  text-align: center;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 18px 60px rgba(0, 0, 0, 0.38);
  box-sizing: border-box;
}

.index-page-store-hours__heading {
  margin: 0 0 clamp(8px, 1.65vw, 12px);
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.85rem, 2.2vw, 1rem);
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #ffffff;
}

.index-page-store-hours__list {
  list-style: none;
  margin: 0 auto;
  padding: 0;
  width: 90%;
  max-width: min(420px, 100%);
  text-align: left;
}

.index-page-store-hours__block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}

.index-page-store-hours__toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: clamp(12px, 3vw, 19px) auto 0;
  padding: 4px;
  border: none;
  border-radius: 8px;
  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.1) 0%,
      rgba(255, 255, 255, 0.03) 40%,
      rgba(0, 0, 0, 0.06) 100%
    ),
    rgba(10, 10, 14, 0.46);
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #ffffff;
  cursor: pointer;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 44px rgba(0, 0, 0, 0.42);
  transition:
    opacity 0.25s ease,
    transform 0.2s ease,
    background 0.35s ease,
    border-color 0.35s ease,
    box-shadow 0.35s ease;
}

.index-page-store-hours__toggle:hover {
  opacity: 0.92;
}

.index-page-store-hours__toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px rgba(255, 170, 210, 0.55);
}

.index-page-store-hours__toggle-icon {
  display: block;
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.index-page-store-hours__row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: clamp(36px, 12vw, 72px);
  box-sizing: border-box;
  max-height: 4.5rem;
  padding: 6px 10px;
  margin: 0;
  overflow: hidden;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.88rem, 2.4vw, 1rem);
  color: #ffffff;
  border-radius: 8px;
  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.12) 0%,
      rgba(255, 255, 255, 0.04) 42%,
      rgba(0, 0, 0, 0.08) 100%
    ),
    rgba(8, 8, 12, 0.44);
  background-clip: padding-box;
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.1),
    inset 0 0 0 1px rgba(255, 255, 255, 0.04),
    0 16px 52px rgba(0, 0, 0, 0.32);
  opacity: 1;
  transition:
    max-height 0.48s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.38s ease,
    padding-top 0.48s cubic-bezier(0.4, 0, 0.2, 1),
    padding-bottom 0.48s cubic-bezier(0.4, 0, 0.2, 1),
    margin-top 0.48s cubic-bezier(0.4, 0, 0.2, 1),
    margin-bottom 0.48s cubic-bezier(0.4, 0, 0.2, 1),
    background 0.35s ease,
    box-shadow 0.35s ease,
    border-color 0.35s ease;
}

.index-page-store-hours__row--collapsed-hide {
  max-height: 0;
  padding-top: 0;
  padding-bottom: 0;
  margin-top: 0;
  margin-bottom: 0;
  opacity: 0;
  pointer-events: none;
  border: none;
  box-shadow: none;
  background: transparent !important;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
}

.index-page-store-hours__row--today {
  background: rgba(255, 105, 180, 0.14);
  backdrop-filter: blur(12px) saturate(1.35);
  -webkit-backdrop-filter: blur(12px) saturate(1.35);
  border-color: rgba(255, 170, 210, 0.5);
  box-shadow:
    inset 0 0 0 1px rgba(255, 170, 210, 0.5),
    0 1px 12px rgba(255, 120, 180, 0.12);
}

.index-page-store-hours__row--today .index-page-store-hours__day,
.index-page-store-hours__row--today .index-page-store-hours__time {
  color: #ffffff;
}

.index-page-store-hours__row:not(.index-page-store-hours__row--today) {
  background: transparent;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  border: none;
  box-shadow: none;
}

.index-page-store-hours__row:not(.index-page-store-hours__row--today):not(
    .index-page-store-hours__row--collapsed-hide
  )
  .index-page-store-hours__day,
.index-page-store-hours__row:not(.index-page-store-hours__row--today):not(
    .index-page-store-hours__row--collapsed-hide
  )
  .index-page-store-hours__time {
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.55);
}

.index-page-store-hours__row:not(.index-page-store-hours__row--today):not(
    .index-page-store-hours__row--collapsed-hide
  ) {
  padding-top: 6px;
  padding-bottom: 6px;
}

.index-page-store-hours__day {
  text-transform: capitalize;
  font-weight: 400;
}

.index-page-store-hours__time {
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
  font-weight: 600;
  color: #ffffff;
}

@media (max-width: 749.98px) {
  .index-page-store-hours__banner {
    padding: 14px 18px;
  }

  .index-page-store-hours__heading {
    margin-bottom: 10px;
    font-size: 1.15rem;
    letter-spacing: 0.1em;
  }

  .index-page-store-hours__list {
    max-width: min(520px, 100%);
  }

  .index-page-store-hours__row:not(.index-page-store-hours__row--collapsed-hide) {
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 1.08rem;
  }

  .index-page-store-hours__row:not(.index-page-store-hours__row--today):not(
      .index-page-store-hours__row--collapsed-hide
    ) {
    padding-top: 6px;
    padding-bottom: 6px;
  }

  .index-page-store-hours__toggle {
    margin-top: 12px;
    padding: 8px 10px;
    border-radius: 12px;
  }
}
</style>

