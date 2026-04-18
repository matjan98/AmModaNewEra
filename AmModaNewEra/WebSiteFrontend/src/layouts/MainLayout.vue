<template>
  <q-layout view="lHh Lpr lFf" class="main-layout">
    <q-header
      ref="headerRef"
      class="main-layout__header"
      unelevated
      :class="{
        'main-layout__header--scrolled': useSidesLayout
      }"
    >
      <div class="main-layout__header-inner" :class="{ 'main-layout__header-inner--scrolled': useSidesLayout }">
        <!-- Logo: click scrolls to top, stays on current tab -->
        <div class="main-layout__brand" :class="{ 'main-layout__brand--center': useSidesLayout }">
          <button
            type="button"
            class="main-layout__logo-btn"
            aria-label="Przewiń na górę"
            @click="onLogoClick"
          >
            <img
              src="../assets/logo.png"
              alt="AM Moda Damska"
              class="main-layout__logo"
              :class="{ 'main-layout__logo--compact': useSidesLayout }"
            >
          </button>
        </div>
        <!-- Contact below logo (when narrow: phones / not enough space) -->
        <div class="main-layout__contact main-layout__contact--below" :class="{ 'main-layout__contact--hidden': useSidesLayout }">
          <a href="tel:+48503115446" class="main-layout__phone-link">
            <q-icon name="phone" size="18px" class="main-layout__phone-icon" />
            503 115 446
          </a>
          <div class="main-layout__hours-toggle-wrap">
            <button
              type="button"
              class="main-layout__open-status"
              :class="[isOpenToday ? 'main-layout__open-status--open' : 'main-layout__open-status--closed', { 'main-layout__open-status--expanded': hoursExpanded }]"
              @click="hoursExpanded = !hoursExpanded"
            >
              <span>
                <span v-if="isOpenToday">
                  Dzisiaj otwarte: {{ todayHours }}
                </span>
                <span v-else>
                  Dzisiaj zamknięte
                </span>
              </span>
              <q-icon :name="hoursExpanded ? 'expand_less' : 'expand_more'" size="20px" class="main-layout__hours-chevron" />
            </button>
            <div v-show="hoursExpanded" class="main-layout__hours-dropdown">
              <div
                v-for="row in openingHours"
                :key="row.label"
                class="main-layout__hours-row"
                :class="{ 'main-layout__hours-row--today': row.dayIndex === todayDayIndex }"
              >
                <span class="main-layout__hours-day">{{ row.label }}</span>
                <span class="main-layout__hours-time">{{ row.hours }}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Contact left (hours) when wide enough for row layout -->
        <div class="main-layout__contact main-layout__contact--left" :class="{ 'main-layout__contact--visible': useSidesLayout }">
          <!-- Small screens: clock icon only, tap to show hours -->
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
            <div v-show="hoursExpanded" class="main-layout__hours-dropdown">
              <div
                v-for="row in openingHours"
                :key="row.label"
                class="main-layout__hours-row"
                :class="{ 'main-layout__hours-row--today': row.dayIndex === todayDayIndex }"
              >
                <span class="main-layout__hours-day">{{ row.label }}</span>
                <span class="main-layout__hours-time">{{ row.hours }}</span>
              </div>
            </div>
          </div>
          <!-- Larger screens: full hours text -->
          <div v-else class="main-layout__hours-toggle-wrap">
            <button
              type="button"
              class="main-layout__open-status"
              :class="[isOpenToday ? 'main-layout__open-status--open' : 'main-layout__open-status--closed', { 'main-layout__open-status--expanded': hoursExpanded }]"
              @click="hoursExpanded = !hoursExpanded"
            >
              <span>
                <span v-if="isOpenToday">
                  Dzisiaj otwarte: {{ todayHours }}
                </span>
                <span v-else>
                  Dzisiaj zamknięte
                </span>
              </span>
              <q-icon :name="hoursExpanded ? 'expand_less' : 'expand_more'" size="20px" class="main-layout__hours-chevron" />
            </button>
            <div v-show="hoursExpanded" class="main-layout__hours-dropdown">
              <div
                v-for="row in openingHours"
                :key="row.label"
                class="main-layout__hours-row"
                :class="{ 'main-layout__hours-row--today': row.dayIndex === todayDayIndex }"
              >
                <span class="main-layout__hours-day">{{ row.label }}</span>
                <span class="main-layout__hours-time">{{ row.hours }}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Contact right (phone) when wide enough for row layout -->
        <div class="main-layout__contact main-layout__contact--right" :class="{ 'main-layout__contact--visible': useSidesLayout }">
          <!-- Small screens: icon only, tap to call -->
          <a
            v-if="isSmallScreen"
            href="tel:+48503115446"
            class="main-layout__contact-icon-btn main-layout__contact-icon-btn--phone"
            aria-label="Zadzwoń"
          >
            <q-icon name="phone" size="22px" />
          </a>
          <!-- Larger screens: full number -->
          <a v-else href="tel:+48503115446" class="main-layout__phone-link">
            <q-icon name="phone" size="18px" class="main-layout__phone-icon" />
            503 115 446
          </a>
        </div>
      </div>
    </q-header>

    <q-page-container class="main-layout__page-container">
      <router-view />
    </q-page-container>

    <div
      class="main-layout__fab-column"
      :class="{ 'main-layout__fab-column--entered': facebookFabEntered }"
    >
      <div class="main-layout__mobile-fab-stack">
      <div class="main-layout__mobile-fab-phone">
        <div class="main-layout__mobile-fab-wrap">
          <button
            type="button"
            class="main-layout__mobile-fab-btn"
            :class="{ 'main-layout__mobile-fab-btn--active': phoneFabExpanded }"
            aria-label="Zadzwoń"
            :aria-expanded="phoneFabExpanded"
            @click="togglePhoneFab"
          >
            <q-icon name="phone" size="22px" />
            <span
              v-if="isStoreOpenNow"
              class="main-layout__fab-status-dot main-layout__fab-status-dot--delay-0"
              aria-hidden="true"
            />
          </button>
          <div
            v-show="phoneFabExpanded"
            class="main-layout__mobile-fab-panel main-layout__mobile-fab-panel--phone"
          >
            <a href="tel:+48503115446" class="main-layout__mobile-fab-phone-link">
              <span class="main-layout__mobile-fab-phone-label">Zadzwoń:</span>
              <span class="main-layout__mobile-fab-phone-number">503 115 446</span>
            </a>
          </div>
        </div>
      </div>
      <div class="main-layout__mobile-fab-hours">
        <div class="main-layout__mobile-fab-wrap">
          <button
            type="button"
            class="main-layout__mobile-fab-btn"
            :class="{ 'main-layout__mobile-fab-btn--active': hoursExpanded }"
            aria-label="Godziny otwarcia"
            :aria-expanded="hoursExpanded"
            @click="toggleHoursFab"
          >
            <q-icon name="fa-regular fa-clock" size="22px" />
            <span
              v-if="isStoreOpenNow"
              class="main-layout__fab-status-dot main-layout__fab-status-dot--delay-1"
              aria-hidden="true"
            />
          </button>
          <div
            v-show="hoursExpanded"
            class="main-layout__mobile-fab-panel main-layout__hours-dropdown"
          >
            <p class="main-layout__mobile-hours-summary">
              <span v-if="isOpenToday">
                Dzisiaj otwarte: {{ todayHours }}
              </span>
              <span v-else>Dzisiaj zamknięte</span>
            </p>
            <div
              v-for="row in openingHours"
              :key="'mobile-' + row.label"
              class="main-layout__hours-row"
              :class="{ 'main-layout__hours-row--today': row.dayIndex === todayDayIndex }"
            >
              <span class="main-layout__hours-day">{{ row.label }}</span>
              <span class="main-layout__hours-time">{{ row.hours }}</span>
            </div>
          </div>
        </div>
      </div>
      </div>
      <a
      :href="facebookUrl"
      target="_blank"
      rel="noopener noreferrer"
      class="main-layout__facebook-fab"
      aria-label="Facebook — AM Moda Damska"
    >
      <svg
        class="main-layout__facebook-fab-icon"
        viewBox="0 0 24 24"
        aria-hidden="true"
        focusable="false"
      >
        <path
          fill="currentColor"
          d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
        />
      </svg>
      <span
        v-if="!isStoreOpenNow"
        class="main-layout__fab-status-dot main-layout__fab-status-dot--delay-fb"
        aria-hidden="true"
      />
    </a>
    </div>
  </q-layout>
