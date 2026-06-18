<template>
  <q-layout view="lHh Lpr lFf" class="main-layout">
    <q-header
      ref="headerEl"
      class="main-layout__header"
      unelevated
      :class="{
        'main-layout__header--scrolled': useSidesLayout
      }"
    >
      <div v-if="isSmallScreen" class="main-layout__mobile-status-row">
        <div class="main-layout__hours-toggle-wrap main-layout__mobile-status-wrap">
          <OpenStatusButton
            :expandable="false"
            :is-open-today="isOpenToday"
            :today-hours="todayHours"
          />
        </div>
      </div>

      <div class="main-layout__header-inner" :class="{ 'main-layout__header-inner--scrolled': useSidesLayout }">
        <div class="main-layout__brand" :class="{ 'main-layout__brand--center': useSidesLayout }">
          <a
            href="#"
            class="main-layout__logo-link"
            aria-label="Przewiń na górę"
            @click.prevent="onLogoClick"
          >
            <img
              src="../assets/logo.webp"
              alt="AM Moda Damska"
              class="main-layout__logo"
              :class="{ 'main-layout__logo--compact': useSidesLayout }"
            >
          </a>
        </div>

        <div class="main-layout__contact main-layout__contact--below" :class="{ 'main-layout__contact--hidden': useSidesLayout }">
          <a :href="PHONE_TEL_HREF" class="main-layout__phone-link">
            <q-icon name="phone" size="18px" class="main-layout__phone-icon" />
            {{ PHONE_DISPLAY }}
          </a>
          <div class="main-layout__hours-toggle-wrap">
            <OpenStatusButton
              v-model:expanded="hoursExpanded"
              :is-open-today="isOpenToday"
              :today-hours="todayHours"
            />
            <HoursDropdown
              v-show="hoursExpanded"
              :rows="openingHours"
              :today-day-index="todayDayIndex"
            />
          </div>
        </div>

        <div class="main-layout__contact main-layout__contact--left" :class="{ 'main-layout__contact--visible': useSidesLayout }">
          <div v-if="isSmallScreen" class="main-layout__hours-toggle-wrap">
            <button
              type="button"
              class="main-layout__contact-icon-btn"
              :class="{ 'main-layout__contact-icon-btn--active': hoursExpanded }"
              aria-label="Godziny otwarcia"
              @click="hoursExpanded = !hoursExpanded"
            >
              <q-icon name="fa-regular fa-clock" size="22px" />
            </button>
            <HoursDropdown
              v-show="hoursExpanded"
              :rows="openingHours"
              :today-day-index="todayDayIndex"
            />
          </div>

          <div v-else class="main-layout__hours-toggle-wrap">
            <OpenStatusButton
              v-model:expanded="hoursExpanded"
              :is-open-today="isOpenToday"
              :today-hours="todayHours"
            />
            <HoursDropdown
              v-show="hoursExpanded"
              :rows="openingHours"
              :today-day-index="todayDayIndex"
            />
          </div>
        </div>

        <div class="main-layout__contact main-layout__contact--right" :class="{ 'main-layout__contact--visible': useSidesLayout }">
          <a
            v-if="isSmallScreen"
            :href="PHONE_TEL_HREF"
            class="main-layout__contact-icon-btn main-layout__contact-icon-btn--phone"
            aria-label="Zadzwoń"
          >
            <q-icon name="phone" size="22px" />
          </a>

          <a v-else :href="PHONE_TEL_HREF" class="main-layout__phone-link">
            <q-icon name="phone" size="18px" class="main-layout__phone-icon" />
            {{ PHONE_DISPLAY }}
          </a>
        </div>
      </div>
    </q-header>

    <q-page-container class="main-layout__page-container">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { PHONE_DISPLAY, PHONE_TEL_HREF } from '../constants/siteInfo.js'
import { useOpeningHours } from '../composables/useOpeningHours.js'
import { useSiteSettings } from '../composables/useSiteSettings.js'
import { useIsSmallScreen } from '../composables/useIsSmallScreen.js'
import HoursDropdown from '../components/layout/HoursDropdown.vue'
import OpenStatusButton from '../components/layout/OpenStatusButton.vue'

