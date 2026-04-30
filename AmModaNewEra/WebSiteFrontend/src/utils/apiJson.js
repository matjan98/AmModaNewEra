import { getApiUrl } from './apiUrl.js'

/**
 * Fetches JSON from backend using `getApiUrl()` and normalizes failure handling.
 *
 * @param {string} path
 * @param {{ signal?: AbortSignal }} [options]
 * @returns {Promise<{ ok: true, data: any } | { ok: false, error?: unknown }>}
 */
export async function apiGetJson(path, options = {}) {
  try {
    const res = await fetch(getApiUrl(path), { signal: options.signal })
    if (!res.ok) return { ok: false }
    const data = await res.json()
    return { ok: true, data }
  } catch (error) {
    return { ok: false, error }
  }
}

