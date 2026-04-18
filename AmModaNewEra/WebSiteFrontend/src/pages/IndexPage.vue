<template>
  <q-page class="index-page">
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
            <section ref="heroIntroRef" class="index-page__hero-intro">
              <div class="index-page__hero-intro-photo">
                <img
                  :src="heroIntroImage"
                  alt="AM Moda"
                  class="index-page__hero-intro-photo-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :style="{ transform: `translateY(${heroIntroCtaOffsetY}px)` }"
              >
                <div ref="heroIntroCtaRef" class="index-page__hero-intro-cta-block">
                  <p
                    class="index-page__hero-intro-address"
                    :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                  >
                    Kozy, ul. Bielska 166
                  </p>
                  <button
                    type="button"
                    class="index-page__hero-intro-cta-btn"
                    :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                    @click="scrollToLocationSection"
                  >
                    <span class="index-page__hero-intro-cta-label">Odwiedź nas</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="index-page__section-two" aria-label="Kategorie produktów">
              <div class="index-page__section-two-grid">
                <article
                  v-for="item in sectionTwoItems"
                  :key="item.name"
                  class="index-page__section-two-card"
                >
                  <img :src="item.photo" :alt="item.name" class="index-page__section-two-img">
                  <div class="index-page__section-two-caption">
                    {{ item.name }}
                  </div>
                </article>
              </div>
            </section>

            <section ref="heroIntroAfterRef" class="index-page__hero-intro">
              <div class="index-page__hero-intro-photo">
                <img
                  :src="heroIntroImage"
                  alt=""
                  class="index-page__hero-intro-photo-img"
                  @load="onHeroIntroImageLoad"
                >
              </div>
              <div
                class="index-page__hero-intro-sticky-cta"
                :style="{ transform: `translateY(${heroIntroAfterCtaOffsetY}px)` }"
              >
                <div ref="heroIntroAfterCtaRef" class="index-page__hero-intro-cta-block">
                  <p
                    class="index-page__hero-intro-address"
                    :class="{ 'index-page__hero-intro-address--visible': heroIntroCtaVisible }"
                  >
                    Kozy, ul. Bielska 166
                  </p>
                  <button
                    type="button"
                    class="index-page__hero-intro-cta-btn"
                    :class="{ 'index-page__hero-intro-cta-btn--visible': heroIntroCtaVisible }"
                    @click="scrollToLocationSection"
                  >
                    <span class="index-page__hero-intro-cta-label">Odwiedź nas</span>
                  </button>
                </div>
              </div>
            </section>

            <!-- 6: Location – text and button above, map below -->
            <section
              ref="locationSectionRef"
              id="mapka"
              class="index-page__split-section index-page__split-section--location"
            >
              <div class="index-page__split-content index-page__split-content--center">
                <h3 class="index-page__split-heading">Znajdziesz nas tutaj:</h3>
                <p class="index-page__split-address">
                  Kozy, ul. Bielska 166
                </p>
                <a
                  :href="mapsUrl"
                  target="_blank"
                  rel="noopener"
                  class="index-page__route-btn q-mt-sm"
                >
                  Wyznacz trasę
                  <q-icon name="open_in_new" size="16px" class="index-page__route-btn-icon" aria-hidden="true" />
                </a>
              </div>
              <div class="index-page__split-media index-page__split-media--map">
                <iframe
                  class="index-page__split-iframe"
                  :src="mapsEmbedUrl"
                  style="border:0;"
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  allowfullscreen
                />
              </div>
            </section>

            <!-- 7: Facebook – centered, single-column -->
            <section class="index-page__split-section index-page__split-section--facebook">
              <div class="index-page__facebook-block">
                <h3 class="index-page__facebook-heading">Bądź na bieżąco</h3>
                <p class="index-page__facebook-text">
                  Zapraszamy na naszą stronę na Facebooku, gdzie publikujemy aktualne zdjęcia nowych kolekcji.
                </p>
                <a
                  :href="facebookUrl"
                  target="_blank"
                  rel="noopener"
                  class="index-page__facebook-link"
                >
                  <div class="index-page__facebook-logo">
                    <span>f</span>
                  </div>
                  <span>@AMModaDamska</span>
                </a>
              </div>
            </section>
            </div>
          </div>
          <div v-else key="gallery" class="index-page__panels">
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
              <div v-if="galleryUploadUnlocked" class="index-page__gallery">
                <template v-if="photoListWithUrls.length">
                  <div
                    v-for="photo in photoListWithUrls"
                    :key="photo.id"
                    class="index-page__thumb-wrap"
                    @click="openPhoto(photo.urlWithCache)"
                  >
                    <img
                      :src="photo.urlWithCache"
                      :alt="'Zdjęcie ' + photo.id"
                      class="index-page__thumb"
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
                  class="index-page__thumb-wrap"
                  @click="openPhoto(photo)"
                >
                  <img :src="photo" alt="Produkt AM Moda Damska" class="index-page__thumb">
                </div>
              </div>
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
    <section class="index-page__brand-footer" aria-label="Logo AM Moda">
      <img :src="footerLogo" alt="AM Moda Damska" class="index-page__brand-footer-logo">
    </section>
  </q-page>
