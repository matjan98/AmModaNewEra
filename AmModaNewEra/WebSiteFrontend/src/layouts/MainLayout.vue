<template>
  <q-layout view="lHh Lpr lFf" class="main-layout">
    <q-header
      class="main-layout__header"
      unelevated
      :class="{
        'main-layout__header--scrolled': useSidesLayout
      }"
    >
      <div class="main-layout__header-inner" :class="{ 'main-layout__header-inner--scrolled': useSidesLayout }">
        <div class="main-layout__brand" :class="{ 'main-layout__brand--center': useSidesLayout }">
          <a
            href="#"
            class="main-layout__logo-link"
            aria-label="Przewiń na górę"
            @click.prevent="onLogoClick"
          >
            <img
              src="../assets/logo.png"
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

    <div class="main-layout__fab-column">
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
              <a :href="PHONE_TEL_HREF" class="main-layout__mobile-fab-phone-link">
                <span class="main-layout__mobile-fab-phone-label">Zadzwoń:</span>
                <span class="main-layout__mobile-fab-phone-number">{{ PHONE_DISPLAY }}</span>
              </a>
            </div>
          </div>
        </div>
        <div class="main-layout__mobile-fab-hours">
          <div class="main-layout__mobile-fab-wrap">
            <button
              ref="mobileHoursFabBtnRef"
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
            <HoursDropdown
              ref="mobileHoursFabPanelRef"
              v-show="hoursExpanded"
              class="main-layout__mobile-fab-panel"
              :class="{ 'main-layout__mobile-fab-panel--flip-up': mobileHoursFabPanelFlipUp }"
              :rows="openingHours"
              :today-day-index="todayDayIndex"
            />
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { OPENING_HOURS, PHONE_DISPLAY, PHONE_TEL_HREF } from '../constants/siteInfo.js'
import { useOpeningHours } from '../composables/useOpeningHours.js'
import { useIsSmallScreen } from '../composables/useIsSmallScreen.js'
import HoursDropdown from '../components/layout/HoursDropdown.vue'
import OpenStatusButton from '../components/layout/OpenStatusButton.vue'

const HOURS_FAB_PANEL_GAP_PX = 8

const { matches: isSmallScreen } = useIsSmallScreen()
const { todayDayIndex, todayHours, isOpenToday, isStoreOpenNow } = useOpeningHours(OPENING_HOURS, {
  liveClock: true,
})

const openingHours = OPENING_HOURS
const hoursExpanded = ref(false)
const phoneFabExpanded = ref(false)
const mobileHoursFabBtnRef = ref(null)
const mobileHoursFabPanelRef = ref(null)
const mobileHoursFabPanelFlipUp = ref(false)

const useSidesLayout = computed(() => !isSmallScreen.value)

function updateMobileHoursFabPanelPlacement() {
  if (!hoursExpanded.value) return
  const btn = mobileHoursFabBtnRef.value
  const panelComp = mobileHoursFabPanelRef.value
  const panel = panelComp?.rootEl ?? panelComp
  if (!btn || !panel) return

  const btnRect = btn.getBoundingClientRect()
  const vv = window.visualViewport
  const viewTop = vv?.offsetTop ?? 0
  const viewBottom = vv ? vv.offsetTop + vv.height : window.innerHeight

  const pr = panel.getBoundingClientRect()
  let panelH = pr.height
  if (panelH < 2) {
    panelH = panel.scrollHeight
  }

  const gap = HOURS_FAB_PANEL_GAP_PX
  const spaceAbove = btnRect.top - viewTop
  const spaceBelow = viewBottom - btnRect.bottom
  const need = panelH + gap

  if (spaceBelow >= need) {
    mobileHoursFabPanelFlipUp.value = false
  } else if (spaceAbove >= need) {
    mobileHoursFabPanelFlipUp.value = true
  } else {
    mobileHoursFabPanelFlipUp.value = spaceAbove > spaceBelow
  }
}

function onMobileHoursFabPlacementLayout() {
  if (hoursExpanded.value) updateMobileHoursFabPanelPlacement()
}