</template>

<script setup>
import { computed, onMounted, onUnmounted, provide, ref } from 'vue'

const SMALL_SCREEN_MAX_WIDTH = 800
const isSmallScreen = ref(false)
const useSidesLayout = computed(() => !isSmallScreen.value)

let mediaQuery = null

function updateSmallScreen() {
  isSmallScreen.value = window.matchMedia(`(max-width: ${SMALL_SCREEN_MAX_WIDTH}px)`).matches
}

const openingHours = [
  { dayIndex: 1, label: 'poniedziałek', hours: '09:00 - 18:00' },
  { dayIndex: 2, label: 'wtorek', hours: '09:00 - 18:00' },
  { dayIndex: 3, label: 'środa', hours: '09:00 - 18:00' },
  { dayIndex: 4, label: 'czwartek', hours: '09:00 - 18:00' },
  { dayIndex: 5, label: 'piątek', hours: '09:00 - 18:00' },
  { dayIndex: 6, label: 'sobota', hours: '09:00 - 14:00' },
  { dayIndex: 0, label: 'niedziela', hours: 'Zamknięte' },
]

const hoursExpanded = ref(false)

/** Wall-clock time for “today” / open-now checks (updates every minute). */
const now = ref(new Date())
const todayDayIndex = computed(() => now.value.getDay())
const openingToday = computed(() => openingHours.find((h) => h.dayIndex === todayDayIndex.value))
const isOpenToday = computed(() => openingToday.value?.hours !== 'Zamknięte')
const todayHours = computed(() => openingToday.value?.hours ?? 'Zamknięte')