</template>

<script setup>
import { computed, inject, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import heroIntroImage from '../assets/Main photos/atf_photo.png'
import sectionTwoBony from '../assets/Main photos/section 2/bony.jpg'
import sectionTwoBut from '../assets/Main photos/section 2/but.jpg'
import sectionTwoCzapka from '../assets/Main photos/section 2/czapka.jpg'
import sectionTwoMarynarka from '../assets/Main photos/section 2/marynarka.jpg'
import sectionTwoPeruka from '../assets/Main photos/section 2/peruka.jpg'
import sectionTwoPortfel from '../assets/Main photos/section 2/portfel.jpg'
import sectionTwoRekawiczki from '../assets/Main photos/section 2/rekawiczki.jpg'
import sectionTwoSukienka from '../assets/Main photos/section 2/sukienka.jpg'
import footerLogo from '../assets/logo.png'

const TAB_STORAGE_KEY = 'index-page-active-tab'
/** Toggle to show the fixed "Galeria" shortcut in the corner again. */
const SHOW_GALLERY_NAV_BUTTON = false
const sectionTwoItems = [
  { name: 'Sukienki', photo: sectionTwoSukienka },
  { name: 'Marynarki', photo: sectionTwoMarynarka },
  { name: 'Buty', photo: sectionTwoBut },
  { name: 'Portfele', photo: sectionTwoPortfel },
  { name: 'Czapki', photo: sectionTwoCzapka },
  { name: 'Rękawiczki', photo: sectionTwoRekawiczki },
  { name: 'Bony', photo: sectionTwoBony },
  { name: 'Peruki', photo: sectionTwoPeruka },
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
  if (value === 'info') {
    nextTick(() => updateHeroIntroCtaPosition())
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
const heroIntroCtaRef = ref(null)
const heroIntroAfterRef = ref(null)
const heroIntroAfterCtaRef = ref(null)
const locationSectionRef = ref(null)
const heroIntroCtaOffsetY = ref(0)
const heroIntroAfterCtaOffsetY = ref(0)
const heroIntroCtaVisible = ref(false)

function scrollToLocationSection() {
  locationSectionRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function clampValue(value, min, max) {
  return Math.min(Math.max(value, min), max)
}

function computeHeroIntroCtaOffsetY(heroElement, ctaElement) {
  if (!heroElement || !ctaElement) return null
  const heroRect = heroElement.getBoundingClientRect()
  if (heroRect.height <= 0) return null

  const viewportHeight = window.innerHeight || document.documentElement.clientHeight
  const ctaHeight = ctaElement.offsetHeight || 47
  const edgeGap = 20
  const minOffset = edgeGap
  const maxOffset = Math.max(edgeGap, heroRect.height - ctaHeight - edgeGap)
  const viewportAnchor = viewportHeight - ctaHeight - edgeGap
  const relativeOffset = viewportAnchor - heroRect.top
  return clampValue(relativeOffset, minOffset, maxOffset)
}

function updateHeroIntroCtaPosition() {
  const top = computeHeroIntroCtaOffsetY(heroIntroRef.value, heroIntroCtaRef.value)
  if (top !== null) heroIntroCtaOffsetY.value = top
  const after = computeHeroIntroCtaOffsetY(heroIntroAfterRef.value, heroIntroAfterCtaRef.value)
  if (after !== null) heroIntroAfterCtaOffsetY.value = after
}

function setupHeroIntroCtaTracking(on) {
  if (on) {
    window.addEventListener('scroll', updateHeroIntroCtaPosition, { passive: true })
    window.addEventListener('resize', updateHeroIntroCtaPosition)
  } else {
    window.removeEventListener('scroll', updateHeroIntroCtaPosition)
    window.removeEventListener('resize', updateHeroIntroCtaPosition)
  }
}

function onHeroIntroImageLoad() {
  updateHeroIntroCtaPosition()
  if (!heroIntroCtaVisible.value) {
    heroIntroCtaVisible.value = true
  }
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
const mapsEmbedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2573.840980829468!2d19.132!3d49.830!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47169dfb8f4dbe4d%3A0x0!2sBielska%20166%2C%20Kozy!5e0!3m2!1spl!2spl!4v1700000000000'

function getApiUrl(path) {
  const base = API_BASE.replace(/\/$/, '')
  if (base) return `${base}/${path}`
  return `${API_SUBPATH}/${path}`
}

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
  loadPhotos()
  await nextTick()
  setupHeroIntroCtaTracking(true)
  updateHeroIntroCtaPosition()
  window.setTimeout(() => {
    updateHeroIntroCtaPosition()
    heroIntroCtaVisible.value = true
  }, 300)
})
onUnmounted(() => {
  setupLightboxKeyboard(false)
  setupHeroIntroCtaTracking(false)
})
</script>

<style scoped>
.index-page {
  background: rgb(0, 0, 0);
  color: rgba(255, 255, 255, 0.92);
  min-height: 100vh;
  padding: 0 0 48px;
  box-sizing: border-box;
}

.index-page__shell {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.index-page__brand-footer {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  margin-top: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgb(0, 0, 0);
}

.index-page__brand-footer-logo {
  width: min(100dvw, 1460px);
  max-height: clamp(82px, 15vw, 220px);
  object-fit: contain;
  display: block;
  /* Uniform white mark from single-color / tinted artwork */
  filter: brightness(0) invert(1);
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

.index-page__aux-wrap--bottom {
  position: static;
  left: auto;
  margin-top: 24px;
  padding: 0;
}


.index-page__main {
  position: relative;
  width: 100%;
  max-width: 100%;
  background: rgb(0, 0, 0);
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
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
  font-size: 1.55rem;
  letter-spacing: 0.02em;
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.index-page__nav-btn:hover {
  opacity: 0.9;
  transform: scale(1.02);
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
    padding: 10px 18px;
    gap: 10px;
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
  overflow: hidden;
  box-sizing: border-box;
}

.index-page__panels-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 6px 24px;
  box-sizing: border-box;
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

/* First hero: full-width image with full height visible */
.index-page__hero-intro {
  position: relative;
  margin-top: 0;
  margin-bottom: 0;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  background: rgb(0, 0, 0);
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

.index-page__hero-intro-sticky-cta {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 6;
  display: flex;
  justify-content: center;
  padding: 0 16px;
  pointer-events: none;
  will-change: transform;
}

.index-page__hero-intro-cta-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: clamp(7px, 1.5vw, 11px);
  max-width: min(92vw, 560px);
  pointer-events: none;
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
  text-shadow: 0 2px 14px rgba(0, 0, 0, 0.35);
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.45s ease 0.05s,
              transform 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
}

.index-page__hero-intro-address--visible {
  opacity: 1;
  transform: translateY(0);
}

.index-page__hero-intro-cta-btn {
  pointer-events: auto;
  cursor: pointer;
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 140px;
  min-height: 47px;
  padding: 8px 15px;
  text-decoration: none;
  font-size: 0.75rem;
  font-weight: 500;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #ffffff;
  background: transparent;
  border: none;
  isolation: isolate;
  --cta-inset-x: 0px;
  --cta-inset-y: 0px;
  opacity: 0;
  transform: scale(0.94);
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
              opacity 0.35s ease,
              --cta-inset-x 0.6s ease,
              --cta-inset-y 0.6s ease;
}

.index-page__hero-intro-cta-btn::before {
  content: '';
  position: absolute;
  inset: var(--cta-inset-y) var(--cta-inset-x);
  background: rgba(0, 0, 0, 0.58);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  border-radius: 4px;
  transition: inset 0.6s ease, background 0.2s ease, border-color 0.2s ease;
  z-index: -1;
}

.index-page__hero-intro-cta-label {
  position: relative;
  z-index: 1;
}

.index-page__hero-intro-cta-btn--visible {
  opacity: 1;
  transform: scale(1);
}

.index-page__hero-intro-cta-btn--visible:hover {
  --cta-inset-x: 5px;
  --cta-inset-y: 2px;
}

.index-page__hero-intro-cta-btn--visible:hover::before {
  background: rgba(0, 0, 0, 0.72);
  border-color: rgba(255, 255, 255, 0.9);
}

.index-page__hero-intro-cta-btn--visible:active {
  --cta-inset-x: 7px;
  --cta-inset-y: 3px;
}

.index-page__section-two {
  position: relative;
  left: 50%;
  width: 100dvw;
  margin-left: -50dvw;
  margin-top: 0;
  margin-bottom: 0;
  background: rgb(0, 0, 0);
}

.index-page__section-two-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0;
}

.index-page__section-two-card {
  min-width: 0;
  background: rgb(0, 0, 0);
}

.index-page__section-two-img {
  width: 100%;
  aspect-ratio: 3 / 4;
  object-fit: cover;
  display: block;
}

.index-page__section-two-caption {
  min-height: 28px;
  padding: 12px 12px 18px;
  background: rgb(0, 0, 0);
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
    padding: 0 12px;
  }

  .index-page__hero-intro-cta-btn {
    min-height: 40px;
    min-width: 124px;
    font-size: 0.65rem;
    padding: 6px 11px;
    letter-spacing: 0.06em;
  }
}

@media (max-width: 750px) {
  .index-page__section-two-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.index-page__split-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  align-items: center;
  min-height: 220px;
  margin-bottom: 48px;
  max-width: 100%;
  box-sizing: border-box;
}

.index-page__split-section:last-child {
  margin-bottom: 0;
}

.index-page__split-section--reverse {
  direction: rtl;
}

.index-page__split-section--reverse > * {
  direction: ltr;
}

.index-page__split-content {
  padding: 16px 0;
}

.index-page__split-content--cta {
  display: flex;
  align-items: center;
  justify-content: center;
}

.index-page__split-content--center {
  text-align: center;
}
.index-page__split-content--center .index-page__split-heading {
  font-size: 1.5rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.95);
}

.index-page__split-title {
  margin: 0;
  font-size: 1.75rem;
  font-weight: 600;
  color: inherit;
}

.index-page__split-heading {
  margin: 0 0 8px;
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.88);
}

.index-page__split-address {
  margin: 0 0 12px;
  font-size: 0.98rem;
  color: rgba(255, 255, 255, 0.92);
}

/* Route button – same shape as Facebook, glassmorphic, pink */
.index-page__route-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  text-decoration: none;
  color: #c41e5a;
  font-weight: 600;
  font-size: 1rem;
  background: rgba(196, 30, 90, 0.08);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 12px;
  border: 1px solid rgba(196, 30, 90, 0.25);
  transition: background 0.2s ease, transform 0.15s ease;
}

.index-page__route-btn:hover {
  background: rgba(196, 30, 90, 0.2);
  transform: translateY(-1px);
}

.index-page__route-btn-icon {
  flex-shrink: 0;
  opacity: 0.9;
}

.index-page__split-text {
  margin: 0;
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.75);
}

