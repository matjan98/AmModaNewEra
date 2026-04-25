<template>
  <q-page class="index-page">
    <div class="index-page__fixed-bottom-photo" aria-hidden="true">
      <img
        :src="facebookShopPhoto"
        alt=""
        class="index-page__fixed-bottom-photo-img"
        decoding="async"
      >
    </div>
    <!-- Fixed at one height, stuck to screen edges (not container) -->
    <div class="index-page__nav-buttons-fixed">
      <button
        v-if="SHOW_GALLERY_NAV_BUTTON && activeTab === 'info'"
        type="button"
        class="index-page__nav-btn index-page__nav-btn--right text-primary"
        @click="goToGallery"
      >
        <span>Galeria</span>
        <q-icon name="chevron_right" size="20px" class="index-page__nav-arrow" />
      </button>
      <button
        v-if="activeTab === 'gallery'"
        type="button"
        class="index-page__nav-btn index-page__nav-btn--left text-primary"
        @click="goToInfo"
      >
        <q-icon name="chevron_left" size="20px" class="index-page__nav-arrow" />
        <span>Informacje</span>
      </button>
    </div>
    <div class="index-page__shell">
      <div v-if="showAuxInput" class="index-page__aux-wrap">
        <input
          ref="topAuxRef"
          v-model="topAux"
          type="password"
          class="index-page__aux-in"
          autocomplete="off"
          tabindex="-1"
          aria-hidden="true"
          @keyup.enter="submitTopAux"
          @keydown.escape="closeTopAux"
        >
      </div>
      <main ref="mainRef" class="index-page__main">
        <div class="index-page__panels-transition-wrap">
        <transition :name="'index-page__slide-' + panelSlideDirection">
          <div v-if="activeTab === 'info'" key="info" class="index-page__panels">
            <div class="index-page__panels-inner">
            <!-- First hero: full-bleed image with center-focused responsive zoom -->
            <section
              ref="heroIntroRef"
              class="index-page__hero-intro"
              :style="heroIntroCtaCssVars"
            >
              <div ref="heroIntroPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                <img
                  :src="heroIntroSecondImage"
                  alt="AM Moda"
                  class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta index-page__hero-intro-sticky-cta--first"
                :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroCtaFloated }"
              >
                <div ref="heroIntroCtaRef" class="index-page__hero-intro-cta-block">
                  <div class="index-page__hero-intro-address-row">
                    <div
                      class="index-page__hero-intro-google-mini"
                      :class="{ 'index-page__hero-intro-google-mini--visible': heroIntroCtaVisible }"
                    >
                      <GoogleReviewsCard variant="mini" :with-margin="false" />
                    </div>
                  </div>
                  <div class="index-page__hero-intro-btn-row">
                    <button
                      type="button"
                      class="index-page__hero-intro-cta-btn index-page__hero-intro-cta-btn--solid"
                      :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                      @click="scrollToLocationSection"
                    >
                      Odwiedź nas
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <section class="index-page__quick-info" aria-label="Kontakt i skróty">
              <div class="index-page__quick-info-inner">
                <div class="index-page__quick-info-row">
                  <span class="index-page__quick-info-label">Kontakt:</span>
                  <a
                    href="tel:+48503115446"
                    class="index-page__quick-info-value index-page__quick-info-link"
                  >
                    503 115 446
                  </a>
                </div>
                <div class="index-page__quick-info-row">
                  <span class="index-page__quick-info-label">Dziś:</span>
                  <span class="index-page__quick-info-value">
                    <span class="index-page__quick-info-today-pill">{{ quickInfoTodayLine }}</span>
                  </span>
                </div>
                <div class="index-page__quick-info-row index-page__quick-info-row--gallery">
                  <span class="index-page__quick-info-label">Galeria:</span>
                  <a
                    :href="facebookUrl"
                    target="_blank"
                    rel="noopener"
                    class="index-page__facebook-cta-btn index-page__quick-info-fb"
                    aria-label="Otwórz galerię na Facebooku AM Moda Damska w nowej karcie"
                  >
                    Facebook
                    <q-icon
                      name="fa-brands fa-facebook"
                      size="18px"
                      class="index-page__facebook-cta-btn-icon index-page__quick-info-fb-brand-icon"
                      aria-hidden="true"
                    />
                  </a>
                </div>
              </div>
            </section>

            <section class="index-page__section-two" aria-label="Kategorie produktów">
              <div class="index-page__section-two-grid">
                <div
                  v-for="(row, rowIndex) in sectionTwoRows"
                  :key="'section-two-row-' + rowIndex"
                  class="index-page__section-two-row index-page__reveal-media"
                >
                  <article
                    v-for="item in row"
                    :key="item.name"
                    class="index-page__section-two-card"
                    :class="{ 'index-page__section-two-card--active': activeSectionTwoName === item.name }"
                    @click="toggleSectionTwoOverlay(item.name)"
                  >
                    <div class="index-page__section-two-photo-wrap">
                      <img
                        :src="item.photo"
                        :alt="item.name"
                        class="index-page__section-two-img index-page__reveal-media-img"
                        loading="lazy"
                        @load="onRevealSectionTwoImageLoad"
                      >
                      <div class="index-page__section-two-overlay" aria-hidden="true">
                        <div class="index-page__section-two-overlay-inner">
                          <div class="index-page__section-two-overlay-title">
                            Sprawdź aktualności:
                          </div>
                          <a
                            :href="facebookUrl"
                            target="_blank"
                            rel="noopener"
                            class="index-page__facebook-cta-btn index-page__facebook-cta-btn--below index-page__facebook-cta-btn--section-two-overlay"
                            aria-label="Otwórz Facebook AM Moda Damska w nowej karcie"
                            @click.stop
                          >
                            Przejdź na Facebook
                            <q-icon
                              name="fa-brands fa-facebook"
                              size="18px"
                              class="index-page__facebook-cta-btn-icon index-page__facebook-cta-btn-icon--external"
                              aria-hidden="true"
                            />
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="index-page__section-two-caption">
                      {{ item.name }}
                    </div>
                  </article>
                </div>
              </div>
            </section>

            <section
              ref="heroIntroAfterRef"
              class="index-page__hero-intro"
              :style="heroIntroCtaCssVars"
            >
              <div ref="heroIntroAfterPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                <img
                  :src="heroIntroFirstImage"
                  alt=""
                  class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroAfterCtaFloated }"
              >
                <div ref="heroIntroAfterCtaRef" class="index-page__hero-intro-cta-block">
                  <div class="index-page__hero-intro-address-row">
                    <p
                      class="index-page__hero-intro-address index-page__hero-intro-address--second"
                      :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                    >
                      Kozy, ul. Bielska 166
                    </p>
                  </div>
                  <div class="index-page__hero-intro-btn-row">
                    <button
                      type="button"
                      class="index-page__hero-intro-cta-btn"
                      :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                      @click="scrollToLocationSection"
                    >
                      Odwiedź nas
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <section
              class="index-page__store-open-banner"
              :class="{ 'index-page__store-open-banner--intro-visible': storeHoursIntroRevealed }"
              :aria-label="`${storeHoursHeadingLabel}, harmonogram tygodnia`"
              :aria-hidden="storeHoursIntroRevealed ? undefined : true"
            >
              <h3 class="index-page__store-hours-heading index-page__store-hours-reveal-line">
                {{ storeHoursHeadingLabel }}
              </h3>
              <div class="index-page__store-hours-block">
                <ul
                  id="index-store-hours-list"
                  class="index-page__store-hours-list index-page__store-hours-reveal-line"
                >
                  <li
                    v-for="row in STORE_OPENING_HOURS"
                    :key="row.dayIndex"
                    class="index-page__store-hours-row"
                    :class="{
                      'index-page__store-hours-row--today': row.dayIndex === todayStoreDayIndex,
                      'index-page__store-hours-row--collapsed-hide':
                        !storeHoursExpanded && row.dayIndex !== todayStoreDayIndex,
                    }"
                    :aria-hidden="
                      !storeHoursExpanded && row.dayIndex !== todayStoreDayIndex ? true : undefined
                    "
                  >
                    <span class="index-page__store-hours-day">{{ row.label }}</span>
                    <span class="index-page__store-hours-time">{{ row.hours }}</span>
                  </li>
                </ul>
                <button
                  type="button"
                  class="index-page__store-hours-toggle index-page__store-hours-reveal-line"
                  :aria-expanded="storeHoursExpanded"
                  aria-controls="index-store-hours-list"
                  aria-label="Pokaż lub ukryj pełny harmonogram tygodnia"
                  @click="storeHoursExpanded = !storeHoursExpanded"
                >
                  <q-icon
                    :name="storeHoursExpanded ? 'expand_less' : 'expand_more'"
                    size="34px"
                    class="index-page__store-hours-toggle-icon"
                    aria-hidden="true"
                  />
                </button>
              </div>
            </section>

            <section
              ref="heroIntroThirdRef"
              class="index-page__hero-intro"
              :style="heroIntroCtaCssVars"
            >
              <div ref="heroIntroThirdPhotoRef" class="index-page__hero-intro-photo index-page__reveal-media">
                <img
                  :src="heroIntroThirdImage"
                  alt=""
                  class="index-page__hero-intro-photo-img index-page__reveal-media-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :class="{ 'index-page__hero-intro-sticky-cta--floated': heroIntroThirdCtaFloated }"
              >
                <div ref="heroIntroThirdCtaRef" class="index-page__hero-intro-cta-block">
                  <div class="index-page__hero-intro-address-row">
                    <p
                      class="index-page__hero-intro-address"
                      :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                    >
                      <span class="index-page__hero-intro-quote">
                        <span class="index-page__hero-intro-quote-text">”Najlepszy taki sklep w okolicy“</span>
                        <br>
                        <span class="index-page__hero-intro-quote-signature">~Jakub Pilarz</span>
                      </span>
                    </p>
                  </div>
                  <div class="index-page__hero-intro-btn-row">
                    <button
                      type="button"
                      class="index-page__hero-intro-cta-btn"
                      :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                      @click="scrollToLocationSection"
                    >
                      Odwiedź nas
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <IndexPageShopBottomSections
              ref="shopBottomSectionsRef"
              :cta-css-vars="heroIntroCtaCssVars"
              :hero-intro-cta-visible="heroIntroCtaVisible"
              :hero-intro-facebook-cta-floated="heroIntroFacebookCtaFloated"
              :facebook-shop-float-pop="facebookShopFloatPop"
              :facebook-url="facebookUrl"
              :maps-url="mapsUrl"
            />
            </div>
          </div>
          <div v-else key="gallery" class="index-page__panels index-page__panels--gallery">
            <div class="index-page__panels-inner">
            <section v-if="galleryUploadUnlocked" class="index-page__section index-page__section--upload">
              <div
                class="index-page__dropzone"
                :class="{ 'index-page__dropzone--active': dragActive }"
                @dragenter.prevent="onDragEnter"
                @dragover.prevent="onDragOver"
                @dragleave.prevent="onDragLeave"
                @drop.prevent="onDrop"
              >
                <div class="index-page__dropzone-inner">
                  <q-icon name="cloud_upload" size="36px" class="q-mb-sm text-primary" />
                  <p class="index-page__dropzone-title">
                    Przeciągnij i upuść zdjęcia tutaj
                  </p>
                  <p class="index-page__dropzone-subtitle">
                    lub
                  </p>
                  <q-btn
                    color="primary"
                    flat
                    no-caps
                    label="Wybierz z dysku"
                    icon="upload"
                    @click="triggerFileInput"
                  />
                  <input
                    ref="fileInputRef"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    multiple
                    class="index-page__file-input"
                    @change="onFileSelected"
                  >
                </div>
              </div>

              <div v-if="pendingFiles.length" class="index-page__pending">
                <div class="index-page__pending-header">
                  <span>Wybrane zdjęcia ({{ pendingFiles.length }})</span>
                  <q-btn
                    flat
                    dense
                    no-caps
                    color="primary"
                    icon="clear_all"
                    label="Wyczyść"
                    @click="clearPending"
                  />
                </div>
                <ul class="index-page__pending-list">
                  <li v-for="file in pendingFiles" :key="file.name" class="index-page__pending-item">
                    <q-icon name="image" size="18px" class="q-mr-sm" />
                    <span class="index-page__pending-name">{{ file.name }}</span>
                    <span class="index-page__pending-size">{{ formatSize(file.size) }}</span>
                  </li>
                </ul>
                <q-btn
                  color="primary"
                  unelevated
                  no-caps
                  class="index-page__pending-submit"
                  label="Zatwierdź i wgraj zdjęcia"
                  icon="cloud_upload"
                  :disable="!pendingFiles.length || loading"
                  @click="uploadPending"
                />
              </div>

              <q-spinner v-if="loading" size="2rem" class="q-mt-md" />
              <div v-if="error" class="index-page__error-wrap">
                <q-banner class="index-page__error bg-negative text-white">
                  {{ error }}
                </q-banner>
              </div>
            </section>

            <section class="index-page__section index-page__section--gallery">
              <h2 class="index-page__gallery-heading">Galeria</h2>
              <div v-if="galleryUploadUnlocked" class="index-page__gallery">
                <template v-if="photoListWithUrls.length">
                  <div
                    v-for="photo in photoListWithUrls"
                    :key="photo.id"
                    class="index-page__thumb-wrap index-page__reveal-media"
                    @click="openPhoto(photo.urlWithCache)"
                  >
                    <img
                      :src="photo.urlWithCache"
                      :alt="'Zdjęcie ' + photo.id"
                      class="index-page__thumb index-page__reveal-media-img"
                    >
                    <q-btn
                      round
                      dense
                      flat
                      icon="delete"
                      size="sm"
                      class="index-page__delete-btn"
                      :disable="deletingId === photo.id"
                      @click.stop="deletePhoto(photo.id)"
                    />
                  </div>
                </template>
                <div v-else class="index-page__placeholder">
                  <q-icon name="image" size="80px" />
                  <span>Brak zdjęć</span>
                </div>
              </div>

              <div class="index-page__gallery index-page__gallery--products">
                <div
                  v-for="photo in productPhotos"
                  :key="photo"
                  class="index-page__thumb-wrap index-page__reveal-media"
                  @click="openPhoto(photo)"
                >
                  <img
                    :src="photo"
                    alt="Produkt AM Moda Damska"
                    class="index-page__thumb index-page__reveal-media-img"
                  >
                </div>
              </div>

              <GoogleReviewsCard class="index-page__google-reviews" />
            </section>
            </div>
          </div>
        </transition>
        </div>

            <q-dialog v-model="lightboxOpen" class="index-page__lightbox-dialog">
              <q-card class="index-page__lightbox-card">
                <button
                  v-if="lightboxHasPrev"
                  type="button"
                  class="index-page__lightbox-arrow index-page__lightbox-arrow--prev"
                  aria-label="Poprzednie zdjęcie"
                  @click="lightboxGoPrev"
                >
                  <q-icon name="chevron_left" size="32px" />
                </button>
                <button
                  v-if="lightboxHasNext"
                  type="button"
                  class="index-page__lightbox-arrow index-page__lightbox-arrow--next"
                  aria-label="Następne zdjęcie"
                  @click="lightboxGoNext"
                >
                  <q-icon name="chevron_right" size="30px" />
                </button>
                <div
                  class="index-page__lightbox-inner"
                  @touchstart.passive="onLightboxTouchStart"
                  @touchend.passive="onLightboxTouchEnd"
                >
                  <span class="index-page__lightbox-img-wrap">
                    <img v-if="lightboxUrl" :src="lightboxUrl" alt="Podgląd zdjęcia" class="index-page__lightbox-img">
                    <q-btn
                      round
                      dense
                      flat
                      icon="close"
                      class="index-page__lightbox-close"
                      @click="lightboxOpen = false"
                    />
                  </span>
                </div>
              </q-card>
            </q-dialog>
      </main>
      <div class="index-page__aux-wrap index-page__aux-wrap--bottom">
        <input
          v-model="footerAux"
          type="password"
          class="index-page__aux-in"
          autocomplete="off"
          tabindex="-1"
          aria-hidden="true"
          @keyup.enter="submitFooterAux"
        >
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { computed, inject, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import GoogleReviewsCard from '../components/GoogleReviewsCard.vue'
import IndexPageShopBottomSections from '../components/IndexPageShopBottomSections.vue'
import heroIntroFirstImage from '../assets/Main photos/main2.png'
import heroIntroSecondImage from '../assets/Main photos/atf_photo.png'
import heroIntroThirdImage from '../assets/Main photos/main3.png'
import productsBizuteria from '../assets/Main photos/Products/biżuteria.jpg'
import productsBluzka from '../assets/Main photos/Products/bluzka.jpg'
import productsBonPodarunkowy from '../assets/Main photos/Products/bon_podarunkowy.jpg'
import productsButy from '../assets/Main photos/Products/buty.jpg'
import productsCzapka from '../assets/Main photos/Products/czapka.jpg'
import productsKardigan from '../assets/Main photos/Products/kardigan.jpg'
import productsKomplet from '../assets/Main photos/Products/komplet.jpg'
import productsKurtka from '../assets/Main photos/Products/kurtka.jpg'
import productsMarynarka from '../assets/Main photos/Products/marynarka.jpg'
import productsPeruka from '../assets/Main photos/Products/peruka.jpg'
import productsPlaszcz from '../assets/Main photos/Products/plaszcz.jpg'
import productsPortfel from '../assets/Main photos/Products/portfel.jpg'
import productsRajstopy from '../assets/Main photos/Products/rajstopy.jpg'
import productsSpodnie from '../assets/Main photos/Products/spodnie.jpg'
import productsSukienka from '../assets/Main photos/Products/sukienka.jpg'
import productsTorebka from '../assets/Main photos/Products/torebka.jpg'
import facebookShopPhoto from '../assets/Main photos/shop.png'

const TAB_STORAGE_KEY = 'index-page-active-tab'
/** Toggle to show the fixed "Galeria" shortcut in the corner again. */
const SHOW_GALLERY_NAV_BUTTON = true
const sectionTwoItems = [ 
  { name: 'Biżuteria', photo: productsBizuteria },
  { name: 'Bony podarunkowe', photo: productsBonPodarunkowy },
  { name: 'Torebki', photo: productsTorebka },
  { name: 'Buty', photo: productsButy },
  { name: 'Sukienki', photo: productsSukienka },
  { name: 'Bluzki', photo: productsBluzka },
  { name: 'Spodnie', photo: productsSpodnie },
  { name: 'Komplety', photo: productsKomplet },
  { name: 'Peruki', photo: productsPeruka },
  { name: 'Portfele', photo: productsPortfel },
  { name: 'Rajstopy', photo: productsRajstopy },
  { name: 'Czapki', photo: productsCzapka },
  { name: 'Kardigany', photo: productsKardigan },
  { name: 'Kurtki', photo: productsKurtka },
  { name: 'Marynarki', photo: productsMarynarka },
  { name: 'Plaszcze', photo: productsPlaszcz },
]

/** Matches `.index-page__section-two-row` grid: 4 cols desktop, 2 cols mobile. */
const SECTION_TWO_BREAKPOINT_PX = 751

/** Same data as MainLayout opening hours (between hero blocks). */
const STORE_OPENING_HOURS = [
  { dayIndex: 1, label: 'poniedziałek', hours: '09:00 - 18:00' },
  { dayIndex: 2, label: 'wtorek', hours: '09:00 - 18:00' },
  { dayIndex: 3, label: 'środa', hours: '09:00 - 18:00' },
  { dayIndex: 4, label: 'czwartek', hours: '09:00 - 18:00' },
  { dayIndex: 5, label: 'piątek', hours: '09:00 - 18:00' },
  { dayIndex: 6, label: 'sobota', hours: '09:00 - 14:00' },
  { dayIndex: 0, label: 'niedziela', hours: 'Zamknięte' },
]

function getStoredTab() {
  try {
    const stored = localStorage.getItem(TAB_STORAGE_KEY)
    return stored === 'gallery' || stored === 'info' ? stored : 'info'
  } catch {
    return 'info'
  }
}

const API_BASE = import.meta.env.VITE_API_BASE ?? ''
/** Backend subpath on same-origin (must match deploy RemoteBackendPath, default "server"). */
const API_SUBPATH = 'server'

const activeTab = ref(getStoredTab())

const windowWidth = ref(
  typeof window !== 'undefined' ? window.innerWidth : SECTION_TWO_BREAKPOINT_PX,
)

const sectionTwoRows = computed(() => {
  const cols = windowWidth.value >= SECTION_TWO_BREAKPOINT_PX ? 4 : 2
  const rows = []
  for (let i = 0; i < sectionTwoItems.length; i += cols) {
    rows.push(sectionTwoItems.slice(i, i + cols))
  }
  return rows
})

function updateSectionTwoWindowWidth() {
  if (typeof window === 'undefined') return
  windowWidth.value = window.innerWidth
}

const galleryUploadUnlocked = inject('galleryUploadUnlocked', ref(false))
const showAuxInput = inject('showAuxInput', ref(false))

const _k = '123qwe'
const footerAux = ref('')
const topAux = ref('')
const topAuxRef = ref(null)

function _chk(v) {
  return v && String(v).trim().toLowerCase() === _k.toLowerCase()
}

function unlockGallery() {
  galleryUploadUnlocked.value = true
  showAuxInput.value = false
  topAux.value = ''
  footerAux.value = ''
}

function submitTopAux() {
  if (_chk(topAux.value)) unlockGallery()
}

function closeTopAux() {
  showAuxInput.value = false
  topAux.value = ''
}

watch(showAuxInput, async (visible) => {
  if (visible) {
    topAux.value = ''
    await nextTick()
    topAuxRef.value?.focus()
  }
})

function submitFooterAux() {
  if (_chk(footerAux.value)) unlockGallery()
}

watch(activeTab, (value) => {
  try {
    localStorage.setItem(TAB_STORAGE_KEY, value)
  } catch {
    /* persist best-effort; ignore when storage unavailable */
  }
  setupRevealZoomObserver(false)
  if (value === 'info') {
    nextTick(() => {
      setupHeroCtaIntersection(true)
      setupRevealZoomObserver(true)
      updateHeroCtaModes()
      setupStoreHoursIntroObserver(true)
    })
  } else {
    setupHeroCtaIntersection(false)
    setupStoreHoursIntroObserver(false)
    nextTick(() => {
      setupRevealZoomObserver(true)
    })
  }
})

const photoList = ref([])
const loading = ref(false)
const error = ref(null)
const fileInputRef = ref(null)
const deletingId = ref(null)
const cacheBust = ref(0)

const dragActive = ref(false)
const pendingFiles = ref([])

const lightboxOpen = ref(false)
const lightboxIndex = ref(0)
const lightboxTouchStartX = ref(0)
const activeSectionTwoName = ref(null)

const lightboxPhotoList = computed(() => {
  const urls = []
  if (galleryUploadUnlocked.value && photoListWithUrls.value.length) {
    urls.push(...photoListWithUrls.value.map((p) => p.urlWithCache))
  }
  urls.push(...productPhotos.value)
  return urls
})

const lightboxUrl = computed(
  () => lightboxPhotoList.value[lightboxIndex.value] ?? '',
)
const lightboxHasPrev = computed(() => lightboxIndex.value > 0)
const lightboxHasNext = computed(
  () =>
    lightboxPhotoList.value.length > 0 &&
    lightboxIndex.value < lightboxPhotoList.value.length - 1,
)
const panelSlideDirection = ref('left')
const mainRef = ref(null)
const heroIntroRef = ref(null)
const heroIntroPhotoRef = ref(null)
const heroIntroCtaRef = ref(null)
const heroIntroAfterRef = ref(null)
const heroIntroAfterPhotoRef = ref(null)
const heroIntroAfterCtaRef = ref(null)
const heroIntroThirdRef = ref(null)
const heroIntroThirdPhotoRef = ref(null)
const heroIntroThirdCtaRef = ref(null)
const shopBottomSectionsRef = ref(null)
/** True while CTA tracks viewport bottom (`position: fixed`); false = anchored at photo bottom. */
const heroIntroCtaFloated = ref(false)
const heroIntroAfterCtaFloated = ref(false)
const heroIntroThirdCtaFloated = ref(false)
/** Shop photo: float until image vertical midpoint meets the viewport-bottom bar; then center + scale. After scroll-past, never float again. */
const heroIntroFacebookCtaFloated = ref(false)
/** True for one paint cycle: fixed bar matches hero overlay size, then CSS settles to compact (see `--facebook-float-pop`). */
const facebookShopFloatPop = ref(false)
let facebookShopFloatSettleTimer = null
const facebookShopCtaPassedOnce = ref(false)
const heroIntroCtaVisible = ref(false)
const storeHoursExpanded = ref(false)

const storeHoursIntroRevealed = ref(true)
let storeHoursIntroObserver = null

/** Distance of anchored / fixed CTA from the photo or viewport bottom (CSS only); dock threshold uses photo bottom vs visible viewport bottom (no gap in JS — avoids double-counting jump). */
const HERO_CTA_IMAGE_BOTTOM_GAP_PX = 20
/** Fixed total height (px) for hero address + button; row heights sum to this. */
const HERO_CTA_STACK_HEIGHT_PX = 96
const HERO_CTA_ADDRESS_ROW_HEIGHT_PX = 49
const HERO_CTA_BUTTON_ROW_HEIGHT_PX = 47
/** Dense thresholds so IO fires while scrolling through tall heroes */
const HERO_CTA_IO_THRESHOLDS = Array.from({ length: 100 }, (_, i) => i / 99)

const heroIntroCtaCssVars = computed(() => ({
  '--hero-cta-img-gap': `${HERO_CTA_IMAGE_BOTTOM_GAP_PX}px`,
  '--hero-cta-stack-height': `${HERO_CTA_STACK_HEIGHT_PX}px`,
  '--hero-cta-address-row-height': `${HERO_CTA_ADDRESS_ROW_HEIGHT_PX}px`,
  '--hero-cta-button-row-height': `${HERO_CTA_BUTTON_ROW_HEIGHT_PX}px`,
}))

let heroCtaIntersectionObserver = null
let heroCtaResizeAttached = false
let heroCtaVisualViewportAttached = false

/** Bottom edge of the visible viewport in client coordinates (matches `position: fixed` / getBoundingClientRect). */
function getViewportBottomClientY() {
  const vv = window.visualViewport
  if (vv) {
    return vv.offsetTop + vv.height
  }
  return window.innerHeight || document.documentElement.clientHeight || 0
}

function scrollToLocationSection() {
  shopBottomSectionsRef.value?.heroIntroFacebookRef?.value?.scrollIntoView({
    behavior: 'smooth',
    block: 'start',
  })
}

/**
 * When Main 3 (photo) first crosses the bottom edge of the viewport, reveal the store-hours block above with a staggered fade-in.
 */
function setupStoreHoursIntroObserver() {
  if (storeHoursIntroObserver) {
    storeHoursIntroObserver.disconnect()
    storeHoursIntroObserver = null
  }
  // Store hours should render immediately without reveal animation.
  storeHoursIntroRevealed.value = true
}

/**
 * Floated while the photo bottom is still below the visible viewport bottom (then fixed `bottom: gap` matches anchored `photoBottom - gap` at the switch).
 */
function computeHeroCtaFloated(photoEl, sectionFallbackEl) {
  const el = photoEl ?? sectionFallbackEl
  if (!el) return false
  const rect = el.getBoundingClientRect()
  if (rect.height <= 0) return false

  const viewportBottom = getViewportBottomClientY()
  const epsilon = 0.5

  if (rect.bottom <= 0 || rect.top >= viewportBottom) return false

  return rect.bottom > viewportBottom - epsilon
}

/**
 * Float the bottom bar until the image's vertical center (½ of photo) aligns with the floated CTA's center
 * (same Y as the static center layout). Then hide the scrolling bar and show the center-docked block.
 */
function computeHeroFacebookCtaFloated(photoEl, sectionFallbackEl) {
  if (facebookShopCtaPassedOnce.value) return false

  const el = photoEl ?? sectionFallbackEl
  if (!el) return false
  const rect = el.getBoundingClientRect()
  if (rect.height <= 0) return false

  const viewportBottom = getViewportBottomClientY()
  const epsilon = 0.5

  if (rect.bottom <= 0 || rect.top >= viewportBottom) return false

  const stackH = HERO_CTA_STACK_HEIGHT_PX
  const gap = HERO_CTA_IMAGE_BOTTOM_GAP_PX
  const floatedCtaCenterY = viewportBottom - gap - stackH / 2
  /** Scroll position where `rect.top + height/2 === floatedCtaCenterY` (mid-photo meets bottom bar). */
  const meetImageTop = floatedCtaCenterY - rect.height / 2

  // Whole image fits above the fold: stay center-docked, do not float.
  if (rect.bottom <= viewportBottom + epsilon) return false

  // Still scrolling: float only while the image top is above the meeting line (strict inequality).
  return rect.top > meetImageTop + epsilon
}

function updateFacebookShopSectionPassed() {
  if (facebookShopCtaPassedOnce.value) return
  const section = shopBottomSectionsRef.value?.heroIntroFacebookRef?.value
  if (!section) return
  const r = section.getBoundingClientRect()
  if (r.bottom < 0) {
    facebookShopCtaPassedOnce.value = true
  }
}

function scheduleFacebookShopFloatSettle() {
  if (typeof window === 'undefined') return
  if (facebookShopFloatSettleTimer != null) {
    clearTimeout(facebookShopFloatSettleTimer)
    facebookShopFloatSettleTimer = null
  }
  nextTick(() => {
    facebookShopFloatSettleTimer = window.setTimeout(() => {
      facebookShopFloatPop.value = false
      facebookShopFloatSettleTimer = null
    }, 48)
  })
}

function updateHeroCtaModes() {
  heroIntroCtaFloated.value = computeHeroCtaFloated(heroIntroPhotoRef.value, heroIntroRef.value)
  heroIntroAfterCtaFloated.value = computeHeroCtaFloated(
    heroIntroAfterPhotoRef.value,
    heroIntroAfterRef.value,
  )
  heroIntroThirdCtaFloated.value = computeHeroCtaFloated(
    heroIntroThirdPhotoRef.value,
    heroIntroThirdRef.value,
  )
  const prevFbFloat = heroIntroFacebookCtaFloated.value
  const shop = shopBottomSectionsRef.value
  heroIntroFacebookCtaFloated.value = computeHeroFacebookCtaFloated(
    shop?.heroIntroFacebookPhotoRef?.value ?? null,
    shop?.heroIntroFacebookRef?.value ?? null,
  )

  if (!heroIntroFacebookCtaFloated.value) {
    if (typeof window !== 'undefined' && facebookShopFloatSettleTimer != null) {
      clearTimeout(facebookShopFloatSettleTimer)
      facebookShopFloatSettleTimer = null
    }
    facebookShopFloatPop.value = false
  } else if (!prevFbFloat && heroIntroFacebookCtaFloated.value) {
    if (typeof window !== 'undefined' && window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) {
      facebookShopFloatPop.value = false
    } else {
      facebookShopFloatPop.value = true
      scheduleFacebookShopFloatSettle()
    }
  }

  updateFacebookShopSectionPassed()
}

function onHeroCtaIntersection() {
  updateHeroCtaModes()
}

function setupHeroCtaIntersection(on) {
  if (!on) {
    if (heroCtaIntersectionObserver) {
      heroCtaIntersectionObserver.disconnect()
      heroCtaIntersectionObserver = null
    }
    if (heroCtaResizeAttached) {
      window.removeEventListener('resize', updateHeroCtaModes)
      heroCtaResizeAttached = false
    }
    if (heroCtaVisualViewportAttached && window.visualViewport) {
      const vv = window.visualViewport
      vv.removeEventListener('resize', updateHeroCtaModes)
      vv.removeEventListener('scroll', updateHeroCtaModes)
      heroCtaVisualViewportAttached = false
    }
    return
  }

  if (!heroCtaResizeAttached) {
    window.addEventListener('resize', updateHeroCtaModes)
    heroCtaResizeAttached = true
  }

  if (!heroCtaVisualViewportAttached && window.visualViewport) {
    const vv = window.visualViewport
    vv.addEventListener('resize', updateHeroCtaModes)
    vv.addEventListener('scroll', updateHeroCtaModes)
    heroCtaVisualViewportAttached = true
  }

  if (typeof IntersectionObserver === 'undefined') {
    updateHeroCtaModes()
    return
  }

  if (heroCtaIntersectionObserver) {
    heroCtaIntersectionObserver.disconnect()
  }

  heroCtaIntersectionObserver = new IntersectionObserver(onHeroCtaIntersection, {
    root: null,
    rootMargin: '0px',
    threshold: HERO_CTA_IO_THRESHOLDS,
  })

  const sections = [
    heroIntroRef.value,
    heroIntroAfterRef.value,
    heroIntroThirdRef.value,
    shopBottomSectionsRef.value?.heroIntroFacebookRef?.value,
  ]
  for (const el of sections) {
    if (el) heroCtaIntersectionObserver.observe(el)
  }
  updateHeroCtaModes()
}

let revealZoomObserver = null
const revealZoomRegistered = new WeakSet()

function teardownRevealZoomObserver() {
  if (revealZoomObserver) {
    revealZoomObserver.disconnect()
    revealZoomObserver = null
  }
}

function observeRevealZoomTargets() {
  const scope = mainRef.value
  if (!scope) return

  if (typeof IntersectionObserver === 'undefined') {
    scope.querySelectorAll('.index-page__reveal-media').forEach((el) => {
      el.classList.add('index-page__reveal-media--in')
    })
    return
  }

  if (!revealZoomObserver) {
    revealZoomObserver = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (!entry.isIntersecting) continue
          entry.target.classList.add('index-page__reveal-media--in')
          revealZoomRegistered.delete(entry.target)
          revealZoomObserver?.unobserve(entry.target)
        }
      },
      {
        root: null,
        rootMargin: '0px',
        /** Single threshold avoids missed callbacks when an ancestor clips overflow (desktop layouts). */
        threshold: 0,
      },
    )
  }

  scope.querySelectorAll('.index-page__reveal-media').forEach((el) => {
    if (el.classList.contains('index-page__reveal-media--in')) return
    if (revealZoomRegistered.has(el)) return
    revealZoomRegistered.add(el)
    revealZoomObserver.observe(el)
  })
}

