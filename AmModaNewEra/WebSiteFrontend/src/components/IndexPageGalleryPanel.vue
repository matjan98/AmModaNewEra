<template>
  <div class="index-page-gallery-panel index-page-gallery-panel__panels index-page-gallery-panel__panels--gallery">
    <div class="index-page-gallery-panel__panels-inner">
      <section v-if="galleryUploadUnlocked" class="index-page-gallery-panel__section index-page-gallery-panel__section--upload">
        <div
          class="index-page-gallery-panel__dropzone"
          :class="{ 'index-page-gallery-panel__dropzone--active': dragActive }"
          @dragenter.prevent="onDragEnter"
          @dragover.prevent="onDragOver"
          @dragleave.prevent="onDragLeave"
          @drop.prevent="onDrop"
        >
          <div class="index-page-gallery-panel__dropzone-inner">
            <q-icon name="cloud_upload" size="36px" class="q-mb-sm text-primary" />
            <p class="index-page-gallery-panel__dropzone-title">
              Przeciągnij i upuść zdjęcia tutaj
            </p>
            <p class="index-page-gallery-panel__dropzone-subtitle">
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
              class="index-page-gallery-panel__file-input"
              @change="onFileSelected"
            >
          </div>
        </div>

        <div v-if="pendingFiles.length" class="index-page-gallery-panel__pending">
          <div class="index-page-gallery-panel__pending-header">
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
          <ul class="index-page-gallery-panel__pending-list">
            <li v-for="file in pendingFiles" :key="file.name" class="index-page-gallery-panel__pending-item">
              <q-icon name="image" size="18px" class="q-mr-sm" />
              <span class="index-page-gallery-panel__pending-name">{{ file.name }}</span>
              <span class="index-page-gallery-panel__pending-size">{{ formatSize(file.size) }}</span>
            </li>
          </ul>
          <q-btn
            color="primary"
            unelevated
            no-caps
            class="index-page-gallery-panel__pending-submit"
            label="Zatwierdź i wgraj zdjęcia"
            icon="cloud_upload"
            :disable="!pendingFiles.length || loading"
            @click="uploadPending"
          />
        </div>

        <q-spinner v-if="loading" size="2rem" class="q-mt-md" />
        <div v-if="error" class="index-page-gallery-panel__error-wrap">
          <q-banner class="index-page-gallery-panel__error bg-negative text-white">
            {{ error }}
          </q-banner>
        </div>
      </section>

      <section class="index-page-gallery-panel__section index-page-gallery-panel__section--gallery">
        <h2 class="index-page-gallery-panel__gallery-heading">Galeria</h2>
        <div v-if="galleryUploadUnlocked" class="index-page-gallery-panel__gallery">
          <template v-if="photoListWithUrls.length">
            <div
              v-for="photo in photoListWithUrls"
              :key="photo.id"
              class="index-page-gallery-panel__thumb-wrap index-page-gallery-panel__reveal-media"
              @click="openPhoto(photo.urlWithCache)"
            >
              <img
                :src="photo.urlWithCache"
                :alt="'Zdjęcie ' + photo.id"
                class="index-page-gallery-panel__thumb index-page-gallery-panel__reveal-media-img"
              >
              <q-btn
                round
                dense
                flat
                icon="delete"
                size="sm"
                class="index-page-gallery-panel__delete-btn"
                :disable="deletingId === photo.id"
                @click.stop="deletePhoto(photo.id)"
              />
            </div>
          </template>
          <div v-else class="index-page-gallery-panel__placeholder">
            <q-icon name="image" size="80px" />
            <span>Brak zdjęć</span>
          </div>
        </div>

        <div class="index-page-gallery-panel__gallery index-page-gallery-panel__gallery--products">
          <div
            v-for="photo in productPhotos"
            :key="photo"
            class="index-page-gallery-panel__thumb-wrap index-page-gallery-panel__reveal-media"
            @click="openPhoto(photo)"
          >
            <img
              :src="photo"
              alt="Produkt AM Moda Damska"
              class="index-page-gallery-panel__thumb index-page-gallery-panel__reveal-media-img"
            >
          </div>
        </div>

        <GoogleReviewsCard class="index-page-gallery-panel__google-reviews" />
      </section>
    </div>

    <q-dialog v-model="lightboxOpen" class="index-page-gallery-panel__lightbox-dialog">
      <q-card class="index-page-gallery-panel__lightbox-card">
        <button
          v-if="lightboxHasPrev"
          type="button"
          class="index-page-gallery-panel__lightbox-arrow index-page-gallery-panel__lightbox-arrow--prev"
          aria-label="Poprzednie zdjęcie"
          @click="lightboxGoPrev"
        >
          <q-icon name="chevron_left" size="32px" />
        </button>
        <button
          v-if="lightboxHasNext"
          type="button"
          class="index-page-gallery-panel__lightbox-arrow index-page-gallery-panel__lightbox-arrow--next"
          aria-label="Następne zdjęcie"
          @click="lightboxGoNext"
        >
          <q-icon name="chevron_right" size="30px" />
        </button>
        <div
          class="index-page-gallery-panel__lightbox-inner"
          @touchstart.passive="onLightboxTouchStart"
          @touchend.passive="onLightboxTouchEnd"
        >
          <span class="index-page-gallery-panel__lightbox-img-wrap">
            <img v-if="lightboxUrl" :src="lightboxUrl" alt="Podgląd zdjęcia" class="index-page-gallery-panel__lightbox-img">
            <q-btn
              round
              dense
              flat
              icon="close"
              class="index-page-gallery-panel__lightbox-close"
              @click="lightboxOpen = false"
            />
          </span>
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, inject, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import GoogleReviewsCard from './GoogleReviewsCard.vue'

