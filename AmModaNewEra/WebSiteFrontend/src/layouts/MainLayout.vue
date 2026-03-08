<template>
  <q-layout view="lHh Lpr lFf">
    <q-header ref="headerRef" class="main-layout__header" unelevated :class="{ 'main-layout__header--scrolled': useSidesLayout, 'main-layout__header--hidden': !headerVisible }">
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
              src="../assets/logo.gif"
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
              <q-icon name="schedule" size="22px" />
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

    <q-page-container>
      <router-view />
    </q-page-container>
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

const today = new Date()
const todayDayIndex = today.getDay()
const openingToday = computed(() => openingHours.find((h) => h.dayIndex === todayDayIndex))
const isOpenToday = computed(() => openingToday.value?.hours !== 'Zamknięte')
const todayHours = computed(() => openingToday.value?.hours ?? 'Zamknięte')

const headerRef = ref(null)
provide('layoutHeaderRef', headerRef)

const headerVisible = ref(true)
let lastScrollY = 0
const SCROLL_THRESHOLD = 60

function onScroll() {
  const scrollY = window.scrollY || window.pageYOffset
  if (scrollY <= SCROLL_THRESHOLD) {
    headerVisible.value = true
  } else if (scrollY > lastScrollY) {
    headerVisible.value = false
  } else {
    headerVisible.value = true
  }
  lastScrollY = scrollY
}

const galleryUploadUnlocked = ref(false)
const showAuxInput = ref(false)

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
  updateSmallScreen()
  mediaQuery = window.matchMedia(`(max-width: ${SMALL_SCREEN_MAX_WIDTH}px)`)
  mediaQuery.addEventListener('change', updateSmallScreen)
  lastScrollY = window.scrollY || window.pageYOffset
  window.addEventListener('scroll', onScroll, { passive: true })
})
onUnmounted(() => {
  if (mediaQuery) mediaQuery.removeEventListener('change', updateSmallScreen)
  window.removeEventListener('scroll', onScroll)
})
</script>

<style scoped>
.main-layout__header {
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.4);
  overflow: visible;
  transition: transform 0.3s ease;
}
.main-layout__header--hidden {
  transform: translateY(-100%);
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
  min-height: 32px;
  padding: 5px 12px;
  border-radius: 999px;
  background: rgba(41, 182, 108, 0.1);
  color: #2e7d32;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
}
.main-layout__phone-icon {
  flex-shrink: 0;
}

.main-layout__phone-link:hover {
  filter: brightness(1.03);
}

/* Icon-only contact buttons (small screens, scrolled header) */
.main-layout__contact-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
  border-radius: 50%;
  border: none;
  background: rgba(0, 0, 0, 0.06);
  cursor: pointer;
  text-decoration: none;
  color: inherit;
  font: inherit;
  transition: background 0.2s ease;
}
.main-layout__contact-icon-btn:hover {
  background: rgba(0, 0, 0, 0.1);
}
.main-layout__contact-icon-btn--phone {
  background: rgba(41, 182, 108, 0.1);
  color: #2e7d32;
}
.main-layout__contact-icon-btn--phone:hover {
  background: rgba(41, 182, 108, 0.18);
}
.main-layout__contact-icon-btn--active {
  background: rgba(41, 182, 108, 0.15);
}

.main-layout__hours-toggle-wrap {
  position: relative;
}

.main-layout__open-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-height: 32px;
  padding: 5px 10px;
  font-size: 0.9rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font: inherit;
  background: transparent;
}

.main-layout__open-status--open {
  background: rgba(41, 182, 108, 0.1);
  color: #2e7d32;
}

.main-layout__open-status--closed {
  background: rgba(229, 57, 53, 0.08);
  color: #c62828;
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
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
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
  color: #333;
  border-radius: 6px;
}

.main-layout__hours-row--today {
  background: rgba(0, 0, 0, 0.06);
}

.main-layout__hours-day {
  text-transform: capitalize;
  color: #555;
  flex-shrink: 0;
  font-weight: 400;
}

.main-layout__hours-time {
  font-weight: 500;
  white-space: nowrap;
  text-align: right;
  flex-shrink: 0;
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
</style>