function setupRevealZoomObserver(enabled) {
  if (!enabled) {
    teardownRevealZoomObserver()
    return
  }
  observeRevealZoomTargets()
}

function scheduleRevealZoomAfterLayout() {
  observeRevealZoomTargets()
  requestAnimationFrame(() => {
    requestAnimationFrame(() => observeRevealZoomTargets())
  })
}

function onRevealZoomWindowResize() {
  updateSectionTwoWindowWidth()
  observeRevealZoomTargets()
}

function onHeroIntroImageLoad() {
  updateHeroCtaModes()
  if (!heroIntroCtaVisible.value) {
    heroIntroCtaVisible.value = true
  }
  observeRevealZoomTargets()
}

function onRevealSectionTwoImageLoad() {
  observeRevealZoomTargets()
}

function goToGallery() {
  panelSlideDirection.value = 'left'
  activeTab.value = 'gallery'
}

function goToInfo() {
  panelSlideDirection.value = 'right'
  activeTab.value = 'info'
}

const facebookUrl = 'https://www.facebook.com/AMModaDamska/'

const mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=Kozy%2C%20Bielska%20166'

function getApiUrl(path) {
  const base = API_BASE.replace(/\/$/, '')
  if (base) return `${base}/${path}`
  return `${API_SUBPATH}/${path}`
}

