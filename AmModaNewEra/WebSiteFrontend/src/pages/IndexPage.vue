<template>
  <q-page class="index-page flex flex-center column q-gutter-md">
    <div class="index-page__gallery">
      <template v-if="photoListWithUrls.length">
        <div
          v-for="photo in photoListWithUrls"
          :key="photo.id"
          class="index-page__thumb-wrap"
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
            @click="deletePhoto(photo.id)"
          />
        </div>
      </template>
      <div v-else class="index-page__placeholder">
        <q-icon name="image" size="80px" />
        <span>Brak zdjęć</span>
      </div>
    </div>
    <div class="index-page__actions">
      <input
        ref="fileInputRef"
        type="file"
        accept="image/jpeg,image/png,image/gif,image/webp"
        multiple
        class="index-page__file-input"
        @change="onFileSelected"
      >
      <q-btn
        color="primary"
        :label="photoList.length ? 'Dodaj zdjęcia' : 'Wgraj zdjęcia'"
        icon="upload"
        @click="triggerFileInput"
      />
    </div>
    <q-spinner v-if="loading" size="2rem" />
    <q-banner v-if="error" class="index-page__error bg-negative text-white">
      {{ error }}
    </q-banner>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE ?? ''
/** Backend subpath on same-origin (must match deploy RemoteBackendPath, default "server"). */
const API_SUBPATH = 'server'
const photoList = ref([])
const loading = ref(false)
const error = ref(null)
const fileInputRef = ref(null)
const deletingId = ref(null)

function getApiUrl(path) {
  const base = API_BASE.replace(/\/$/, '')
  if (base) return `${base}/${path}`
  return `${API_SUBPATH}/${path}`
}

const cacheBust = ref(0)
const photoListWithUrls = computed(() =>
  photoList.value.map((p) => ({
    ...p,
    urlWithCache: getApiUrl(p.url) + '&t=' + cacheBust.value,
  }))
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
  } catch {
    error.value = 'Nie udało się załadować zdjęć.'
    photoList.value = []
  } finally {
    loading.value = false
  }
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

async function onFileSelected(ev) {
  const fileList = ev.target?.files
  if (!fileList?.length) return
  loading.value = true
  error.value = null
  const form = new FormData()
  for (let i = 0; i < fileList.length; i++) {
    form.append('photos[]', fileList[i])
  }
  try {
    const res = await fetch(getApiUrl('api/upload.php'), {
      method: 'POST',
      body: form,
    })
    const data = await res.json()
    if (data.ok) {
      await loadPhotos()
    } else {
      error.value = data.error || 'Błąd wgrywania.'
    }
  } catch {
    error.value = 'Błąd połączenia z serwerem.'
  } finally {
    loading.value = false
    ev.target.value = ''
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
  } catch {
    error.value = 'Błąd połączenia z serwerem.'
  } finally {
    deletingId.value = null
  }
}

onMounted(loadPhotos)
</script>

<style scoped>
.index-page__gallery {
  width: 100%;
  max-width: 600px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 0.5rem;
  border: 2px dashed rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  padding: 0.5rem;
  min-height: 160px;
}

.index-page__thumb-wrap {
  position: relative;
  aspect-ratio: 1;
  border-radius: 6px;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.05);
}

.index-page__delete-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(0, 0, 0, 0.5);
  color: white;
}

.index-page__delete-btn:hover {
  background: rgba(0, 0, 0, 0.7);
}

.index-page__thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.index-page__placeholder {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: rgba(0, 0, 0, 0.5);
}

.index-page__file-input {
  display: none;
}

.index-page__error {
  border-radius: 8px;
}
</style>
