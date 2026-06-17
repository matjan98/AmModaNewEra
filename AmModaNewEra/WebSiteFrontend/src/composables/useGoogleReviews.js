import { computed, ref } from 'vue'
import { apiGetJson } from '../utils/apiJson.js'

const FALLBACK_RATING = 4.7
const FALLBACK_RATING_COUNT = 171

const rating = ref(FALLBACK_RATING)
const ratingCount = ref(FALLBACK_RATING_COUNT)

/** @type {Promise<void> | null} */
let loadPromise = null

function applyReviewsPayload(data) {
  if (!data || data.ok !== true) return
  const fetchedRating = Number(data.rating)
  const fetchedCount = Number(data.ratingCount)
  if (Number.isFinite(fetchedRating) && fetchedRating > 0) rating.value = fetchedRating
  if (Number.isFinite(fetchedCount) && fetchedCount >= 0) ratingCount.value = fetchedCount
}

function ensureGoogleReviewsLoaded() {
  if (!loadPromise) {
    loadPromise = apiGetJson('api/reviews.php').then((res) => {
      if (res.ok) applyReviewsPayload(res.data)
    })
  }
  return loadPromise
}

/** Start loading reviews as early as possible (e.g. from IndexPage mount). */
export function prefetchGoogleReviews() {
  return ensureGoogleReviewsLoaded()
}

/**
 * Shared Google reviews state — one API request per page session.
 */
export function useGoogleReviews() {
  const ratingDisplay = computed(() => rating.value.toFixed(1).replace('.', ','))

  const starIcons = computed(() => {
    const value = rating.value
    const full = Math.floor(value)
    const fractional = value - full
    const hasHalf = fractional >= 0.25 && fractional < 0.75
    const fullCount = fractional >= 0.75 ? full + 1 : full
    const icons = []
    for (let i = 0; i < fullCount; i += 1) icons.push('star')
    if (hasHalf) icons.push('star_half')
    while (icons.length < 5) icons.push('star_border')
    return icons.slice(0, 5)
  })

  const opinionsLabel = computed(() => {
    const n = ratingCount.value
    const abs = Math.abs(n)
    const lastTwo = abs % 100
    const last = abs % 10
    if (abs === 1) return 'opinia'
    if (last >= 2 && last <= 4 && (lastTwo < 12 || lastTwo > 14)) return 'opinie'
    return 'opinii'
  })

  return {
    rating,
    ratingCount,
    ratingDisplay,
    starIcons,
    opinionsLabel,
  }
}