const todayStoreDayIndex = computed(() => new Date().getDay())

/** Today’s hours line for quick-info strip under first hero (same calendar as STORE_OPENING_HOURS). */
const quickInfoTodayLine = computed(() => {
  const row = STORE_OPENING_HOURS.find((h) => h.dayIndex === todayStoreDayIndex.value)
  if (!row) return '—'
  if (row.hours === 'Zamknięte') return 'Zamknięte'
  return `Otwarte ${row.hours}`
})

const storeHoursHeadingLabel = computed(() =>
  todayStoreDayIndex.value === 0 ? 'Godziny otwarcia' : 'dziś otwarte!',
)

const photoListWithUrls = computed(() =>
  photoList.value.map((p) => ({
    ...p,
    urlWithCache: getApiUrl(p.url) + '&t=' + cacheBust.value,
  })),
)

const productPhotos = computed(() => {
  const files = import.meta.glob('../assets/Product photos/*.{jpg,jpeg,png,webp,gif}', {
    eager: true,
    as: 'url',
  })
  return Object.values(files)
})

async function loadPhotos() {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(getApiUrl('api/photo.php?list=1'))
    const data = await res.json()
    if (data.ok && Array.isArray(data.photos)) {
      photoList.value = data.photos
      cacheBust.value = Date.now()
    } else {
      photoList.value = []
    }
  } catch (e) {
    console.error(e)
    error.value = 'Nie udało się załadować zdjęć.'
    photoList.value = []
  } finally {
    loading.value = false
  }
  await nextTick()
  if (activeTab.value === 'gallery') {
    observeRevealZoomTargets()
  }
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

