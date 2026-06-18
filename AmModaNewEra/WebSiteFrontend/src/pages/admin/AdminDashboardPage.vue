<template>
  <q-page class="admin-dashboard-page">
    <div class="admin-dashboard-page__inner">
      <q-tabs
        v-model="activeTab"
        dense
        class="admin-dashboard-page__tabs"
        active-color="primary"
        indicator-color="primary"
        align="left"
        no-caps
      >
        <q-tab name="gallery" label="Galeria" />
        <q-tab name="news" label="Aktualności" />
        <q-tab name="hours" label="Godziny otwarcia" />
        <q-tab name="reviews" label="Opinie Google" />
      </q-tabs>

      <q-separator />

      <q-tab-panels v-model="activeTab" animated class="admin-dashboard-page__panels">
        <q-tab-panel name="gallery" class="admin-dashboard-page__panel">
          <div class="admin-dashboard-page__section-header">
            <h2 class="admin-dashboard-page__section-title">Galeria zdjęć</h2>
            <div class="admin-dashboard-page__section-actions">
              <q-btn
                v-if="hasSelection"
                color="negative"
                no-caps
                unelevated
                :label="`Usuń zaznaczone (${selectedCount})`"
                :loading="deletingSelected"
                :disable="uploading || savingOrder"
                @click="deleteSelectedPhotos"
              />
              <q-btn
                v-if="hasSelection"
                outline
                color="primary"
                no-caps
                label="Przenieś na górę"
                :disable="uploading || deletingSelected || savingOrder || reorderAnimating"
                @click="moveSelectedToTop"
              />
              <q-btn
                v-if="hasSelection"
                outline
                color="primary"
                no-caps
                label="Przenieś na dół"
                :disable="uploading || deletingSelected || savingOrder || reorderAnimating"
                @click="moveSelectedToBottom"
              />
              <q-btn
                color="primary"
                no-caps
                unelevated
                label="Dodaj zdjęcia"
                :loading="uploading"
                :disable="uploading"
                @click="fileInputRef?.click()"
              />
              <input
                ref="fileInputRef"
                type="file"
                accept="image/jpeg,image/png,image/gif,image/webp,image/bmp"
                multiple
                class="admin-dashboard-page__file-input"
                @change="onFilesSelected"
              >
            </div>
          </div>

          <q-checkbox
            v-if="photos.length"
            v-model="allSelected"
            label="Zaznacz wszystkie"
            class="admin-dashboard-page__select-all"
          />

          <q-linear-progress
            v-if="uploading"
            :value="uploadProgress"
            color="primary"
            stripe
            animated
            class="admin-dashboard-page__upload-progress"
          />
          <p
            v-if="uploading && uploadProgressLabel"
            class="admin-dashboard-page__upload-progress-label"
          >
            {{ uploadProgressLabel }}
          </p>

          <q-banner
            v-if="galleryMessage"
            dense
            rounded
            class="admin-dashboard-page__message"
            :class="galleryMessageOk ? 'bg-positive text-white' : 'bg-negative text-white'"
          >
            {{ galleryMessage }}
          </q-banner>

          <div ref="galleryRef">
            <VueDraggable
              v-if="photos.length"
              v-model="photos"
              class="admin-dashboard-page__gallery"
              :animation="180"
              :delay="180"
              :delay-on-touch-only="true"
              :touch-start-threshold="5"
              :force-fallback="true"
              :disabled="uploading || deletingSelected || savingOrder || reorderAnimating"
              filter=".admin-dashboard-page__select-checkbox, .admin-dashboard-page__delete-btn, .admin-dashboard-page__preview-btn"
              :prevent-on-filter="false"
              ghost-class="admin-dashboard-page__thumb-wrap--ghost"
              chosen-class="admin-dashboard-page__thumb-wrap--chosen"
              drag-class="admin-dashboard-page__thumb-wrap--drag"
              @end="onReorderEnd"
            >
              <div
                v-for="photo in photos"
                :key="photo.id"
                class="admin-dashboard-page__thumb-wrap"
                :class="{ 'admin-dashboard-page__thumb-wrap--selected': isPhotoSelected(photo.id) }"
                :data-photo-id="photo.id"
                @contextmenu.prevent
              >
                <q-checkbox
                  :model-value="isPhotoSelected(photo.id)"
                  dense
                  class="admin-dashboard-page__select-checkbox"
                  :aria-label="'Zaznacz zdjęcie ' + photo.id"
                  @update:model-value="(checked) => togglePhotoSelection(photo.id, checked)"
                  @click.stop
                />
                <img
                  :src="photo.urlResolved"
                  :alt="'Zdjęcie ' + photo.id"
                  class="admin-dashboard-page__thumb"
                  loading="lazy"
                  draggable="false"
                  @contextmenu.prevent
                  @load="onGalleryThumbLoad(photo.urlResolved, $event)"
                >
                <q-btn
                  round
                  dense
                  color="negative"
                  icon="delete"
                  class="admin-dashboard-page__delete-btn"
                  aria-label="Usuń zdjęcie"
                  :disable="uploading"
                  @click="deletePhoto(photo.id)"
                />
                <q-btn
                  round
                  dense
                  color="primary"
                  icon="visibility"
                  class="admin-dashboard-page__preview-btn"
                  aria-label="Podgląd zdjęcia"
                  @click.stop="previewPhoto(photo, $event)"
                />
              </div>
            </VueDraggable>
            <p v-else class="admin-dashboard-page__empty">Brak zdjęć w galerii.</p>
          </div>
        </q-tab-panel>

        <q-tab-panel name="news" class="admin-dashboard-page__panel">
          <h2 class="admin-dashboard-page__section-title">Aktualności</h2>

          <q-checkbox
            v-model="newsEnabled"
            label="Pokaż sekcję aktualności"
            class="admin-dashboard-page__checkbox"
          />

          <q-input
            v-model="newsText"
            type="textarea"
            outlined
            autogrow
            label="Treść aktualności"
            class="admin-dashboard-page__textarea"
          />

          <q-btn
            color="primary"
            no-caps
            unelevated
            label="Zapisz aktualności"
            :loading="savingNews"
            @click="saveNews"
          />
        </q-tab-panel>

        <q-tab-panel name="hours" class="admin-dashboard-page__panel">
          <h2 class="admin-dashboard-page__section-title">Stałe godziny otwarcia</h2>

          <div class="admin-dashboard-page__weekly-grid">
            <div
              v-for="row in weeklyHours"
              :key="row.day_index"
              class="admin-dashboard-page__weekly-row"
            >
              <span class="admin-dashboard-page__weekly-label">{{ row.label }}</span>
              <q-input
                v-model="row.hours"
                dense
                outlined
                class="admin-dashboard-page__weekly-input"
              />
            </div>
          </div>

          <q-btn
            color="primary"
            no-caps
            unelevated
            label="Zapisz godziny tygodniowe"
            class="admin-dashboard-page__save-weekly-btn"
            :loading="savingWeekly"
            @click="saveWeeklyHours"
          />

          <h3 class="admin-dashboard-page__subsection-title">Wyjątki w kalendarzu</h3>
          <p class="admin-dashboard-page__hint">
            Kliknij datę, aby ustawić wyjątkowe godziny lub zamknięcie. Kalendarz ma pierwszeństwo przed stałym harmonogramem.
          </p>

          <q-date
            v-model="selectedDate"
            minimal
            mask="YYYY-MM-DD"
            class="admin-dashboard-page__calendar"
            :events="overrideDates"
            event-color="primary"
            @update:model-value="openOverrideDialog"
          />

          <div v-if="overrides.length" class="admin-dashboard-page__override-list">
            <div
              v-for="item in overrides"
              :key="item.override_date"
              class="admin-dashboard-page__override-item"
            >
              <span>{{ formatOverrideDate(item.override_date) }} - {{ item.hours }}</span>
              <q-btn
                flat
                dense
                color="negative"
                label="Usuń"
                @click="removeOverride(item.override_date)"
              />
            </div>
          </div>
        </q-tab-panel>

        <q-tab-panel name="reviews" class="admin-dashboard-page__panel">
          <h2 class="admin-dashboard-page__section-title">Opinie Google</h2>

          <q-toggle
            v-model="reviewsAutoSync"
            label="Automatyczna synchronizacja z Google"
            :disable="savingReviewsAutoSync || syncingReviews"
            class="admin-dashboard-page__checkbox"
            @update:model-value="saveReviewsAutoSync"
          />

          <p class="admin-dashboard-page__hint">
            Gdy włączone, oceny odświeżają się automatycznie (najwyżej raz na dobę) przy ruchu na stronie.
          </p>

          <div class="admin-dashboard-page__reviews-summary">
            <div class="admin-dashboard-page__reviews-row">
              <span class="admin-dashboard-page__reviews-label">Ocena</span>
              <span class="admin-dashboard-page__reviews-value">{{ reviewsRatingDisplay }}</span>
            </div>
            <div class="admin-dashboard-page__reviews-row">
              <span class="admin-dashboard-page__reviews-label">Liczba opinii</span>
              <span class="admin-dashboard-page__reviews-value">
                {{ reviewsRatingCount === null ? '—' : reviewsRatingCount }} {{ reviewsOpinionsLabel }}
              </span>
            </div>
            <div class="admin-dashboard-page__reviews-row">
              <span class="admin-dashboard-page__reviews-label">Ostatnia synchronizacja</span>
              <span class="admin-dashboard-page__reviews-value">
                {{ reviewsUpdatedAtDisplay }}<span v-if="reviewsStale"> (nieaktualne)</span>
              </span>
            </div>
          </div>

          <q-btn
            color="primary"
            no-caps
            unelevated
            label="Synchronizuj teraz"
            class="admin-dashboard-page__save-weekly-btn"
            :loading="syncingReviews"
            :disable="savingReviewsAutoSync"
            @click="syncReviewsNow"
          />

          <q-banner
            v-if="reviewsMessage"
            dense
            rounded
            class="admin-dashboard-page__message admin-dashboard-page__reviews-message"
            :class="reviewsMessageOk ? 'bg-positive text-white' : 'bg-negative text-white'"
          >
            {{ reviewsMessage }}
          </q-banner>
        </q-tab-panel>
      </q-tab-panels>
    </div>

    <q-dialog v-model="overrideDialogOpen">
      <q-card class="admin-dashboard-page__dialog">
        <q-card-section>
          <div class="admin-dashboard-page__dialog-title">
            Wyjątek: {{ formatOverrideDate(selectedDate) }}
          </div>
        </q-card-section>

        <q-card-section>
          <q-option-group
            v-model="overrideClosed"
            :options="overrideModeOptions"
            type="radio"
          />
          <q-input
            v-if="!overrideClosed"
            v-model="overrideHours"
            dense
            outlined
            label="Godziny (np. 9:00 - 18:00)"
            class="admin-dashboard-page__override-hours-input"
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Anuluj" no-caps @click="overrideDialogOpen = false" />
          <q-btn
            color="primary"
            label="Zapisz"
            no-caps
            unelevated
            :loading="savingOverride"
            @click="saveOverride"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { apiGetJson, apiPostForm, apiPostJson, apiPutJson } from '../../utils/apiJson.js'