const { matches: isSmallScreen } = useIsSmallScreen()
const { effectiveOpeningHours, effectiveTodayHours } = useSiteSettings()
const { todayDayIndex, todayHours, isOpenToday } = useOpeningHours(effectiveOpeningHours, {
  liveClock: true,
  todayHours: effectiveTodayHours,
})

const openingHours = effectiveOpeningHours
const hoursExpanded = ref(false)
const headerEl = ref(null)

let headerResizeObserver = null

const useSidesLayout = computed(() => !isSmallScreen.value)

function updateHeaderHeightCssVar() {
  const headerComponent = headerEl.value
  const el = headerComponent?.$el ?? headerComponent
  if (!el || typeof el.getBoundingClientRect !== 'function') return

  const height = Math.ceil(el.getBoundingClientRect().height)
  document.documentElement.style.setProperty('--main-layout-header-height', `${height}px`)
}

function closeHoursDropdown() {
  hoursExpanded.value = false
}

function onWindowScroll() {
  if (hoursExpanded.value) closeHoursDropdown()
}

function onDocumentPointerDownOutsideHours(event) {
  const target = event.target
  if (!(target instanceof Node)) return

  if (hoursExpanded.value) {
    const inHoursToggle = target.closest('.main-layout__hours-toggle-wrap')
    const inMobileStatus = target.closest('.main-layout__mobile-status-row')
    if (!inHoursToggle && !inMobileStatus) closeHoursDropdown()
  }
}

function scrollToTop() {
  document.querySelectorAll('.main-layout .q-page-container, .main-layout .q-page').forEach((node) => {
    if (node instanceof HTMLElement) {
      node.scrollTo({ top: 0, behavior: 'smooth' })
    }
  })
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function onLogoClick() {
  scrollToTop()
}

onMounted(() => {
  window.addEventListener('scroll', onWindowScroll, { passive: true })
  window.addEventListener('resize', updateHeaderHeightCssVar, { passive: true })
  document.addEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)

  nextTick(() => {
    updateHeaderHeightCssVar()
    const headerComponent = headerEl.value
    const el = headerComponent?.$el ?? headerComponent
    if (el && typeof ResizeObserver !== 'undefined') {
      headerResizeObserver = new ResizeObserver(() => updateHeaderHeightCssVar())
      headerResizeObserver.observe(el)
    }
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', onWindowScroll)
  window.removeEventListener('resize', updateHeaderHeightCssVar)
  document.removeEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)
  if (headerResizeObserver) {
    headerResizeObserver.disconnect()
    headerResizeObserver = null
  }
})
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  background: var(--am-page-bg, #bababa);
  color: rgba(255, 255, 255, 0.92);
}

.main-layout__header {
  background: transparent;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  border-bottom: none;
  overflow: visible;
}

.main-layout__page-container {
  padding-top: 0 !important;
}

.main-layout__header :deep(.q-header__content) {
  display: flex;
  align-items: center;
  padding: 13px;
  background: transparent;
  transition: padding 0.3s ease;
  overflow: visible;
  min-height: 0;
}

.main-layout__header--scrolled :deep(.q-header__content) {
  padding: 13px;
}

.main-layout__header-inner {
  position: relative;
  max-width: 1120px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 13px;
  width: 100%;
  transition: min-height 0.35s ease, gap 0.35s ease;
  overflow: visible;
}


.main-layout__header-inner:not(.main-layout__header-inner--scrolled) {
  min-height: 94px;
}

.main-layout__header-inner--scrolled {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  column-gap: 10px;
  min-height: unset;
}

@media (min-width: 1000px) {
  .main-layout__header-inner:not(.main-layout__header-inner--scrolled) {
    gap: 9px;
    min-height: 90px;
  }
  .main-layout__contact--below {
    margin-top: 59px;
  }
  .main-layout__contact--below.main-layout__contact--hidden {
    top: 59px;
  }
}


.main-layout__brand {
  position: absolute;
  top: 6px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  width: auto;
  max-width: min(480px, calc(100vw - 24px));
  margin: 5px 0 0;
  flex-shrink: 0;
  pointer-events: auto;
}

.main-layout__header-inner--scrolled .main-layout__brand {
  position: static;
  left: auto;
  top: auto;
  transform: none;
  grid-column: 2;
  grid-row: 1;
  justify-self: center;
}

.main-layout__header-inner--scrolled .main-layout__contact--below.main-layout__contact--hidden {
  grid-column: 1 / -1;
  grid-row: 1;
  justify-self: stretch;
}

.main-layout__logo-link {
  display: inline-block;
  padding: 0;
  margin: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  font: inherit;
  line-height: 0;
  text-decoration: none;
  color: inherit;
  -webkit-tap-highlight-color: transparent;
}

.main-layout__logo-link:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.85);
  outline-offset: 4px;
  border-radius: 8px;
}

