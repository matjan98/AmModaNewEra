import { computed, onMounted, onUnmounted, ref, unref } from 'vue'

const DEFAULT_CLOSED_LABEL = 'Zamknięte'
const DEFAULT_OPEN_HEADING_LABEL = 'Dziś otwarte!'
const DEFAULT_SUNDAY_HEADING_LABEL = 'Godziny otwarcia'

const OPEN_STATUS_TICK_MS = 60_000

function parseOpeningRangeMinutes(hoursStr) {
  if (!hoursStr || hoursStr === DEFAULT_CLOSED_LABEL) return null
  const m = hoursStr.trim().match(/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/)
  if (!m) return null
  const startMin = Number(m[1]) * 60 + Number(m[2])
  const endMin = Number(m[3]) * 60 + Number(m[4])
  return { startMin, endMin }
}

/**
 * Shared store opening-hours logic.
 *
 * @param {Array<{dayIndex:number,label:string,hours:string}>} openingHours
 * @param {{ liveClock?: boolean, closedLabel?: string, todayHours?: import('vue').MaybeRef<string|null|undefined> }} [options]
 */
export function useOpeningHours(openingHours, options = {}) {
  const closedLabel = options.closedLabel ?? DEFAULT_CLOSED_LABEL
  const liveClock = options.liveClock === true

  const now = ref(new Date())
  let tickTimer = null

  const todayDayIndex = computed(() => now.value.getDay())
  const resolvedOpeningHours = computed(() => unref(openingHours) ?? [])
  const todayRow = computed(() => resolvedOpeningHours.value.find((h) => h.dayIndex === todayDayIndex.value) ?? null)
  const todayHours = computed(() => {
    const override = unref(options.todayHours)
    if (typeof override === 'string' && override.length > 0) {
      return override
    }
    return todayRow.value?.hours ?? closedLabel
  })
  const isOpenToday = computed(() => todayHours.value !== closedLabel)

  const storeHoursHeadingLabel = computed(() =>
    todayDayIndex.value === 0 ? DEFAULT_SUNDAY_HEADING_LABEL : DEFAULT_OPEN_HEADING_LABEL,
  )

  const isStoreOpenNow = computed(() => {
    const range = parseOpeningRangeMinutes(todayHours.value)
    if (!range) return false
    const mins = now.value.getHours() * 60 + now.value.getMinutes()
    return mins >= range.startMin && mins < range.endMin
  })

  onMounted(() => {
    if (!liveClock) return
    now.value = new Date()
    tickTimer = window.setInterval(() => {
      now.value = new Date()
    }, OPEN_STATUS_TICK_MS)
  })

  onUnmounted(() => {
    if (tickTimer != null) {
      window.clearInterval(tickTimer)
      tickTimer = null
    }
  })

  return {
    now,
    todayDayIndex,
    todayRow,
    todayHours,
    isOpenToday,
    isStoreOpenNow,
    storeHoursHeadingLabel,
  }
}