import { getApiUrl } from '../../utils/apiUrl.js'
import { uploadPhotosInBatches } from '../../utils/galleryBatchUpload.js'
import {
  moveItemsToBottom,
  moveItemsToTop,
} from '../../utils/galleryReorder.js'

const activeTab = ref('gallery')
const fileInputRef = ref(null)
const galleryRef = ref(null)
const reorderAnimating = ref(false)

const photos = ref([])
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadProgressLabel = ref('')
const deletingSelected = ref(false)
const savingOrder = ref(false)
const galleryMessage = ref('')
const galleryMessageOk = ref(true)
const selectedPhotoIds = ref(new Set())
const photoDims = ref(new Map())
const pswpLightbox = ref(null)

const slides = computed(() =>
  photos.value.map((photo) => {
    const dims = photoDims.value.get(photo.urlResolved)
    return {
      src: photo.urlResolved,
      width: dims?.width ?? 1600,
      height: dims?.height ?? 1600,
      alt: `Zdjęcie ${photo.id}`,
    }
  }),
)

const selectedCount = computed(() => selectedPhotoIds.value.size)
const hasSelection = computed(() => selectedCount.value > 0)

const allSelected = computed({
  get() {
    return photos.value.length > 0
      && photos.value.every((photo) => selectedPhotoIds.value.has(photo.id))
  },
  set(checked) {
    selectedPhotoIds.value = checked
      ? new Set(photos.value.map((photo) => photo.id))
      : new Set()
  },
})