.index-page__split-media {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.5);
  min-height: 200px;
  background: rgb(0, 0, 0);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

/* Location: stacked – text and button first, map below */
.index-page__split-section--location {
  grid-template-columns: 1fr;
  min-height: 0;
  scroll-margin-top: clamp(88px, 18vw, 132px);
}

.index-page__split-section--location > .index-page__split-content {
  margin-bottom: 24px;
}

.index-page__split-section--location .index-page__split-heading {
  font-weight: 400;
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.65);
}

.index-page__split-section--location .index-page__split-address {
  font-size: 1.75rem;
  font-weight: 600;
  line-height: 1.3;
  color: rgba(255, 255, 255, 0.96);
}

.index-page__split-section--location > .index-page__split-media--map {
  max-width: 1000px;
  min-height: 420px;
  width: 100%;
  justify-self: center;
}

.index-page__split-media--map {
  min-height: 220px;
}

.index-page__split-img {
  width: 100%;
  height: 100%;
  min-height: 200px;
  object-fit: cover;
  display: block;
}

.index-page__split-iframe {
  width: 100%;
  height: 100%;
  min-height: 220px;
  display: block;
}

.index-page__split-section--location .index-page__split-iframe {
  min-height: 420px;
}

/* Facebook section: centered, card-style */
.index-page__split-section--facebook {
  grid-template-columns: 1fr;
  justify-items: center;
  text-align: center;
}
.index-page__facebook-block {
  max-width: 480px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
.index-page__facebook-heading {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.96);
}
.index-page__facebook-text {
  margin: 0;
  font-size: 1rem;
  line-height: 1.5;
  color: rgba(255, 255, 255, 0.72);
}
.index-page__facebook-link {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 14px 24px;
  text-decoration: none;
  color: #1877f2;
  font-weight: 600;
  font-size: 1.05rem;
  background: rgba(24, 119, 242, 0.08);
  border-radius: 12px;
  border: 1px solid rgba(24, 119, 242, 0.2);
  transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
}
.index-page__facebook-link:hover {
  background: rgba(24, 119, 242, 0.12);
  box-shadow: 0 4px 12px rgba(24, 119, 242, 0.15);
  transform: translateY(-1px);
}

.index-page__facebook-logo {
  width: 36px;
  height: 36px;
  border-radius: 999px;
  background: #1877f2;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 700;
  font-size: 1.4rem;
  flex-shrink: 0;
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
  margin-top: 8px;
}

.index-page__section-heading {
  margin: 0 0 10px;
  font-size: 1.05rem;
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

@media (max-width: 960px) {
  .index-page__split-section {
    grid-template-columns: 1fr;
    direction: ltr;
  }

  .index-page__split-section--reverse > * {
    direction: ltr;
  }
}

@media (max-width: 768px) {
  .index-page__gallery {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 600px) {
  .index-page {
    padding: 0 0 32px;
  }

  .index-page__shell {
    padding: 0 10px;
    gap: 16px;
  }

  .index-page__gallery {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .index-page__thumb-wrap {
    min-height: 120px;
  }

  .index-page__brand-footer {
    margin-top: 0;
    padding-top: 0;
    padding-bottom: 0;
  }
}
</style>
