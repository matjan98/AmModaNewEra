<template>
  <router-view />
</template>

<script setup>
import { nextTick, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { prefetchGoogleReviews } from './composables/useGoogleReviews.js'
import { prefetchSiteSettings } from './composables/useSiteSettings.js'
import { prefetchGalleryPhotos } from './composables/useGalleryPhotos.js'

const router = useRouter()

// Signal the prerenderer (Puppeteer) that the page is ready to be snapshotted.
// During prerender (`window.__PRERENDER__`) we first wait for the data that
// feeds the visible content + JSON-LD, so the snapshot is as complete as the
// build environment allows. In normal runtime nobody listens to the event.
onMounted(async () => {
  try {
    await router.isReady()

    if (typeof window !== 'undefined' && window.__PRERENDER__) {
      const tasks = [prefetchSiteSettings(), prefetchGoogleReviews()]
      if (router.currentRoute.value.path.startsWith('/galeria')) {
        tasks.push(prefetchGalleryPhotos())
      }
      await Promise.allSettled(tasks)
    }

    await nextTick()
  } finally {
    document.dispatchEvent(new Event('app-prerender-ready'))
  }
})
</script>