const newsText = ref('')
const newsEnabled = ref(false)
const savingNews = ref(false)

const weeklyHours = ref([])
const overrides = ref([])
const savingWeekly = ref(false)

const selectedDate = ref('')
const overrideDialogOpen = ref(false)
const overrideClosed = ref(false)
const overrideHours = ref('9:00 - 18:00')
const savingOverride = ref(false)

const overrideModeOptions = [
  { label: 'Otwarte - inne godziny', value: false },
  { label: 'Zamknięte', value: true },
]

const reviewsAutoSync = ref(false)
const reviewsRating = ref(null)
const reviewsRatingCount = ref(null)
const reviewsUpdatedAt = ref(null)
const reviewsStale = ref(false)
const savingReviewsAutoSync = ref(false)
const syncingReviews = ref(false)
const reviewsMessage = ref('')
const reviewsMessageOk = ref(true)
const reviewsLoaded = ref(false)

const reviewsRatingDisplay = computed(() =>
  reviewsRating.value === null ? '—' : reviewsRating.value.toFixed(1).replace('.', ','),
)

const reviewsOpinionsLabel = computed(() => {
  const n = reviewsRatingCount.value
  if (n === null) return ''
  const abs = Math.abs(n)
  const lastTwo = abs % 100
  const last = abs % 10
  if (abs === 1) return 'opinia'
  if (last >= 2 && last <= 4 && (lastTwo < 12 || lastTwo > 14)) return 'opinie'
  return 'opinii'
})