function parseOpeningRangeMinutes(hoursStr) {
  if (!hoursStr || hoursStr === 'Zamknięte') return null
  const m = hoursStr.trim().match(/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/)
  if (!m) return null
  const startMin = Number(m[1]) * 60 + Number(m[2])
  const endMin = Number(m[3]) * 60 + Number(m[4])
  return { startMin, endMin }
}

/** True only within today’s opening interval (local time); end time is exclusive. */
const isStoreOpenNow = computed(() => {
  const range = parseOpeningRangeMinutes(openingToday.value?.hours)
  if (!range) return false
  const mins = now.value.getHours() * 60 + now.value.getMinutes()
  return mins >= range.startMin && mins < range.endMin
})

const headerRef = ref(null)
provide('layoutHeaderRef', headerRef)

function closeHoursDropdown() {
  hoursExpanded.value = false
}

function closePhoneFab() {
  phoneFabExpanded.value = false
}

function onWindowScroll() {
  if (hoursExpanded.value) closeHoursDropdown()
  if (phoneFabExpanded.value) closePhoneFab()
}

/** Close hours / phone FAB panels on tap outside (capture runs before toggle handlers). */
function onDocumentPointerDownOutsideHours(event) {
  const target = event.target
  if (!(target instanceof Node)) return

  if (hoursExpanded.value) {
    const inLegacyHours = target.closest('.main-layout__hours-toggle-wrap')
    const inMobileHours = target.closest('.main-layout__mobile-fab-hours')
    if (!inLegacyHours && !inMobileHours) closeHoursDropdown()
  }

  if (phoneFabExpanded.value) {
    if (!target.closest('.main-layout__mobile-fab-phone')) closePhoneFab()
  }
}

const galleryUploadUnlocked = ref(false)
const showAuxInput = ref(false)

const facebookUrl = 'https://www.facebook.com/AMModaDamska/'
const facebookFabEntered = ref(false)
let facebookFabEnterTimer = null
const FACEBOOK_FAB_ENTER_DELAY_MS = 2000
const OPEN_STATUS_TICK_MS = 60_000

const phoneFabExpanded = ref(false)
let openStatusTickTimer = null

function togglePhoneFab() {
  phoneFabExpanded.value = !phoneFabExpanded.value
  if (phoneFabExpanded.value) hoursExpanded.value = false
}