.main-layout__logo--compact {
  max-height: 32px;
}

.main-layout__logo {
  display: block;
  min-width: 224px;
  width: auto;
  height: auto;
  max-height: 42px;
  max-width: 100%;
  object-fit: contain;
  border-radius: 6px;
}

.main-layout__contact {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  justify-content: flex-start;
  gap: 10px;
  margin-bottom: 13px;
}


.main-layout__contact--below {
  opacity: 1;
  
  margin-top: 63px;
}
.main-layout__contact--below.main-layout__contact--hidden {
  position: absolute;
  left: 0;
  right: 0;
  top: 63px;
  opacity: 0;
  pointer-events: none;
  margin-top: 0;
  margin-bottom: 0;
}


.main-layout__contact--left,
.main-layout__contact--right {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
  pointer-events: none;
  margin-bottom: 0;
}
.main-layout__contact--left {
  left: 0;
  right: auto;
}
.main-layout__contact--right {
  left: auto;
  right: 0;
}
.main-layout__contact--left.main-layout__contact--visible,
.main-layout__contact--right.main-layout__contact--visible {
  position: static;
  transform: none;
  opacity: 1;
  pointer-events: auto;
}
.main-layout__header-inner--scrolled .main-layout__contact--left.main-layout__contact--visible {
  grid-column: 1;
  grid-row: 1;
  justify-self: start;
  min-width: 0;
  margin-bottom: 0;
}
.main-layout__header-inner--scrolled .main-layout__contact--right.main-layout__contact--visible {
  grid-column: 3;
  grid-row: 1;
  justify-self: end;
  min-width: 0;
  margin-bottom: 0;
}
.main-layout__contact--left {
  order: -1;
  justify-content: flex-start;
}
.main-layout__contact--right {
  order: 1;
  justify-content: flex-end;
}

.main-layout__phone-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 30px;
  padding: 6px 13px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.78);
  backdrop-filter: blur(14px) saturate(1.2);
  -webkit-backdrop-filter: blur(14px) saturate(1.2);
  border: 1px solid rgba(255, 255, 255, 0.95);
  box-shadow:
    0 4px 18px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
  color: #141414;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: background 0.2s ease, box-shadow 0.2s ease;
}
.main-layout__phone-icon {
  flex-shrink: 0;
  color: #141414;
}

.main-layout__phone-link:hover {
  background: rgba(255, 255, 255, 0.92);
  box-shadow:
    0 6px 22px rgba(0, 0, 0, 0.12),
    inset 0 1px 0 rgba(255, 255, 255, 0.95);
}