const reviewsUpdatedAtDisplay = computed(() => {
  if (!reviewsUpdatedAt.value) return '—'
  const parsed = new Date(reviewsUpdatedAt.value.replace(' ', 'T'))
  if (Number.isNaN(parsed.getTime())) return reviewsUpdatedAt.value
  return parsed.toLocaleString('pl-PL', { dateStyle: 'medium', timeStyle: 'short' })
})

const overrideDates = computed(() => overrides.value.map((item) => item.override_date))

function formatOverrideDate(dateStr) {
  if (!dateStr) return ''
  const [year, month, day] = dateStr.split('/').length === 3
    ? dateStr.split('/')
    : dateStr.split('-')
  if (year.length === 4) {
    return `${day}.${month}.${year}`
  }
  return dateStr
}

function toApiDate(qDateValue) {
  if (!qDateValue) return ''
  if (qDateValue.includes('-')) return qDateValue
  const [year, month, day] = qDateValue.split('/')
  return `${year}-${month}-${day}`
}

function setGalleryFeedback(message, ok = true) {
  galleryMessage.value = message
  galleryMessageOk.value = ok
}

function setPhotoDims(url, width, height) {
  if (!url || !width || !height) return
  if (!Number.isFinite(width) || !Number.isFinite(height)) return
  if (width < 2 || height < 2) return
  const current = photoDims.value.get(url)
  if (current && current.width === width && current.height === height) return
  photoDims.value.set(url, { width, height })
}

function setPhotoDimsFromImgEl(url, imgEl) {
  if (!imgEl) return
  const w = imgEl.naturalWidth
  const h = imgEl.naturalHeight
  if (w && h) setPhotoDims(url, w, h)
}

function onGalleryThumbLoad(url, ev) {
  setPhotoDimsFromImgEl(url, ev?.target)
}

function previewPhoto(photo, ev) {
  const list = photos.value.map((item) => item.urlResolved)
  const idx = list.indexOf(photo.urlResolved)
  const index = idx >= 0 ? idx : 0

  const wrap = ev?.currentTarget?.closest?.('.admin-dashboard-page__thumb-wrap')
  const imgEl = wrap?.querySelector?.('img') ?? null
  setPhotoDimsFromImgEl(photo.urlResolved, imgEl)

  const lb = pswpLightbox.value
  if (!lb) return
  lb.options.dataSource = slides.value
  lb.loadAndOpen(index)
}

function onReorderEnd(event) {
  if (event && event.oldIndex === event.newIndex) {
    return
  }
  persistPhotoOrder()
}

const FLIP_DURATION = 260

function prefersReducedMotion() {
  return window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches ?? false
}

/**
 * Records the current on-screen position of every gallery thumbnail,
 * keyed by photo id. This is the "First" step of a FLIP animation.
 */
function captureThumbRects() {
  const rects = new Map()
  const root = galleryRef.value
  if (!root) {
    return rects
  }
  root.querySelectorAll('[data-photo-id]').forEach((el) => {
    rects.set(el.getAttribute('data-photo-id'), el.getBoundingClientRect())
  })
  return rects
}