function toggleHoursFab() {
  hoursExpanded.value = !hoursExpanded.value
  if (hoursExpanded.value) phoneFabExpanded.value = false
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function onLogoClick() {
  scrollToTop()
  showAuxInput.value = true
}

provide('galleryUploadUnlocked', galleryUploadUnlocked)
provide('showAuxInput', showAuxInput)

onMounted(() => {
  now.value = new Date()
  openStatusTickTimer = window.setInterval(() => {
    now.value = new Date()
  }, OPEN_STATUS_TICK_MS)

  updateSmallScreen()
  mediaQuery = window.matchMedia(`(max-width: ${SMALL_SCREEN_MAX_WIDTH}px)`)
  mediaQuery.addEventListener('change', updateSmallScreen)
  window.addEventListener('scroll', onWindowScroll, { passive: true })
  document.addEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)
  facebookFabEnterTimer = window.setTimeout(() => {
    facebookFabEntered.value = true
  }, FACEBOOK_FAB_ENTER_DELAY_MS)
})
onUnmounted(() => {
  if (openStatusTickTimer != null) {
    window.clearInterval(openStatusTickTimer)
    openStatusTickTimer = null
  }
  if (mediaQuery) mediaQuery.removeEventListener('change', updateSmallScreen)
  window.removeEventListener('scroll', onWindowScroll)
  document.removeEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)
  if (facebookFabEnterTimer != null) {
    window.clearTimeout(facebookFabEnterTimer)
    facebookFabEnterTimer = null
  }
})
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  background: rgb(0, 0, 0);
  color: rgba(255, 255, 255, 0.92);
}

.main-layout__header {
  background: transparent;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  border-bottom: 1px solid transparent;
  overflow: visible;
  transition: none;
}

.main-layout__page-container {
  padding-top: 0 !important;
}

.main-layout__header :deep(.q-header__content) {
  padding: 16px;
  transition: padding 0.3s ease;
  overflow: visible;
}

.main-layout__header--scrolled :deep(.q-header__content) {
  padding: 16px;
}

.main-layout__header-inner {
  position: relative;
  max-width: 1120px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  width: 100%;
  min-height: 118px;
  transition: min-height 0.35s ease, gap 0.35s ease;
  overflow: visible;
}

.main-layout__header-inner--scrolled {
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  min-height: 52px;
}

@media (min-width: 800px) {
  /* Big screens only: reduce header height by ~5px (small screens stay unchanged) */
  .main-layout__header-inner {
    gap: 11px;
    min-height: 113px;
  }
  .main-layout__header-inner--scrolled {
    min-height: 60px;
  }
  .main-layout__contact--below {
    margin-top: 67px;
  }
  .main-layout__contact--below.main-layout__contact--hidden {
    top: 67px;
  }
}

/* Logo fixed in place – same position whether scrolled or not */
.main-layout__brand {
  position: absolute;
  top: 8px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  width: auto;
  max-width: 350px;
  margin: 0;
  flex-shrink: 0;
  pointer-events: auto;
}

.main-layout__logo-btn {
  display: block;
  padding: 0;
  margin: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  font: inherit;
  line-height: 0;
}

.main-layout__logo--compact {
  height: 36px;
}

.main-layout__logo {
  height: clamp(32px, 8vw, 48px);
  width: auto;
  max-width: 100%;
  object-fit: contain;
  border-radius: 6px;
}

.main-layout__contact {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 16px;
}

/* Contact below logo: visible when not scrolled, fades out when scrolled */
.main-layout__contact--below {
  opacity: 1;
  /* Reserve space for fixed logo above (8px + logo height + 16px gap) */
  margin-top: 72px;
}
.main-layout__contact--below.main-layout__contact--hidden {
  position: absolute;
  left: 0;
  right: 0;
  top: 72px;
  opacity: 0;
  pointer-events: none;
  margin-top: 0;
  margin-bottom: 0;
}

@media (max-width: 800px) {
  .main-layout__header :deep(.q-header__content) {
    padding: 10px 16px;
  }
  .main-layout__header-inner {
    min-height: 70px;
    gap: 10px;
  }
  .main-layout__brand {
    top: 6px;
  }
  .main-layout__contact--below {
    margin-top: 46px;
    margin-bottom: 6px;
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
    top: 40px;
  }
}

