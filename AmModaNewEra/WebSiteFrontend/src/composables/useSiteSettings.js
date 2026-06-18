import { computed, ref } from 'vue'
import {
  HOMEPAGE_NEWS_SECTION_ENABLED,
  HOMEPAGE_NEWS_SECTION_TEXT,
  OPENING_HOURS,
} from '../constants/siteInfo.js'
import { apiGetJson } from '../utils/apiJson.js'

const newsText = ref(HOMEPAGE_NEWS_SECTION_TEXT)
const newsEnabled = ref(Boolean(HOMEPAGE_NEWS_SECTION_ENABLED))
const openingHours = ref([...OPENING_HOURS])
const todayHours = ref(OPENING_HOURS.find((row) => row.dayIndex === new Date().getDay())?.hours ?? 'Zamknięte')

const now = ref(new Date())
/** @type {ReturnType<typeof setInterval> | null} */
let dayWatchTimer = null

/** @type {Promise<void> | null} */
let loadPromise = null

function findNextDateForDayIndex(dayIndex, fromDate) {
  const currentDayIndex = fromDate.getDay()
  const daysAhead = (dayIndex - currentDayIndex + 7) % 7
  const nextDate = new Date(fromDate)
  nextDate.setHours(0, 0, 0, 0)
  nextDate.setDate(nextDate.getDate() + daysAhead)
  return nextDate
}

function formatDateKey(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function buildOverridesMap(overrides) {
  const map = new Map()
  if (!Array.isArray(overrides)) return map
  overrides.forEach((item) => {
    if (!item?.override_date) return
    map.set(String(item.override_date), String(item.hours ?? ''))
  })
  return map
}

function computeEffectiveOpeningHours(weeklyRows, overrides, fromDate) {
  const overrideMap = buildOverridesMap(overrides)
  return weeklyRows.map((row) => {
    const dayIndex = Number(row.day_index ?? row.dayIndex)
    const nextDate = findNextDateForDayIndex(dayIndex, fromDate)
    const dateKey = formatDateKey(nextDate)
    const hours = overrideMap.get(dateKey) ?? String(row.hours ?? '')

    return {
      dayIndex,
      label: String(row.label ?? ''),
      hours,
    }
  })
}

function computeTodayHours(weeklyRows, overrides, fromDate) {
  const overrideMap = buildOverridesMap(overrides)
  const dateKey = formatDateKey(fromDate)
  if (overrideMap.has(dateKey)) {
    return overrideMap.get(dateKey) ?? 'Zamknięte'
  }

  const dayIndex = fromDate.getDay()
  const weeklyRow = weeklyRows.find((row) => Number(row.day_index ?? row.dayIndex) === dayIndex)
  return weeklyRow ? String(weeklyRow.hours ?? 'Zamknięte') : 'Zamknięte'
}

let weeklyRowsCache = OPENING_HOURS.map((row) => ({
  day_index: row.dayIndex,
  label: row.label,
  hours: row.hours,
}))
let overridesCache = []

function applyComputedHours() {
  const currentDate = now.value
  openingHours.value = computeEffectiveOpeningHours(weeklyRowsCache, overridesCache, currentDate)
  todayHours.value = computeTodayHours(weeklyRowsCache, overridesCache, currentDate)
}

function applySettingsPayload(data) {
  if (!data || data.ok !== true) return

  if (data.news) {
    if (typeof data.news.text === 'string') newsText.value = data.news.text
    if (typeof data.news.enabled === 'boolean') newsEnabled.value = data.news.enabled
  }

  if (Array.isArray(data.weeklyHours) && data.weeklyHours.length > 0) {
    weeklyRowsCache = data.weeklyHours
  } else if (Array.isArray(data.openingHours) && data.openingHours.length > 0) {
    weeklyRowsCache = data.openingHours.map((row) => ({
      day_index: row.dayIndex,
      label: row.label,
      hours: row.hours,
    }))
  }

  if (Array.isArray(data.overrides)) {
    overridesCache = data.overrides
  }

  applyComputedHours()
}

function ensureDayWatchTimer() {
  if (dayWatchTimer != null) return

  let lastDayKey = formatDateKey(now.value)
  dayWatchTimer = setInterval(() => {
    now.value = new Date()
    const dayKey = formatDateKey(now.value)
    if (dayKey === lastDayKey) return

    lastDayKey = dayKey
    loadPromise = null
    void ensureSiteSettingsLoaded()
  }, 60_000)
}

function ensureSiteSettingsLoaded() {
  if (!loadPromise) {
    loadPromise = apiGetJson('api/settings.php').then((res) => {
      if (res.ok) applySettingsPayload(res.data)
      ensureDayWatchTimer()
    })
  }
  return loadPromise
}

export function prefetchSiteSettings() {
  return ensureSiteSettingsLoaded()
}

export function useSiteSettings() {
  const homepageNewsSectionText = computed(() => newsText.value)
  const homepageNewsSectionEnabled = computed(() => newsEnabled.value)
  const effectiveOpeningHours = computed(() => openingHours.value)
  const effectiveTodayHours = computed(() => todayHours.value)

  return {
    homepageNewsSectionText,
    homepageNewsSectionEnabled,
    effectiveOpeningHours,
    effectiveTodayHours,
    prefetchSiteSettings,
  }
}