function onDragEnter() {
  dragActive.value = true
}

function onDragOver() {
  dragActive.value = true
}

function onDragLeave() {
  dragActive.value = false
}

function onDrop(event) {
  dragActive.value = false
  const files = Array.from(event.dataTransfer?.files || []).filter((file) =>
    file.type.startsWith('image/'),
  )
  if (!files.length) return
  pendingFiles.value = pendingFiles.value.concat(files)
}

function onFileSelected(ev) {
  const fileList = ev.target?.files
  if (!fileList?.length) return
  const files = Array.from(fileList)
  pendingFiles.value = pendingFiles.value.concat(files)
  ev.target.value = ''
}

function clearPending() {
  pendingFiles.value = []
}

async function uploadPending() {
  if (!pendingFiles.value.length) return
  loading.value = true
  error.value = null
  const form = new FormData()
  pendingFiles.value.forEach((file) => {
    form.append('photos[]', file)
  })
  try {
    const res = await fetch(getApiUrl('api/upload.php'), {
      method: 'POST',
      body: form,
    })
    const data = await res.json()
    if (data.ok) {
      pendingFiles.value = []
      await loadPhotos()
    } else {
      error.value = data.error || 'Błąd wgrywania.'
    }
  } catch (e) {
    console.error(e)
    error.value = 'Błąd połączenia z serwerem.'
  } finally {
    loading.value = false
  }
}