const props = defineProps({
  observeRevealZoomTargets: {
    type: Function,
    default: null,
  },
})

const API_BASE = import.meta.env.VITE_API_BASE ?? ''
/** Backend subpath on same-origin (must match deploy RemoteBackendPath, default "server"). */
const API_SUBPATH = 'server'

const galleryUploadUnlocked = inject('galleryUploadUnlocked', ref(false))

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

const lightboxPhotoList = computed(() => {
  const urls = []
  if (galleryUploadUnlocked.value && photoListWithUrls.value.length) {
    urls.push(...photoListWithUrls.value.map((p) => p.urlWithCache))
  }
  urls.push(...productPhotos.value)
  return urls
})

const lightboxUrl = computed(() => lightboxPhotoList.value[lightboxIndex.value] ?? '')
const lightboxHasPrev = computed(() => lightboxIndex.value > 0)
const lightboxHasNext = computed(
  () =>
    lightboxPhotoList.value.length > 0 &&
    lightboxIndex.value < lightboxPhotoList.value.length - 1,
)

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
  props.observeRevealZoomTargets?.()
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

onMounted(() => {
  loadPhotos()
})

onUnmounted(() => {
  setupLightboxKeyboard(false)
})
</script>

<style scoped>
.index-page-gallery-panel__section--upload {
  margin-top: 32px;
}

.index-page-gallery-panel__dropzone {
  border-radius: 14px;
  border: 2px dashed rgba(255, 255, 255, 0.22);
  padding: 18px 16px;
  background: rgba(255, 255, 255, 0.04);
  transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
}

.index-page-gallery-panel__dropzone--active {
  border-color: #e61971;
  background: rgba(230, 25, 113, 0.12);
  box-shadow: 0 0 0 2px rgba(230, 25, 113, 0.15);
}

.index-page-gallery-panel__dropzone-inner {
  text-align: center;
}

.index-page-gallery-panel__dropzone-title {
  margin: 0 0 4px;
  font-weight: 500;
}

.index-page-gallery-panel__dropzone-subtitle {
  margin: 0 0 8px;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.55);
}

.index-page-gallery-panel__file-input {
  display: none;
}

.index-page-gallery-panel__pending {
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.index-page-gallery-panel__pending-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.88);
}

.index-page-gallery-panel__pending-list {
  list-style: none;
  margin: 0;
  padding: 0;
  max-height: 160px;
  overflow-y: auto;
}