.main-layout__contact-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  min-width: 34px;
  min-height: 34px;
  border-radius: 4px;
  border: 1px solid rgba(255, 255, 255, 0.9);
  background: rgba(255, 255, 255, 0.85);
  box-shadow:
    0 4px 16px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
  cursor: pointer;
  text-decoration: none;
  color: #141414;
  font: inherit;
  transition: background 0.2s ease, box-shadow 0.2s ease;
}
.main-layout__contact-icon-btn:hover {
  background: rgba(255, 255, 255, 0.88);
  box-shadow:
    0 6px 20px rgba(0, 0, 0, 0.12),
    inset 0 1px 0 rgba(255, 255, 255, 0.95);
}
.main-layout__contact-icon-btn--phone {
  border-color: rgba(255, 255, 255, 0.9);
}
.main-layout__contact-icon-btn--phone:hover {
  background: rgba(255, 255, 255, 0.88);
}
.main-layout__contact-icon-btn--active {
  background: rgba(255, 255, 255, 0.92);
  box-shadow:
    0 0 0 1px rgba(20, 20, 20, 0.12),
    0 6px 20px rgba(0, 0, 0, 0.12),
    inset 0 1px 0 rgba(255, 255, 255, 0.95);
}

.main-layout__hours-toggle-wrap {
  position: relative;
}

.main-layout__header .main-layout__open-status :deep(.q-icon) {
  color: #141414 !important;
  opacity: 1 !important;
}

@media (min-width: 1000px) {
  .main-layout__phone-link,
  .main-layout__open-status {
    font-size: 1.05rem;
  }

  .main-layout__hours-row {
    font-size: 1.05rem;
  }
}

