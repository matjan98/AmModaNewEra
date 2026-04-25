<template>
  <div class="google-reviews-card" :class="{ 'google-reviews-card--with-margin': withMargin }" aria-label="Ocena Google">
    <div class="google-reviews-card__inner">
      <span class="google-reviews-card__brand" aria-hidden="true">
        <svg
          class="google-reviews-card__google-g"
          viewBox="0 0 48 48"
          aria-hidden="true"
          focusable="false"
        >
          <path
            fill="#FFC107"
            d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.179 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.047 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"
          />
          <path
            fill="#FF3D00"
            d="M6.306 14.691l6.571 4.819C14.655 16.108 19.01 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.047 6.053 29.268 4 24 4c-7.683 0-14.356 4.346-17.694 10.691z"
          />
          <path
            fill="#4CAF50"
            d="M24 44c5.076 0 9.773-1.948 13.31-5.122l-6.141-5.198C29.11 35.091 26.683 36 24 36c-5.158 0-9.615-3.316-11.271-7.946l-6.52 5.023C9.505 39.556 16.227 44 24 44z"
          />
          <path
            fill="#1976D2"
            d="M43.611 20.083H42V20H24v8h11.303c-.793 2.223-2.231 4.096-4.134 5.287l.003-.002 6.141 5.198C36.88 39.975 44 35 44 24c0-1.341-.138-2.65-.389-3.917z"
          />
        </svg>
      </span>

      <span class="google-reviews-card__name">A&amp;M Moda Damska</span>

      <div class="google-reviews-card__row">
        <span class="google-reviews-card__rating">{{ ratingDisplay }}</span>
        <span class="google-reviews-card__stars" aria-hidden="true">
          <q-icon
            v-for="(icon, idx) in starIcons"
            :key="idx"
            :name="icon"
            size="16px"
          />
        </span>
        <span class="google-reviews-card__count">{{ ratingCount }} {{ opinionsLabel }}</span>
        <q-icon name="info" size="16px" class="google-reviews-card__info" aria-hidden="true" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'

defineProps({
  withMargin: {
    type: Boolean,
    default: true,
  },
})

const FALLBACK_RATING = 4.7
const FALLBACK_RATING_COUNT = 171

const rating = ref(FALLBACK_RATING)
const ratingCount = ref(FALLBACK_RATING_COUNT)

const API_BASE = import.meta.env.VITE_API_BASE ?? ''
const API_SUBPATH = 'server'

function getApiUrl(path) {
  const base = API_BASE.replace(/\/$/, '')
  if (base) return `${base}/${path}`
  return `${API_SUBPATH}/${path}`
}

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

onMounted(async () => {
  try {
    const res = await fetch(getApiUrl('api/reviews.php'))
    if (!res.ok) return
    const data = await res.json()
    if (!data || data.ok !== true) return
    const fetchedRating = Number(data.rating)
    const fetchedCount = Number(data.ratingCount)
    if (Number.isFinite(fetchedRating) && fetchedRating > 0) rating.value = fetchedRating
    if (Number.isFinite(fetchedCount) && fetchedCount >= 0) ratingCount.value = fetchedCount
  } catch {
    // fallback values pozostaja
  }
})
</script>

<style scoped>
.google-reviews-card {
  display: flex;
  justify-content: center;
  width: 100%;
}

.google-reviews-card--with-margin {
  margin-bottom: 5vh;
}

.google-reviews-card__inner {
  width: fit-content;
  max-width: min(560px, 92vw);
  padding: 12px 16px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 34px rgba(0, 0, 0, 0.35);
  text-align: center;
  text-shadow:
    0 1px 1px rgba(0, 0, 0, 0.5),
    0 6px 18px rgba(0, 0, 0, 0.18);
}

.google-reviews-card__brand {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 6px;
}

.google-reviews-card__google-g {
  width: 34px;
  height: 34px;
  display: block;
}

.google-reviews-card__name {
  display: block;
  margin: 0 0 8px;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(1.15rem, 2.6vw, 1.55rem);
  font-weight: 600;
  letter-spacing: 0.02em;
  color: rgba(255, 255, 255, 0.95);
}

.google-reviews-card__row {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-family: 'Poppins', sans-serif;
  color: rgba(255, 255, 255, 0.9);
}

.google-reviews-card__rating {
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.google-reviews-card__stars {
  display: inline-flex;
  align-items: center;
  gap: 2px;
  color: rgba(255, 214, 102, 0.95);
}

.google-reviews-card__count {
  font-size: 1.05rem;
  font-weight: 500;
  color: rgba(170, 210, 255, 0.9);
}

.google-reviews-card__info {
  opacity: 0.8;
}
</style>