/**
 * Slides every thumbnail from its previous position (firstRects) to its
 * current one using a transform transition (the "Invert" and "Play" steps
 * of FLIP). Inline styles are cleared once the transition finishes so they
 * do not interfere with subsequent drag operations.
 */
function playThumbFlip(firstRects) {
  const root = galleryRef.value
  if (!root) {
    return
  }

  const elements = [...root.querySelectorAll('[data-photo-id]')]

  elements.forEach((el) => {
    const first = firstRects.get(el.getAttribute('data-photo-id'))
    if (!first) {
      return
    }
    const last = el.getBoundingClientRect()
    const dx = first.left - last.left
    const dy = first.top - last.top
    if (dx === 0 && dy === 0) {
      return
    }
    el.style.transition = 'none'
    el.style.transform = `translate(${dx}px, ${dy}px)`
  })

  void root.offsetHeight

  requestAnimationFrame(() => {
    elements.forEach((el) => {
      if (!el.style.transform) {
        return
      }
      el.style.transition = `transform ${FLIP_DURATION}ms ease`
      el.style.transform = ''
      const clearFlipStyles = (event) => {
        if (event.target !== el || event.propertyName !== 'transform') {
          return
        }
        el.style.transition = ''
        el.removeEventListener('transitionend', clearFlipStyles)
      }
      el.addEventListener('transitionend', clearFlipStyles)
    })
  })
}

async function animateReorder(reorder) {
  if (!hasSelection.value || reorderAnimating.value) {
    return
  }

  if (prefersReducedMotion()) {
    reorder()
    persistPhotoOrder()
    return
  }

  reorderAnimating.value = true
  const firstRects = captureThumbRects()
  reorder()
  await nextTick()
  playThumbFlip(firstRects)
  setTimeout(() => {
    reorderAnimating.value = false
  }, FLIP_DURATION + 50)
  persistPhotoOrder()
}

function moveSelectedToTop() {
  animateReorder(() => {
    photos.value = moveItemsToTop(photos.value, selectedPhotoIds.value)
  })
}

function moveSelectedToBottom() {
  animateReorder(() => {
    photos.value = moveItemsToBottom(photos.value, selectedPhotoIds.value)
  })
}

async function persistPhotoOrder() {
  const order = photos.value.map((photo) => photo.id)
  savingOrder.value = true
  setGalleryFeedback('')
  try {
    const res = await apiPostJson('api/reorder.php', { order }, { credentials: 'include' })
    if (!res.ok || res.data?.ok !== true) {
      setGalleryFeedback(res.data?.error ?? 'Nie udało się zapisać kolejności.', false)
      await loadPhotos()
      return
    }
    setGalleryFeedback('Kolejność zapisana.')
  } finally {
    savingOrder.value = false
  }
}

function isPhotoSelected(id) {
  return selectedPhotoIds.value.has(id)
}

function togglePhotoSelection(id, checked) {
  const next = new Set(selectedPhotoIds.value)
  if (checked) {
    next.add(id)
  } else {
    next.delete(id)
  }
  selectedPhotoIds.value = next
}

function clearPhotoSelection() {
  selectedPhotoIds.value = new Set()
}

function resetUploadProgress() {
  uploadProgress.value = 0
  uploadProgressLabel.value = ''
}

function handleUploadProgress({ uploadedCount, totalCount, batchIndex, batchCount }) {
  uploadProgress.value = totalCount > 0 ? uploadedCount / totalCount : 0
  uploadProgressLabel.value = totalCount > 0
    ? `Wgrywanie zdjęć: ${uploadedCount}/${totalCount} (paczka ${batchIndex}/${batchCount})`
    : ''
}

function formatUploadedCountMessage(count) {
  if (count === 1) return 'Zapisano 1 zdjęcie.'
  return `Zapisano ${count} zdjęć.`
}

async function loadPhotos() {
  const res = await apiGetJson('api/photo.php?list=1')
  if (!res.ok || res.data?.ok !== true || !Array.isArray(res.data.photos)) {
    photos.value = []
    clearPhotoSelection()
    return
  }

  photos.value = res.data.photos.map((photo) => ({
    ...photo,
    urlResolved: getApiUrl(typeof photo.url === 'string' ? photo.url : ''),
  }))

  const validIds = new Set(photos.value.map((photo) => photo.id))
  selectedPhotoIds.value = new Set(
    [...selectedPhotoIds.value].filter((id) => validIds.has(id)),
  )
}

