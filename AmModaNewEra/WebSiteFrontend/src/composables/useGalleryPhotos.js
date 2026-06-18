import { ref } from 'vue'
import { apiGetJson } from '../utils/apiJson.js'

const photos = ref([])

/** @type {Promise<void> | null} */
let loadPromise = null

/** Ignores stale responses when multiple gallery fetches overlap. */
let loadGeneration = 0

function galleryListPath(cacheBust = false) {
  const suffix = cacheBust ? `&_=${Date.now()}` : ''
  return `api/photo.php?list=1${suffix}`
}

function applyPhotosPayload(data) {
  if (data?.ok === true && Array.isArray(data.photos)) {
    photos.value = data.photos
  } else {
    photos.value = []
  }
}

function loadGalleryPhotos(cacheBust = false) {
  const generation = ++loadGeneration
  return apiGetJson(galleryListPath(cacheBust)).then((res) => {
    if (generation !== loadGeneration) {
      return
    }
    if (res.ok) {
      applyPhotosPayload(res.data)
    } else {
      if (res.error) console.error(res.error)
      photos.value = []
    }
  })
}

function ensureGalleryPhotosLoaded() {
  if (!loadPromise) {
    loadPromise = loadGalleryPhotos()
  }
  return loadPromise
}

/** Start loading gallery photos as early as possible (e.g. from prerender). */
export function prefetchGalleryPhotos() {
  return ensureGalleryPhotosLoaded()
}

/**
 * Fetches the gallery list again (bypasses the in-session singleton cache).
 * Use after admin changes or when re-activating the public gallery panel.
 */
export function reloadGalleryPhotos() {
  loadPromise = loadGalleryPhotos(true)
  return loadPromise
}

/**
 * Shared gallery photos state - one API request per page session, reused by the
 * gallery panel and awaited during prerender so the snapshot can include them.
 */
export function useGalleryPhotos() {
  return {
    photos,
    prefetchGalleryPhotos,
    reloadGalleryPhotos,
  }
}
