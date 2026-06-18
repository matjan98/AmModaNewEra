/**
 * Polish statutory public holidays (days off work) per the Act of 18 January 1951
 * on days off work (consolidated text Dz.U. 2025 poz. 296).
 * Includes Wigilia (24 December) effective from 1 February 2025.
 */

export const POLISH_STATUTORY_HOLIDAYS = [
  { type: 'fixed', month: 1, day: 1, name: 'Nowy Rok' },
  { type: 'fixed', month: 1, day: 6, name: 'Święto Trzech Króli' },
  { type: 'easter', offsetDays: 0, name: 'pierwszy dzień Wielkiej Nocy' },
  { type: 'easter', offsetDays: 1, name: 'drugi dzień Wielkiej Nocy' },
  { type: 'fixed', month: 5, day: 1, name: 'Święto Państwowe' },
  { type: 'fixed', month: 5, day: 3, name: 'Święto Narodowe Trzeciego Maja' },
  { type: 'easter', offsetDays: 49, name: 'pierwszy dzień Zielonych Świątek' },
  { type: 'easter', offsetDays: 60, name: 'dzień Bożego Ciała' },
  { type: 'fixed', month: 8, day: 15, name: 'Wniebowzięcie Najświętszej Maryi Panny' },
  { type: 'fixed', month: 11, day: 1, name: 'Wszystkich Świętych' },
  { type: 'fixed', month: 11, day: 11, name: 'Narodowe Święto Niepodległości' },
  { type: 'fixed', month: 12, day: 24, name: 'Wigilia Bożego Narodzenia' },
  { type: 'fixed', month: 12, day: 25, name: 'pierwszy dzień Bożego Narodzenia' },
  { type: 'fixed', month: 12, day: 26, name: 'drugi dzień Bożego Narodzenia' },
]

/**
 * Gregorian Easter Sunday (Meeus/Jones/Butcher algorithm).
 */
export function calculateEasterSunday(year) {
  const a = year % 19
  const b = Math.floor(year / 100)
  const c = year % 100
  const d = Math.floor(b / 4)
  const e = b % 4
  const f = Math.floor((b + 8) / 25)
  const g = Math.floor((b - f + 1) / 3)
  const h = (19 * a + b - d - g + 15) % 30
  const i = Math.floor(c / 4)
  const k = c % 4
  const l = (32 + 2 * e + 2 * i - h - k) % 7
  const m = Math.floor((a + 11 * h + 22 * l) / 451)
  const month = Math.floor((h + l - 7 * m + 114) / 31)
  const day = ((h + l - 7 * m + 114) % 31) + 1

  return new Date(year, month - 1, day)
}

function padTwo(value) {
  return String(value).padStart(2, '0')
}

function toIsoDate(date) {
  return `${date.getFullYear()}-${padTwo(date.getMonth() + 1)}-${padTwo(date.getDate())}`
}

function addDays(date, days) {
  const next = new Date(date)
  next.setDate(next.getDate() + days)
  return next
}

function startOfDay(date) {
  const normalized = new Date(date)
  normalized.setHours(0, 0, 0, 0)
  return normalized
}

/**
 * @returns {Array<{ date: string, name: string }>}
 */
export function getPolishPublicHolidaysForYear(year) {
  const easterSunday = calculateEasterSunday(year)
  const holidays = []

  for (const entry of POLISH_STATUTORY_HOLIDAYS) {
    if (entry.type === 'fixed') {
      holidays.push({
        date: `${year}-${padTwo(entry.month)}-${padTwo(entry.day)}`,
        name: entry.name,
      })
      continue
    }

    holidays.push({
      date: toIsoDate(addDays(easterSunday, entry.offsetDays)),
      name: entry.name,
    })
  }

  return holidays.sort((left, right) => left.date.localeCompare(right.date))
}

/**
 * @returns {Array<{ date: string, name: string }>}
 */
export function getNextPolishPublicHolidays(fromDate = new Date(), count = 3) {
  const today = startOfDay(fromDate)
  const todayIso = toIsoDate(today)
  const startYear = today.getFullYear()
  const candidates = []

  for (let year = startYear; year <= startYear + 1; year += 1) {
    for (const holiday of getPolishPublicHolidaysForYear(year)) {
      if (holiday.date >= todayIso) {
        candidates.push(holiday)
      }
    }
  }

  return candidates
    .sort((left, right) => left.date.localeCompare(right.date))
    .slice(0, count)
}