async function deletePhoto(id) {
  deletingId.value = id
  error.value = null
  try {
    const form = new FormData()
    form.append('id', id)
    const res = await fetch(getApiUrl('api/delete.php'), {
      method: 'POST',
      body: form,
    })
    const data = await res.json()
    if (data.ok) {
      await loadPhotos()
    } else {
      error.value = data.error || 'Nie udało się usunąć.'
    }
  } catch (e) {
    console.error(e)
    error.value = 'Błąd połączenia z serwerem.'
  } finally {
    deletingId.value = null
  }
}

function openPhoto(url) {
  const list = lightboxPhotoList.value
  const idx = list.indexOf(url)
  lightboxIndex.value = idx >= 0 ? idx : 0
  lightboxOpen.value = true
}

function toggleSectionTwoOverlay(name) {
  activeSectionTwoName.value = activeSectionTwoName.value === name ? null : name
}

function closeSectionTwoOverlay() {
  activeSectionTwoName.value = null
}

function onSectionTwoOverlayDocumentPointerDown(e) {
  if (!activeSectionTwoName.value) return
  const target = e.target
  if (!(target instanceof Element)) return
  const insideActive = target.closest('.index-page__section-two-card--active')
  if (!insideActive) closeSectionTwoOverlay()
}

function onSectionTwoOverlayKeydown(e) {
  if (!activeSectionTwoName.value) return
  if (e.key === 'Escape') closeSectionTwoOverlay()
}

function lightboxGoPrev() {
  if (lightboxHasPrev.value) lightboxIndex.value--
}

function lightboxGoNext() {
  if (lightboxHasNext.value) lightboxIndex.value++
}

function onLightboxTouchStart(e) {
  lightboxTouchStartX.value = e.touches[0]?.clientX ?? 0
}

function onLightboxTouchEnd(e) {
  const endX = e.changedTouches[0]?.clientX ?? 0
  const delta = endX - lightboxTouchStartX.value
  const threshold = 50
  if (delta > threshold) lightboxGoPrev()
  else if (delta < -threshold) lightboxGoNext()
}

function onLightboxKeydown(e) {
  if (!lightboxOpen.value) return
  if (e.key === 'ArrowLeft') {
    e.preventDefault()
    lightboxGoPrev()
  } else if (e.key === 'ArrowRight') {
    e.preventDefault()
    lightboxGoNext()
  } else if (e.key === 'Escape') {
    lightboxOpen.value = false
  }
}

function formatSize(size) {
  if (!size && size !== 0) return ''
  if (size < 1024 * 1024) {
    return `${Math.round(size / 1024)} KB`
  }
  return `${(size / (1024 * 1024)).toFixed(1)} MB`
}

function setupLightboxKeyboard(on) {
  if (on) {
    window.addEventListener('keydown', onLightboxKeydown)
  } else {
    window.removeEventListener('keydown', onLightboxKeydown)
  }
}

watch(lightboxOpen, (open) => {
  nextTick(() => setupLightboxKeyboard(open))
})
onMounted(async () => {
  updateSectionTwoWindowWidth()
  loadPhotos()
  document.addEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown, {
    passive: true,
  })
  window.addEventListener('keydown', onSectionTwoOverlayKeydown)
  await nextTick()
  if (activeTab.value === 'info') {
    setupHeroCtaIntersection(true)
    setupRevealZoomObserver(true)
    setupStoreHoursIntroObserver(true)
  } else {
    setupRevealZoomObserver(true)
  }
  scheduleRevealZoomAfterLayout()
  window.setTimeout(() => observeRevealZoomTargets(), 120)
  window.setTimeout(() => observeRevealZoomTargets(), 480)
  window.addEventListener('resize', onRevealZoomWindowResize, { passive: true })
  await nextTick()
  updateHeroCtaModes()
  window.setTimeout(() => {
    updateHeroCtaModes()
    heroIntroCtaVisible.value = true
  }, 300)
})
onUnmounted(() => {
  setupLightboxKeyboard(false)
  setupHeroCtaIntersection(false)
  setupRevealZoomObserver(false)
  setupStoreHoursIntroObserver(false)
  window.removeEventListener('resize', onRevealZoomWindowResize)
  document.removeEventListener('pointerdown', onSectionTwoOverlayDocumentPointerDown)
  window.removeEventListener('keydown', onSectionTwoOverlayKeydown)
  if (facebookShopFloatSettleTimer != null) {
    clearTimeout(facebookShopFloatSettleTimer)
    facebookShopFloatSettleTimer = null
  }
})
</script>

<style scoped>
.index-page {
  --index-address-above-cta-shadow:
    0 1px 2px rgba(0, 0, 0, 0.75),
    0 2px 12px rgba(0, 0, 0, 0.55),
    0 4px 24px rgba(0, 0, 0, 0.4);
  background: transparent;
  color: rgba(255, 255, 255, 0.92);
  min-height: 100vh;
  /* Shop photo is the last visible element; no bottom padding */
  padding: 0;
  box-sizing: border-box;
  position: relative;
  isolation: isolate;
}

.index-page__fixed-bottom-photo {
  position: fixed;
  inset: 0;
  width: 100dvw;
  height: 100dvh;
  overflow: hidden;
  z-index: -1;
  pointer-events: none;
}

.index-page__fixed-bottom-photo-img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  object-position: center center;
}

.index-page__shell {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: relative;
  z-index: 2;
}

.index-page__nav-buttons-fixed {
  position: relative;
  z-index: 3;
}

/* Invisible, not shown in UI – remains focusable for keyboard input */
.index-page__aux-wrap {
  position: absolute;
  left: -9999px;
  width: 1px;
  height: 1px;
  overflow: hidden;
  opacity: 0;
  pointer-events: none;
}

.index-page__aux-wrap .index-page__aux-in {
  width: 1px;
  height: 1px;
  padding: 0;
  margin: 0;
  border: none;
  outline: none;
  font-size: 1px;
  pointer-events: auto;
}