@media (max-width: 999.98px) {
  .main-layout {
    --main-layout-header-bg:
      linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.08) 0%,
        rgba(255, 255, 255, 0.02) 38%,
        rgba(0, 0, 0, 0.06) 100%
      ),
      rgba(10, 10, 14, 0.9);
  }

  .main-layout__header,
  .main-layout__header :deep(.q-header__content) {
    background: var(--main-layout-header-bg);
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
  }

  .main-layout__header :deep(.q-header__content) {
    flex-direction: column;
    align-items: stretch;
    padding: 0;
  }

  .main-layout__header-inner {
    padding: 0 16px;
    box-sizing: border-box;
  }

  .main-layout__header-inner:not(.main-layout__header-inner--scrolled) {
    min-height: 0;
    gap: 0;
  }

  .main-layout__mobile-status-row {
    display: flex;
    width: 100%;
    flex-shrink: 0;
    box-sizing: border-box;
  }

  .main-layout__mobile-status-wrap {
    width: 100%;
    max-width: none;
    min-width: 0;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status {
    display: flex;
    width: 100%;
    max-width: none;
    box-sizing: border-box;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-size: clamp(1.28rem, 5.4vw, 1.52rem);
    font-weight: 600;
    line-height: 1.1;
    min-height: 0;
    max-height: none;
    padding: 5px 16px;
    border-radius: 0;
    gap: 8px;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status span {
    line-height: 1.1;
    text-align: center;
  }

  .main-layout__mobile-status-wrap .main-layout__hours-value {
    margin-left: clamp(6px, 2vw, 10px);
    font-weight: 700;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status :deep(.q-icon) {
    font-size: 1.2em !important;
  }

  .main-layout__mobile-status-dropdown.main-layout__hours-dropdown {
    left: 0;
    right: 0;
    transform: none;
    width: 100%;
    min-width: unset;
    max-width: none;
  }

  .main-layout__brand {
    position: static;
    left: auto;
    top: auto;
    transform: none;
    margin: 0;
    padding: 5px 0;
    box-sizing: border-box;
  }

  .main-layout__contact--below {
    margin-top: 42px;
    margin-bottom: 5px;
    flex-direction: row;
    justify-content: center;
  }

  .main-layout__contact--below .main-layout__hours-toggle-wrap {
    order: 1;
  }

  .main-layout__contact--below .main-layout__phone-link {
    order: 2;
  }

  .main-layout__contact--below.main-layout__contact--hidden {
    top: 37px;
  }

  .main-layout__hours-dropdown {
    left: 0;
    right: auto;
    min-width: 220px;
    max-width: min(320px, calc(100vw - 24px));
  }

  .main-layout__contact--below {
    display: none !important;
  }
}
</style>

<style>
/* Shared hours-related styles (unscoped so child components HoursDropdown.vue
 * and OpenStatusButton.vue can pick them up). */
.main-layout__open-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  min-height: 30px;
  padding: 6px 11px;
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 10px;
  border: 1px solid transparent;
  cursor: pointer;
  background: transparent;
  transition: background 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.main-layout__open-status--static {
  cursor: default;
  pointer-events: none;
}

.main-layout__header .main-layout__open-status,
.main-layout__header button.main-layout__open-status {
  padding: 6px 13px;
  gap: 8px;
  background: rgba(255, 255, 255, 0.78) !important;
  backdrop-filter: blur(14px) saturate(1.2);
  -webkit-backdrop-filter: blur(14px) saturate(1.2);
  border: 1px solid rgba(255, 255, 255, 0.95) !important;
  box-shadow:
    0 4px 18px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
  color: #141414 !important;
  -webkit-text-fill-color: #141414;
  transition: background 0.2s ease, box-shadow 0.2s ease;
}

.main-layout__header .main-layout__open-status:hover,
.main-layout__header button.main-layout__open-status:hover {
  background: rgba(255, 255, 255, 0.92) !important;
  box-shadow:
    0 6px 22px rgba(0, 0, 0, 0.12),
    inset 0 1px 0 rgba(255, 255, 255, 0.95);
}

.main-layout__header .main-layout__open-status span {
  color: inherit !important;
}

.main-layout__header .main-layout__open-status :deep(.q-icon) {
  color: #141414 !important;
  opacity: 1 !important;
}

@media (min-width: 1000px) {
  .main-layout__header .main-layout__open-status--open-today,
  .main-layout__header .main-layout__open-status--open-today span {
    color: #141414 !important;
    -webkit-text-fill-color: #141414 !important;
  }

  .main-layout__header .main-layout__open-status--open-today :deep(.q-icon) {
    color: #141414 !important;
  }
}

@media (max-width: 999.98px) {
  .main-layout__mobile-status-wrap .main-layout__open-status,
  .main-layout__mobile-status-wrap button.main-layout__open-status {
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    background: transparent !important;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status--open-today,
  .main-layout__mobile-status-wrap button.main-layout__open-status--open-today {
    background: transparent !important;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status:hover,
  .main-layout__mobile-status-wrap button.main-layout__open-status:hover,
  .main-layout__mobile-status-wrap .main-layout__open-status--open-today:hover,
  .main-layout__mobile-status-wrap button.main-layout__open-status--open-today:hover {
    box-shadow: none !important;
    background: transparent !important;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status,
  .main-layout__mobile-status-wrap button.main-layout__open-status,
  .main-layout__mobile-status-wrap .main-layout__open-status span,
  .main-layout__mobile-status-wrap .main-layout__open-status--open-today,
  .main-layout__mobile-status-wrap .main-layout__open-status--open-today span {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
  }

  .main-layout__mobile-status-wrap .main-layout__open-status :deep(.q-icon) {
    color: #ffffff !important;
  }
}

.main-layout__hours-chevron {
  margin-left: 2px;
  flex-shrink: 0;
}

.main-layout__hours-value {
  margin-left: 10px;
  font-weight: 600;
}

.main-layout__hours-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 6px;
  min-width: 260px;
  max-width: calc(100vw - 24px);
  padding: 12px 14px;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border: 1px solid rgba(255, 255, 255, 0.95);
  border-radius: 12px;
  box-shadow:
    0 12px 40px rgba(0, 0, 0, 0.14),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
  z-index: 1100;
  box-sizing: border-box;
}

.main-layout__hours-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 20px;
  font-size: 0.9rem;
  padding: 4px 8px;
  margin: 0 -8px;
  color: #1a1a1a;
  border-radius: 6px;
}

.main-layout__hours-row--today {
  background: rgba(0, 0, 0, 0.055);
}

.main-layout__hours-day {
  text-transform: capitalize;
  color: rgba(0, 0, 0, 0.52);
  flex-shrink: 0;
  font-weight: 400;
}

.main-layout__hours-time {
  font-weight: 600;
  white-space: nowrap;
  text-align: right;
  flex-shrink: 0;
  color: #121212;
}
</style>