watch(hoursExpanded, (open) => {
  if (!open) {
    mobileHoursFabPanelFlipUp.value = false
    return
  }
  nextTick(() => {
    requestAnimationFrame(() => {
      requestAnimationFrame(() => updateMobileHoursFabPanelPlacement())
    })
  })
})

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

function togglePhoneFab() {
  phoneFabExpanded.value = !phoneFabExpanded.value
  if (phoneFabExpanded.value) hoursExpanded.value = false
}

function toggleHoursFab() {
  hoursExpanded.value = !hoursExpanded.value
  if (hoursExpanded.value) phoneFabExpanded.value = false
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
  window.addEventListener('resize', onMobileHoursFabPlacementLayout, { passive: true })
  if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', onMobileHoursFabPlacementLayout)
    window.visualViewport.addEventListener('scroll', onMobileHoursFabPlacementLayout)
  }
  document.addEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onWindowScroll)
  window.removeEventListener('resize', onMobileHoursFabPlacementLayout)
  if (window.visualViewport) {
    window.visualViewport.removeEventListener('resize', onMobileHoursFabPlacementLayout)
    window.visualViewport.removeEventListener('scroll', onMobileHoursFabPlacementLayout)
  }
  document.removeEventListener('pointerdown', onDocumentPointerDownOutsideHours, true)
})
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  background: transparent;
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

@media (min-width: 750px) {
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

@media (min-width: 750px) {
  .main-layout__phone-link,
  .main-layout__open-status {
    font-size: 1.05rem;
  }

  .main-layout__hours-row {
    font-size: 1.05rem;
  }

  .main-layout__fab-column {
    top: auto;
    bottom: max(18px, env(safe-area-inset-bottom, 0px));
    transform: none;
    
    right: max(40px, env(safe-area-inset-right, 0px));
  }

  .main-layout__fab-column--entered .main-layout__facebook-fab {
    transition-delay: 0s;
  }

  .main-layout__mobile-fab-stack {
    display: none;
  }

  .main-layout__facebook-fab {
    width: 42px;
    height: 42px;
    border-radius: 99px;
  }

  .main-layout__facebook-fab-icon {
    width: 22px;
    height: 22px;
  }
}

@media (max-width: 749.98px) {
  .main-layout__header :deep(.q-header__content) {
    padding: 8px 16px;
  }

  .main-layout__header-inner:not(.main-layout__header-inner--scrolled) {
    min-height: 50px;
    gap: 8px;
  }

  .main-layout__brand {
    top: 5px;
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

  .main-layout__fab-column {
    top: calc(50% - 30vh);
    bottom: auto;
    
    transform: translateY(calc(-50% + 20px));
  }

  .main-layout__contact--below {
    display: none !important;
  }
 
  .main-layout__fab-column {
    display: none !important;
  }

  .main-layout__mobile-fab-stack {
    position: relative;
    z-index: 6201;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
  }

  .main-layout__mobile-fab-btn {
    width: 42px;
    height: 42px;
    border-radius: 99px;
  }

  .main-layout__facebook-fab {
    width: 42px;
    height: 42px;
    border-radius: 99px;
  }
}

.main-layout__mobile-fab-wrap {
  position: relative;
}

.main-layout__mobile-fab-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  margin: 0;
  border: 1px solid rgba(255, 255, 255, 0.55);
  border-radius: 99px;
  background: transparent;
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
  top: calc(100% + 8px);
  bottom: auto;
  margin-top: 0;
  left: auto;
  min-width: min(280px, calc(100vw - 48px));
  max-width: calc(100vw - 24px);
}

.main-layout__mobile-fab-panel.main-layout__hours-dropdown.main-layout__mobile-fab-panel--flip-up {
  top: auto;
  bottom: calc(100% + 8px);
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

.main-layout__facebook-fab {
  position: relative;
  z-index: 6199;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  margin: 0;
  background: #1877f2;
  border: 1px solid rgba(255, 255, 255, 0.38);
  border-radius: 99px;
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

.main-layout__header .main-layout__open-status .main-layout__hours-chevron {
  color: #141414 !important;
  opacity: 1 !important;
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
