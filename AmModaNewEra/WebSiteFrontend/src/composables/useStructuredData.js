import { computed } from 'vue'
import { useHead } from '@unhead/vue'
import { SITE_ORIGIN } from '../constants/siteOrigin.js'
import {
  BUSINESS_ADDRESS,
  BUSINESS_DESCRIPTION,
  BUSINESS_GEO,
  BUSINESS_NAME,
  FACEBOOK_URL,
  MAPS_URL,
  OPENING_HOURS,
  PHONE_E164,
} from '../constants/siteInfo.js'
import { useGoogleReviews } from './useGoogleReviews.js'

const DAY_OF_WEEK = {
  0: 'Sunday',
  1: 'Monday',
  2: 'Tuesday',
  3: 'Wednesday',
  4: 'Thursday',
  5: 'Friday',
  6: 'Saturday',
}

/**
 * Convert the human opening-hours rows (e.g. "9:00 - 18:00") into schema.org
 * OpeningHoursSpecification entries. Closed days ("Zamknięte") are skipped.
 */
function buildOpeningHoursSpecification(rows) {
  const spec = []
  for (const row of rows) {
    const match = String(row.hours).match(/(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})/)
    const dayOfWeek = DAY_OF_WEEK[row.dayIndex]
    if (!match || !dayOfWeek) continue
    spec.push({
      '@type': 'OpeningHoursSpecification',
      dayOfWeek,
      opens: `${match[1].padStart(2, '0')}:${match[2]}`,
      closes: `${match[3].padStart(2, '0')}:${match[4]}`,
    })
  }
  return spec
}

/**
 * Inject the ClothingStore JSON-LD into <head> via @unhead/vue.
 *
 * The aggregateRating is sourced from the live Google reviews state
 * (`useGoogleReviews`), so JS-rendering crawlers (and the prerendered snapshot,
 * when the API is reachable at build time) get the real rating instead of a
 * hardcoded value.
 */
export function useBusinessJsonLd() {
  const { rating, ratingCount } = useGoogleReviews()

  const jsonLdString = computed(() => {
    const data = {
      '@context': 'https://schema.org',
      '@type': 'ClothingStore',
      name: BUSINESS_NAME,
      description: BUSINESS_DESCRIPTION,
      url: `${SITE_ORIGIN}/`,
      image: `${SITE_ORIGIN}/icons/open-graph-1200x630.jpg`,
      telephone: PHONE_E164,
      address: {
        '@type': 'PostalAddress',
        ...BUSINESS_ADDRESS,
      },
      geo: {
        '@type': 'GeoCoordinates',
        latitude: BUSINESS_GEO.latitude,
        longitude: BUSINESS_GEO.longitude,
      },
      hasMap: MAPS_URL,
      openingHoursSpecification: buildOpeningHoursSpecification(OPENING_HOURS),
      sameAs: [FACEBOOK_URL],
    }

    if (ratingCount.value > 0) {
      data.aggregateRating = {
        '@type': 'AggregateRating',
        ratingValue: String(rating.value),
        reviewCount: String(ratingCount.value),
        bestRating: '5',
        worstRating: '1',
      }
    }

    return JSON.stringify(data)
  })

  useHead({
    script: [
      {
        type: 'application/ld+json',
        key: 'ld-clothingstore',
        innerHTML: jsonLdString,
      },
    ],
  })
}
