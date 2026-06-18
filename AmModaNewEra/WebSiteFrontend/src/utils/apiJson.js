import { getApiUrl } from './apiUrl.js'

const DEFAULT_TIMEOUT_MS = 8000

/**
 * @param {string} path
 * @param {{
 *   method?: string,
 *   body?: unknown,
 *   signal?: AbortSignal,
 *   timeoutMs?: number,
 *   credentials?: RequestCredentials,
 *   headers?: Record<string, string>,
 * }} [options]
 */
export async function apiRequestJson(path, options = {}) {
  const {
    method = 'GET',
    body,
    signal: externalSignal,
    timeoutMs = DEFAULT_TIMEOUT_MS,
    credentials,
    headers = {},
  } = options

  const controller = new AbortController()
  const timeoutId = timeoutMs > 0 ? setTimeout(() => controller.abort(), timeoutMs) : null

  const onExternalAbort = () => controller.abort()
  if (externalSignal) {
    if (externalSignal.aborted) controller.abort()
    else externalSignal.addEventListener('abort', onExternalAbort, { once: true })
  }

  const requestHeaders = { ...headers }
  let requestBody = body

  if (body !== undefined && !(body instanceof FormData)) {
    requestHeaders['Content-Type'] = 'application/json'
    requestBody = JSON.stringify(body)
  }

  try {
    const res = await fetch(getApiUrl(path), {
      method,
      body: requestBody,
      credentials,
      headers: requestHeaders,
      signal: controller.signal,
    })

    let data = null
    const contentType = res.headers.get('content-type') ?? ''
    if (contentType.includes('application/json')) {
      data = await res.json()
    }

    if (!res.ok) {
      return { ok: false, status: res.status, data, error: data?.error }
    }

    return { ok: true, data, status: res.status }
  } catch (error) {
    return { ok: false, error }
  } finally {
    if (timeoutId != null) clearTimeout(timeoutId)
    if (externalSignal) externalSignal.removeEventListener('abort', onExternalAbort)
  }
}

/**
 * @param {string} path
 * @param {{ signal?: AbortSignal, timeoutMs?: number, credentials?: RequestCredentials }} [options]
 */
export async function apiGetJson(path, options = {}) {
  return apiRequestJson(path, options)
}

/**
 * @param {string} path
 * @param {unknown} body
 * @param {{ credentials?: RequestCredentials }} [options]
 */
export async function apiPostJson(path, body, options = {}) {
  return apiRequestJson(path, {
    method: 'POST',
    body,
    credentials: options.credentials,
  })
}

/**
 * @param {string} path
 * @param {unknown} body
 * @param {{ credentials?: RequestCredentials }} [options]
 */
export async function apiPutJson(path, body, options = {}) {
  return apiRequestJson(path, {
    method: 'PUT',
    body,
    credentials: options.credentials ?? 'include',
  })
}

/**
 * @param {string} path
 * @param {FormData} formData
 */
export async function apiPostForm(path, formData) {
  return apiRequestJson(path, {
    method: 'POST',
    body: formData,
    credentials: 'include',
  })
}