async function loadAdminSettings() {
  const res = await apiGetJson('api/admin/settings.php', { credentials: 'include' })
  if (!res.ok || res.data?.ok !== true) return

  newsText.value = res.data.news?.text ?? ''
  newsEnabled.value = Boolean(res.data.news?.enabled)
  weeklyHours.value = (res.data.weeklyHours ?? []).map((row) => ({ ...row }))
  overrides.value = res.data.overrides ?? []
}

async function onFilesSelected(event) {
  const input = event.target
  const fileList = input?.files
  if (!fileList || fileList.length === 0) return

  const files = Array.from(fileList)
  uploading.value = true
  setGalleryFeedback('')
  resetUploadProgress()

  try {
    const result = await uploadPhotosInBatches({
      files,
      postForm: (path, formData, options) => apiPostForm(path, formData, options),
      deleteForm: (path, formData) => apiPostForm(path, formData),
      onProgress: handleUploadProgress,
    })

    if (!result.ok) {
      setGalleryFeedback(result.error, false)
      await loadPhotos()
      return
    }

    setGalleryFeedback(formatUploadedCountMessage(result.uploadedCount))
    await loadPhotos()
  } finally {
    uploading.value = false
    resetUploadProgress()
    if (input) input.value = ''
  }
}

async function deletePhoto(id) {
  if (!window.confirm('Usunąć to zdjęcie?')) return

  const formData = new FormData()
  formData.append('id', id)

  const res = await apiPostForm('api/delete.php', formData)
  if (!res.ok || res.data?.ok !== true) {
    setGalleryFeedback(res.data?.error ?? 'Usuwanie nie powiodło się.', false)
    return
  }

  setGalleryFeedback('Zdjęcie usunięte.')
  togglePhotoSelection(id, false)
  await loadPhotos()
}

async function deleteSelectedPhotos() {
  const ids = [...selectedPhotoIds.value]
  if (ids.length === 0) return
  if (!window.confirm(`Usunąć ${ids.length} zaznaczonych zdjęć?`)) return

  const formData = new FormData()
  ids.forEach((id) => formData.append('ids[]', id))

  deletingSelected.value = true
  setGalleryFeedback('')

  try {
    const res = await apiPostForm('api/delete.php', formData)
    if (!res.ok || res.data?.ok !== true) {
      setGalleryFeedback(res.data?.error ?? 'Usuwanie nie powiodło się.', false)
      return
    }

    const deleted = res.data.deleted ?? ids.length
    setGalleryFeedback(
      deleted === 1 ? 'Zdjęcie usunięte.' : `Usunięto ${deleted} zdjęć.`,
    )
    clearPhotoSelection()
    await loadPhotos()
  } finally {
    deletingSelected.value = false
  }
}

async function saveNews() {
  savingNews.value = true
  try {
    const res = await apiPutJson('api/admin/settings.php', {
      news: {
        text: newsText.value,
        enabled: newsEnabled.value,
      },
    })

    if (!res.ok || res.data?.ok !== true) {
      window.alert(res.data?.error ?? 'Zapis aktualności nie powiódł się.')
      return
    }

    window.alert('Zapisano aktualności.')
  } finally {
    savingNews.value = false
  }
}

async function saveWeeklyHours() {
  savingWeekly.value = true
  try {
    const res = await apiPutJson('api/admin/settings.php', {
      weeklyHours: weeklyHours.value,
    })

    if (!res.ok || res.data?.ok !== true) {
      window.alert(res.data?.error ?? 'Zapis godzin nie powiódł się.')
      return
    }

    overrides.value = res.data.overrides ?? overrides.value
    window.alert('Zapisano godziny tygodniowe.')
  } finally {
    savingWeekly.value = false
  }
}

function openOverrideDialog(dateValue) {
  selectedDate.value = dateValue
  const apiDate = toApiDate(dateValue)
  const existing = overrides.value.find((item) => item.override_date === apiDate)

  if (existing) {
    overrideClosed.value = existing.hours === 'Zamknięte'
    overrideHours.value = existing.hours === 'Zamknięte' ? '9:00 - 18:00' : existing.hours
  } else {
    overrideClosed.value = false
    overrideHours.value = '9:00 - 18:00'
  }

  overrideDialogOpen.value = true
}

