<template>
  <q-page class="index-page">
    <!-- Fixed at one height, stuck to screen edges (not container) -->
    <div class="index-page__nav-buttons-fixed">
      <button
        v-if="activeTab === 'info'"
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
            <!-- 1: Visit us (left) | Photo (right) -->
            <section class="index-page__split-section">
              <div class="index-page__split-content">
                <h2 class="index-page__split-title">Visit us</h2>
              </div>
              <div class="index-page__split-media">
                <img v-if="mainPhotos[0]" :src="mainPhotos[0]" alt="AM Moda" class="index-page__split-img">
              </div>
            </section>

            <!-- 2: Photo (left) | Dresses (right) -->
            <section class="index-page__split-section index-page__split-section--reverse">
              <div class="index-page__split-media">
                <img v-if="mainPhotos[1]" :src="mainPhotos[1]" alt="Sukienki" class="index-page__split-img">
              </div>
              <div class="index-page__split-content">
                <h2 class="index-page__split-title">Sukienki</h2>
              </div>
            </section>

            <!-- 3: Portfele (left) | Photo (right) -->
            <section class="index-page__split-section">
              <div class="index-page__split-content">
                <h2 class="index-page__split-title">Portfele</h2>
              </div>
              <div class="index-page__split-media">
                <img v-if="mainPhotos[2]" :src="mainPhotos[2]" alt="Portfele" class="index-page__split-img">
              </div>
            </section>

            <!-- 4: Photo (left) | Tschüss! (right) -->
            <section class="index-page__split-section index-page__split-section--reverse">
              <div class="index-page__split-content">
                <h2 class="index-page__split-title">Tschüss!</h2>
              </div>
              <div class="index-page__split-media">
                <img v-if="mainPhotos[3]" :src="mainPhotos[3]" alt="AM Moda" class="index-page__split-img">
              </div>
            </section>

            <!-- 5: And many more (left) | Photo (right) -->
            <section class="index-page__split-section">
              <div class="index-page__split-content">
                <h2 class="index-page__split-title">…i wiele więcej</h2>
              </div>
              <div class="index-page__split-media">
                <img v-if="sectionFifthPhoto" :src="sectionFifthPhoto" alt="Więcej" class="index-page__split-img">
              </div>
            </section>

            <!-- 6: Location text (left) | Map (right) -->
            <section class="index-page__split-section">
              <div class="index-page__split-content">
                <h3 class="index-page__split-heading">Znajdziesz nas tutaj</h3>
                <p class="index-page__split-address">
                  Kozy<br>
                  ul. Bielska 166
                </p>
                <q-btn
                  color="primary"
                  unelevated
                  no-caps
                  class="q-mt-sm"
                  label="Wyznacz trasę"
                  :href="mapsUrl"
                  target="_blank"
                />
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

            <!-- 7: Facebook (same section style) -->
            <section class="index-page__split-section">
              <div class="index-page__split-content">
                <h3 class="index-page__split-heading">Bądź na bieżąco</h3>
                <p class="index-page__split-text">
                  Zapraszamy na naszą stronę na Facebooku, gdzie publikujemy zawsze aktualne zdjęcia nowych kolekcji.
                </p>
              </div>
              <div class="index-page__split-content index-page__split-content--cta">
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

            <q-dialog v-model="lightboxOpen">
              <q-card class="index-page__lightbox-card">
                <div class="index-page__lightbox-inner">
                  <span class="index-page__lightbox-img-wrap">
                    <img :src="lightboxUrl" alt="Podgląd zdjęcia" class="index-page__lightbox-img">
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
import { computed, inject, nextTick, onMounted, ref, watch } from 'vue'

const TAB_STORAGE_KEY = 'index-page-active-tab'

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
const lightboxUrl = ref('')
const panelSlideDirection = ref('left')
const mainRef = ref(null)

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

const mainPhotos = computed(() => {
  const files = import.meta.glob('../assets/Main photos/*.{jpg,jpeg,png,webp,gif}', {
    eager: true,
    as: 'url',
  })
  return Object.values(files)
})

const sectionFifthPhoto = computed(() => {
  const photos = mainPhotos.value
  return photos[4] ?? photos[photos.length - 1] ?? photos[0]
})

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
  lightboxUrl.value = url
  lightboxOpen.value = true
}

function formatSize(size) {
  if (!size && size !== 0) return ''
  if (size < 1024 * 1024) {
    return `${Math.round(size / 1024)} KB`
  }
  return `${(size / (1024 * 1024)).toFixed(1)} MB`
}

onMounted(async () => {
  loadPhotos()
})
</script>

<style scoped>
.index-page {
  background: #ffffff;
  min-height: 100vh;
  padding: 24px 0 48px;
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
  background: #ffffff;
  border-radius: 16px;
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
  font-family: 'Dancing Script', cursive;
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
  width: 100vw;
  margin-left: -50vw;
  overflow: hidden;
  box-sizing: border-box;
}

.index-page__panels-inner {
  max-width: 1120px;
  margin: 0 auto;
  padding: 16px 16px 24px;
  box-sizing: border-box;
}

/* Slide transition: both panels overlap so one hides just behind the other (no white gap) */
.index-page__slide-left-enter-active,
.index-page__slide-left-leave-active,
.index-page__slide-right-enter-active,
.index-page__slide-right-leave-active {
  position: absolute;
  left: 50%;
  margin-left: -50vw;
  top: 0;
  width: 100vw;
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

.index-page__split-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  align-items: center;
  min-height: 220px;
  margin-bottom: 24px;
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

.index-page__split-title {
  margin: 0;
  font-size: 1.75rem;
  font-weight: 600;
  color: inherit;
}

.index-page__split-heading {
  margin: 0 0 8px;
  font-size: 1.1rem;
}

.index-page__split-address {
  margin: 0 0 12px;
  font-size: 0.98rem;
}

.index-page__split-text {
  margin: 0;
  font-size: 0.95rem;
  color: #444444;
}

.index-page__split-media {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
  min-height: 200px;
  background: #f3f3f3;
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

.index-page__facebook-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: #1b74e4;
  font-weight: 500;
}

.index-page__facebook-logo {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  background: #1877f2;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 700;
  font-size: 1.1rem;
}

.index-page__section--upload {
  margin-top: 32px;
}

.index-page__dropzone {
  border-radius: 14px;
  border: 2px dashed rgba(0, 0, 0, 0.15);
  padding: 18px 16px;
  background: #fafafa;
  transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
}

.index-page__dropzone--active {
  border-color: #e61971;
  background: #fff0f6;
  box-shadow: 0 0 0 2px rgba(230, 25, 113, 0.1);
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
  color: #777777;
}

.index-page__file-input {
  display: none;
}

.index-page__pending {
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  background: #f6f6f6;
}

.index-page__pending-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.9rem;
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
}

.index-page__pending-name {
  flex: 1;
  margin-right: 8px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.index-page__pending-size {
  color: #777777;
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
  background: #f3f3f3;
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
  color: rgba(0, 0, 0, 0.5);
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
  max-width: 90vw;
  max-height: 90vh;
  padding: 0;
  background: transparent;
  box-shadow: none;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
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
    padding: 16px 0 32px;
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
}
</style>
