import { ref } from 'vue'
import { apiGetJson } from '../utils/apiJson.js'

const photos = ref([])

/** @type {Promise<void> | null} */
let loadPromise = null

function applyPhotosPayload(data) {
  if (data?.ok === true && Array.isArray(data.photos)) {
    photos.value = data.photos
  } else {
    photos.value = []
  }
}

function ensureGalleryPhotosLoaded() {
  if (!loadPromise) {
    loadPromise = apiGetJson('api/photo.php?list=1').then((res) => {
      if (res.ok) {
        applyPhotosPayload(res.data)
      } else {
        if (res.error) console.error(res.error)
        photos.value = []
      }
    })
  }
  return loadPromise
}

/** Start loading gallery photos as early as possible (e.g. from prerender). */
export function prefetchGalleryPhotos() {
  return ensureGalleryPhotosLoaded()
}

/**
 * Shared gallery photos state - one API request per page session, reused by the
 * gallery panel and awaited during prerender so the snapshot can include them.
 */
export function useGalleryPhotos() {
  return {
    photos,
    prefetchGalleryPhotos,
  }
}