async function saveOverride() {
  const apiDate = toApiDate(selectedDate.value)
  if (!apiDate) return

  savingOverride.value = true
  try {
    const res = await apiPutJson('api/admin/settings.php', {
      overrides: [{
        override_date: apiDate,
        hours: overrideClosed.value ? 'Zamknięte' : overrideHours.value.trim(),
      }],
    })

    if (!res.ok || res.data?.ok !== true) {
      window.alert(res.data?.error ?? 'Zapis wyjątku nie powiódł się.')
      return
    }

    overrides.value = res.data.overrides ?? []
    overrideDialogOpen.value = false
  } finally {
    savingOverride.value = false
  }
}

async function removeOverride(date) {
  const res = await apiPutJson('api/admin/settings.php', {
    overrides: [{ override_date: date, delete: true }],
  })

  if (!res.ok || res.data?.ok !== true) {
    window.alert(res.data?.error ?? 'Usuwanie wyjątku nie powiodło się.')
    return
  }

  overrides.value = res.data.overrides ?? []
}

function setReviewsFeedback(message, ok = true) {
  reviewsMessage.value = message
  reviewsMessageOk.value = ok
}

function applyReviewsStatus(data) {
  if (!data) return
  if (typeof data.autoSyncEnabled === 'boolean') reviewsAutoSync.value = data.autoSyncEnabled
  const fetchedRating = Number(data.rating)
  const fetchedCount = Number(data.ratingCount)
  reviewsRating.value = Number.isFinite(fetchedRating) ? fetchedRating : null
  reviewsRatingCount.value = Number.isFinite(fetchedCount) ? fetchedCount : null
  reviewsUpdatedAt.value = typeof data.updatedAt === 'string' ? data.updatedAt : null
  reviewsStale.value = Boolean(data.stale)
}

async function loadReviewsAdmin() {
  const res = await apiGetJson('api/admin/reviews.php', { credentials: 'include' })
  if (res.data) applyReviewsStatus(res.data)
  if (!res.ok || res.data?.ok !== true) {
    setReviewsFeedback(res.data?.error ?? 'Nie udało się pobrać danych opinii.', false)
    return
  }
  reviewsLoaded.value = true
  setReviewsFeedback('')
}

async function saveReviewsAutoSync(enabled) {
  savingReviewsAutoSync.value = true
  setReviewsFeedback('')
  try {
    const res = await apiPutJson('api/admin/reviews.php', { autoSyncEnabled: enabled })
    if (!res.ok || res.data?.ok !== true) {
      reviewsAutoSync.value = !enabled
      setReviewsFeedback(res.data?.error ?? 'Nie udało się zapisać ustawienia.', false)
      return
    }
    applyReviewsStatus(res.data)
    setReviewsFeedback(enabled
      ? 'Automatyczna synchronizacja włączona.'
      : 'Automatyczna synchronizacja wyłączona.')
  } finally {
    savingReviewsAutoSync.value = false
  }
}

async function syncReviewsNow() {
  syncingReviews.value = true
  setReviewsFeedback('')
  try {
    const res = await apiPostJson('api/admin/reviews.php', {}, { credentials: 'include' })
    if (res.data) applyReviewsStatus(res.data)
    if (!res.ok || res.data?.ok !== true) {
      setReviewsFeedback(res.data?.error ?? 'Synchronizacja nie powiodła się.', false)
      return
    }
    setReviewsFeedback('Zsynchronizowano z Google.')
  } finally {
    syncingReviews.value = false
  }
}

watch(activeTab, (tab) => {
  if (tab === 'reviews' && !reviewsLoaded.value) {
    loadReviewsAdmin()
  }
})

onMounted(async () => {
  const lb = new PhotoSwipeLightbox({
    dataSource: slides.value,
    pswpModule: () => import('photoswipe'),
    showHideAnimationType: 'zoom',
    bgOpacity: 0.92,
  })
  lb.init()
  pswpLightbox.value = lb

  await Promise.all([loadPhotos(), loadAdminSettings()])
})

onUnmounted(() => {
  pswpLightbox.value?.destroy()
  pswpLightbox.value = null
})
</script>

<style scoped>
.admin-dashboard-page {
  background: #f7f8fb;
}

.admin-dashboard-page__inner {
  max-width: 1100px;
  margin: 0 auto;
  padding: 16px;
}