/* Contact left/right: hidden when not scrolled – pin to final position so no movement during fade */
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
.main-layout__header-inner--scrolled .main-layout__contact--left,
.main-layout__header-inner--scrolled .main-layout__contact--right {
  flex: 0 1 auto;
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
  gap: 10px;
  min-height: 38px;
  padding: 8px 16px;
  border-radius: 4px;
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

/* Icon-only contact buttons (small screens, scrolled header) */
.main-layout__contact-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  min-width: 42px;
  min-height: 42px;
  border-radius: 4px;
  border: 1px solid rgba(255, 255, 255, 0.9);
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: blur(14px) saturate(1.2);
  -webkit-backdrop-filter: blur(14px) saturate(1.2);
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

.main-layout__open-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-height: 38px;
  padding: 8px 14px;
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 4px;
  border: 1px solid transparent;
  cursor: pointer;
  background: transparent;
  transition: background 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.main-layout__open-status--open {
  background: rgba(255, 255, 255, 0.78);
  backdrop-filter: blur(14px) saturate(1.2);
  -webkit-backdrop-filter: blur(14px) saturate(1.2);
  border-color: rgba(255, 255, 255, 0.95);
  box-shadow:
    0 4px 18px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
  color: #141414;
}

.main-layout__open-status--open:hover {
  background: rgba(255, 255, 255, 0.9);
}

.main-layout__open-status--closed {
  background: rgba(22, 22, 22, 0.62);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
  border-color: rgba(255, 255, 255, 0.22);
  box-shadow:
    0 4px 18px rgba(0, 0, 0, 0.22),
    inset 0 1px 0 rgba(255, 255, 255, 0.12);
  color: rgba(255, 255, 255, 0.94);
}

.main-layout__open-status--closed:hover {
  background: rgba(30, 30, 30, 0.72);
}

.main-layout__hours-chevron {
  margin-left: 2px;
  flex-shrink: 0;
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

@media (min-width: 800px) {
  .main-layout__phone-link,
  .main-layout__open-status {
    font-size: 1.05rem;
  }
  .main-layout__hours-row {
    font-size: 1.05rem;
  }
}

@media (max-width: 960px) {
  .main-layout__contact {
    justify-content: flex-start;
  }
}

/* Keep hours dropdown within viewport on mobile */
@media (max-width: 600px) {
  .main-layout__hours-dropdown {
    left: 0;
    right: auto;
    min-width: 220px;
    max-width: min(320px, calc(100vw - 24px));
  }
}

/* Fixed column: mobile = vertical cluster; desktop (751+) = Facebook only, bottom-right */
.main-layout__fab-column {
  position: fixed;
  right: max(16px, env(safe-area-inset-right, 0px));
  z-index: 6000;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
  pointer-events: none;
}

@media (max-width: 750px) {
  .main-layout__fab-column {
    top: calc(50% - 30vh);
    bottom: auto;
    transform: translateY(-50%);
  }
}

@media (min-width: 751px) {
  .main-layout__fab-column {
    top: auto;
    bottom: max(20px, env(safe-area-inset-bottom, 0px));
    transform: none;
  }
}

.main-layout__fab-column > * {
  pointer-events: auto;
}

.main-layout__mobile-fab-phone,
.main-layout__mobile-fab-hours,
.main-layout__facebook-fab {
  transform: translate3d(calc(100% + 28px), 0, 0);
  transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
  will-change: transform;
}

.main-layout__fab-column--entered .main-layout__mobile-fab-phone {
  transform: translate3d(0, 0, 0);
  transition-delay: 0s;
}

.main-layout__fab-column--entered .main-layout__mobile-fab-hours {
  transform: translate3d(0, 0, 0);
  transition-delay: 0.1s;
}

.main-layout__fab-column--entered .main-layout__facebook-fab {
  transform: translate3d(0, 0, 0);
  transition-delay: 0.2s;
}

@media (min-width: 751px) {
  .main-layout__fab-column--entered .main-layout__facebook-fab {
    transition-delay: 0s;
  }

  .main-layout__mobile-fab-stack {
    display: none;
  }

  .main-layout__facebook-fab {
    width: 64px;
    height: 48px;
    border-radius: 3px;
  }

  .main-layout__facebook-fab-icon {
    width: 28px;
    height: 28px;
  }
}

@media (max-width: 750px) {
  .main-layout__contact--below {
    display: none !important;
  }

  .main-layout__mobile-fab-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
  }

  .main-layout__facebook-fab {
    width: 48px;
    height: 48px;
    border-radius: 50%;
  }
}

.main-layout__mobile-fab-stack {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
}

.main-layout__mobile-fab-wrap {
  position: relative;
}

.main-layout__mobile-fab-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 38px;
  padding: 0;
  margin: 0;
  border: 1px solid rgba(255, 255, 255, 0.55);
  border-radius: 2px;
  background: #000000;
  color: #ffffff;
  cursor: pointer;
  box-sizing: border-box;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.main-layout__mobile-fab-btn:hover {
  border-color: rgba(255, 255, 255, 0.85);
}

.main-layout__mobile-fab-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.9);
  outline-offset: 2px;
}