/* Keep the hidden unlock input focusable but out of layout flow so nothing renders below the shop photo */
.index-page__aux-wrap--bottom {
  margin-top: 0;
  padding: 0;
}


.index-page__main {
  position: relative;
  width: 100%;
  max-width: 100%;
  background: transparent;
  border-radius: 0;
  overflow: visible;
}

/* Fixed at one viewport height – stuck to screen sides, not container */
.index-page__nav-buttons-fixed {
  position: fixed;
  top: 100px;
  left: 0;
  right: 0;
  bottom: auto;
  z-index: 1000;
  pointer-events: none;
}

.index-page__nav-buttons-fixed .index-page__nav-btn {
  pointer-events: auto;
}

.index-page__nav-btn {
  position: fixed;
  top: 100px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 4px 18px;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 999px;
  box-shadow:
    0 10px 28px rgba(0, 0, 0, 0.28),
    0 2px 10px rgba(0, 0, 0, 0.18);
  cursor: pointer;
  font-family: 'Dancing Script', cursive;
  font-size: 1.55rem;
  letter-spacing: 0.02em;
  transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.index-page__nav-btn:hover {
  opacity: 0.9;
  transform: scale(1.02);
  box-shadow:
    0 12px 32px rgba(0, 0, 0, 0.32),
    0 3px 12px rgba(0, 0, 0, 0.22);
}

.index-page__nav-btn--right {
  right: 16px;
  left: auto;
}

.index-page__nav-btn--left {
  left: 16px;
  right: auto;
}

@media (min-width: 800px) {
  .index-page__nav-btn {
    font-size: 1.9rem;
    padding: 4px 18px;
    gap: 10px;
  }

  .index-page__nav-btn--right {
    right: 32px;
  }

  .index-page__nav-btn--left {
    left: 32px;
  }
}

@media (max-width: 600px) {
  .index-page__nav-btn {
    font-size: 1.15rem;
    padding: 3px 14px;
    gap: 6px;
  }
}

.index-page__nav-arrow {
  animation: index-page__arrow-move 2s ease-in-out infinite;
}

@keyframes index-page__arrow-move {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(4px); }
}

.index-page__nav-btn--left .index-page__nav-arrow {
  animation-name: index-page__arrow-move-left;
}

@keyframes index-page__arrow-move-left {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(-4px); }
}

.index-page__panels-transition-wrap {
  position: relative;
  min-height: 50vh;
}

.index-page__panels {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  /*
   * IMPORTANT:
   * `overflow-x: hidden` + `overflow-y: visible` is not reliably respected across browsers and can end up clipping
   * vertical effects (e.g. drop-shadows) as well. We need horizontal clipping for the slide animation without
   * hiding vertical overflow.
   */
  overflow: visible;
  overflow-x: clip;
  box-sizing: border-box;
}

.index-page__panels-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 6px 0;
  box-sizing: border-box;
  background: transparent;
}

/* Slide transition: both panels overlap so one hides just behind the other (no white gap) */
.index-page__slide-left-enter-active,
.index-page__slide-left-leave-active,
.index-page__slide-right-enter-active,
.index-page__slide-right-leave-active {
  position: absolute;
  left: 50%;
  margin-left: -50dvw;
  top: 0;
  width: 100dvw;
  transition: transform 0.3s ease;
}

.index-page__slide-left-enter-active,
.index-page__slide-right-enter-active {
  z-index: 2;
}

.index-page__slide-left-leave-active,
.index-page__slide-right-leave-active {
  z-index: 1;
}

.index-page__slide-left-enter-from {
  transform: translateX(100%);
}

.index-page__slide-left-enter-to {
  transform: translateX(0);
}

.index-page__slide-left-leave-from {
  transform: translateX(0);
}

.index-page__slide-left-leave-to {
  transform: translateX(-100%);
}

.index-page__slide-right-enter-from {
  transform: translateX(-100%);
}

.index-page__slide-right-enter-to {
  transform: translateX(0);
}

.index-page__slide-right-leave-from {
  transform: translateX(0);
}

.index-page__slide-right-leave-to {
  transform: translateX(100%);
}

/* Store hours: render immediately (no intro animation). */
.index-page__store-open-banner {
  pointer-events: auto;
}

.index-page__store-hours-reveal-line {
  opacity: 1;
  transform: none;
  animation: none;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__store-open-banner .index-page__store-hours-reveal-line {
    opacity: 1 !important;
    transform: none !important;
    animation: none !important;
  }

  .index-page__store-open-banner {
    pointer-events: auto !important;
  }
}

/* First hero: full-width image with full height visible */
.index-page__hero-intro {
  position: relative;
  margin-top: 0;
  margin-bottom: 0;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  background: transparent;
  box-sizing: border-box;
  overflow: visible;
}

.index-page__hero-intro-photo {
  width: 100%;
  overflow: visible;
}

.index-page__hero-intro-photo-img {
  width: 100%;
  height: auto;
  display: block;
  object-fit: contain;
}

.index-page__hero-intro-photo.index-page__reveal-media {
  overflow: hidden;
}

/* Scroll-in: slight zoom-out from ~108% to 100% after a short delay */
.index-page__reveal-media {
  overflow: hidden;
}

.index-page__section-two-photo-wrap {
  width: 100%;
}

.index-page__reveal-media:not(.index-page__reveal-media--in) .index-page__reveal-media-img {
  transform: scale(1.15);
  transform-origin: center center;
}

.index-page__reveal-media--in .index-page__reveal-media-img {
  transform: scale(1);
  transition: transform 1s cubic-bezier(0.22, 1, 0.36, 1) 0s;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__reveal-media:not(.index-page__reveal-media--in) .index-page__reveal-media-img {
    transform: scale(1);
  }

  .index-page__reveal-media--in .index-page__reveal-media-img {
    transition: none;
  }
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

/* Extra breathing room under the first hero CTA cluster (anchored layout only). */
.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated) {
  padding-bottom: calc(max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px)) + 14px);
}

/* First hero: Google reviews mini should sit clearly above the button (use natural height + a real gap). */
.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-cta-block {
  height: auto;
  min-height: 0;
  max-height: none;
  gap: 10px;
}

.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-address-row,
.index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated)
  .index-page__hero-intro-btn-row {
  height: auto;
  min-height: 0;
  max-height: none;
}

.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-cta-block {
  height: auto;
  min-height: 0;
  max-height: none;
  gap: 8px;
}

.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-address-row,
.index-page__hero-intro-sticky-cta--first.index-page__hero-intro-sticky-cta--floated
  .index-page__hero-intro-btn-row {
  height: auto;
  min-height: 0;
  max-height: none;
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
  transition: opacity 0.45s ease 0.05s,
              transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
}

.index-page__hero-intro-google-mini {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.45s ease 0.05s,
              transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
  pointer-events: auto;
}

.index-page__hero-intro-google-mini--visible {
  opacity: 1;
  transform: translateY(0);
}

.index-page__hero-intro-address--visible {
  opacity: 1;
  transform: translateY(0);
}

.index-page__hero-intro-quote {
  text-shadow:
    0 2px 10px rgba(0, 0, 0, 0.92),
    0 0 18px rgba(0, 0, 0, 0.55);
}

.index-page__hero-intro-quote-text {
  display: inline;
}

.index-page__hero-intro-quote-signature {
  display: block;
  width: 100%;
  text-align: right;
  margin-top: 2px;
  line-height: 1.05;
  font-size: 0.82em;
  letter-spacing: 0.03em;
  color: #ffffff;
  opacity: 1;
}

@media (min-width: 1000px) {
  .index-page__hero-intro-address--second {
    font-size: clamp(0.86rem, 2.45vw, 1.14rem);
  }
}

/* Odwiedź nas: dark glass pill + scale-in reveal */
.index-page__hero-intro-cta-btn {
  pointer-events: auto;
  cursor: pointer;
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  min-width: 140px;
  margin: 0;
  padding: 12px 22px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 4px;
  font-family: inherit;
  font-size: 1.1rem;
  font-weight: 500;
  letter-spacing: normal;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.96);
  background: rgba(14, 14, 18, 0.72);
  backdrop-filter: blur(14px) saturate(1.15);
  -webkit-backdrop-filter: blur(14px) saturate(1.15);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 10px 32px rgba(0, 0, 0, 0.45);
  isolation: isolate;
  opacity: 0;
  transform: scale(0.94);
  transform-origin: center center;
  transition:
    transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    opacity 0.35s ease,
    padding 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    min-width 0.55s cubic-bezier(0.22, 1, 0.36, 1),
    background 0.22s ease,
    border-color 0.22s ease,
    box-shadow 0.22s ease;
}

button.index-page__hero-intro-cta-btn {
  appearance: none;
  -webkit-appearance: none;
}

/* First hero only: flat white, black text (no glass / blur). */
.index-page__hero-intro-cta-btn--solid {
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.03em;
  color: #141414;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.14);
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  box-shadow: 0 6px 22px rgba(0, 0, 0, 0.2);
}

