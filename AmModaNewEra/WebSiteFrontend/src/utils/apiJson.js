import { getApiUrl } from './apiUrl.js'

const DEFAULT_TIMEOUT_MS = 8000

/**
 * Fetches JSON from backend using `getApiUrl()` and normalizes failure handling.
 *
 * @param {string} path
 * @param {{ signal?: AbortSignal, timeoutMs?: number }} [options]
 * @returns {Promise<{ ok: true, data: any, status: number } | { ok: false, status?: number, error?: unknown }>}
 */
export async function apiGetJson(path, options = {}) {
  const { signal: externalSignal, timeoutMs = DEFAULT_TIMEOUT_MS } = options

  const controller = new AbortController()
  const timeoutId = timeoutMs > 0 ? setTimeout(() => controller.abort(), timeoutMs) : null

  const onExternalAbort = () => controller.abort()
  if (externalSignal) {
    if (externalSignal.aborted) controller.abort()
    else externalSignal.addEventListener('abort', onExternalAbort, { once: true })
  }

  try {
    const res = await fetch(getApiUrl(path), { signal: controller.signal })
    if (!res.ok) return { ok: false, status: res.status }
    const data = await res.json()
    return { ok: true, data, status: res.status }
  } catch (error) {
    return { ok: false, error }
  } finally {
    if (timeoutId != null) clearTimeout(timeoutId)
    if (externalSignal) externalSignal.removeEventListener('abort', onExternalAbort)
  }
}
