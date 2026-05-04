<template>
  <a
    class="google-reviews-card google-reviews-card__link"
    :class="{
      'google-reviews-card--with-margin': withMargin,
      'google-reviews-card--full-container': variant !== 'mini',
    }"
    aria-label="Ocena Google"
    :href="externalUrl"
    target="_blank"
    rel="noopener"
  >
    <div v-if="variant === 'mini'" class="google-reviews-card__mini">
      <div class="google-reviews-card__mini-inner">
        <span class="google-reviews-card__google-bubble" aria-hidden="true">
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

        <div class="google-reviews-card__row google-reviews-card__row--mini">
          <span class="google-reviews-card__rating google-reviews-card__rating--mini">{{ ratingDisplay }}</span>
          <span class="google-reviews-card__stars google-reviews-card__stars--mini" aria-hidden="true">
            <q-icon
              v-for="(icon, idx) in starIcons"
              :key="idx"
              :name="icon"
              size="1em"
            />
          </span>
          <span class="google-reviews-card__count google-reviews-card__count--mini">{{ ratingCount }} {{ opinionsLabel }}</span>
        </div>
      </div>
    </div>

    <div v-else class="google-reviews-card__inner">
      <q-icon
        name="open_in_new"
        size="16px"
        class="google-reviews-card__corner-indicator"
        aria-hidden="true"
      />
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

      <span class="google-reviews-card__name">{{ businessName }}</span>

      <div class="google-reviews-card__row">
        <span class="google-reviews-card__rating">{{ ratingDisplay }}</span>
        <span class="google-reviews-card__stars" aria-hidden="true">
          <q-icon
            v-for="(icon, idx) in starIcons"
            :key="idx"
            :name="icon"
            size="24px"
          />
        </span>
        <span class="google-reviews-card__count">{{ ratingCount }} {{ opinionsLabel }}</span>
      </div>
    </div>
  </a>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { apiGetJson } from '../utils/apiJson.js'

defineProps({
  withMargin: {
    type: Boolean,
    default: true,
  },
  variant: {
    type: String,
    default: 'full',
    validator: (value) => ['full', 'mini'].includes(value),
  },
  businessName: {
    type: String,
    default: 'A&M Moda Damska',
  },
  externalUrl: {
    type: String,
    default:
      'https://www.google.com/search?q=ammoda&oq=ammoda&gs_lcrp=EgZjaHJvbWUqBggAEEUYOzIGCAAQRRg7MgYIARBFGDwyBggCEEUYPDILCAMQABgKGBMYgAQyCQgEEC4YExiABDILCAUQABgKGBMYgAQyCwgGEAAYChgTGIAEMgYIBxBFGDzSAQgyMTgzajBqN6gCALACAA&sourceid=chrome&ie=UTF-8',
  },
})

const FALLBACK_RATING = 4.7
const FALLBACK_RATING_COUNT = 171

const rating = ref(FALLBACK_RATING)
const ratingCount = ref(FALLBACK_RATING_COUNT)

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
  const res = await apiGetJson('api/reviews.php')
  if (!res.ok) return
  const data = res.data
  if (!data || data.ok !== true) return
  const fetchedRating = Number(data.rating)
  const fetchedCount = Number(data.ratingCount)
  if (Number.isFinite(fetchedRating) && fetchedRating > 0) rating.value = fetchedRating
  if (Number.isFinite(fetchedCount) && fetchedCount >= 0) ratingCount.value = fetchedCount
})
</script>

<style scoped>
.google-reviews-card {
  display: flex;
  justify-content: center;
  width: 100%;
}

.google-reviews-card__link {
  text-decoration: none;
  color: inherit;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

.google-reviews-card__link:focus-visible {
  outline: 2px solid rgba(170, 210, 255, 0.7);
  outline-offset: 6px;
  border-radius: 16px;
}

.google-reviews-card--full-container {
  height: 10vh;
  align-items: center;
  justify-content: center;
}

.google-reviews-card--with-margin {
  margin-bottom: 5vh;
}

@media (max-width: 749.98px) {
  .google-reviews-card--with-margin {
    margin-bottom: 12vh;
  }

  
  .google-reviews-card__inner {
    background: transparent;
    border: 0;
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    box-shadow: none;
  }

  
  .google-reviews-card__row {
    flex-wrap: wrap;
    gap: 6px;
  }

  .google-reviews-card__count {
    flex-basis: 100%;
    margin: 2px 0 0 0;
  }

  .google-reviews-card__row--mini {
    flex-wrap: wrap;
    gap: 6px;
  }

  .google-reviews-card__count--mini {
    flex-basis: 100%;
  }
}

.google-reviews-card__inner {
  position: relative;
  width: fit-content;
  max-width: min(560px, 92vw);
  padding: 12px 16px;
  border-radius: 14px;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 12px 24px rgba(0, 0, 0, 0.82),
    0 36px 92px rgba(0, 0, 0, 0.88);
  text-align: center;
  text-shadow:
    0 1px 1px rgba(0, 0, 0, 0.5),
    0 6px 18px rgba(0, 0, 0, 0.18);
  filter:
    drop-shadow(0 12px 22px rgba(0, 0, 0, 0.82))
    drop-shadow(0 34px 86px rgba(0, 0, 0, 0.88));
}

.google-reviews-card__corner-indicator {
  position: absolute;
  top: 10px;
  right: 10px;
  opacity: 0.75;
  pointer-events: none;
  color: rgba(255, 255, 255, 0.9);
  filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.6));
}