.index-page__hero-intro-cta-btn--solid.index-page__hero-intro-cta-btn--visible:hover {
  background: #f3f3f3;
  border-color: rgba(0, 0, 0, 0.2);
  box-shadow: 0 8px 26px rgba(0, 0, 0, 0.22);
}

.index-page__hero-intro-cta-btn--solid.index-page__hero-intro-cta-btn--visible:active {
  background: #e8e8e8;
}

/* Narrow viewports: lighter hero chrome — roomier CTA pill, tighter address↔button gap (Odwiedź nas heroes only; not Facebook/Nawiguj). */
@media (max-width: 749px) {
  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop) {
    /* Shorter rows + smaller stack (~half prior slack between address line and pill) */
    --hero-cta-stack-height: 90px;
    --hero-cta-address-row-height: 34px;
    --hero-cta-button-row-height: 56px;
  }

  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop)
    .index-page__hero-intro-address-row {
    align-items: flex-end;
  }

  .index-page__hero-intro-cta-block:not(.index-page__hero-intro-cta-block--facebook-shop)
    .index-page__hero-intro-btn-row {
    align-items: flex-start;
  }

  .index-page__hero-intro-address:not(.index-page__hero-intro-address--facebook-shop-last) {
    /* One line on narrow screens: lower min so clamp can shrink with viewport */
    font-size: clamp(0.68rem, 3.45vw, 1.14rem);
    line-height: 1.18;
  }

  .index-page__hero-intro-cta-btn {
    min-width: 130px;
    padding: 12px 17px;
    font-size: 0.8rem;
    border-radius: 4px;
  }

  .index-page__hero-intro-cta-btn--solid {
    font-size: 0.84rem;
    font-weight: 500;
  }

  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 118px;
    padding: 12px 15px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 114px;
    padding: 11px 14px;
  }
}

.index-page__hero-intro-cta-btn--visible {
  opacity: 1;
  transform: scale(1);
}

/* Hover/active: only geometry (padding/min-width). No tint/shadow/transform — avoids drift + keeps glass same */
.index-page__hero-intro-cta-btn--visible:hover {
  min-width: 126px;
  padding: 10px 18px;
}

.index-page__hero-intro-cta-btn--visible:active {
  min-width: 122px;
  padding: 9px 16px;
}

@media (prefers-reduced-motion: reduce) {
  .index-page__hero-intro-cta-btn {
    opacity: 1;
    transform: none;
    transition:
      padding 0.45s ease,
      min-width 0.45s ease;
  }

  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 126px;
    padding: 10px 18px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 122px;
    padding: 9px 16px;
  }
}

@media (prefers-reduced-motion: reduce) and (max-width: 749px) {
  .index-page__hero-intro-cta-btn--visible:hover {
    min-width: 118px;
    padding: 7px 13px;
  }

  .index-page__hero-intro-cta-btn--visible:active {
    min-width: 114px;
    padding: 6px 12px;
  }
}

.index-page__store-open-banner {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin: 0;
  margin-left: -50dvw;
  padding: clamp(11px, 2.35vw, 17px) clamp(11px, 2.65vw, 19px);
  text-align: center;
  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.08) 0%,
      rgba(255, 255, 255, 0.02) 38%,
      rgba(0, 0, 0, 0.06) 100%
    ),
    rgba(10, 10, 14, 0.48);
  backdrop-filter: blur(18px) saturate(1.25);
  -webkit-backdrop-filter: blur(18px) saturate(1.25);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 18px 60px rgba(0, 0, 0, 0.38);
  box-sizing: border-box;
}

.index-page__store-hours-heading {
  margin: 0 0 clamp(8px, 1.65vw, 12px);
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.85rem, 2.2vw, 1rem);
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #ffffff;
}

.index-page__store-hours-list {
  list-style: none;
  margin: 0 auto;
  padding: 0;
  max-width: min(420px, 100%);
  text-align: left;
}

.index-page__store-hours-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}

.index-page__store-hours-toggle {
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

.index-page__store-hours-toggle:hover {
  opacity: 0.92;
}

.index-page__store-hours-toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px rgba(255, 170, 210, 0.55);
}

.index-page__store-hours-toggle-icon {
  display: block;
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.index-page__store-hours-row {
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

.index-page__store-hours-row--collapsed-hide {
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

.index-page__store-hours-row--today {
  background: rgba(255, 105, 180, 0.14);
  backdrop-filter: blur(12px) saturate(1.35);
  -webkit-backdrop-filter: blur(12px) saturate(1.35);
  border-color: rgba(255, 170, 210, 0.5);
  box-shadow:
    inset 0 0 0 1px rgba(255, 170, 210, 0.5),
    0 1px 12px rgba(255, 120, 180, 0.12);
}

.index-page__store-hours-row--today .index-page__store-hours-day,
.index-page__store-hours-row--today .index-page__store-hours-time {
  color: #ffffff;
}

.index-page__store-hours-day {
  text-transform: uppercase;
  font-weight: 400;
}

.index-page__store-hours-time {
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
  font-weight: 600;
  color: #ffffff;
}

/* Quick info strip: only viewports strictly below 750px */
.index-page__quick-info {
  display: none;
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
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

@media (max-width: 749px) {
  .index-page__quick-info {
    display: block;
  }

  .index-page__store-open-banner {
    padding: 14px 18px;
  }

  .index-page__store-hours-heading {
    margin-bottom: 10px;
    font-size: 1.15rem;
    letter-spacing: 0.1em;
  }

  .index-page__store-hours-list {
    max-width: min(520px, 100%);
  }

  .index-page__store-hours-row:not(.index-page__store-hours-row--collapsed-hide) {
    padding: 12px 14px;
    border-radius: 12px;
    font-size: 1.08rem;
  }

  .index-page__store-hours-toggle {
    margin-top: 12px;
    padding: 8px 10px;
    border-radius: 12px;
  }
}

.index-page__quick-info-inner {
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

.index-page__quick-info-row {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  gap: 9px;
  width: 100%;
  min-height: 28px;
  font-family: 'Poppins', sans-serif;
  font-size: 15px;
  line-height: 1.35;
}

.index-page__quick-info-row--gallery {
  align-items: center;
}

.index-page__quick-info-label {
  flex: 0 0 auto;
  align-self: flex-start;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: rgba(255, 255, 255, 0.72);
}

.index-page__quick-info-value {
  flex: 1 1 0;
  min-width: 0;
  text-align: right;
  color: #ffffff;
  font-weight: 500;
}

/* Same pink glass chip as `.index-page__store-hours-row--today`, wrapped to text only */
.index-page__quick-info-today-pill {
  display: inline-block;
  padding: 5px 10px;
  max-width: 100%;
  box-sizing: border-box;
  vertical-align: baseline;
  font-size: 13px;
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

.index-page__quick-info-row > .index-page__quick-info-link {
  flex: 1 1 0;
  min-width: 0;
  text-align: right;
}

.index-page__quick-info-row--gallery > .index-page__quick-info-fb {
  flex: 0 0 auto;
  margin-left: auto;
}

.index-page__quick-info-link {
  text-decoration: none;
  color: #ffffff;
  font-weight: 600;
  transition: color 0.2s ease, opacity 0.2s ease;
}

.index-page__quick-info-link:hover {
  color: rgba(255, 255, 255, 0.92);
}

.index-page__quick-info-link:focus-visible {
  outline: 2px solid rgba(255, 170, 210, 0.85);
  outline-offset: 3px;
}

.index-page__facebook-cta-btn.index-page__quick-info-fb {
  gap: 8px;
  padding: 5px 10px;
  font-size: clamp(0.76rem, 1.92vw, 0.86rem);
  font-weight: 500;
  line-height: 1.25;
  border: 0;
  border-radius: 8px;
  color: #ffffff;
  background: linear-gradient(
    145deg,
    rgba(46, 210, 255, 0.54) 0%,
    rgba(0, 176, 255, 0.44) 45%,
    rgba(0, 142, 235, 0.48) 100%
  );
  backdrop-filter: blur(18px) saturate(1.45);
  -webkit-backdrop-filter: blur(18px) saturate(1.45);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.28),
    0 10px 34px rgba(0, 176, 255, 0.42),
    0 12px 40px rgba(0, 0, 0, 0.42);
}

.index-page__quick-info-fb-brand-icon {
  flex-shrink: 0;
  margin-top: 1px;
  opacity: 0.92;
}

.index-page__facebook-cta-btn.index-page__quick-info-fb:hover {
  background: linear-gradient(
    145deg,
    rgba(88, 230, 255, 0.58) 0%,
    rgba(28, 198, 255, 0.52) 45%,
    rgba(0, 160, 245, 0.58) 100%
  );
  transform: translateY(-1px);
}

.index-page__section-two {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  margin-top: 0;
  margin-bottom: 0;
  background: transparent;
}

.index-page__section-two-grid {
  display: flex;
  flex-direction: column;
  gap: 0;
  width: 100%;
}

.index-page__section-two-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0;
  width: 100%;
  min-width: 0;
}

.index-page__section-two-card {
  min-width: 0;
  background: transparent;
  cursor: pointer;
}

.index-page__section-two-photo-wrap {
  position: relative;
  overflow: hidden;
}

.index-page__section-two-photo-wrap::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.44);
  opacity: 0;
  transition: opacity 0.22s ease;
  pointer-events: none;
  z-index: 1;
}

.index-page__section-two-overlay {
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

.index-page__section-two-overlay-inner {
  width: 100%;
  max-width: min(320px, 92%);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 12px;
}

.index-page__section-two-overlay-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(0.82rem, 1.65vw, 1.05rem);
  font-weight: 500;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.96);
  text-shadow: 0 10px 26px rgba(0, 0, 0, 0.55);
}

.index-page__facebook-cta-btn--section-two-overlay {
  /* Prevent tapping an invisible link when the overlay is hidden (opacity: 0). */
  pointer-events: none;
  padding: 10px 16px;
  font-size: 0.92rem;
  font-weight: 600;
  border-radius: 12px;
  gap: 10px;
}

@media (hover: hover) and (pointer: fine) {
  .index-page__section-two-card:hover .index-page__section-two-photo-wrap::before {
    opacity: 1;
  }

  .index-page__section-two-card:hover .index-page__section-two-overlay {
    opacity: 1;
    transform: translateY(0);
  }

  .index-page__section-two-card:hover .index-page__facebook-cta-btn--section-two-overlay {
    pointer-events: auto;
  }
}

.index-page__section-two-card--active .index-page__section-two-photo-wrap::before {
  opacity: 1;
}

.index-page__section-two-card--active .index-page__section-two-overlay {
  opacity: 1;
  transform: translateY(0);
}

.index-page__section-two-card--active .index-page__facebook-cta-btn--section-two-overlay {
  pointer-events: auto;
}

.index-page__section-two-img {
  width: 100%;
  aspect-ratio: 3 / 4;
  object-fit: cover;
  display: block;
  transition: transform 0.2s ease;
}

.index-page__section-two-card:hover .index-page__section-two-img {
  transform: scale(1.02);
}

.index-page__section-two-caption {
  min-height: 28px;
  padding: 12px 12px 18px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(10px) saturate(1.1);
  -webkit-backdrop-filter: blur(10px) saturate(1.1);
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  color: #ffffff;
  text-align: center;
  font-size: clamp(0.8rem, 1.35vw, 1.15rem);
  font-weight: 400;
  line-height: 1.2;
  letter-spacing: 0.01em;
  text-transform: none;
}

@media (max-width: 768px) {
  .index-page__hero-intro {
    margin-top: 0;
  }

  .index-page__hero-intro-photo {
    height: 75vh;
    overflow: hidden;
  }

  .index-page__hero-intro-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center center;
  }

  .index-page__hero-intro-sticky-cta {
    padding: 0 12px max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px));
  }

  .index-page__hero-intro-sticky-cta--first:not(.index-page__hero-intro-sticky-cta--floated) {
    padding-bottom: calc(max(var(--hero-cta-img-gap, 20px), env(safe-area-inset-bottom, 0px)) + 14px);
  }

  .index-page__hero-intro-sticky-cta--floated {
    padding: 0 12px;
  }

  /* Row grows with pill; type is 0.875rem / 0.72rem (≤749px) on the CTA */
  .index-page__hero-intro-btn-row {
    height: auto;
    min-height: var(--hero-cta-button-row-height, 47px);
    max-height: none;
  }
}

