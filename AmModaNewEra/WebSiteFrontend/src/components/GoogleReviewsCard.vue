<template>
  <div class="google-reviews-card" :class="{ 'google-reviews-card--with-margin': withMargin }" aria-label="Ocena Google">
    <div v-if="variant === 'mini'" class="google-reviews-card__mini">
      <div class="google-reviews-card__mini-inner">
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

        <div class="google-reviews-card__row google-reviews-card__row--mini">
          <span class="google-reviews-card__rating google-reviews-card__rating--mini">{{ ratingText }}</span>
          <span class="google-reviews-card__stars google-reviews-card__stars--mini" aria-hidden="true">
            <q-icon name="star" size="1em" />
            <q-icon name="star" size="1em" />
            <q-icon name="star" size="1em" />
            <q-icon name="star" size="1em" />
            <q-icon name="star_half" size="1em" />
          </span>
          <span class="google-reviews-card__count google-reviews-card__count--mini">{{ reviewsCountText }}</span>
        </div>
      </div>
    </div>

    <div v-else class="google-reviews-card__inner">
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
        <span class="google-reviews-card__rating">{{ ratingText }}</span>
        <span class="google-reviews-card__stars" aria-hidden="true">
          <q-icon name="star" size="16px" />
          <q-icon name="star" size="16px" />
          <q-icon name="star" size="16px" />
          <q-icon name="star" size="16px" />
          <q-icon name="star_half" size="16px" />
        </span>
        <span class="google-reviews-card__count">{{ reviewsCountText }}</span>
        <q-icon name="info" size="16px" class="google-reviews-card__info" aria-hidden="true" />
      </div>
    </div>
  </div>
</template>

<script setup>
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
  ratingText: {
    type: String,
    default: '4,7',
  },
  reviewsCountText: {
    type: String,
    default: '171 opinii',
  },
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

@media (max-width: 600px) {
  .google-reviews-card--with-margin {
    margin-bottom: 12vh;
  }
}

.google-reviews-card__inner,
.google-reviews-card__mini-inner {
  filter:
    drop-shadow(0 12px 22px rgba(0, 0, 0, 0.82))
    drop-shadow(0 34px 86px rgba(0, 0, 0, 0.88));
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
    0 12px 24px rgba(0, 0, 0, 0.82),
    0 36px 92px rgba(0, 0, 0, 0.88);
  text-align: center;
  text-shadow:
    0 1px 1px rgba(0, 0, 0, 0.5),
    0 6px 18px rgba(0, 0, 0, 0.18);
}

.google-reviews-card__mini {
  width: fit-content;
  max-width: min(560px, 92vw);
  display: flex;
  align-items: center;
  justify-content: center;
}

.google-reviews-card__mini-inner {
  width: fit-content;
  max-width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 4px;
  padding: 8px 12px;
  margin: 0;
  background: rgba(0, 0, 0, 0.22);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.06),
    0 12px 26px rgba(0, 0, 0, 0.78);
  backdrop-filter: none;
  -webkit-backdrop-filter: none;
  text-shadow:
    0 2px 10px rgba(0, 0, 0, 0.9),
    0 0 16px rgba(0, 0, 0, 0.55);
  text-align: center;
}

@media (min-width: 1000px) {
  .google-reviews-card--with-margin {
    margin-bottom: 10vh;
  }

  .google-reviews-card__inner,
  .google-reviews-card__mini-inner {
    filter:
      drop-shadow(0 14px 26px rgba(0, 0, 0, 0.86))
      drop-shadow(0 40px 110px rgba(0, 0, 0, 0.9));
  }
}

.google-reviews-card__row--mini {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.google-reviews-card__rating--mini {
  font-size: 0.95rem;
}

.google-reviews-card__stars--mini {
  gap: 1px;
  font-size: 12px;
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
  width: 22px;
  height: 22px;
}

@media (min-width: 1000px) {
  .google-reviews-card__mini-inner {
    gap: 6px;
  }

  .google-reviews-card__mini .google-reviews-card__google-g {
    width: 30px;
    height: 30px;
  }

  .google-reviews-card__rating--mini,
  .google-reviews-card__count--mini {
    font-size: 1.1rem;
  }

  .google-reviews-card__stars--mini {
    font-size: 15px;
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
  gap: 10px;
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

