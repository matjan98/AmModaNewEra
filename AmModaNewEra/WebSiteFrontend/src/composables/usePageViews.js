import { apiGetJson } from '../utils/apiJson.js'

/** @type {Promise<void> | null} */
let incrementPromise = null

/**
 * Registers one page view per full document load (public site only).
 */
export function prefetchPageViewIncrement() {
  if (!incrementPromise) {
    incrementPromise = apiGetJson('api/page-views.php').then(() => {})
  }
  return incrementPromise
}