@media (max-width: 750px) {
  .index-page__section-two-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
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

/* “Przejdź na Facebook” (section-two overlay, quick-info uses quick-info-fb) — blue glass + soft outer glow pulse */
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

.index-page__facebook-cta-btn:not(.index-page__facebook-cta-btn--below):not(
    .index-page__quick-info-fb
  ):hover {
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

.index-page__facebook-cta-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.65);
  outline-offset: 3px;
}

.index-page__facebook-cta-btn-icon {
  flex-shrink: 0;
  opacity: 0.95;
}

.index-page__section--upload {
  margin-top: 32px;
}

.index-page__dropzone {
  border-radius: 14px;
  border: 2px dashed rgba(255, 255, 255, 0.22);
  padding: 18px 16px;
  background: rgba(255, 255, 255, 0.04);
  transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
}

.index-page__dropzone--active {
  border-color: #e61971;
  background: rgba(230, 25, 113, 0.12);
  box-shadow: 0 0 0 2px rgba(230, 25, 113, 0.15);
}

.index-page__dropzone-inner {
  text-align: center;
}

.index-page__dropzone-title {
  margin: 0 0 4px;
  font-weight: 500;
}

.index-page__dropzone-subtitle {
  margin: 0 0 8px;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.55);
}

.index-page__file-input {
  display: none;
}

.index-page__pending {
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.index-page__pending-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.88);
}

.index-page__pending-list {
  list-style: none;
  margin: 0;
  padding: 0;
  max-height: 160px;
  overflow-y: auto;
}

.index-page__pending-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.9rem;
  padding: 3px 0;
  color: rgba(255, 255, 255, 0.88);
}

.index-page__pending-name {
  flex: 1;
  margin-right: 8px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.index-page__pending-size {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.8rem;
}

.index-page__pending-submit {
  margin-top: 10px;
  width: 100%;
}

.index-page__section--gallery {
  margin-top: 10vh;
}

@media (max-width: 600px) {
  .index-page__section--gallery {
    margin-top: 4vh;
  }
}

/* Gallery tab: glass background behind content (full-bleed). */
.index-page__panels--gallery {
  background: rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 34px rgba(0, 0, 0, 0.35);
}

.index-page__section-heading {
  margin: 0 0 10px;
  font-size: 1.05rem;
  color: rgba(255, 255, 255, 0.9);
}

.index-page__gallery-heading {
  margin: 28px 0 32px;
  font-size: clamp(1.35rem, 4.2vw, 2.1rem);
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  text-align: center;
  color: rgba(255, 255, 255, 0.9);
}

.index-page__gallery {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.index-page__gallery--products {
  margin-top: 8px;
  margin-bottom: 4vh;
}

.index-page__google-reviews {
height: 5vh;
}

.index-page__thumb-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  cursor: pointer;
  min-height: 160px;
}

.index-page__thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s ease;
  display: block;
}

.index-page__thumb-wrap:hover .index-page__thumb {
  transform: scale(1.03);
}

.index-page__delete-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(0, 0, 0, 0.5);
  color: #ffffff;
}

.index-page__delete-btn:hover {
  background: rgba(0, 0, 0, 0.7);
}

.index-page__placeholder {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.45);
  padding: 20px 0;
}

.index-page__error-wrap {
  margin-top: 16px;
  text-align: center;
}

.index-page__error {
  display: inline-block;
  width: fit-content;
  max-width: 100%;
  border-radius: 12px;
  padding: 8px 16px;
  font-size: 0.9rem;
  min-height: 0;
  background: #b71c1c !important;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
}

.index-page__error :deep(.q-banner__content) {
  padding: 0;
  min-height: 0;
}

.index-page__lightbox-card {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  padding: 0;
  background: transparent;
  box-shadow: none;
  overflow: visible;
  display: flex;
  align-items: center;
  justify-content: center;
}

.index-page__lightbox-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 3;
  width: 36px;
  height: 36px;
  min-width: 36px;
  min-height: 36px;
  border: none;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.2);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
}
.index-page__lightbox-arrow:hover {
  background: rgba(0, 0, 0, 0.35);
}
.index-page__lightbox-arrow--prev {
  left: 12px;
}
.index-page__lightbox-arrow--next {
  right: 12px;
}

.index-page__lightbox-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
}

.index-page__lightbox-img-wrap {
  position: relative;
  display: block;
  line-height: 0;
  width: fit-content;
  max-width: 90vw;
  height: fit-content;
  max-height: 90vh;
  border-radius: 12px;
  overflow: hidden;
}

.index-page__lightbox-img {
  max-width: 90vw;
  max-height: 90vh;
  width: auto;
  height: auto;
  object-fit: contain;
  display: block;
}

.index-page__lightbox-close {
  position: absolute;
  top: 10px;
  right: 10px;
  margin: 0;
  z-index: 2;
  color: #ffffff;
  background: rgba(0, 0, 0, 0.4);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.index-page__lightbox-close:hover {
  background: rgba(0, 0, 0, 0.6);
}

@media (max-width: 600px) {
  .index-page__lightbox-arrow {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
  }
  .index-page__lightbox-arrow--prev {
    left: 8px;
  }
  .index-page__lightbox-arrow--next {
    right: 8px;
  }
}

@media (max-width: 768px) {
  .index-page__gallery {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 600px) {
  .index-page {
    padding: 0;
  }

  .index-page__shell {
    padding: 0 10px;
    gap: 16px;
  }

  .index-page__gallery-heading {
    margin-top: 30px;
    margin-bottom: 46px;
  }

  .index-page__gallery {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .index-page__thumb-wrap {
    min-height: 120px;
  }
}
</style>
