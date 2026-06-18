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
      </q-tabs>

      <q-separator />

      <q-tab-panels v-model="activeTab" animated class="admin-dashboard-page__panels">
        <q-tab-panel name="gallery" class="admin-dashboard-page__panel">
          <div class="admin-dashboard-page__section-header">
            <h2 class="admin-dashboard-page__section-title">Galeria zdjęć</h2>
            <q-btn
              color="primary"
              no-caps
              unelevated
              label="Dodaj zdjęcia"
              :loading="uploading"
              @click="fileInputRef?.click()"
            />
            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/png,image/gif,image/webp"
              multiple
              class="admin-dashboard-page__file-input"
              @change="onFilesSelected"
            >
          </div>

          <q-banner
            v-if="galleryMessage"
            dense
            rounded
            class="admin-dashboard-page__message"
            :class="galleryMessageOk ? 'bg-positive text-white' : 'bg-negative text-white'"
          >
            {{ galleryMessage }}
          </q-banner>

          <div v-if="photos.length" class="admin-dashboard-page__gallery">
            <div
              v-for="photo in photos"
              :key="photo.id"
              class="admin-dashboard-page__thumb-wrap"
            >
              <img
                :src="photo.urlResolved"
                :alt="'Zdjęcie ' + photo.id"
                class="admin-dashboard-page__thumb"
                loading="lazy"
              >
              <q-btn
                round
                dense
                color="negative"
                icon="delete"
                class="admin-dashboard-page__delete-btn"
                aria-label="Usuń zdjęcie"
                @click="deletePhoto(photo.id)"
              />
            </div>
          </div>
          <p v-else class="admin-dashboard-page__empty">Brak zdjęć w galerii.</p>
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
import { computed, onMounted, ref } from 'vue'
import { apiGetJson, apiPostForm, apiPutJson } from '../../utils/apiJson.js'
import { getApiUrl } from '../../utils/apiUrl.js'

const activeTab = ref('gallery')
const fileInputRef = ref(null)

const photos = ref([])
const uploading = ref(false)
const galleryMessage = ref('')
const galleryMessageOk = ref(true)

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

async function loadPhotos() {
  const res = await apiGetJson('api/photo.php?list=1')
  if (!res.ok || res.data?.ok !== true || !Array.isArray(res.data.photos)) {
    photos.value = []
    return
  }

  photos.value = res.data.photos.map((photo) => ({
    ...photo,
    urlResolved: getApiUrl(typeof photo.url === 'string' ? photo.url : ''),
  }))
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

  const formData = new FormData()
  Array.from(fileList).forEach((file) => formData.append('photos[]', file))

  uploading.value = true
  setGalleryFeedback('')

  try {
    const res = await apiPostForm('api/upload.php', formData)
    if (!res.ok || res.data?.ok !== true) {
      setGalleryFeedback(res.data?.error ?? 'Upload nie powiódł się.', false)
      return
    }

    setGalleryFeedback(res.data.message ?? 'Zapisano zdjęcia.')
    await loadPhotos()
  } finally {
    uploading.value = false
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
  await loadPhotos()
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

onMounted(async () => {
  await Promise.all([loadPhotos(), loadAdminSettings()])
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

.admin-dashboard-page__thumb-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  background: #ddd;
}

.admin-dashboard-page__thumb {
  width: 100%;
  display: block;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}

.admin-dashboard-page__delete-btn {
  position: absolute;
  top: 8px;
  right: 8px;
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
}

.admin-dashboard-page__dialog-title {
  font-size: 1.05rem;
  font-weight: 600;
}

.admin-dashboard-page__override-hours-input {
  margin-top: 12px;
}
</style>
