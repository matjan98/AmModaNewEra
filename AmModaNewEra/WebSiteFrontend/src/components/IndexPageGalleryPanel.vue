<template>
  <div
    class="index-page__panels index-page-gallery-panel index-page-gallery-panel__panels index-page-gallery-panel__panels--gallery"
    :class="{ 'index-page-gallery-panel__panels--empty': !photoListWithUrls.length }"
  >
    <div class="index-page-gallery-panel__panels-inner">
      <section class="index-page-gallery-panel__section index-page-gallery-panel__section--gallery">
        <h2 class="index-page-gallery-panel__gallery-heading">Galeria</h2>
        <div class="index-page-gallery-panel__gallery">
          <template v-if="photoListWithUrls.length">
            <div
              v-for="photo in photoListWithUrls"
              :key="photo.id"
              class="index-page-gallery-panel__thumb-wrap index-page__reveal-media"
              @click="onThumbClick(photo.urlResolved, $event)"
            >
              <img
                :src="photo.urlResolved"
                :alt="'Zdjęcie ' + photo.id"
                class="index-page-gallery-panel__thumb index-page__reveal-media-img"
                loading="lazy"
                decoding="async"
                @load="onGalleryThumbLoad(photo.urlResolved, $event)"
              >
            </div>
          </template>
          <div v-else class="index-page-gallery-panel__placeholder">
            <q-icon name="image" size="80px" />
            <span>Brak zdjęć</span>
          </div>
        </div>

        <div class="index-page-gallery-panel__google-reviews-slot" aria-label="Ocena Google">
          <GoogleReviewsCard
            :with-margin="false"
            class="index-page-gallery-panel__google-reviews"
          />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'
import { computed, nextTick, onActivated, onMounted, onUnmounted, ref } from 'vue'
import { getApiUrl } from '../utils/apiUrl.js'
import { apiGetJson } from '../utils/apiJson.js'
import GoogleReviewsCard from './GoogleReviewsCard.vue'

defineOptions({ name: 'IndexPageGalleryPanel' })

const props = defineProps({
  observeRevealZoomTargets: {
    type: Function,
    default: null,
  },
})

const photoList = ref([])

const photoDims = ref(new Map())
const pswpLightbox = ref(null)

const photoListWithUrls = computed(() =>
  photoList.value.map((p) => {
    const path = typeof p.url === 'string' ? p.url : ''
    const baseUrl = getApiUrl(path)
    return {
      ...p,
      urlResolved: baseUrl,
    }
  }),
)

const slides = computed(() =>
  photoListWithUrls.value.map((photo) => {
    const dims = photoDims.value.get(photo.urlResolved)
    return {
      src: photo.urlResolved,
      width: dims?.width ?? 1600,
      height: dims?.height ?? 1600,
      alt: 'Zdjęcie',
    }
  }),
)

function setDims(url, width, height) {
  if (!url || !width || !height) return
  if (!Number.isFinite(width) || !Number.isFinite(height)) return
  if (width < 2 || height < 2) return
  const current = photoDims.value.get(url)
  if (current && current.width === width && current.height === height) return
  photoDims.value.set(url, { width, height })
}

function setDimsFromImgEl(url, imgEl) {
  if (!imgEl) return
  const w = imgEl.naturalWidth
  const h = imgEl.naturalHeight
  if (w && h) setDims(url, w, h)
}

function onGalleryThumbLoad(url, ev) {
  const imgEl = ev?.target
  setDimsFromImgEl(url, imgEl)
}

async function loadPhotos() {
  const res = await apiGetJson('api/photo.php?list=1')
  if (!res.ok && res.error) console.error(res.error)
  const data = res.ok ? res.data : null
  if (data?.ok && Array.isArray(data.photos)) {
    photoList.value = data.photos
  } else {
    photoList.value = []
  }
  await nextTick()
  props.observeRevealZoomTargets?.()
}

function onThumbClick(url, ev) {
  const list = photoListWithUrls.value.map((photo) => photo.urlResolved)
  const idx = list.indexOf(url)
  const index = idx >= 0 ? idx : 0

  const wrap = ev?.currentTarget
  const imgEl = wrap?.querySelector?.('img') ?? null
  setDimsFromImgEl(url, imgEl)

  const lb = pswpLightbox.value
  if (!lb) return
  lb.options.dataSource = slides.value
  lb.loadAndOpen(index)
}

onMounted(() => {
  loadPhotos()

  const lb = new PhotoSwipeLightbox({
    dataSource: slides.value,
    pswpModule: () => import('photoswipe'),
    showHideAnimationType: 'zoom',
    bgOpacity: 0.92,
  })
  lb.init()
  pswpLightbox.value = lb
  nextTick(() => props.observeRevealZoomTargets?.())
})

onActivated(() => {
  nextTick(() => props.observeRevealZoomTargets?.())
})

onUnmounted(() => {
  pswpLightbox.value?.destroy()
  pswpLightbox.value = null
})
</script>

<style scoped>
.index-page-gallery-panel__panels-inner {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 16px;
}

.index-page-gallery-panel__section--gallery {
  margin-top: 10vh;
  margin-bottom: 10vh;
}


.index-page-gallery-panel__panels--gallery {
  box-sizing: border-box;
  overflow-x: clip;
  border-left: 0;
  border-right: 0;
  border-radius: 0;
}

.index-page-gallery-panel__panels--empty {
  min-height: 100dvh;
}

.index-page-gallery-panel__gallery-heading {
  margin: 40px 0 32px;
  font-size: clamp(1.35rem, 4.2vw, 2.1rem);
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  text-align: center;
  color: rgba(255, 255, 255, 0.9);
  text-shadow:
    0 2px 8px rgba(0, 0, 0, 0.85),
    0 0 22px rgba(0, 0, 0, 0.55);
}

.index-page-gallery-panel__gallery {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.index-page-gallery-panel__google-reviews-slot {
  flex: 0 0 auto;
  width: 100%;
  margin-top: clamp(24px, 4vh, 40px);
  padding: 5vh 0;
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
}

.index-page-gallery-panel__google-reviews {
  flex: 0 0 auto;
  width: 100%;
  height: auto;
  align-items: center;
}

.index-page-gallery-panel__thumb-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  min-height: 160px;
}

.index-page-gallery-panel__thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.2s ease;
}

.index-page-gallery-panel__thumb-wrap:hover .index-page-gallery-panel__thumb {
  transform: scale(1.03);
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

:deep(.pswp) {
  --pswp-bg: rgba(8, 8, 12, 0.92);
  --pswp-placeholder-bg: rgba(255, 255, 255, 0.06);
  --pswp-icon-color: rgba(255, 255, 255, 0.92);
  --pswp-icon-color-secondary: rgba(255, 255, 255, 0.65);
  --pswp-icon-stroke-color: rgba(0, 0, 0, 0.55);
  --pswp-icon-stroke-width: 2px;
  --pswp-error-text-color: rgba(255, 255, 255, 0.88);
}

:deep(.pswp__button) {
  opacity: 0.92;
  transition: opacity 0.18s ease;
}

:deep(.pswp__button:hover) {
  opacity: 1;
}

@media (max-width: 999.98px) {
  .index-page-gallery-panel__section--gallery {
    margin-top: 4vh;
  }

  .index-page-gallery-panel__gallery-heading {
    margin-top: 112px;
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