.main-layout__mobile-fab-btn--active {
  border-color: rgba(255, 255, 255, 0.9);
  box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.25);
}

.main-layout__mobile-fab-panel {
  position: absolute;
  right: 0;
  bottom: calc(100% + 8px);
  z-index: 6100;
  box-sizing: border-box;
}

.main-layout__mobile-fab-panel.main-layout__hours-dropdown {
  top: auto;
  margin-top: 0;
  left: auto;
  min-width: min(280px, calc(100vw - 48px));
  max-width: calc(100vw - 24px);
}

.main-layout__mobile-fab-panel--phone {
  min-width: 0;
  max-width: calc(100vw - 48px);
  padding: 6px 11px;
  background: rgba(14, 14, 18, 0.92);
  backdrop-filter: blur(18px) saturate(1.15);
  -webkit-backdrop-filter: blur(18px) saturate(1.15);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 12px;
  box-shadow:
    0 14px 44px rgba(0, 0, 0, 0.55),
    inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.main-layout__mobile-fab-phone-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  font-size: 0.95rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.96);
  text-align: center;
  text-decoration: none;
}

.main-layout__mobile-fab-phone-label {
  display: block;
  font-size: 0.82rem;
  font-weight: 400;
  line-height: 1.2;
  color: rgba(255, 255, 255, 0.52);
}

.main-layout__mobile-fab-phone-number {
  display: block;
  font-size: 0.95rem;
  font-weight: 600;
  white-space: nowrap;
  line-height: 1.2;
  letter-spacing: 0.02em;
}

.main-layout__mobile-fab-phone-link:hover .main-layout__mobile-fab-phone-number {
  text-decoration: underline;
}

.main-layout__mobile-hours-summary {
  margin: 0 0 10px;
  padding: 0 4px 10px;
  font-size: 0.92rem;
  font-weight: 600;
  color: #141414;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.main-layout__facebook-fab {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 38px;
  padding: 0;
  margin: 0;
  background: #1877f2;
  border: 1px solid rgba(255, 255, 255, 0.38);
  border-radius: 2px;
  color: #ffffff;
  text-decoration: none;
  box-sizing: border-box;
}

.main-layout__facebook-fab:hover {
  border-color: rgba(255, 255, 255, 0.55);
  background: #166fe5;
}

.main-layout__facebook-fab:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.9);
  outline-offset: 2px;
}

.main-layout__fab-status-dot {
  position: absolute;
  top: 5px;
  right: 5px;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #34c759;
  border: 1px solid #0a0a0a;
  box-sizing: border-box;
  pointer-events: none;
  z-index: 1;
  animation: main-layout-fab-dot-pulse 5s ease-in-out infinite;
  animation-delay: 0s;
}

.main-layout__fab-status-dot--delay-0 {
  animation-delay: 0s;
}

.main-layout__fab-status-dot--delay-1 {
  animation-delay: 0.1s;
}

.main-layout__fab-status-dot--delay-fb {
  animation-delay: 0s;
}

.main-layout__facebook-fab .main-layout__fab-status-dot {
  border-color: rgba(255, 255, 255, 0.55);
}

@keyframes main-layout-fab-dot-pulse {
  /* 3s cycle: ~2.4s visible, then 0.3s fade out + 0.3s fade in (each = 10% of period) */
  0%,
  80% {
    opacity: 1;
  }
  90% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@media (prefers-reduced-motion: reduce) {
  .main-layout__fab-status-dot {
    animation: none;
  }
}

.main-layout__facebook-fab-icon {
  width: 22px;
  height: 22px;
  display: block;
  flex-shrink: 0;
}
</style>