.admin-dashboard-page__panels {
  background: transparent;
}

.admin-dashboard-page__panel {
  padding-top: 20px;
}

.admin-dashboard-page__section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.admin-dashboard-page__section-header .admin-dashboard-page__section-title {
  margin: 0;
}

.admin-dashboard-page__section-actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.admin-dashboard-page__select-all {
  margin-bottom: 12px;
}

.admin-dashboard-page__upload-progress {
  margin-bottom: 8px;
}

.admin-dashboard-page__upload-progress-label {
  margin: 0 0 12px;
  font-size: 0.9rem;
  color: rgba(0, 0, 0, 0.62);
}

.admin-dashboard-page__section-title {
  margin: 0 0 16px;
  font-size: 1.25rem;
  font-weight: 600;
}

.admin-dashboard-page__subsection-title {
  margin: 28px 0 8px;
  font-size: 1.05rem;
  font-weight: 600;
}

.admin-dashboard-page__hint {
  margin: 0 0 12px;
  color: rgba(0, 0, 0, 0.62);
}

.admin-dashboard-page__file-input {
  display: none;
}

.admin-dashboard-page__gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 12px;
}

@media (min-width: 768px) {
  .admin-dashboard-page__gallery {
    grid-template-columns: repeat(4, 1fr);
  }
}

.admin-dashboard-page__thumb-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  background: #ddd;
  cursor: grab;
}

.admin-dashboard-page__thumb-wrap:active {
  cursor: grabbing;
}

.admin-dashboard-page__thumb-wrap--selected {
  outline: 2px solid var(--q-primary);
  outline-offset: -2px;
}

.admin-dashboard-page__thumb-wrap--ghost {
  opacity: 0.5;
}

.admin-dashboard-page__thumb-wrap--chosen {
  outline: 2px solid var(--q-primary);
  outline-offset: -2px;
}

.admin-dashboard-page__thumb-wrap--drag {
  opacity: 0.9;
}

.admin-dashboard-page__select-checkbox {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 1;
  background: rgba(255, 255, 255, 0.88);
  border-radius: 4px;
  padding: 2px 4px;
}

.admin-dashboard-page__thumb {
  width: 100%;
  display: block;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  pointer-events: none;
  -webkit-touch-callout: none;
  user-select: none;
  -webkit-user-select: none;
}

.admin-dashboard-page__delete-btn {
  position: absolute;
  top: 8px;
  right: 8px;
}

.admin-dashboard-page__preview-btn {
  position: absolute;
  bottom: 8px;
  right: 8px;
  z-index: 1;
}

.admin-dashboard-page__empty {
  color: rgba(0, 0, 0, 0.55);
}

.admin-dashboard-page__checkbox {
  margin-bottom: 12px;
}

.admin-dashboard-page__textarea {
  margin-bottom: 16px;
}

.admin-dashboard-page__weekly-grid {
  display: grid;
  gap: 10px;
  max-width: 520px;
}

.admin-dashboard-page__weekly-row {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 10px;
  align-items: center;
}

.admin-dashboard-page__weekly-label {
  text-transform: capitalize;
}

.admin-dashboard-page__save-weekly-btn {
  margin-top: 16px;
}

.admin-dashboard-page__calendar {
  max-width: 320px;
}

.admin-dashboard-page__override-list {
  margin-top: 16px;
  display: grid;
  gap: 8px;
}

.admin-dashboard-page__override-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 12px;
  border-radius: 8px;
  background: #fff;
}

.admin-dashboard-page__dialog {
  width: min(100%, 420px);
  color: rgba(0, 0, 0, 0.87);
}

.admin-dashboard-page__dialog-title {
  font-size: 1.05rem;
  font-weight: 600;
}

.admin-dashboard-page__override-hours-input {
  margin-top: 12px;
}

.admin-dashboard-page__reviews-summary {
  margin: 16px 0;
  display: grid;
  gap: 8px;
  max-width: 420px;
}

.admin-dashboard-page__reviews-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 12px;
  border-radius: 8px;
  background: #fff;
}

.admin-dashboard-page__reviews-label {
  color: rgba(0, 0, 0, 0.62);
}

.admin-dashboard-page__reviews-value {
  font-weight: 600;
}

.admin-dashboard-page__reviews-message {
  margin-top: 16px;
}
</style>
