<template>
  <q-page class="index-page flex flex-center column q-gutter-md">
    <div class="index-page__photo-wrap">
      <img
        v-if="photoUrl"
        :key="photoUrl"
        :src="photoUrl"
        alt="Zdjęcie główne"
        class="index-page__photo"
      >
      <div v-else class="index-page__placeholder">
        <q-icon name="image" size="80px" />
        <span>Brak zdjęcia</span>
      </div>
    </div>
    <div class="index-page__actions">
      <input
        ref="fileInputRef"
        type="file"
        accept="image/jpeg,image/png,image/gif,image/webp"
        class="index-page__file-input"
        @change="onFileSelected"
      >
      <q-btn
        color="primary"
        :label="photoUrl ? 'Podmień zdjęcie' : 'Wgraj zdjęcie'"
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
import { ref, onMounted } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE ?? ''
const photoUrl = ref(null)
const loading = ref(false)
const error = ref(null)
const fileInputRef = ref(null)

function getApiUrl(path) {
  const base = API_BASE.replace(/\/$/, '')
  return base ? `${base}/${path}` : path
}

async function loadPhoto() {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(getApiUrl('api/photo.php'))
    const data = await res.json()
    if (data.ok && data.hasPhoto && data.url) {
      const url = getApiUrl(data.url) + '&t=' + Date.now()
      photoUrl.value = url
    } else {
      photoUrl.value = null
    }
  } catch {
    error.value = 'Nie udało się załadować zdjęcia.'
    photoUrl.value = null
  } finally {
    loading.value = false
  }
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

async function onFileSelected(ev) {
  const file = ev.target?.files?.[0]
  if (!file) return
  loading.value = true
  error.value = null
  const form = new FormData()
  form.append('photo', file)
  try {
    const res = await fetch(getApiUrl('api/upload.php'), {
      method: 'POST',
      body: form
    })
    const data = await res.json()
    if (data.ok) {
      await loadPhoto()
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

onMounted(loadPhoto)
</script>

<style scoped>
.index-page__photo-wrap {
  width: 100%;
  max-width: 400px;
  aspect-ratio: 1;
  border: 2px dashed rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.index-page__photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.index-page__placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
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