.index-page-gallery-panel__pending-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.9rem;
  padding: 3px 0;
  color: rgba(255, 255, 255, 0.88);
}

.index-page-gallery-panel__pending-name {
  flex: 1;
  margin-right: 8px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.index-page-gallery-panel__pending-size {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.8rem;
}

.index-page-gallery-panel__pending-submit {
  margin-top: 10px;
  width: 100%;
}

.index-page-gallery-panel__section--gallery {
  margin-top: 10vh;
}

@media (max-width: 600px) {
  .index-page-gallery-panel__section--gallery {
    margin-top: 4vh;
  }
}

/* Gallery tab: glass background behind content (full-bleed). */
.index-page-gallery-panel__panels--gallery {
  background: rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 34px rgba(0, 0, 0, 0.35);
}

.index-page-gallery-panel__gallery-heading {
  margin: 28px 0 32px;
  font-size: clamp(1.35rem, 4.2vw, 2.1rem);
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  text-align: center;
  color: rgba(255, 255, 255, 0.9);
}

.index-page-gallery-panel__gallery {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.index-page-gallery-panel__gallery--products {
  margin-top: 8px;
  margin-bottom: 10vh;
}

.index-page-gallery-panel__google-reviews {
  height: 5vh;
}

.index-page-gallery-panel__thumb-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  cursor: pointer;
  min-height: 160px;
}

.index-page-gallery-panel__thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s ease;
  display: block;
}

.index-page-gallery-panel__thumb-wrap:hover .index-page-gallery-panel__thumb {
  transform: scale(1.03);
}

.index-page-gallery-panel__delete-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(0, 0, 0, 0.5);
  color: #ffffff;
}

.index-page-gallery-panel__delete-btn:hover {
  background: rgba(0, 0, 0, 0.7);
}

.index-page-gallery-panel__placeholder {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.45);
  padding: 20px 0;
}

.index-page-gallery-panel__error-wrap {
  margin-top: 16px;
  text-align: center;
}

.index-page-gallery-panel__error {
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

.index-page-gallery-panel__error :deep(.q-banner__content) {
  padding: 0;
  min-height: 0;
}

.index-page-gallery-panel__lightbox-card {
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

.index-page-gallery-panel__lightbox-arrow {
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

.index-page-gallery-panel__lightbox-arrow:hover {
  background: rgba(0, 0, 0, 0.35);
}

.index-page-gallery-panel__lightbox-arrow--prev {
  left: 12px;
}

.index-page-gallery-panel__lightbox-arrow--next {
  right: 12px;
}

.index-page-gallery-panel__lightbox-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
}

.index-page-gallery-panel__lightbox-img-wrap {
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

.index-page-gallery-panel__lightbox-img {
  max-width: 90vw;
  max-height: 90vh;
  width: auto;
  height: auto;
  object-fit: contain;
  display: block;
}

.index-page-gallery-panel__lightbox-close {
  position: absolute;
  top: 10px;
  right: 10px;
  margin: 0;
  z-index: 2;
  color: #ffffff;
  background: rgba(0, 0, 0, 0.4);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.index-page-gallery-panel__lightbox-close:hover {
  background: rgba(0, 0, 0, 0.6);
}

@media (max-width: 600px) {
  .index-page-gallery-panel__lightbox-arrow {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
  }

  .index-page-gallery-panel__lightbox-arrow--prev {
    left: 8px;
  }

  .index-page-gallery-panel__lightbox-arrow--next {
    right: 8px;
  }
}

@media (max-width: 768px) {
  .index-page-gallery-panel__gallery {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 749px) {
  .index-page-gallery-panel__gallery-heading {
    margin-top: 96px;
    margin-bottom: 28px;
  }
}

@media (max-width: 600px) {
  .index-page-gallery-panel__gallery-heading {
    margin-top: 96px;
    margin-bottom: 28px;
  }

  .index-page-gallery-panel__gallery {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .index-page-gallery-panel__thumb-wrap {
    min-height: 120px;
  }
}
</style>