.google-reviews-card__mini {
  width: fit-content;
  max-width: min(560px, 92vw);
  display: flex;
  align-items: center;
  justify-content: center;
}


.google-reviews-card__mini-inner {
  --google-reviews-card-mini-shadow:
    0 3px 10px rgba(0, 0, 0, 0.96),
    0 0 22px rgba(0, 0, 0, 0.72);

  width: fit-content;
  max-width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 4px;
  padding: 2px 4px;
  margin: 0;
  background: transparent;
  border: 0;
  border-radius: 12px;
  box-shadow: none;
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  filter: none;
  text-shadow: var(--google-reviews-card-mini-shadow);
  text-align: center;
}

.google-reviews-card__google-bubble {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 8px;
  margin-bottom: 6px;
  border-radius: 12px;
  background: #ffffff;
  box-shadow:
    0 10px 22px rgba(0, 0, 0, 0.32),
    0 2px 6px rgba(0, 0, 0, 0.24);
}

.google-reviews-card__google-bubble::after {
  content: '';
  position: absolute;
  left: 50%;
  bottom: -6px;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid #ffffff;
  filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.22));
}

.google-reviews-card__mini .google-reviews-card__google-bubble .google-reviews-card__google-g {
  filter: none;
}


.google-reviews-card__row--mini {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.google-reviews-card__rating--mini {
  font-size: 0.95rem;
}

.google-reviews-card__stars--mini {
  gap: 2px;
  font-size: 19px;
}

.google-reviews-card__stars--mini :deep(.q-icon) {
  filter: drop-shadow(var(--google-reviews-card-mini-shadow));
}

.google-reviews-card__count--mini {
  font-size: 0.95rem;
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
  filter:
    drop-shadow(0 2px 6px rgba(0, 0, 0, 0.75))
    drop-shadow(0 10px 22px rgba(0, 0, 0, 0.55));
}

.google-reviews-card__mini .google-reviews-card__google-g {
  width: 32px;
  height: 32px;
  filter: drop-shadow(var(--google-reviews-card-mini-shadow));
}

@media (min-width: 750px) {
  .google-reviews-card__google-bubble {
    padding: 8px 10px;
    margin-bottom: 8px;
    border-radius: 14px;
  }

  .google-reviews-card__google-bubble::after {
    bottom: -6px;
    border-left-width: 7px;
    border-right-width: 7px;
    border-top-width: 7px;
  }

  .google-reviews-card__stars--mini {
    font-size: 26px;
  }

  .google-reviews-card__rating--mini {
    font-size: 1.25rem;
  }

  .google-reviews-card__count--mini {
    font-size: 1.25rem;
  }

  .google-reviews-card__mini .google-reviews-card__google-g {
    width: 42px;
    height: 42px;
  }

  .google-reviews-card--with-margin {
    margin-bottom: 10vh;
  }

  .google-reviews-card__inner {
    filter:
      drop-shadow(0 14px 26px rgba(0, 0, 0, 0.86))
      drop-shadow(0 40px 110px rgba(0, 0, 0, 0.9));
  }

  .google-reviews-card__mini-inner {
    gap: 6px;
  }

  .google-reviews-card__mini .google-reviews-card__google-g {
    width: 40px;
    height: 40px;
  }

  .google-reviews-card__rating--mini,
  .google-reviews-card__count--mini {
    font-size: 1.1rem;
  }

  .google-reviews-card__stars--mini {
    font-size: 19px;
    margin-bottom: 1px;
  }
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
  gap: 0px;
  font-family: 'Poppins', sans-serif;
  color: rgba(255, 255, 255, 0.9);
  text-shadow:
    0 2px 10px rgba(0, 0, 0, 0.92),
    0 0 18px rgba(0, 0, 0, 0.55);
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
  margin-left: 10px;
  color: rgba(255, 214, 102, 0.95);
}

.google-reviews-card__count {
  font-size: 1.05rem;
  margin: 0 0 0 15px;
  font-weight: 500;
  padding-right: 10px;
  padding-left: 10px;
  color: rgba(170, 210, 255, 0.9);
}

.google-reviews-card__count.google-reviews-card__count--mini {
  margin: 0;
}

</style>

